<?php
declare(strict_types=1);
/*
 * Buzon FE: lectura SOLO LECTURA del buzon IMAP de facturaelectronica@lospaticas.com y extraccion de comprobantes
 * electronicos de Hacienda (FE/TE/NC/ND/FEC/FEE/REP v4.3/v4.4, MensajeHacienda, MensajeReceptor, PDF, ZIP).
 * - IMAP: EXAMINE (carpeta en solo lectura) + BODY.PEEK (no marca \Seen). Nunca STORE/COPY/MOVE/EXPUNGE/APPEND.
 * - No envia correos ni nada a Hacienda.
 */

const BZ_BASE = '/var/lib/lospaticas/fe/buzon';
const BZ_ESTADO = '/var/lib/lospaticas/panel/buzon-estado.json';
const BZ_LOG = '/var/lib/lospaticas/panel/buzon-sync.log';
const BZ_TIPOS = ['FacturaElectronica' => 'FE', 'TiqueteElectronico' => 'TE', 'NotaCreditoElectronica' => 'NC',
    'NotaDebitoElectronica' => 'ND', 'FacturaElectronicaCompra' => 'FEC', 'FacturaElectronicaExportacion' => 'FEE',
    'ReciboElectronicoPago' => 'REP', 'MensajeHacienda' => 'MH', 'MensajeReceptor' => 'MR'];

/* =========================== Cliente IMAP minimo (solo lectura) =========================== */
final class BzImap
{
    /** @var resource */
    private $s;
    private int $tag = 0;
    public array $cap = [];

    public function __construct(string $host, int $port, bool $verify = true, int $timeout = 30)
    {
        $ctx = stream_context_create(['ssl' => ['verify_peer' => $verify, 'verify_peer_name' => $verify, 'peer_name' => $host, 'SNI_enabled' => true]]);
        $s = @stream_socket_client("ssl://$host:$port", $en, $es, $timeout, STREAM_CLIENT_CONNECT, $ctx);
        if (!$s) throw new RuntimeException("No se pudo conectar a $host:$port ($es)");
        $this->s = $s;
        stream_set_timeout($this->s, 180);
        $l = $this->line();
        if (!preg_match('/^\* (OK|PREAUTH)/i', $l)) throw new RuntimeException('Saludo IMAP inesperado: ' . substr(trim($l), 0, 120));
        $this->capability();
    }

    private function line(): string
    {
        $l = fgets($this->s);
        if ($l === false) {
            $m = stream_get_meta_data($this->s);
            throw new RuntimeException($m['timed_out'] ? 'Tiempo de espera IMAP agotado' : 'Conexion IMAP cerrada');
        }
        return $l;
    }

    private function read(int $n): string
    {
        $b = '';
        while (strlen($b) < $n) {
            $x = fread($this->s, min(262144, $n - strlen($b)));
            if ($x === false || $x === '') {
                $m = stream_get_meta_data($this->s);
                if ($m['timed_out'] || feof($this->s)) throw new RuntimeException('Lectura IMAP incompleta');
                continue;
            }
            $b .= $x;
        }
        return $b;
    }

    /** Envia un comando y devuelve las respuestas no etiquetadas: [['t' => texto con \x01N\x01 por literal, 'l' => [literales]]]. */
    public function cmd(string $c, ?string $comoLog = null, ?string $continuacion = null): array
    {
        $t = 'B' . str_pad((string)++$this->tag, 4, '0', STR_PAD_LEFT);
        if (preg_match('/^\s*(STORE|COPY|MOVE|EXPUNGE|APPEND|DELETE|RENAME|CREATE|SELECT|UID\s+(STORE|COPY|MOVE|EXPUNGE))\b/i', $c)) {
            throw new LogicException('Comando IMAP no permitido (solo lectura)');
        }
        fwrite($this->s, "$t $c\r\n");
        $out = [];
        while (true) {
            $l = $this->line();
            if ($continuacion !== null && str_starts_with($l, '+')) { fwrite($this->s, $continuacion . "\r\n"); $continuacion = null; continue; }
            $cur = ['t' => '', 'l' => []];
            while (preg_match('/\{(\d+)\}\r?\n$/', $l, $m)) {
                $cur['t'] .= substr($l, 0, -strlen($m[0])) . "\x01" . count($cur['l']) . "\x01";
                $cur['l'][] = $this->read((int)$m[1]);
                $l = $this->line();
            }
            $cur['t'] .= rtrim($l, "\r\n");
            if (str_starts_with($cur['t'], "$t ")) {
                if (!preg_match("/^$t OK/i", $cur['t'])) throw new RuntimeException('IMAP ' . ($comoLog ?? strtok($c, ' ')) . ': ' . substr($cur['t'], strlen($t) + 1, 200));
                return $out;
            }
            $out[] = $cur;
        }
    }

