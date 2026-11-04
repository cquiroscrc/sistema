<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';

$a = $_GET['a'] ?? '';
try {
    switch ($a) {
        case 'status':   ph_json(api_status());
        case 'logs':     ph_json(api_logs());
        case 'docs':     ph_json(api_docs());
        case 'doc':      ph_json(api_doc());
        case 'cola':     ph_json(api_cola());
        case 'reintentar': ph_require_post(); ph_json(api_reintentar());
        case 'servicio': ph_require_post(); ph_json(api_servicio());
        default:         ph_json(['ok' => false, 'error' => 'Accion desconocida'], 400);
    }
} catch (Throwable $e) {
    error_log('panel-hacienda: ' . $e->getMessage());
    ph_json(['ok' => false, 'error' => ph_mask(ph_utf8($e->getMessage()))], 500);
}

function api_status(): array
{
    $t = ph_systemctl_show(PH_UNIT . '.timer', ['ActiveState', 'UnitFileState', 'LastTriggerUSec', 'NextElapseUSecRealtime']);
    $s = ph_systemctl_show(PH_UNIT . '.service', ['ActiveState', 'SubState', 'Result', 'ExecMainStartTimestamp', 'ExecMainExitTimestamp', 'ExecMainStatus', 'ConditionResult']);
    $db = ph_db(); $info = ph_db_info();
    $q = fn(string $sql) => $db->query($sql)->fetchColumn();
    $restore = null;
    try { $restore = $q("SELECT CONVERT(varchar(19), MAX(restore_date), 120) FROM msdb.dbo.restorehistory WHERE destination_database_name = DB_NAME()"); } catch (Throwable $e) {}
    return [
        'ok' => true,
        'ahora' => date('Y-m-d H:i:s'),
        'usuario' => $_SERVER['REMOTE_USER'],
        'corte_realizado' => file_exists(PH_GUARD),
        'timer' => $t, 'servicio' => $s,
        'bd' => $info + ['restaurada' => $restore, 'es_copia' => !file_exists(PH_GUARD)],
        'ultima_bitacora' => $q("SELECT CONVERT(varchar(19), MAX(FECHA_HORA), 120) FROM dbo.BITACORA"),
        'ultimo_documento' => $q("SELECT CONVERT(varchar(19), MAX(FECHA_HORA), 120) FROM dbo.DOC_ELECTRONICO_PROCESADO"),
        'conteos' => [
            'error' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE ESTADO = 'error'"),
            'reintentar' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE ESTADO = 'reintentar'"),
            'pendiente_respuesta' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE (XML_RESPUESTA IS NULL OR XML_RESPUESTA = '') AND ENVIADO = 'S' AND FECHATIMBRADO >= '2025-01-01'"),
            'rechazado_hoy' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE ESTADO = 'rechazado' AND FECHA_HORA >= CAST(GETDATE() AS date)"),
            'aceptado_hoy' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE ESTADO LIKE 'aceptado%' AND FECHA_HORA >= CAST(GETDATE() AS date)"),
            'cola' => (int)$db->query('SELECT COUNT(*) FROM (' . cola_sql('') . ') x')->fetchColumn(),
        ],
    ];
}

