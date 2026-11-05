<?php
declare(strict_types=1);
/*
 * Sincronizacion del Buzon FE (CLI, usuario www-data; lo ejecuta paticas-buzon-fe.service cada 5 min con flock).
 *   buzon-sync.php                 corrida normal (lectura IMAP solo lectura + extraccion)
 *   buzon-sync.php --listar        conectar y listar carpetas con cantidad de mensajes
 *   buzon-sync.php --schema        = db/migrar.php --aplicar (crea las tablas que falten; idempotente)
 *   buzon-sync.php --importar-eml=archivo  importar un .eml local a la BD (cuenta 'prueba-local', para pruebas)
 *   buzon-sync.php --eml=archivo   analizar un .eml local (no escribe nada) y mostrar el resumen en JSON
 *   buzon-sync.php --max=N         limitar mensajes por corrida (por defecto BUZON_MAX_POR_CORRIDA o 300)
 * Configuracion (variables de entorno, desde /etc/lospaticas/buzon-fe.env): IMAP_HOST, IMAP_PORT, IMAP_USER,
 * IMAP_PASS o IMAP_PASS_B64, IMAP_CARPETAS (INBOX | lista separada por comas | *), IMAP_EXCLUIR,
 * BUZON_IGNORAR_EMISORES, BUZON_MAX_POR_CORRIDA, BUZON_MAX_MB, IMAP_VERIFICAR_CERT.
 */
date_default_timezone_set('America/Costa_Rica');
umask(0077);
require __DIR__ . '/buzon-lib.php';

$opt = getopt('', ['listar', 'schema', 'eml:', 'max:', 'importar-eml:']);
$E = fn(string $k, string $d = '') => ($v = getenv($k)) === false || $v === '' ? $d : $v;
$cfg = [
    'host' => $E('IMAP_HOST', 'mail.lospaticas.com'), 'port' => (int)$E('IMAP_PORT', '993'), 'user' => $E('IMAP_USER'),
    'carpetas' => $E('IMAP_CARPETAS', 'INBOX'), 'excluir' => $E('IMAP_EXCLUIR', 'Trash,Sent,Drafts,Junk,Spam,Papelera,Enviados,Borradores,Basura'),
    'propios' => array_filter(array_map('trim', explode(',', $E('BUZON_IGNORAR_EMISORES', '3101090358,310109035837')))),
    'max' => (int)($opt['max'] ?? $E('BUZON_MAX_POR_CORRIDA', '300')), 'max_mb' => (int)$E('BUZON_MAX_MB', '25'),
    'verificar' => $E('IMAP_VERIFICAR_CERT', '1') !== '0',
];
$pass = $E('IMAP_PASS');
if ($pass === '' && $E('IMAP_PASS_B64') !== '') $pass = (string)base64_decode($E('IMAP_PASS_B64'), true);
putenv('IMAP_PASS'); putenv('IMAP_PASS_B64');

function bz_log(string $m): void
{
    if (is_file(BZ_LOG) && filesize(BZ_LOG) > 5 * 1048576) @rename(BZ_LOG, BZ_LOG . '.1');
    @file_put_contents(BZ_LOG, date('Y-m-d H:i:s') . " $m\n", FILE_APPEND | LOCK_EX);
    if (PHP_SAPI === 'cli') fwrite(STDERR, "$m\n");
}

