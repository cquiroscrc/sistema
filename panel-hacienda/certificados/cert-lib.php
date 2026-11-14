<?php
declare(strict_types=1);
/**
 * Certificados de firma (.p12) de Hacienda: ver y reemplazar el archivo configurado en COMPAÑIAS.
 * La FE lee IDP_<AMBIENTE>_CERT_URL (archivo) e IDP_<AMBIENTE>_PIN (nchar(4)) de COMPAÑIAS con CONFIGURADA='SI'.
 * El PIN nunca se devuelve al navegador ni se escribe en bitácoras.
 */

const PH_CERT_MAX = 51200;               // ~50 KB
const PH_CERT_RAIZ = '/var/www/lospaticas/sistema/fe/';
const PH_CERT_NOMBRE_RE = '/^[A-Za-z0-9._-]{1,120}\.(p12|pfx)$/i';

/** Configuración de ambos ambientes (incluye el PIN solo para uso interno). */
function cert_config(): array
{
    $db = ph_db();
    $n = (int)$db->query("SELECT COUNT(*) FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI'")->fetchColumn();
    if ($n !== 1) throw new RuntimeException("Se esperaba 1 compañía CONFIGURADA='SI' y hay $n");
    $r = $db->query("SELECT RTRIM(AMBIENTE) AMBIENTE, RTRIM(IDP_PROD_CERT_URL) PROD_URL, RTRIM(IDP_PROD_RUTA_CERT) PROD_RUTA, IDP_PROD_PIN PROD_PIN,
        RTRIM(IDP_STAG_CERT_URL) STAG_URL, RTRIM(IDP_STAG_RUTA_CERT) STAG_RUTA, IDP_STAG_PIN STAG_PIN FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI'")->fetch(PDO::FETCH_ASSOC);
    $amb = strtoupper(trim((string)$r['AMBIENTE']));
    $out = ['ambiente' => $amb, 'ambientes' => []];
    foreach (['PROD', 'STAG'] as $a) {
        $out['ambientes'][$a] = ['url' => ph_utf8((string)$r[$a . '_URL']), 'ruta' => ph_utf8((string)$r[$a . '_RUTA']), 'pin' => (string)($r[$a . '_PIN'] ?? '')];
    }
    return $out;
}

/** Valida que el destino configurado sea un archivo .p12/.pfx dentro de la carpeta configurada y bajo la raíz de la FE. */
function cert_destino(string $url, string $ruta, string $raiz = PH_CERT_RAIZ): string
{
    if ($url === '' || $url[0] !== '/' || str_contains($url, "\0") || preg_match('#(^|/)\.\.(/|$)#', $url)) throw new RuntimeException('La ruta del certificado configurada no es una ruta absoluta válida');
    $nombre = basename($url);
    if (!preg_match(PH_CERT_NOMBRE_RE, $nombre)) throw new RuntimeException('El nombre de archivo configurado no es un .p12/.pfx válido');
    $dir = realpath(dirname($url));
    if ($dir === false || !is_dir($dir)) throw new RuntimeException('La carpeta del certificado no existe en el servidor');
    $raizReal = realpath($raiz);
    if ($raizReal === false || !str_starts_with($dir . '/', rtrim($raizReal, '/') . '/')) throw new RuntimeException('La carpeta del certificado está fuera de la carpeta permitida');
    if ($ruta !== '') {
        $rutaReal = realpath($ruta);
        if ($rutaReal === false || $rutaReal !== $dir) throw new RuntimeException('El archivo configurado no está en la carpeta de certificados configurada');
    }
    return $dir . '/' . $nombre;
}

/** Datos del certificado leído con el PIN (sin exponerlo). */
function cert_leer(string $contenido, string $pin): ?array
{
    $c = [];
    if (!@openssl_pkcs12_read($contenido, $c, $pin) || empty($c['cert'])) return null;
    $x = openssl_x509_parse($c['cert']) ?: [];
    $hasta = (int)($x['validTo_time_t'] ?? 0);
    $fmt = fn(int $t) => $t ? (new DateTimeImmutable('@' . $t))->setTimezone(new DateTimeZone('America/Costa_Rica'))->format('d/m/Y H:i') : '';
    $sujeto = $x['subject'] ?? [];
    return [
        'sujeto' => ph_utf8((string)($sujeto['CN'] ?? '')) . (isset($sujeto['serialNumber']) ? ' (' . $sujeto['serialNumber'] . ')' : ''),
        'emisor' => ph_utf8((string)($x['issuer']['CN'] ?? '')),
        'desde' => $fmt((int)($x['validFrom_time_t'] ?? 0)),
        'hasta' => $fmt($hasta),
        'dias' => $hasta ? (int)floor(($hasta - time()) / 86400) : null,
        'vencido' => $hasta > 0 && $hasta < time(),
        'llave' => !empty($c['pkey']),
    ];
}

function cert_archivo(string $path, string $pin): array
{
    if (!is_file($path)) return ['existe' => false];
    $info = ['existe' => true, 'nombre' => basename($path), 'tamano' => filesize($path), 'modificado' => ph_fecha_local(date('Y-m-d H:i:s', (int)filemtime($path)))];
    $cont = @file_get_contents($path);
    if ($cont === false) return $info + ['legible' => false, 'aviso' => 'El panel no puede leer el archivo'];
    $c = cert_leer($cont, rtrim($pin));
    return $info + ($c ? ['legible' => true] + $c : ['legible' => false, 'aviso' => 'No se pudo abrir con el PIN configurado']);
}

function api_certificados(): array
{
    $cfg = cert_config();
    $amb = [];
    foreach ($cfg['ambientes'] as $a => $c) {
        $item = ['ambiente' => $a, 'activo' => $a === $cfg['ambiente'], 'url' => $c['url'], 'ruta' => $c['ruta'], 'pin_configurado' => trim($c['pin']) !== ''];
        try { $item['archivo'] = cert_archivo(cert_destino($c['url'], $c['ruta']), $c['pin']); }
        catch (Throwable $e) { $item['error'] = $c['url'] === '' ? 'Sin ruta configurada' : $e->getMessage(); }
        $amb[] = $item;
    }
    return ['ok' => true, 'ambiente' => $cfg['ambiente'], 'max' => PH_CERT_MAX, 'ambientes' => $amb];
}

/**
 * Reemplaza el archivo de forma atómica: respalda el actual (sufijo con fecha, misma carpeta), escribe un temporal en la
 * misma carpeta con el dueño/permisos del original (o 0600) y lo renombra. Devuelve el nombre del respaldo o null.
 */
function cert_reemplazar(string $destino, string $contenido): ?string
{
    $dir = dirname($destino);
    if (!is_writable($dir)) throw new RuntimeException('El panel no tiene permiso de escritura en la carpeta del certificado');
    $modo = is_file($destino) ? (fileperms($destino) & 0777) : 0600;
    $respaldo = null;
    if (is_file($destino)) {
        $respaldo = $destino . '.' . date('Ymd-His') . '.bak';
        if (!copy($destino, $respaldo)) throw new RuntimeException('No se pudo respaldar el certificado actual');
        @chmod($respaldo, 0600);
    }
    $tmp = tempnam($dir, '.subida-');
    if ($tmp === false) throw new RuntimeException('No se pudo crear el archivo temporal');
    try {
        if (file_put_contents($tmp, $contenido) !== strlen($contenido)) throw new RuntimeException('No se pudo escribir el archivo');
        chmod($tmp, $modo);
        if (!rename($tmp, $destino)) throw new RuntimeException('No se pudo reemplazar el certificado');
    } finally { if (is_file($tmp)) @unlink($tmp); }
    clearstatcache(true, $destino);
    return $respaldo ? basename($respaldo) : null;
}

/** Valida la subida (sin escribir). Devuelve [contenido, datos del certificado]. */
function cert_validar_subida(array $f, string $pin): array
{
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new RuntimeException('No se recibió el archivo');
    if ((int)$f['size'] <= 0 || (int)$f['size'] > PH_CERT_MAX) throw new RuntimeException('El archivo debe pesar menos de ' . intdiv(PH_CERT_MAX, 1024) . ' KB');
    if (!preg_match('/\.(p12|pfx)$/i', (string)($f['name'] ?? ''))) throw new RuntimeException('Solo se aceptan archivos .p12 o .pfx');
    $cont = (string)file_get_contents((string)$f['tmp_name']);
    $c = cert_leer($cont, $pin);
    if (!$c) throw new RuntimeException('El archivo no es un certificado .p12 válido o el PIN no corresponde');
    if (!$c['llave']) throw new RuntimeException('El archivo no contiene la llave privada');
    return [$cont, $c];
}

function api_certificado_subir(): array
{
    $amb = strtoupper((string)($_POST['ambiente'] ?? ''));
    if (!in_array($amb, ['PROD', 'STAG'], true)) return ['ok' => false, 'error' => 'Ambiente inválido'];
    $cfg = cert_config();
    $c = $cfg['ambientes'][$amb];
    $pinCfg = rtrim($c['pin']);
    $pinForm = (string)($_POST['pin'] ?? '');
    $usaForm = $pinForm !== '';
    $pin = $usaForm ? $pinForm : $pinCfg;
    if ($pin === '') return ['ok' => false, 'error' => 'No hay PIN configurado para ' . $amb . '; escríbalo en el formulario'];
    try {
        $destino = cert_destino($c['url'], $c['ruta']);
        [$cont, $info] = cert_validar_subida($_FILES['archivo'] ?? [], $pin);
    } catch (Throwable $e) {
        ph_accion_log("certificado $amb: subida rechazada (" . $e->getMessage() . ')');
        return ['ok' => false, 'error' => $e->getMessage()];
    }
    $pinDistinto = $usaForm && $pinForm !== $pinCfg;
    $actualizarPin = ($_POST['actualizar_pin'] ?? '') === '1';
    if ($pinDistinto && !$actualizarPin) {
        return ['ok' => false, 'codigo' => 'pin_distinto', 'error' => 'El PIN escrito no coincide con el configurado en COMPAÑIAS. La facturación firma con el PIN de COMPAÑIAS: marque «Actualizar el PIN» para guardarlo junto con el certificado.'];
    }
    if ($pinDistinto && !preg_match('/^\d{4}$/', $pinForm)) {
        return ['ok' => false, 'error' => 'El PIN debe tener 4 dígitos para guardarlo en COMPAÑIAS (columna de 4 caracteres)'];
    }
    $respaldo = cert_reemplazar($destino, $cont);
    if ($pinDistinto) {
        $st = ph_db()->prepare("UPDATE dbo.[COMPAÑIAS] SET IDP_{$amb}_PIN = ? WHERE CONFIGURADA = 'SI'");
        $st->execute([$pinForm]);
    }
    ph_accion("certificado $amb: reemplazado " . basename($destino) . ' por ' . basename((string)$_FILES['archivo']['name']) . " ({$info['sujeto']}, vence {$info['hasta']})"
        . ($respaldo ? "; respaldo $respaldo" : '') . ($pinDistinto ? '; PIN actualizado en COMPAÑIAS' : ''));
    return ['ok' => true, 'mensaje' => 'Certificado reemplazado' . ($respaldo ? " (respaldo $respaldo)" : '') . ($pinDistinto ? '; PIN actualizado' : ''),
        'aviso' => $info['vencido'] ? 'Atención: el certificado subido está vencido' : null] + api_certificados();
}