/* ---------------- Registros ---------------- */
function api_logs(): array
{
    $src   = $_GET['src'] ?? 'bitacora';
    $limit = max(10, min(500, (int)($_GET['limit'] ?? 100)));
    $texto = trim((string)($_GET['q'] ?? ''));
    $nivel = in_array($_GET['nivel'] ?? '', ['Debug', 'Info', 'Warning', 'Error'], true) ? $_GET['nivel'] : '';
    $doc   = trim((string)($_GET['doc'] ?? ''));
    $desde = ph_date($_GET['desde'] ?? null);
    $hasta = ph_date($_GET['hasta'] ?? null);
    $since = isset($_GET['since']) && $_GET['since'] !== '' ? (string)$_GET['since'] : null;
    $before = isset($_GET['before']) && $_GET['before'] !== '' ? (string)$_GET['before'] : null;

    if ($src === 'bitacora') {
        $w = []; $p = [];
        if ($since !== null) { $w[] = 'FECHA_HORA > CONVERT(datetime, ?, 121)'; $p[] = ph_date($since) ?? date('Y-m-d H:i:s'); }
        if ($before !== null) { $w[] = 'FECHA_HORA < CONVERT(datetime, ?, 121)'; $p[] = ph_date($before) ?? date('Y-m-d H:i:s'); }
        if ($desde) { $w[] = 'FECHA_HORA >= CONVERT(datetime, ?, 121)'; $p[] = $desde; }
        if ($hasta) { $w[] = 'FECHA_HORA <= CONVERT(datetime, ?, 121)'; $p[] = strlen($hasta) === 10 ? "$hasta 23:59:59" : $hasta; }
        if (!$desde && $since === null && $before === null && $texto !== '') { $w[] = 'FECHA_HORA >= DATEADD(day, -7, GETDATE())'; }
        if ($nivel) { $w[] = 'TIPO = ?'; $p[] = $nivel; }
        if ($doc !== '') { $w[] = 'DOCUMENTO LIKE ?'; $p[] = '%' . $doc . '%'; }
        if ($texto !== '') { $w[] = 'TEXTO LIKE ?'; $p[] = '%' . $texto . '%'; }
        $where = $w ? 'WHERE ' . implode(' AND ', $w) : '';
        $order = $since !== null ? 'ASC' : 'DESC';
        $st = ph_db()->prepare("SELECT TOP $limit CONVERT(varchar(23), FECHA_HORA, 121) AS f, RTRIM(TIPO) AS nivel, TEXTO AS texto, RTRIM(DOCUMENTO) AS doc, RTRIM(TIPO_DOCUMENTO) AS tipo, RTRIM(SESSION_ID) AS sesion FROM dbo.BITACORA $where ORDER BY FECHA_HORA $order");
        $st->execute($p);
        $rows = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $rows[] = ['f' => $r['f'], 'nivel' => $r['nivel'], 'doc' => trim(($r['tipo'] ?? '') . ' ' . ($r['doc'] ?? '')), 'sesion' => $r['sesion'], 'texto' => ph_mask(ph_utf8($r['texto']))];
        }
        if ($since !== null) $rows = array_reverse($rows);
        $newest = $rows ? $rows[0]['f'] : $since;
        $oldest = $rows ? end($rows)['f'] : null;
        return ['ok' => true, 'src' => $src, 'rows' => $rows, 'cursor_nuevo' => $newest, 'cursor_antiguo' => $oldest, 'mas' => count($rows) >= $limit];
    }

    $files = ['sender' => PH_SENDER_LOG, 'php' => PH_PHP_LOG, 'errores' => PH_ERRORS_LOG];
    if (!isset($files[$src])) return ['ok' => false, 'error' => 'Fuente desconocida'];
    $file = $files[$src];
    $filter = function (string $l) use ($src, $texto, $nivel, $doc): bool {
        if ($src === 'php' && !preg_match('#api44|fe/API|panel-hacienda|Bitacora|hacienda#i', $l)) return false;
        if ($texto !== '' && stripos($l, $texto) === false) return false;
        if ($doc !== '' && stripos($l, $doc) === false) return false;
        if ($nivel !== '' && ph_nivel($l) !== $nivel) return false;
        return true;
    };
    [$lines, $cursor, $end, $size] = ph_tail($file, $limit, $before !== null ? (int)$before : null, $since !== null ? (int)$since : null, $filter);
    if ($size < 0) return ['ok' => true, 'src' => $src, 'rows' => [], 'aviso' => 'El archivo no existe o no es legible: ' . $file, 'cursor_nuevo' => '0', 'cursor_antiguo' => null, 'mas' => false];
    $rows = [];
    foreach ($lines as $off => $l) {
        $l = ph_utf8($l);
        $f = '';
        if (preg_match('#^\[(\d{2})/(\d{2})/(\d{4}) (\d{2}:\d{2}:\d{2})\]#', $l, $m)) $f = "$m[3]-$m[2]-$m[1] $m[4]";
        elseif (preg_match('#^\[(\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2})#', $l, $m)) $f = date('Y-m-d H:i:s', strtotime($m[1]) ?: 0);
        if (($desde && $f && $f < $desde) || ($hasta && $f && $f > (strlen($hasta) === 10 ? "$hasta 23:59:59" : $hasta))) continue;
        $rows[] = ['f' => $f, 'nivel' => ph_nivel($l), 'doc' => '', 'texto' => ph_mask(mb_substr($l, 0, 4000)), 'off' => $off];
    }
    if ($since !== null) $rows = array_reverse($rows);
    return ['ok' => true, 'src' => $src, 'archivo' => $file, 'tamano' => $size, 'rows' => $rows,
        'cursor_nuevo' => (string)$size, 'cursor_antiguo' => (string)$cursor, 'mas' => $since === null && $cursor > 0];
}