function bz_estado(array $e): void
{
    static $base = null;
    if ($base === null) $base = is_file(BZ_ESTADO) ? (json_decode((string)file_get_contents(BZ_ESTADO), true) ?: []) : [];
    $base = array_merge($base, $e);
    $tmp = BZ_ESTADO . '.tmp';
    file_put_contents($tmp, json_encode($base, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    @chmod($tmp, 0640);
    rename($tmp, BZ_ESTADO);
}

function bz_db(): PDO
{
    static $p = null;
    if (!$p) { $c = require '/etc/lospaticas/db.php'; $p = new PDO($c['pdo_dsn'], $c['user'], $c['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); }
    return $p;
}

require_once dirname(__DIR__) . '/db/migrar-lib.php';

/** Tablas FE_BUZON_* presentes (se crean con las migraciones: panel-hacienda/db/migrar.php --aplicar). */
function bz_tablas_faltantes(PDO $db): array
{
    return mig_faltantes($db, ['dbo.FE_BUZON_CORREOS', 'dbo.FE_BUZON_DOCUMENTOS', 'dbo.FE_BUZON_ARCHIVOS', 'dbo.FE_BUZON_DOC_CUENTAS']);
}

/** Guarda un archivo bajo BZ_BASE y devuelve la ruta relativa (reutiliza si ya existe identico). */
function bz_guardar(string $rel, string $datos): string
{
    $rel = ltrim($rel, '/');
    if (str_contains($rel, '..')) throw new RuntimeException('ruta invalida');
    $abs = BZ_BASE . '/' . $rel;
    if (is_file($abs)) {
        if (hash_file('sha256', $abs) === hash('sha256', $datos)) return $rel;
        $i = pathinfo($rel);
        $rel = ($i['dirname'] !== '.' ? $i['dirname'] . '/' : '') . $i['filename'] . '_' . substr(hash('sha256', $datos), 0, 8) . '.' . ($i['extension'] ?? 'bin');
        $abs = BZ_BASE . '/' . $rel;
        if (is_file($abs)) return $rel;
    }
    if (!is_dir(dirname($abs)) && !mkdir(dirname($abs), 0700, true) && !is_dir(dirname($abs))) throw new RuntimeException('no se pudo crear ' . dirname($rel));
    $tmp = $abs . '.tmp' . getmypid();
    if (file_put_contents($tmp, $datos) !== strlen($datos)) throw new RuntimeException("no se pudo escribir $rel");
    chmod($tmp, 0600);
    rename($tmp, $abs);
    return $rel;
}

function bz_carpeta_clave(string $clave, ?string $fecha): string
{
    $f = $fecha ?: bz_fecha_clave($clave) ?: date('Y-m-d');
    return substr($f, 0, 4) . '/' . substr($f, 5, 2) . '/' . $clave;
}

/** Inserta/actualiza el documento (no sobrescribe datos ya presentes). */
function bz_doc_upsert(PDO $db, array $x, int $correoId, array $propios): void
{
    $clave = $x['clave'];
    $id = $db->prepare('SELECT ID, FECHA_EMISION FROM dbo.FE_BUZON_DOCUMENTOS WHERE CLAVE = ?');
    $id->execute([$clave]);
    $act = $id->fetch(PDO::FETCH_ASSOC) ?: null; $id->closeCursor();
    $prop = array_map('bz_id_norm', $propios);
    $nitR = $x['nit_receptor'] ?? null;
    $para = $nitR !== null && $nitR !== '' && in_array(bz_id_norm($nitR), $prop, true) ? 1 : 0;
    $tipoClave = ['01' => 'FE', '02' => 'ND', '03' => 'NC', '04' => 'TE', '08' => 'FEC', '09' => 'FEE', '10' => 'REP'][substr($clave, 29, 2)] ?? null;
    if (!$act) {
        $db->prepare("INSERT INTO dbo.FE_BUZON_DOCUMENTOS (CLAVE, TIPO_DOC, CONSECUTIVO, FECHA_EMISION, NIT_EMISOR, CORREO_ID, CARPETA_ARCHIVOS)
            VALUES (?, ?, ?, ?, ?, ?, ?)")->execute([$clave, $tipoClave, substr($clave, 21, 20), bz_fecha_clave($clave), ltrim(substr($clave, 9, 12), '0'), $correoId, bz_carpeta_clave($clave, null)]);
    }
    $u = fn(string $sql, array $p) => $db->prepare("UPDATE dbo.FE_BUZON_DOCUMENTOS SET $sql, FECHA_ACTUALIZADO = SYSDATETIME() WHERE CLAVE = ?")->execute([...$p, $clave]);
    if ($x['tipo'] === 'MH') {
        $u('ESTADO_HACIENDA = ?, MENSAJE_HACIENDA = ?, TIENE_RESPUESTA = 1, NOMBRE = COALESCE(NOMBRE, ?), NIT_EMISOR = COALESCE(NULLIF(NIT_EMISOR, \'\'), ?),
            NIT_RECEPTOR = COALESCE(NIT_RECEPTOR, ?), NOMBRE_RECEPTOR = COALESCE(NOMBRE_RECEPTOR, ?), PARA_LOS_PATICAS = CASE WHEN ? = 1 THEN 1 ELSE PARA_LOS_PATICAS END,
            TOTALIMPUESTO = COALESCE(TOTALIMPUESTO, ?), TOTALCOMPROBANTE = COALESCE(TOTALCOMPROBANTE, ?)',
           [$x['estado'], mb_substr($x['detalle'], 0, 8000), $x['nombre_emisor'] ?: null, $x['nit_emisor'] ?: null, $nitR ?: null, $x['nombre_receptor'] ?: null, $para,
            $x['total_impuesto'], $x['total_comprobante']]);
        return;
    }
    if ($x['tipo'] === 'MR') return;
    $f = bz_fecha($x['fecha_emision']);
    $t = $x['totales'];
    $u('TIPO_DOC = ?, VERSION = ?, CONSECUTIVO = ?, FECHA_EMISION = COALESCE(?, FECHA_EMISION), FECHA_EMISION_TXT = ?, NIT_EMISOR = ?, TIPO_ID_EMISOR = ?, NOMBRE = ?,
        EMAIL_EMISOR = ?, NIT_RECEPTOR = ?, NOMBRE_RECEPTOR = ?, PARA_LOS_PATICAS = ?, CODIGO_ACTIVIDAD = ?, MONEDA = ?, TIPO_CAMBIO = ?, '
        . implode(', ', array_map(fn($k) => "$k = ?", array_keys($t))) . ', TIENE_XML = 1, CARPETA_ARCHIVOS = ?',
       [$x['tipo'], $x['version'], substr((string)$x['consecutivo'], 0, 40) ?: null, $f, substr((string)$x['fecha_emision'], 0, 40) ?: null, substr((string)$x['nit_emisor'], 0, 40) ?: null, substr((string)$x['tipo_id_emisor'], 0, 2) ?: null,
        mb_substr($x['nombre_emisor'], 0, 250) ?: null, mb_substr($x['email_emisor'], 0, 160) ?: null, substr((string)$nitR, 0, 40) ?: null, mb_substr((string)$x['nombre_receptor'], 0, 250) ?: null,
        $para, substr($x['codigo_actividad'], 0, 6) ?: null, substr($x['moneda'], 0, 3), $x['tipo_cambio'], ...array_values($t), bz_carpeta_clave($clave, $f)]);
}

/** Procesa un mensaje ya descargado dentro de una transaccion. Devuelve [estado, documentos]. */
function bz_procesar(PDO $db, array $m, string $raw, int $correoId, array $propios): array
{
    $a = bz_analizar($raw, $propios);
    $omit = array_flip($a['omitidas']);
    $n = 0; $claves = [];
    $fechas = [];
    foreach ($a['docs'] as $d) if ($d['clase'] === 'xml') $fechas[$d['xml']['clave']] = bz_fecha($d['xml']['fecha_emision'] ?? null);
    $ins = $db->prepare('INSERT INTO dbo.FE_BUZON_ARCHIVOS (CORREO_ID, CLAVE, CLASE, NOMBRE, ORIGEN_ZIP, RUTA, SHA256, TAMANO) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $ya = $db->prepare('SELECT COUNT(*) FROM dbo.FE_BUZON_ARCHIVOS WHERE CLAVE = ? AND SHA256 = ?');
    $cuenta = (string)getenv('IMAP_USER');
    $origen = function (string $clave) use ($db, $cuenta, $correoId): void {
        $db->prepare('IF NOT EXISTS (SELECT 1 FROM dbo.FE_BUZON_DOC_CUENTAS WHERE CLAVE = ? AND CUENTA = ?) INSERT INTO dbo.FE_BUZON_DOC_CUENTAS (CLAVE, CUENTA, CORREO_ID) VALUES (?, ?, ?)')->execute([$clave, $cuenta, $clave, $cuenta, $correoId]);
    };
    $guardaArchivo = function (?string $clave, string $clase, array $f, string $rel) use ($db, $ins, $ya, $correoId): void {
        if ($clave) { $ya->execute([$clave, $f['sha']]); $c = (int)$ya->fetchColumn(); $ya->closeCursor(); if ($c) return; }
        $ruta = bz_guardar($rel, $f['datos']);
        $ins->execute([$correoId, $clave, $clase, mb_substr($f['nombre'], 0, 260), $f['origen'] ? mb_substr($f['origen'], 0, 260) : null, $ruta, $f['sha'], strlen($f['datos'])]);
    };
    // primero comprobantes, luego respuestas (para que el documento ya exista con sus datos)
    usort($a['docs'], fn($x, $y) => ($x['clase'] === 'xml' ? 0 : 1) <=> ($y['clase'] === 'xml' ? 0 : 1));
    foreach ($a['docs'] as $d) {
        $c = $d['xml']['clave'];
        if (isset($omit[$c])) continue;
        $dir = bz_carpeta_clave($c, $fechas[$c] ?? null);
        $suf = ['xml' => '', 'respuesta' => '_respuesta', 'receptor' => '_receptor'][$d['clase']];
        bz_doc_upsert($db, $d['xml'], $correoId, $propios);
        $origen($c);
        $guardaArchivo($c, $d['clase'], $d, "$dir/$c$suf.xml");
        $claves[$c] = true; $n++;
    }
    foreach ($a['pdfs'] as $p) {
        $c = $p['clave'];
        if ($c && isset($omit[$c])) continue;
        if ($c && !isset($claves[$c])) {     // PDF de un comprobante que llego en otro correo
            $e = $db->prepare('SELECT COUNT(*) FROM dbo.FE_BUZON_DOCUMENTOS WHERE CLAVE = ?'); $e->execute([$c]);
            if (!(int)$e->fetchColumn()) $c = null;
        }
        if ($c) {
            $origen($c);
            $guardaArchivo($c, 'pdf', $p, bz_carpeta_clave($c, $fechas[$c] ?? null) . "/$c.pdf");
            $db->prepare('UPDATE dbo.FE_BUZON_DOCUMENTOS SET TIENE_PDF = 1 WHERE CLAVE = ?')->execute([$c]);
        } elseif (!$a['omitidas']) {
            $guardaArchivo(null, 'pdf', $p, 'sin-clave/' . date('Y/m') . "/$correoId/" . bz_nombre_seguro($p['nombre'], 'documento.pdf'));
        }
    }
    $estado = $n ? 'ok' : ($a['omitidas'] ? 'propio' : 'sin_documentos');
    $db->prepare('UPDATE dbo.FE_BUZON_CORREOS SET MESSAGE_ID = ?, REMITENTE = ?, REMITENTE_EMAIL = ?, PARA = ?, ASUNTO = ?, FECHA_CORREO = ?, ADJUNTOS = ?, DOCUMENTOS = ?, ESTADO = ? WHERE ID = ?')
       ->execute([mb_substr($a['message_id'], 0, 400) ?: null, mb_substr($a['remitente'], 0, 400), $a['remitente_email'], mb_substr($a['para'], 0, 400),
                  mb_substr($a['asunto'], 0, 1000), $a['fecha'], $a['adjuntos'], $n, $estado, $correoId]);
    return [$estado, $n];
}

/* ------------------------------------------------------------------------------------------------ */
try {
    if (isset($opt['eml'])) {
        $a = bz_analizar((string)file_get_contents($opt['eml']), $cfg['propios']);
        $res = fn(array $f) => ['clase' => $f['clase'], 'nombre' => $f['nombre'], 'origen' => $f['origen'], 'bytes' => strlen($f['datos'])]
            + (isset($f['xml']) ? ['xml' => array_diff_key($f['xml'], ['totales' => 1]) + ['TOTALCOMPROBANTE' => $f['xml']['totales']['TOTALCOMPROBANTE'] ?? null, 'TOTALIMPUESTO' => $f['xml']['totales']['TOTALIMPUESTO'] ?? null]] : [])
            + (array_key_exists('clave', $f) ? ['clave_pdf' => $f['clave']] : []);
        echo json_encode(['asunto' => $a['asunto'], 'remitente' => $a['remitente'], 'fecha' => $a['fecha'], 'message_id' => $a['message_id'], 'adjuntos' => $a['adjuntos'],
            'docs' => array_map($res, $a['docs']), 'pdfs' => array_map($res, $a['pdfs']), 'otros' => array_map($res, $a['otros']), 'omitidas' => $a['omitidas']],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), "\n";
        exit(0);
    }
    if (isset($opt['importar-eml'])) {
        $db = bz_db(); $raw = (string)file_get_contents($opt['importar-eml']);
        $db->beginTransaction();
        $ins = $db->prepare("INSERT INTO dbo.FE_BUZON_CORREOS (CUENTA, CARPETA, UIDVALIDITY, UID, FECHA_RECIBIDO, TAMANO, ESTADO) OUTPUT INSERTED.ID VALUES ('prueba-local', 'local', 0, ?, ?, ?, 'procesando')");
        $ins->execute([abs(crc32($raw)), date('Y-m-d H:i:s'), strlen($raw)]); $cid = (int)$ins->fetchColumn(); $ins->closeCursor();
        [$est, $n] = bz_procesar($db, [], $raw, $cid, $cfg['propios']);
        $db->commit();
        echo json_encode(['correo_id' => $cid, 'estado' => $est, 'documentos' => $n]), "\n";
        exit(0);
    }
    if (isset($opt['schema'])) {      // compatibilidad: equivale a db/migrar.php --aplicar
        [$h, $err] = mig_aplicar(bz_db(), 'cli:buzon-sync --schema');
        echo $h ? 'Aplicadas: ' . implode(', ', $h) . "\n" : "Nada pendiente.\n";
        if ($err) { echo "$err\n"; exit(1); }
        exit(0);
    }

    $pub = ['host' => $cfg['host'], 'port' => $cfg['port'], 'usuario' => $cfg['user'], 'carpetas_cfg' => $cfg['carpetas'],
            'ignorar_emisores' => implode(',', $cfg['propios']), 'max_por_corrida' => $cfg['max'], 'cuenta' => BZ_CUENTA];
    if ($cfg['user'] === '' || $pass === '') {
        bz_estado(['estado' => 'sin credenciales', 'mensaje' => 'Falta IMAP_PASS en /etc/lospaticas/buzon-fe.env', 'inicio' => date('Y-m-d H:i:s'), 'fin' => date('Y-m-d H:i:s'), 'config' => $pub]);
        if (PHP_SAPI === 'cli') fwrite(STDERR, "sin credenciales\n");
        exit(0);
    }
    if (!isset($opt['listar']) && ($falt = bz_tablas_faltantes(bz_db()))) {
        $m = 'Faltan las tablas ' . implode(', ', $falt) . ' en la BD (¿restauración nueva?). Aplique las migraciones: botón "Aplicar migraciones" del panel o sudo -u www-data php8.5 panel-hacienda/db/migrar.php --aplicar';
        bz_estado(['estado' => 'sin tablas', 'mensaje' => $m, 'inicio' => date('Y-m-d H:i:s'), 'fin' => date('Y-m-d H:i:s'), 'config' => $pub]);
        bz_log('SIN TABLAS: ' . implode(', ', $falt) . ' (no se sincroniza)');
        if (PHP_SAPI === 'cli') fwrite(STDERR, "sin tablas\n");
        exit(0);
    }
    $t0 = microtime(true);
    if (!isset($opt['listar'])) bz_estado(['estado' => 'en curso', 'mensaje' => 'Conectando…', 'inicio' => date('Y-m-d H:i:s'), 'fin' => null, 'config' => $pub]);
    $im = new BzImap($cfg['host'], $cfg['port'], $cfg['verificar']);
    $im->login($cfg['user'], $pass);
    unset($pass);
    $todas = $im->carpetas();
    $excl = array_filter(array_map('trim', explode(',', strtolower($cfg['excluir']))));
    if ($cfg['carpetas'] === '*') $sel = array_values(array_filter($todas, function ($c) use ($excl) {
        foreach ($excl as $e) if ($e !== '' && str_contains(strtolower($c['visible']), $e)) return false;
        return !preg_match('/\\\\(Trash|Junk|Sent|Drafts)/i', $c['flags']);
    }));
    else { $w = array_map('trim', explode(',', $cfg['carpetas'])); $sel = array_values(array_filter($todas, fn($c) => in_array($c['nombre'], $w, true) || in_array($c['visible'], $w, true))); }

    if (isset($opt['listar'])) {
        foreach ($todas as $c) { $s = $im->status($c['nombre']); printf("%-40s %8d mensajes  uidvalidity=%s %s\n", $c['visible'], $s['messages'] ?? 0, $s['uidvalidity'] ?? '?', in_array($c, $sel, true) ? '[se sincroniza]' : ''); }
        $im->logout();
        exit(0);
    }

    $db = bz_db();
    if (!is_dir(BZ_BASE)) mkdir(BZ_BASE, 0700, true);
    $tot = ['correos' => 0, 'documentos' => 0, 'errores' => 0, 'por_estado' => []]; $resumen = []; $pend = 0; $presupuesto = $cfg['max'];
    foreach ($sel as $c) {
        $ex = $im->examine($c['nombre']);                      // SOLO LECTURA
        $uv = $ex['uidvalidity'];
        $uids = $ex['exists'] ? $im->uids() : [];
        $k = $db->prepare('SELECT UID FROM dbo.FE_BUZON_CORREOS WHERE CUENTA = ? AND CARPETA = ? AND UIDVALIDITY = ?');
        $k->execute([$cfg['user'], $c['nombre'], $uv]);
        $conocidos = array_flip(array_map('intval', $k->fetchAll(PDO::FETCH_COLUMN)));
        $nuevos = array_values(array_filter($uids, fn($u) => !isset($conocidos[$u])));
        $hacer = array_slice($nuevos, 0, max(0, $presupuesto));
        $pend += count($nuevos) - count($hacer);
        $r = ['carpeta' => $c['visible'], 'uidvalidity' => $uv, 'mensajes' => count($uids), 'nuevos' => count($nuevos), 'procesados' => 0];
        bz_estado(['mensaje' => "{$c['visible']}: " . count($nuevos) . ' mensajes nuevos']);
        foreach (array_chunk($hacer, 50) as $lote) {
            foreach ($im->cabeceras($lote) as $uid => $h) {
                $hd = bz_headers($h['hdr']);
                $mid = trim($hd['message-id'][0] ?? '', " <>\t");
                $fr = $h['internaldate'] ? date('Y-m-d H:i:s', strtotime($h['internaldate'])) : null;
                $insSql = 'INSERT INTO dbo.FE_BUZON_CORREOS (CUENTA, CARPETA, UIDVALIDITY, UID, MESSAGE_ID, REMITENTE, ASUNTO, FECHA_RECIBIDO, TAMANO, ESTADO, ERROR)
                    OUTPUT INSERTED.ID VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $fila = fn(string $est, ?string $err) => [$cfg['user'], $c['nombre'], $uv, $uid, mb_substr($mid, 0, 400) ?: null, mb_substr(bz_hdr_decode($hd['from'][0] ?? ''), 0, 400),
                    mb_substr(bz_hdr_decode($hd['subject'][0] ?? ''), 0, 1000), $fr, $h['size'], $est, $err];
                $db->beginTransaction();
                try {
                    $dup = false;
                    if ($mid !== '') { $q = $db->prepare("SELECT COUNT(*) FROM dbo.FE_BUZON_CORREOS WHERE MESSAGE_ID = ? AND CUENTA = ? AND ESTADO IN ('ok','propio','sin_documentos')"); $q->execute([$mid, $cfg['user']]); $dup = (int)$q->fetchColumn() > 0; $q->closeCursor(); }
                    $grande = $h['size'] > $cfg['max_mb'] * 1048576;
                    $estado = $dup ? 'duplicado' : ($grande ? 'muy_grande' : null); $n = 0;
                    $ins = $db->prepare($insSql);
                    $ins->execute($fila($estado ?? 'procesando', null));
                    $cid = (int)$ins->fetchColumn(); $ins->closeCursor();
                    if (!$estado) [$estado, $n] = bz_procesar($db, $h, $im->mensaje($uid), $cid, $cfg['propios']);
                    $db->commit();
                } catch (Throwable $e) {
                    if ($db->inTransaction()) $db->rollBack();
                    if ($e instanceof LogicException || preg_match('/^(IMAP|Conexion|Tiempo|Lectura IMAP|No se pudo conectar)/', $e->getMessage())) throw $e;
                    // error al interpretar/guardar este mensaje: se registra y se sigue con el resto (no se reintenta en bucle)
                    $estado = 'error'; $n = 0; $tot['errores']++;
                    bz_log("UID $uid ({$c['visible']}): " . mb_substr($e->getMessage(), 0, 300));
                    $ins = $db->prepare($insSql); $ins->execute($fila('error', mb_substr(get_class($e) . ': ' . $e->getMessage(), 0, 2000))); $ins->closeCursor();
                }
                $tot['correos']++; $tot['documentos'] += $n; $r['procesados']++; $tot['por_estado'][$estado] = ($tot['por_estado'][$estado] ?? 0) + 1;
                $presupuesto--;
            }
            bz_estado(['mensaje' => "{$c['visible']}: {$r['procesados']} de " . count($hacer) . ' procesados', 'progreso' => $tot]);
        }
        $resumen[] = $r;
    }
    $im->logout();
    $seg = round(microtime(true) - $t0, 1);
    bz_log("OK correos={$tot['correos']} documentos={$tot['documentos']} errores={$tot['errores']} pendientes=$pend segundos=$seg " . json_encode($tot['por_estado']));
    bz_estado(['estado' => 'ok', 'mensaje' => $pend ? "Correcto; quedan $pend mensajes para las próximas corridas" : 'Correcto', 'fin' => date('Y-m-d H:i:s'),
               'carpetas' => $resumen, 'ultima' => $tot, 'pendientes' => $pend, 'segundos' => $seg, 'error' => null, 'ultimo_ok' => date('Y-m-d H:i:s')]);
} catch (Throwable $e) {
    $msg = preg_replace('/AUTHENTICATE PLAIN \S+/', 'AUTHENTICATE PLAIN ***', $e->getMessage());
    bz_log('ERROR ' . $msg);
    bz_estado(['estado' => 'error', 'mensaje' => $msg, 'error' => $msg, 'fin' => date('Y-m-d H:i:s')]);
    exit(1);
}