    public function capability(): void
    {
        foreach ($this->cmd('CAPABILITY') as $r) if (preg_match('/^\* CAPABILITY (.+)$/i', $r['t'], $m)) $this->cap = array_map('strtoupper', explode(' ', $m[1]));
    }

    public static function q(string $s): string { return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $s) . '"'; }

    public function login(string $user, string $pass): void
    {
        if (in_array('AUTH=PLAIN', $this->cap, true)) {
            $b = base64_encode("\0$user\0$pass");
            if (in_array('SASL-IR', $this->cap, true)) $this->cmd("AUTHENTICATE PLAIN $b", 'AUTHENTICATE');
            else $this->cmd('AUTHENTICATE PLAIN', 'AUTHENTICATE', $b);
        } else {
            if (preg_match('/[\r\n\x00]/', $user . $pass)) throw new RuntimeException('Credenciales con caracteres no soportados por LOGIN');
            $this->cmd('LOGIN ' . self::q($user) . ' ' . self::q($pass), 'LOGIN');
        }
        $this->capability();
    }

    /** @return array<int, array{nombre:string, visible:string, flags:string}> */
    public function carpetas(): array
    {
        $r = [];
        foreach ($this->cmd('LIST "" "*"') as $x) {
            if (!preg_match('/^\* LIST \(([^)]*)\) (?:NIL|"(?:[^"\\\\]|\\\\.)*") (.+)$/i', $x['t'], $m)) continue;
            $n = $m[2];
            if (preg_match("/^\x01(\d+)\x01$/", $n, $k)) $n = $x['l'][(int)$k[1]];
            elseif ($n[0] === '"') $n = stripcslashes(substr($n, 1, -1));
            if (stripos($m[1], '\\Noselect') !== false || stripos($m[1], '\\NonExistent') !== false) continue;
            $v = @mb_convert_encoding($n, 'UTF-8', 'UTF7-IMAP');
            $r[] = ['nombre' => $n, 'visible' => is_string($v) && $v !== '' ? $v : $n, 'flags' => $m[1]];
        }
        return $r;
    }

    public function status(string $carpeta): array
    {
        $o = [];
        foreach ($this->cmd('STATUS ' . self::q($carpeta) . ' (MESSAGES UIDVALIDITY UIDNEXT)') as $x)
            foreach (['MESSAGES', 'UIDVALIDITY', 'UIDNEXT'] as $k) if (preg_match("/\\b$k (\\d+)/i", $x['t'], $m)) $o[strtolower($k)] = (int)$m[1];
        return $o;
    }

    /** Abre la carpeta en SOLO LECTURA. */
    public function examine(string $carpeta): array
    {
        $o = ['exists' => 0, 'uidvalidity' => 0];
        foreach ($this->cmd('EXAMINE ' . self::q($carpeta)) as $x) {
            if (preg_match('/^\* (\d+) EXISTS/i', $x['t'], $m)) $o['exists'] = (int)$m[1];
            if (preg_match('/\[UIDVALIDITY (\d+)\]/i', $x['t'], $m)) $o['uidvalidity'] = (int)$m[1];
        }
        return $o;
    }

    /** @return int[] */
    public function uids(): array
    {
        $u = [];
        foreach ($this->cmd('UID SEARCH ALL') as $x) if (preg_match('/^\* SEARCH ?(.*)$/i', $x['t'], $m)) foreach (preg_split('/\s+/', trim($m[1])) as $n) if ($n !== '') $u[] = (int)$n;
        sort($u);
        return $u;
    }

    /** Cabeceras y tamano de varios mensajes (BODY.PEEK: no cambia \Seen). */
    public function cabeceras(array $uids): array
    {
        $o = [];
        if (!$uids) return $o;
        foreach ($this->cmd('UID FETCH ' . implode(',', $uids) . ' (UID RFC822.SIZE INTERNALDATE BODY.PEEK[HEADER.FIELDS (FROM TO SUBJECT DATE MESSAGE-ID)])') as $x) {
            if (!preg_match('/^\* \d+ FETCH /i', $x['t']) || !preg_match('/\bUID (\d+)/i', $x['t'], $m)) continue;
            $r = ['uid' => (int)$m[1], 'size' => preg_match('/RFC822\.SIZE (\d+)/i', $x['t'], $s) ? (int)$s[1] : 0,
                  'internaldate' => preg_match('/INTERNALDATE "([^"]+)"/i', $x['t'], $d) ? $d[1] : null, 'hdr' => $x['l'][0] ?? ''];
            $o[$r['uid']] = $r;
        }
        return $o;
    }

    public function mensaje(int $uid): string
    {
        foreach ($this->cmd("UID FETCH $uid (UID BODY.PEEK[])") as $x) if (preg_match('/\bUID ' . $uid . '\b/', $x['t']) && isset($x['l'][0])) return $x['l'][0];
        throw new RuntimeException("Mensaje UID $uid no disponible");
    }

    public function logout(): void
    {
        try { $this->cmd('LOGOUT'); } catch (Throwable) {}
        @fclose($this->s);
    }
}