/* ---------------- Comprobantes ---------------- */
function docs_where(array &$p): string
{
    $w = [];
    $estado = $_GET['estado'] ?? '';
    switch ($estado) {
        case 'aceptado': $w[] = "d.ESTADO LIKE 'aceptado%'"; break;
        case 'rechazado': $w[] = "d.ESTADO = 'rechazado'"; break;
        case 'error': $w[] = "d.ESTADO = 'error'"; break;
        case 'reintentar': $w[] = "d.ESTADO = 'reintentar'"; break;
        case 'procesando': $w[] = "d.ESTADO = 'procesando'"; break;
        case 'pendiente': $w[] = "(d.XML_RESPUESTA IS NULL OR d.XML_RESPUESTA = '') AND d.ENVIADO = 'S'"; break;
    }
    if (in_array($_GET['tipo'] ?? '', ['FE', 'NC', 'ND', 'TE', 'FEC', 'FEE'], true)) { $w[] = 'd.TIPO_DOC = ?'; $p[] = $_GET['tipo']; }
    if ($d = ph_date($_GET['desde'] ?? null)) { $w[] = 'd.FECHA_HORA >= CONVERT(datetime, ?, 121)'; $p[] = $d; }
    if ($h = ph_date($_GET['hasta'] ?? null)) { $w[] = 'd.FECHA_HORA <= CONVERT(datetime, ?, 121)'; $p[] = strlen($h) === 10 ? "$h 23:59:59" : $h; }
    $q = trim((string)($_GET['q'] ?? ''));
    if ($q !== '') {
        $w[] = '(d.NUMERO_DOC LIKE ? OR d.SELLO_SAT LIKE ? OR d.NIT_RECEPTOR LIKE ? OR d.EMAIL_RECEPTOR LIKE ?)';
        array_push($p, "%$q%", "%$q%", "%$q%", "%$q%");
    }
    return $w ? 'WHERE ' . implode(' AND ', $w) : '';
}

function estado_ui(array $r): string
{
    $e = strtolower(trim((string)$r['ESTADO']));
    if (str_starts_with($e, 'aceptado')) return 'aceptado';
    if ($e === 'rechazado' || $e === 'error' || $e === 'reintentar' || $e === 'procesando') return $e;
    if (($r['ENVIADO'] ?? '') === 'S' && empty($r['TIENE_RESP'])) return 'pendiente';
    return $e !== '' ? $e : 'pendiente';
}

function api_docs(): array
{
    $p = [];
    $where = docs_where($p);
    $page = max(1, (int)($_GET['page'] ?? 1)); $size = max(10, min(200, (int)($_GET['size'] ?? 50)));
    $off = ($page - 1) * $size;
    $db = ph_db();
    $st = $db->prepare("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO d $where"); $st->execute($p); $total = (int)$st->fetchColumn();
    $st = $db->prepare("SELECT RTRIM(d.TIPO_DOC) TIPO_DOC, RTRIM(d.NUMERO_DOC) NUMERO_DOC, RTRIM(d.SELLO_SAT) SELLO_SAT, CONVERT(varchar(19), d.FECHA_HORA, 120) FECHA_HORA,
        RTRIM(d.FECHATIMBRADO) FECHATIMBRADO, RTRIM(d.NIT_RECEPTOR) NIT_RECEPTOR, RTRIM(d.EMAIL_RECEPTOR) EMAIL_RECEPTOR, d.TOTALCOMPROBANTE, RTRIM(d.ESTADO) ESTADO,
        d.ENVIADO, d.ACEPTADO, RTRIM(d.EMAIL_ENVIADO) EMAIL_ENVIADO, LEFT(d.ULTIMO_ERROR, 300) ULTIMO_ERROR,
        CASE WHEN d.XML_RESPUESTA IS NULL OR d.XML_RESPUESTA = '' THEN 0 ELSE 1 END TIENE_RESP
        FROM dbo.DOC_ELECTRONICO_PROCESADO d $where ORDER BY d.FECHA_HORA DESC OFFSET $off ROWS FETCH NEXT $size ROWS ONLY");
    $st->execute($p);
    $rows = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $clave = (string)$r['SELLO_SAT'];
        $rows[] = [
            'tipo' => $r['TIPO_DOC'], 'numero' => $r['NUMERO_DOC'], 'clave' => $clave,
            'consecutivo' => strlen($clave) === 50 ? substr($clave, 21, 20) : '',
            'fecha' => $r['FECHA_HORA'], 'fecha_emision' => $r['FECHATIMBRADO'],
            'receptor' => $r['NIT_RECEPTOR'], 'email' => $r['EMAIL_RECEPTOR'],
            'total' => $r['TOTALCOMPROBANTE'] !== null ? (float)$r['TOTALCOMPROBANTE'] : null,
            'estado' => estado_ui($r), 'estado_bd' => $r['ESTADO'], 'correo' => $r['EMAIL_ENVIADO'],
            'error' => ph_mask(ph_utf8($r['ULTIMO_ERROR'])),
        ];
    }
    return ['ok' => true, 'total' => $total, 'page' => $page, 'size' => $size, 'rows' => $rows];
}

function xml_pretty(?string $x): string
{
    $x = trim(ph_utf8($x));
    if ($x === '') return '';
    if (!str_starts_with($x, '<') && preg_match('/^[A-Za-z0-9+\/=\s]+$/', $x)) { $d = base64_decode($x, true); if ($d !== false && str_starts_with(ltrim($d), '<')) $x = ph_utf8($d); }
    $dom = new DOMDocument(); $dom->preserveWhiteSpace = false; $dom->formatOutput = true;
    return @$dom->loadXML($x, LIBXML_NONET) ? $dom->saveXML() : $x;
}

function xml_val(string $xml, string $path): string
{
    if ($xml === '') return '';
    $dom = new DOMDocument();
    if (!@$dom->loadXML($xml, LIBXML_NONET)) return '';
    $xp = new DOMXPath($dom);
    $n = $xp->query($path);
    return $n && $n->length ? trim($n->item(0)->textContent) : '';
}