/* =========================== MIME =========================== */
function bz_hdr_decode(string $v): string
{
    $v = trim($v);
    if ($v === '') return '';
    if (str_contains($v, '=?')) {
        $d = @iconv_mime_decode($v, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        if ($d === false) $d = mb_decode_mimeheader($v);
        $v = $d;
    }
    return mb_check_encoding($v, 'UTF-8') ? $v : mb_convert_encoding($v, 'UTF-8', 'Windows-1252');
}

/** @return array<string, string[]> cabeceras en minusculas, desdobladas, sin decodificar */
function bz_headers(string $h): array
{
    $h = preg_replace("/\r?\n[ \t]+/", ' ', $h);
    $o = [];
    foreach (preg_split("/\r?\n/", $h) as $l) if (preg_match('/^([!-9;-~]+):\s*(.*)$/', $l, $m)) $o[strtolower($m[1])][] = $m[2];
    return $o;
}

/** "tipo/sub; a=b; c*=utf-8''x" -> ['v' => 'tipo/sub', 'p' => [...]] (RFC 2231 incluido) */
function bz_hparam(string $v): array
{
    $parts = []; $cur = ''; $q = false;
    for ($i = 0, $n = strlen($v); $i < $n; $i++) {
        $c = $v[$i];
        if ($c === '"' && ($i === 0 || $v[$i - 1] !== '\\')) $q = !$q;
        if ($c === ';' && !$q) { $parts[] = $cur; $cur = ''; continue; }
        $cur .= $c;
    }
    $parts[] = $cur;
    $val = strtolower(trim(array_shift($parts)));
    $p = []; $cont = [];
    foreach ($parts as $x) {
        if (!preg_match('/^\s*([^=\s]+)\s*=\s*(.*)$/s', $x, $m)) continue;
        $k = strtolower($m[1]); $w = trim($m[2]);
        if (strlen($w) > 1 && $w[0] === '"' && substr($w, -1) === '"') $w = stripcslashes(substr($w, 1, -1));
        if (preg_match('/^(.+?)\*(\d+)(\*?)$/', $k, $mm)) { $cont[$mm[1]][(int)$mm[2]] = [$w, $mm[3] === '*']; continue; }
        if (str_ends_with($k, '*')) { $p[substr($k, 0, -1)] = bz_rfc2231($w, true); continue; }
        $p[$k] = $w;
    }
    foreach ($cont as $k => $segs) {
        ksort($segs);
        $enc = $segs[array_key_first($segs)][1];
        $p[$k] = bz_rfc2231(implode('', array_column($segs, 0)), $enc);
    }
    return ['v' => $val, 'p' => $p];
}

function bz_rfc2231(string $w, bool $enc): string
{
    if (!$enc) return $w;
    if (preg_match("/^([^']*)'[^']*'(.*)$/s", $w, $m)) {
        $s = rawurldecode($m[2]); $cs = $m[1] ?: 'UTF-8';
        $r = @mb_convert_encoding($s, 'UTF-8', $cs);
        return is_string($r) ? $r : $s;
    }
    return rawurldecode($w);
}

/** Parte un mensaje en hojas: [['ctype','nombre','datos','disp']]. Incluye message/rfc822 reenviados. */
function bz_mime(string $raw, int $prof = 0): array
{
    if ($prof > 8) return [];
    $sep = strpos($raw, "\r\n\r\n"); $len = 4;
    $sep2 = strpos($raw, "\n\n");
    if ($sep === false || ($sep2 !== false && $sep2 < $sep)) { $sep = $sep2; $len = 2; }
    if ($sep === false) { $hraw = $raw; $body = ''; } else { $hraw = substr($raw, 0, $sep); $body = substr($raw, $sep + $len); }
    $h = bz_headers($hraw);
    $ct = bz_hparam($h['content-type'][0] ?? 'text/plain');
    $cd = bz_hparam($h['content-disposition'][0] ?? '');
    $cte = strtolower(trim($h['content-transfer-encoding'][0] ?? '7bit'));
    if (str_starts_with($ct['v'], 'multipart/') && !empty($ct['p']['boundary'])) {
        $b = preg_quote($ct['p']['boundary'], '/');
        $trozos = preg_split("/\r?\n?--$b(?:--)?[ \t]*(?:\r?\n|$)/", "\n" . $body);
        array_shift($trozos);              // preambulo
        $o = [];
        foreach ($trozos as $t) if (trim($t) !== '') $o = array_merge($o, bz_mime($t, $prof + 1));
        return $o;
    }
    $datos = match ($cte) {
        'base64' => (string)base64_decode(preg_replace('/[^A-Za-z0-9+\/=]/', '', $body)),
        'quoted-printable' => quoted_printable_decode($body),
        default => $body,
    };
    if ($ct['v'] === 'message/rfc822') return bz_mime($datos, $prof + 1);
    $nombre = $cd['p']['filename'] ?? $ct['p']['name'] ?? '';
    return [['ctype' => $ct['v'], 'nombre' => bz_hdr_decode($nombre), 'datos' => $datos, 'disp' => $cd['v']]];
}

function bz_nombre_seguro(string $n, string $def = 'archivo'): string
{
    $n = basename(str_replace('\\', '/', $n));
    $n = preg_replace('/[^A-Za-z0-9._-]+/', '_', $n) ?? '';
    $n = trim($n, '._');
    if ($n === '') $n = $def;
    return substr($n, -120);
}

/**
 * Hojas -> archivos candidatos [['clase' => xml|pdf, 'nombre', 'datos']] incluyendo el contenido de ZIP.
 * Limites: 200 entradas por ZIP, 30 MB por entrada, 100 MB descomprimido en total.
 */
function bz_archivos(array $hojas): array
{
    $o = [];
    foreach ($hojas as $h) {
        $n = strtolower($h['nombre']); $d = $h['datos']; $ct = $h['ctype'];
        if ($d === '') continue;
        if (str_ends_with($n, '.zip') || in_array($ct, ['application/zip', 'application/x-zip-compressed', 'application/x-zip'], true) || (str_starts_with($d, "PK\x03\x04") && $n !== '' && !str_ends_with($n, '.xml'))) {
            $o = array_merge($o, bz_zip($d, $h['nombre']));
            continue;
        }
        $c = bz_clase($n, $ct, $d);
        if ($c) $o[] = ['clase' => $c, 'nombre' => $h['nombre'] ?: ($c === 'pdf' ? 'documento.pdf' : 'documento.xml'), 'datos' => $d, 'origen' => ''];
    }
    return $o;
}

function bz_clase(string $n, string $ct, string $d): ?string
{
    if (str_ends_with($n, '.pdf') || $ct === 'application/pdf' || str_starts_with($d, '%PDF')) return str_starts_with(ltrim($d), '%PDF') ? 'pdf' : null;
    $t = ltrim(preg_replace('/^\xEF\xBB\xBF/', '', $d));
    if ((str_ends_with($n, '.xml') || str_contains($ct, 'xml') || ($n === '' && str_starts_with($t, '<?xml'))) && str_starts_with($t, '<')) return 'xml';
    return null;
}

function bz_zip(string $d, string $nombreZip): array
{
    $o = [];
    if (!class_exists('ZipArchive')) return $o;
    $tmp = tempnam(sys_get_temp_dir(), 'bzz');
    file_put_contents($tmp, $d);
    $z = new ZipArchive();
    if ($z->open($tmp, ZipArchive::RDONLY) === true) {
        $total = 0;
        for ($i = 0; $i < min($z->numFiles, 200); $i++) {
            $st = $z->statIndex($i);
            if (!$st || str_ends_with($st['name'], '/') || $st['size'] > 30 * 1048576 || ($total += $st['size']) > 100 * 1048576) continue;
            $n = strtolower($st['name']);
            if (!str_ends_with($n, '.xml') && !str_ends_with($n, '.pdf')) continue;
            $c = $z->getFromIndex($i);
            if ($c === false) continue;
            $cl = bz_clase($n, '', $c);
            if ($cl) $o[] = ['clase' => $cl, 'nombre' => basename($st['name']), 'datos' => $c, 'origen' => basename($nombreZip)];
        }
        $z->close();
    }
    @unlink($tmp);
    return $o;
}

/* =========================== XML de Hacienda =========================== */
function bz_num(?string $v): ?string
{
    $v = trim((string)$v);
    return $v === '' || !is_numeric($v) ? null : $v;
}

/** Interpreta un XML de Hacienda. null si no es un comprobante reconocido. */
function bz_xml(string $xml): ?array
{
    $xml = preg_replace('/^\xEF\xBB\xBF/', '', ltrim($xml));
    if (stripos($xml, '<!DOCTYPE') !== false || strlen($xml) > 20 * 1048576) return null;   // sin DTD/entidades (XXE)
    $d = new DOMDocument();
    $prev = libxml_use_internal_errors(true);
    $ok = $d->loadXML($xml, LIBXML_NONET | LIBXML_COMPACT);
    libxml_clear_errors(); libxml_use_internal_errors($prev);
    if (!$ok || !$d->documentElement) return null;
    $root = $d->documentElement->localName;
    if (!isset(BZ_TIPOS[$root])) return null;
    $x = new DOMXPath($d);
    $g = function (string $ruta) use ($x): string {
        $p = '/*' . implode('', array_map(fn($s) => "/*[local-name()='$s']", explode('/', $ruta)));
        return trim((string)$x->evaluate("string($p)"));
    };
    $ns = (string)$d->documentElement->namespaceURI;
    $r = ['raiz' => $root, 'tipo' => BZ_TIPOS[$root], 'version' => preg_match('/v(\d\.\d)/', $ns, $m) ? $m[1] : null, 'clave' => $g('Clave')];
    if (!preg_match('/^\d{50}$/', $r['clave'])) return null;
    if ($root === 'MensajeHacienda') {
        $msj = $g('Mensaje');
        $r += ['nit_emisor' => $g('NumeroCedulaEmisor'), 'nombre_emisor' => $g('NombreEmisor'), 'tipo_id_emisor' => $g('TipoIdentificacionEmisor'),
            'nit_receptor' => $g('NumeroCedulaReceptor'), 'nombre_receptor' => $g('NombreReceptor'),
            'estado' => ['1' => 'aceptado', '2' => 'aceptado_parcial', '3' => 'rechazado'][$msj] ?? null,
            'detalle' => $g('DetalleMensaje'), 'total_impuesto' => bz_num($g('MontoTotalImpuesto')), 'total_comprobante' => bz_num($g('TotalFactura'))];
        return $r;
    }
    if ($root === 'MensajeReceptor') {
        $r += ['nit_emisor' => $g('NumeroCedulaEmisor'), 'nit_receptor' => $g('NumeroCedulaReceptor'), 'mensaje' => $g('Mensaje'),
            'detalle' => $g('DetalleMensaje'), 'fecha_emision' => $g('FechaEmisionDoc')];
        return $r;
    }
    $moneda = $g('ResumenFactura/CodigoTipoMoneda/CodigoMoneda') ?: $g('ResumenFactura/CodigoMoneda');
    $tc = $g('ResumenFactura/CodigoTipoMoneda/TipoCambio') ?: $g('ResumenFactura/TipoCambio');
    $R = fn(string $k) => bz_num($g("ResumenFactura/$k"));
    $r += [
        'consecutivo' => $g('NumeroConsecutivo'), 'fecha_emision' => $g('FechaEmision'),
        'codigo_actividad' => $g('CodigoActividadEmisor') ?: $g('CodigoActividad'),
        'nombre_emisor' => $g('Emisor/Nombre'), 'tipo_id_emisor' => $g('Emisor/Identificacion/Tipo'), 'nit_emisor' => $g('Emisor/Identificacion/Numero'),
        'email_emisor' => $g('Emisor/CorreoElectronico'),
        'nombre_receptor' => $g('Receptor/Nombre'), 'nit_receptor' => $g('Receptor/Identificacion/Numero') ?: $g('Receptor/IdentificacionExtranjero'),
        'moneda' => $moneda ?: 'CRC', 'tipo_cambio' => bz_num($tc),
        'totales' => [
            'TOTALSERVGRAVADOS' => $R('TotalServGravados'), 'TOTALSERVEXENTOS' => $R('TotalServExentos'),
            'TOTAL_SERVICIOS_EXONERADOS' => $R('TotalServExonerado'), 'TOTALMERCANCIASGRAVADAS' => $R('TotalMercanciasGravadas'),
            'TOTALMERCANCIASEXENTAS' => $R('TotalMercanciasExentas'), 'TOTAL_MERCANCIAS_EXONERADAS' => $R('TotalMercExonerada'),
            'TOTALGRAVADO' => $R('TotalGravado'), 'TOTALEXENTO' => $R('TotalExento'), 'TOTAL_EXONERADO' => $R('TotalExonerado'),
            'TOTALVENTA' => $R('TotalVenta'), 'TOTALDESCUENTOS' => $R('TotalDescuentos'), 'TOTALVENTANETA' => $R('TotalVentaNeta'),
            'TOTALIMPUESTO' => $R('TotalImpuesto'), 'TOTAL_IVA_DEVUELTO' => $R('TotalIVADevuelto'), 'TOTAL_OTROS_CARGOS' => $R('TotalOtrosCargos'),
            'TOTALCOMPROBANTE' => $R('TotalComprobante'),
        ],
    ];
    return $r;
}

/** Fecha ISO de Hacienda -> 'Y-m-d H:i:s' hora de Costa Rica. */
function bz_fecha(?string $s): ?string
{
    if (!$s) return null;
    try { return (new DateTimeImmutable($s))->setTimezone(new DateTimeZone('America/Costa_Rica'))->format('Y-m-d H:i:s'); }
    catch (Throwable) { return null; }
}

/** Fecha desde la clave (posiciones 4-9 = ddmmaa). */
function bz_fecha_clave(string $clave): ?string
{
    if (!preg_match('/^\d{3}(\d\d)(\d\d)(\d\d)/', $clave, $m) || !checkdate((int)$m[2], (int)$m[1], 2000 + (int)$m[3])) return null;
    return sprintf('20%s-%s-%s 00:00:00', $m[3], $m[2], $m[1]);
}

function bz_id_norm(?string $s): string { return ltrim(preg_replace('/\D/', '', (string)$s), '0'); }

/** Analiza un mensaje completo: cabeceras + documentos + PDF asociados. No escribe nada. */
function bz_analizar(string $raw, array $propios): array
{
    $sep = preg_split("/\r?\n\r?\n/", $raw, 2);
    $h = bz_headers($sep[0]);
    $from = bz_hdr_decode($h['from'][0] ?? '');
    $hojas = bz_mime($raw);
    $arch = bz_archivos($hojas);
    $docs = []; $pdfs = []; $otros = [];
    foreach ($arch as $a) {
        $a['sha'] = hash('sha256', $a['datos']);
        if ($a['clase'] === 'pdf') { $pdfs[] = $a; continue; }
        $p = bz_xml($a['datos']);
        if (!$p) { $otros[] = $a; continue; }
        $a['xml'] = $p;
        $a['clase'] = match ($p['tipo']) { 'MH' => 'respuesta', 'MR' => 'receptor', default => 'xml' };
        $docs[] = $a;
    }
    // PDF -> clave: clave (50 digitos) o consecutivo (20) en el nombre; si el correo trae un solo comprobante, a ese.
    $claves = array_values(array_unique(array_map(fn($d) => $d['xml']['clave'], $docs)));
    $consec = [];
    foreach ($docs as $d) if (!empty($d['xml']['consecutivo'])) $consec[$d['xml']['consecutivo']] = $d['xml']['clave'];
    foreach ($pdfs as &$p) {
        $p['clave'] = null;
        if (preg_match_all('/\d{50}/', $p['nombre'], $m)) foreach ($m[0] as $c) if (in_array($c, $claves, true) || !$claves) { $p['clave'] = $c; break; }
        if (!$p['clave'] && preg_match_all('/\d{20}/', $p['nombre'], $m)) foreach ($m[0] as $c) if (isset($consec[$c])) { $p['clave'] = $consec[$c]; break; }
        if (!$p['clave'] && count($claves) === 1) $p['clave'] = $claves[0];
    }
    unset($p);
    // Documentos emitidos por Los Paticas (configurable): se omiten
    $prop = array_map('bz_id_norm', $propios);
    $esPropio = function (string $clave, ?string $nit) use ($prop): bool {
        $n = bz_id_norm($nit ?: substr($clave, 9, 12));
        return $n !== '' && in_array($n, $prop, true);
    };
    $omitidas = [];
    foreach ($docs as $d) if ($d['clase'] !== 'receptor' && $esPropio($d['xml']['clave'], $d['xml']['nit_emisor'] ?? null)) $omitidas[$d['xml']['clave']] = true;
    return [
        'message_id' => trim($h['message-id'][0] ?? '', " <>\t"), 'remitente' => $from,
        'remitente_email' => preg_match('/[\w.+%-]+@[\w.-]+\.\w+/', $from, $m) ? strtolower($m[0]) : null,
        'para' => bz_hdr_decode($h['to'][0] ?? ''), 'asunto' => bz_hdr_decode($h['subject'][0] ?? ''),
        'fecha' => ($t = strtotime($h['date'][0] ?? '')) ? date('Y-m-d H:i:s', $t) : null,
        'adjuntos' => count(array_filter($hojas, fn($x) => $x['nombre'] !== '' || $x['disp'] === 'attachment')),
        'docs' => $docs, 'pdfs' => $pdfs, 'otros' => $otros, 'omitidas' => array_keys($omitidas),
    ];
}