function api_doc(): array
{
    $tipo = (string)($_GET['tipo'] ?? ''); $num = (string)($_GET['numero'] ?? '');
    $st = ph_db()->prepare("SELECT TOP 1 RTRIM(TIPO_DOC) TIPO_DOC, RTRIM(NUMERO_DOC) NUMERO_DOC, RTRIM(SELLO_SAT) SELLO_SAT, CONVERT(varchar(19), FECHA_HORA, 120) FECHA_HORA, RTRIM(FECHATIMBRADO) FECHATIMBRADO,
        RTRIM(NIT_EMISOR) NIT_EMISOR, RTRIM(NIT_RECEPTOR) NIT_RECEPTOR, RTRIM(EMAIL_RECEPTOR) EMAIL_RECEPTOR, ENVIADO, ACEPTADO, RTRIM(ESTADO) ESTADO, ERROR_LOCAL, ERROR_HACIENDA,
        ULTIMO_ERROR, LINK_CONSULTA, TOTALCOMPROBANTE, TOTALIMPUESTO, TOTALVENTANETA, RTRIM(EMAIL_ENVIADO) EMAIL_ENVIADO, ESTADO_COMPROBANTE, XML_PATH, PDF_PATH,
        DOCUMENTO_ELECTRONICO, XML_RESPUESTA, CAST(RESPUESTA_HACIENDA AS nvarchar(max)) RESPUESTA_HACIENDA
        FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE TIPO_DOC = ? AND NUMERO_DOC = ?");
    $st->execute([$tipo, $num]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if (!$r) return ['ok' => false, 'error' => 'Documento no encontrado'];
    $xml = xml_pretty($r['DOCUMENTO_ELECTRONICO']); $resp = xml_pretty($r['XML_RESPUESTA']);
    $b = ph_db()->prepare("SELECT TOP 200 CONVERT(varchar(23), FECHA_HORA, 121) f, RTRIM(TIPO) nivel, TEXTO texto FROM dbo.BITACORA WHERE DOCUMENTO = ? ORDER BY FECHA_HORA DESC");
    $b->execute([$num]);
    $bit = array_map(fn($x) => ['f' => $x['f'], 'nivel' => $x['nivel'], 'texto' => ph_mask(ph_utf8($x['texto']))], $b->fetchAll(PDO::FETCH_ASSOC));
    unset($r['DOCUMENTO_ELECTRONICO'], $r['XML_RESPUESTA']);
    foreach ($r as $k => $v) $r[$k] = is_string($v) ? ph_mask(ph_utf8($v)) : $v;
    $r['ESTADO_UI'] = estado_ui($r + ['TIENE_RESP' => $resp !== '']);
    return ['ok' => true, 'doc' => $r,
        'receptor_nombre' => xml_val($xml, "//*[local-name()='Receptor']/*[local-name()='Nombre']"),
        'consecutivo' => xml_val($xml, "//*[local-name()='NumeroConsecutivo']"),
        'mensaje_hacienda' => xml_val($resp, "//*[local-name()='DetalleMensaje']"),
        'xml' => $xml, 'xml_respuesta' => $resp, 'bitacora' => $bit,
        'puede_reintentar' => puede_reintentar($r)];
}

/* ---------------- Cola de envio (misma consulta que ProcesarPendientesEnvio, solo lectura) ---------------- */
function cola_sql(string $top): string
{
    // Misma logica que ProcesarPendientesEnvio (admin.php); NIT y FECHA_NORMATIVA se leen como subconsulta
    return "SELECT $top V.TIPO_DOCUMENTO, RTRIM(V.DOCUMENTO) AS DOCUMENTO, RTRIM(V.NIT) NIT, CONVERT(varchar(19), V.FECHA, 120) FECHA, RTRIM(p.ESTADO) ESTADO
        FROM V_DOCUMENTO_ELECTRONICO v LEFT JOIN DOC_ELECTRONICO_PROCESADO p ON v.DOCUMENTO = p.NUMERO_DOC
        WHERE v.ACEPTA_DOC_ELECTRONICO = 'S' AND V.TIPO_DOCUMENTO IN ('FE','NC','ND','TE')
        AND v.NIT NOT IN (SELECT RTRIM(NIT) FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI')
        AND NOT v.SAT IS NULL AND v.FECHA >= (SELECT TOP 1 FECHA_NORMATIVA FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI')
        AND v.DOCUMENTO <> 'F-00000000027825'
        AND ((p.ENVIADO IS NULL AND p.ACEPTADO IS NULL) OR p.ENVIADO = 'N')
        AND (p.ESTADO IS NULL OR p.ESTADO NOT IN ('rechazado', 'error'))";
}

function api_cola(): array
{
    $rows = ph_db()->query(cola_sql('TOP 500') . ' ORDER BY v.FECHA ASC')->fetchAll(PDO::FETCH_ASSOC);
    return ['ok' => true, 'rows' => array_map(fn($r) => array_map(fn($v) => ph_utf8((string)$v), $r), $rows)];
}

/* ---------------- Acciones ---------------- */
function puede_reintentar(array $r): bool
{
    return strtolower(trim((string)$r['ESTADO'])) === 'error' && trim((string)($r['ENVIADO'] ?? 'N')) !== 'S' && trim((string)($r['ACEPTADO'] ?? 'N')) !== 'S';
}

function api_reintentar(): array
{
    $tipo = (string)($_POST['tipo'] ?? ''); $num = (string)($_POST['numero'] ?? '');
    $dry = ($_POST['dry_run'] ?? '1') !== '0' || !file_exists(PH_GUARD);    // antes del corte SIEMPRE es simulacion
    $st = ph_db()->prepare("SELECT RTRIM(ESTADO) ESTADO, ENVIADO, ACEPTADO FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE TIPO_DOC = ? AND NUMERO_DOC = ?");
    $st->execute([$tipo, $num]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if (!$r) return ['ok' => false, 'simulacion' => $dry, 'error' => "No existe $tipo $num en DOC_ELECTRONICO_PROCESADO"];
    if (!puede_reintentar($r)) return ['ok' => false, 'simulacion' => $dry, 'error' => "Solo se reintentan documentos en estado 'error' no enviados (estado actual: {$r['ESTADO']})"];
    if ($dry) {
        return ['ok' => true, 'simulacion' => true, 'mensaje' => "SIMULACION: $tipo $num quedaria con ESTADO='reintentar' y lo tomaria el proximo lote del servicio" . (file_exists(PH_GUARD) ? '' : ' (bloqueado: aun no se realiza el corte, no se modifica nada)')];
    }
    $db = ph_db();
    $u = $db->prepare("UPDATE dbo.DOC_ELECTRONICO_PROCESADO SET ESTADO = 'reintentar', ULTIMO_ERROR = LEFT(? + ISNULL(ULTIMO_ERROR, ''), 4000)
        WHERE TIPO_DOC = ? AND NUMERO_DOC = ? AND ESTADO = 'error' AND ISNULL(ENVIADO, 'N') <> 'S' AND ISNULL(ACEPTADO, 'N') <> 'S'");
    $u->execute(['[Reintento solicitado en panel por ' . $_SERVER['REMOTE_USER'] . ' ' . date('Y-m-d H:i:s') . '] ', $tipo, $num]);
    $n = $u->rowCount();
    $b = $db->prepare("INSERT INTO dbo.BITACORA (SESSION_ID, TIPO, TEXTO, FECHA_HORA, DOCUMENTO, TIPO_DOCUMENTO) VALUES ('panel-hacienda', 'Info', ?, SYSDATETIME(), ?, ?)");
    $b->execute(['Panel: reintento solicitado por ' . $_SERVER['REMOTE_USER'], $num, $tipo]);
    return ['ok' => $n === 1, 'simulacion' => false, 'mensaje' => $n === 1 ? "$tipo $num marcado para reintento; se enviara en el proximo lote si el servicio esta iniciado" : 'No se actualizo (el estado cambio)'];
}

function api_servicio(): array
{
    $acc = $_POST['accion'] ?? '';
    if (!in_array($acc, ['start', 'stop'], true)) return ['ok' => false, 'error' => 'Accion invalida'];
    if ($acc === 'start' && !file_exists(PH_GUARD)) {
        return ['ok' => false, 'error' => 'Bloqueado: el corte no se ha realizado (falta ' . PH_GUARD . '). El envio sigue en el servidor Windows 172.16.12.3; iniciar aqui duplicaria comprobantes.'];
    }
    $out = []; $rc = 0;
    exec('/usr/bin/sudo -n /usr/bin/systemctl ' . $acc . ' ' . escapeshellarg(PH_UNIT . '.timer') . ' 2>&1', $out, $rc);
    error_log('panel-hacienda: ' . $_SERVER['REMOTE_USER'] . " systemctl $acc " . PH_UNIT . ".timer rc=$rc");
    return ['ok' => $rc === 0, 'rc' => $rc, 'salida' => implode("\n", $out), 'timer' => ph_systemctl_show(PH_UNIT . '.timer', ['ActiveState'])];
}
