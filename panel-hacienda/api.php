<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';

require_once __DIR__ . '/buzon/buzon-lib.php';
$a = $_GET['a'] ?? '';
try {
    // Sin sesion solo se permite: sesion (token CSRF y estado), login y logout.
    switch ($a) {
        case 'sesion': ph_json(['ok' => true, 'autenticado' => ph_user() !== '', 'usuario' => ph_user(), 'nombre' => $_SESSION['nombre'] ?? '', 'csrf' => $_SESSION['csrf']]);
        case 'login':  ph_require_post(); $r = ph_login((string)($_POST['usuario'] ?? ''), (string)($_POST['password'] ?? '')); ph_json($r + ['csrf' => $_SESSION['csrf']], $r['ok'] ? 200 : 401);
        case 'logout': ph_require_post(); if (ph_user() !== '') ph_accion('logout'); $_SESSION = []; session_regenerate_id(true); $_SESSION['csrf'] = bin2hex(random_bytes(32)); ph_json(['ok' => true, 'csrf' => $_SESSION['csrf']]);
    }
    ph_require_login();
    switch ($a) {
        case 'grafico':  ph_json(api_grafico());
        case 'status':   ph_json(api_status());
        case 'logs':     ph_json(api_logs());
        case 'docs':     ph_json(api_docs());
        case 'doc':      ph_json(api_doc());
        case 'cola':     ph_json(api_cola());
        case 'reintentar': ph_require_post(); ph_json(api_reintentar());
        case 'servicio': ph_require_post(); ph_json(api_servicio());
        case 'pdf':      api_pdf();
        case 'xml':      api_xml();
        case 'rutas':    ph_json(api_rutas_preview());
        case 'rutas_aplicar':  ph_require_post(); ph_json(api_rutas_aplicar());
        case 'rutas_revertir': ph_require_post(); ph_json(api_rutas_revertir());
        case 'dedata':   ph_json(api_dedata_estado());
        case 'buzon_estado':  ph_json(api_buzon_estado());
        case 'buzon_sync':    ph_require_post(); ph_json(api_buzon_sync());
        case 'buzon_kpis':    ph_json(api_buzon_kpis());
        case 'buzon_docs':    ph_json(api_buzon_docs());
        case 'buzon_doc':     ph_json(api_buzon_doc());
        case 'buzon_correos': ph_json(api_buzon_correos());
        case 'buzon_archivo': api_buzon_archivo();
        case 'dedata_sync': ph_require_post(); ph_json(api_dedata_sync());
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
        'usuario' => ph_user(),
        'servicio_activo' => ($t['ActiveState'] ?? '') === 'active',
        'timer' => $t, 'servicio' => $s,
        'bd' => $info + ['restaurada' => $restore],
        'ultima_bitacora' => $q("SELECT CONVERT(varchar(19), MAX(FECHA_HORA), 120) FROM dbo.BITACORA"),
        'ultimo_documento' => $q("SELECT CONVERT(varchar(19), MAX(FECHA_HORA), 120) FROM dbo.DOC_ELECTRONICO_PROCESADO"),
        'conteos' => [
            'error' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE ESTADO = 'error'"),
            'reintentar' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE ESTADO = 'reintentar'"),
            'pendiente_respuesta' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE (XML_RESPUESTA IS NULL OR XML_RESPUESTA = '') AND ENVIADO = 'S' AND FECHATIMBRADO >= '2025-01-01'"),
            'rechazado_hoy' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE ESTADO = 'rechazado' AND FECHA_HORA >= CAST(GETDATE() AS date)"),
            'enviados_hoy' => (int)$q("SELECT COUNT(*) FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE ENVIADO = 'S' AND FECHA_HORA >= CAST(GETDATE() AS date)"),
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
        CASE WHEN d.XML_RESPUESTA IS NULL OR d.XML_RESPUESTA = '' THEN 0 ELSE 1 END TIENE_RESP,
        CASE WHEN DATALENGTH(d.PDF) > 0 THEN 1 ELSE 0 END TIENE_PDF, d.PDF_PATH
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
            'pdf' => (int)$r['TIENE_PDF'] === 1 || ($clave !== '' && ph_dedata_file($r['PDF_PATH'], $r['FECHATIMBRADO'], $clave . '.pdf') !== null),
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
        'puede_reintentar' => puede_reintentar($r),
        'pdf' => doc_pdf($tipo, $num, true) !== null,
        'xml_firmado' => $xml !== '' || ph_dedata_file($r['XML_PATH'], $r['FECHATIMBRADO'], $r['SELLO_SAT'] . '.xml') !== null,
        'xml_resp' => $resp !== '' || ph_dedata_file($r['XML_PATH'], $r['FECHATIMBRADO'], 'RSP_' . $r['SELLO_SAT'] . '.xml') !== null];
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

function ph_servicio_activo(): bool
{
    return (ph_systemctl_show(PH_UNIT . '.timer', ['ActiveState'])['ActiveState'] ?? '') === 'active';
}

function api_reintentar(): array
{
    $tipo = (string)($_POST['tipo'] ?? ''); $num = (string)($_POST['numero'] ?? '');
    if (!ph_servicio_activo()) return ['ok' => false, 'error' => 'El servicio de envio esta detenido: inicielo para poder reintentar documentos'];
    $st = ph_db()->prepare("SELECT RTRIM(ESTADO) ESTADO, ENVIADO, ACEPTADO FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE TIPO_DOC = ? AND NUMERO_DOC = ?");
    $st->execute([$tipo, $num]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if (!$r) return ['ok' => false, 'error' => "No existe $tipo $num en DOC_ELECTRONICO_PROCESADO"];
    if (!puede_reintentar($r)) return ['ok' => false, 'error' => "Solo se reintentan documentos en estado 'error' no enviados (estado actual: {$r['ESTADO']})"];
    $u = ph_db()->prepare("UPDATE dbo.DOC_ELECTRONICO_PROCESADO SET ESTADO = 'reintentar', ULTIMO_ERROR = LEFT(? + ISNULL(ULTIMO_ERROR, ''), 4000)
        WHERE TIPO_DOC = ? AND NUMERO_DOC = ? AND ESTADO = 'error' AND ISNULL(ENVIADO, 'N') <> 'S' AND ISNULL(ACEPTADO, 'N') <> 'S'");
    $u->execute(['[Reintento solicitado en panel por ' . ph_user() . ' ' . date('Y-m-d H:i:s') . '] ', $tipo, $num]);
    $n = $u->rowCount();
    if ($n === 1) ph_accion("reintento solicitado $tipo $num", 'Info', $num, $tipo);
    return ['ok' => $n === 1, 'mensaje' => $n === 1 ? "$tipo $num marcado para reintento; lo enviara el proximo lote" : 'No se actualizo (el estado cambio)'];
}

function api_servicio(): array
{
    $acc = $_POST['accion'] ?? '';
    if (!in_array($acc, ['start', 'stop'], true)) return ['ok' => false, 'error' => 'Accion invalida'];
    if ($acc === 'start' && ($_POST['confirmar'] ?? '') !== 'INICIAR') {
        ph_accion_log('servicio: inicio rechazado (sin palabra de confirmacion)');
        return ['ok' => false, 'error' => 'Escriba INICIAR para confirmar el inicio del envio'];
    }
    // start: enable --now (persiste tras reinicio) / stop: disable --now
    $cmd = $acc === 'start' ? 'enable --now' : 'disable --now';
    $out = []; $rc = 0;
    exec('/usr/bin/sudo -n /usr/bin/systemctl ' . $cmd . ' ' . escapeshellarg(PH_UNIT . '.timer') . ' 2>&1', $out, $rc);
    ph_accion('servicio: ' . ($acc === 'start' ? 'INICIADO y habilitado' : 'DETENIDO y deshabilitado') . " (systemctl $cmd " . PH_UNIT . ".timer rc=$rc)", $rc === 0 ? 'Info' : 'Error');
    return ['ok' => $rc === 0, 'rc' => $rc, 'salida' => implode("\n", $out), 'timer' => ph_systemctl_show(PH_UNIT . '.timer', ['ActiveState', 'UnitFileState'])];
}

function api_grafico(): array
{
    $dias = in_array((int)($_GET['dias'] ?? 30), [14, 30, 90], true) ? (int)$_GET['dias'] : 30;
    $rows = ph_db()->query("SELECT CONVERT(varchar(10), CAST(FECHA_HORA AS date), 120) dia,
        SUM(CASE WHEN ESTADO LIKE 'aceptado%' THEN 1 ELSE 0 END) aceptados,
        SUM(CASE WHEN ESTADO = 'rechazado' THEN 1 ELSE 0 END) rechazados,
        SUM(CASE WHEN ESTADO LIKE 'aceptado%' OR ESTADO = 'rechazado' THEN 0 ELSE 1 END) otros
        FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE FECHA_HORA >= DATEADD(day, -$dias, CAST(GETDATE() AS date))
        GROUP BY CAST(FECHA_HORA AS date) ORDER BY 1")->fetchAll(PDO::FETCH_ASSOC);
    return ['ok' => true, 'rows' => array_map(fn($r) => ['dia' => $r['dia'], 'aceptados' => (int)$r['aceptados'], 'rechazados' => (int)$r['rechazados'], 'otros' => (int)$r['otros']], $rows)];
}

/* ---------------- PDF / XML ---------------- */
function doc_row(string $tipo, string $num, string $extra = ''): ?array
{
    $st = ph_db()->prepare("SELECT TOP 1 RTRIM(TIPO_DOC) TIPO_DOC, RTRIM(NUMERO_DOC) NUMERO_DOC, RTRIM(SELLO_SAT) SELLO_SAT, RTRIM(FECHATIMBRADO) FECHATIMBRADO, PDF_PATH, XML_PATH $extra
        FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE TIPO_DOC = ? AND NUMERO_DOC = ?");
    $st->execute([$tipo, $num]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    return $r ?: null;
}

function lob($v): string
{
    if (is_resource($v)) $v = stream_get_contents($v);
    return (string)$v;
}

/** Devuelve [contenido, origen] del PDF (BD columna PDF o archivo en DEData), o null. */
function doc_pdf(string $tipo, string $num, bool $soloExiste = false): ?array
{
    $r = doc_row($tipo, $num, ', DATALENGTH(PDF) PDF_LEN');
    if (!$r) return null;
    $clave = preg_replace('/\D/', '', (string)$r['SELLO_SAT']);
    $f = strlen($clave) === 50 ? ph_dedata_file($r['PDF_PATH'], $r['FECHATIMBRADO'], $clave . '.pdf') : null;
    if ($f !== null) return [$soloExiste ? '' : file_get_contents($f), 'archivo DEData', $clave];
    if ((int)$r['PDF_LEN'] > 0) {
        if ($soloExiste) return ['', 'BD', $clave];
        $st = ph_db()->prepare('SELECT PDF FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE TIPO_DOC = ? AND NUMERO_DOC = ?');
        $st->execute([$tipo, $num]);
        $pdf = lob($st->fetchColumn());
        if (!str_starts_with($pdf, '%PDF')) { $d = base64_decode($pdf, true); if ($d !== false && str_starts_with($d, '%PDF')) $pdf = $d; }
        if (str_starts_with($pdf, '%PDF')) return [$pdf, 'BD', $clave];
    }
    return null;
}

function no_disponible(string $msg): never
{
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>No disponible</title><body style="font-family:sans-serif;padding:2rem"><h3>' . htmlspecialchars($msg) . '</h3><p>No disponible.</p></body>';
    exit;
}

function api_pdf(): never
{
    $tipo = (string)($_GET['tipo'] ?? ''); $num = (string)($_GET['numero'] ?? '');
    $p = doc_pdf($tipo, $num);
    if ($p === null) no_disponible("PDF de $tipo $num no disponible");
    [$data, $origen, $clave] = $p;
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . ($clave ?: preg_replace('/[^A-Za-z0-9-]/', '', $num)) . '.pdf"');
    header('Content-Length: ' . strlen($data));
    header('X-PDF-Origen: ' . ($origen === 'BD' ? 'bd' : 'dedata'));
    echo $data;
    exit;
}

function api_xml(): never
{
    $tipo = (string)($_GET['tipo'] ?? ''); $num = (string)($_GET['numero'] ?? '');
    $kind = ($_GET['kind'] ?? 'firmado') === 'respuesta' ? 'respuesta' : 'firmado';
    $r = doc_row($tipo, $num);
    if (!$r) no_disponible("Documento $tipo $num no encontrado");
    $clave = preg_replace('/\D/', '', (string)$r['SELLO_SAT']);
    $name = ($kind === 'respuesta' ? 'RSP_' : '') . $clave . '.xml';
    $f = strlen($clave) === 50 ? ph_dedata_file($r['XML_PATH'], $r['FECHATIMBRADO'], $name) : null;
    $data = $f !== null ? (string)file_get_contents($f) : '';
    if ($data === '') {
        $st = ph_db()->prepare('SELECT ' . ($kind === 'respuesta' ? 'XML_RESPUESTA' : 'DOCUMENTO_ELECTRONICO') . ' FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE TIPO_DOC = ? AND NUMERO_DOC = ?');
        $st->execute([$tipo, $num]);
        $data = trim(lob($st->fetchColumn()));
        if ($data !== '' && !str_starts_with($data, '<')) { $d = base64_decode($data, true); if ($d !== false && str_starts_with(ltrim($d), '<')) $data = $d; }
    }
    if ($data === '') no_disponible('XML ' . ($kind === 'respuesta' ? 'de respuesta de Hacienda' : 'firmado') . " de $tipo $num no disponible");
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . ($clave ? $name : preg_replace('/[^A-Za-z0-9-]/', '', $num) . '.xml') . '"');
    echo $data;
    exit;
}

/* ---------------- Correccion de rutas (corte-hacienda.sql) ---------------- */

function rutas_plan(): array
{
    $sql = @file_get_contents(PH_CORTE_SQL);
    if ($sql === false) throw new RuntimeException('No se puede leer ' . PH_CORTE_SQL);
    if (!preg_match("/UPDATE\s+dbo\.\[COMPAÑIAS\]\s+SET(.+?)WHERE\s+CONFIGURADA\s*=\s*'SI'\s*;/su", $sql, $m)) throw new RuntimeException('No se encontro el UPDATE de COMPAÑIAS en ' . PH_CORTE_SQL);
    preg_match_all("/(\w+)\s*=\s*'([^']*)'/u", $m[1], $mm, PREG_SET_ORDER);
    $cols = [];
    foreach ($mm as $x) {
        if (!preg_match(RUTAS_COLS, $x[1])) throw new RuntimeException("Columna no permitida en el script: {$x[1]}");
        $cols[$x[1]] = $x[2];
    }
    if (!$cols) throw new RuntimeException('El script no define columnas');
    $proceso = (bool)preg_match("/UPDATE\s+dbo\.PROCESO\s+SET\s+EN_PROCESO\s*=\s*'NO'/i", $sql);
    return [$cols, $proceso];
}

function rutas_actuales(array $cols): array
{
    $db = ph_db();
    $n = (int)$db->query("SELECT COUNT(*) FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI'")->fetchColumn();
    if ($n !== 1) throw new RuntimeException("Se esperaba 1 compañía CONFIGURADA='SI' y hay $n");
    $r = $db->query('SELECT ' . implode(', ', array_map(fn($c) => "RTRIM([$c]) AS [$c]", array_keys($cols))) . " FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI'")->fetch(PDO::FETCH_ASSOC);
    $r = array_map(fn($v) => $v === null ? null : ph_utf8((string)$v), $r);
    $pr = $db->query('SELECT TOP 1 RTRIM(EN_PROCESO) FROM dbo.PROCESO')->fetchColumn();
    return [$r, $pr === false ? null : $pr];
}

function rutas_respaldos(): array
{
    $f = glob(PH_DATA . '/rutas-respaldo-*.json') ?: [];
    rsort($f);
    return $f;
}

function api_rutas_preview(): array
{
    [$cols, $proceso] = rutas_plan();
    [$act, $pr] = rutas_actuales($cols);
    $filas = [];
    foreach ($cols as $c => $nuevo) $filas[] = ['campo' => "COMPAÑIAS.$c", 'actual' => ph_mask((string)$act[$c]), 'nuevo' => ph_mask($nuevo), 'cambia' => trim((string)$act[$c]) !== $nuevo];
    if ($proceso) $filas[] = ['campo' => 'PROCESO.EN_PROCESO', 'actual' => (string)$pr, 'nuevo' => 'NO', 'cambia' => trim((string)$pr) !== 'NO'];
    $resp = rutas_respaldos();
    $ultimo = $resp ? json_decode((string)file_get_contents($resp[0]), true) : null;
    $info = ph_db_info();
    return ['ok' => true, 'bd' => $info, 'script' => PH_CORTE_SQL, 'filas' => $filas,
        'aplicado' => !array_filter($filas, fn($f) => $f['cambia']),
        'ultimo_respaldo' => $ultimo ? ['archivo' => basename($resp[0]), 'fecha' => $ultimo['fecha'], 'usuario' => $ultimo['usuario'], 'revertido' => $ultimo['revertido'] ?? null] : null,
        'historial' => array_slice(array_reverse(array_filter(explode("\n", (string)@file_get_contents(PH_DATA . '/acciones.log')), fn($l) => str_contains($l, 'rutas'))), 0, 10)];
}

function api_rutas_aplicar(): array
{
    if (($_POST['confirmar'] ?? '') !== 'APLICAR') return ['ok' => false, 'error' => 'Escriba APLICAR para confirmar'];
    [$cols, $proceso] = rutas_plan();
    $db = ph_db();
    if (!is_dir(PH_DATA) && !mkdir(PH_DATA, 0700, true)) throw new RuntimeException('No se pudo crear ' . PH_DATA);
    $db->beginTransaction();
    try {
        [$act, $pr] = rutas_actuales($cols);
        $info = ph_db_info();
        $bk = ['fecha' => date('Y-m-d H:i:s'), 'usuario' => ph_user(), 'bd' => $info, 'compania' => $act, 'proceso' => $pr, 'nuevos' => $cols];
        $file = PH_DATA . '/rutas-respaldo-' . date('Ymd-His') . '.json';
        if (file_put_contents($file, json_encode($bk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) === false) throw new RuntimeException('No se pudo guardar el respaldo');
        chmod($file, 0600);
        $set = implode(', ', array_map(fn($c) => "[$c] = ?", array_keys($cols)));
        $u = $db->prepare("UPDATE dbo.[COMPAÑIAS] SET $set WHERE CONFIGURADA = 'SI'");
        $u->execute(array_values($cols));
        if ($u->rowCount() !== 1) throw new RuntimeException('UPDATE afecto ' . $u->rowCount() . ' filas (se esperaba 1)');
        if ($proceso) $db->exec("UPDATE dbo.PROCESO SET EN_PROCESO = 'NO'");
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        if (isset($file) && is_file($file)) @rename($file, $file . '.fallido');
        ph_accion_log('rutas: ERROR al aplicar: ' . $e->getMessage());
        throw $e;
    }
    ph_accion("rutas: aplicadas en {$info['host']}/{$info['database']}; respaldo " . basename($file));
    return ['ok' => true, 'mensaje' => 'Rutas aplicadas; respaldo ' . basename($file)] + api_rutas_preview();
}

function api_rutas_revertir(): array
{
    if (($_POST['confirmar'] ?? '') !== 'REVERTIR') return ['ok' => false, 'error' => 'Escriba REVERTIR para confirmar'];
    $resp = array_values(array_filter(rutas_respaldos(), fn($f) => !(json_decode((string)file_get_contents($f), true)['revertido'] ?? null)));
    if (!$resp) return ['ok' => false, 'error' => 'No hay respaldo pendiente de revertir'];
    $file = $resp[0];
    $bk = json_decode((string)file_get_contents($file), true);
    $info = ph_db_info();
    if (($bk['bd']['host'] ?? '') !== $info['host'] || ($bk['bd']['database'] ?? '') !== $info['database']) return ['ok' => false, 'error' => 'El respaldo es de otra base de datos'];
    $cols = array_filter($bk['compania'], fn($c) => preg_match(RUTAS_COLS, (string)$c), ARRAY_FILTER_USE_KEY);
    $db = ph_db();
    $db->beginTransaction();
    try {
        $u = $db->prepare('UPDATE dbo.[COMPAÑIAS] SET ' . implode(', ', array_map(fn($c) => "[$c] = ?", array_keys($cols))) . " WHERE CONFIGURADA = 'SI'");
        $u->execute(array_values($cols));
        if ($u->rowCount() !== 1) throw new RuntimeException('UPDATE afecto ' . $u->rowCount() . ' filas');
        if ($bk['proceso'] !== null) $db->prepare('UPDATE dbo.PROCESO SET EN_PROCESO = ?')->execute([$bk['proceso']]);
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        ph_accion_log('rutas: ERROR al revertir: ' . $e->getMessage());
        throw $e;
    }
    $bk['revertido'] = date('Y-m-d H:i:s') . ' por ' . ph_user();
    file_put_contents($file, json_encode($bk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    ph_accion("rutas: revertidas en {$info['host']}/{$info['database']} desde " . basename($file));
    return ['ok' => true, 'mensaje' => 'Valores anteriores restaurados desde ' . basename($file)] + api_rutas_preview();
}

/* ---------------- Sincronizacion DEData ---------------- */
function api_dedata_estado(): array
{
    $log = is_readable(PH_SYNC_LOG) ? array_slice(file(PH_SYNC_LOG, FILE_IGNORE_NEW_LINES) ?: [], -40) : [];
    $fin = null; $ini = null;
    foreach (array_reverse($log) as $l) { if (!$fin && str_contains($l, ' FIN ')) $fin = $l; if (!$ini && str_contains($l, ' INICIO ')) $ini = $l; }
    $enCurso = false;
    if ($fh = @fopen('/run/lock/paticas-dedata-sync.lock', 'r')) { $enCurso = !flock($fh, LOCK_SH | LOCK_NB); if (!$enCurso) flock($fh, LOCK_UN); fclose($fh); }
    return ['ok' => true, 'destino' => PH_DEDATA, 'credenciales' => file_exists('/etc/lospaticas/smb-dedata.cred'), 'en_curso' => $enCurso,
        'ultimo_inicio' => $ini, 'ultimo_fin' => $fin, 'log' => array_map('ph_mask', $log)];
}

function api_dedata_sync(): array
{
    if (($_POST['confirmar'] ?? '') !== '1') return ['ok' => false, 'error' => 'Falta confirmacion'];
    $e = api_dedata_estado();
    if ($e['en_curso']) return ['ok' => false, 'error' => 'Ya hay una sincronizacion en curso'];
    exec('/usr/bin/sudo -n ' . PH_SYNC_BIN . ' > /dev/null 2>&1 &', $o, $rc);
    ph_accion('dedata: sincronizacion iniciada');
    usleep(1500000);
    return ['ok' => $rc === 0, 'mensaje' => 'Sincronizacion iniciada en segundo plano'] + api_dedata_estado();
}

/* ---------------- Buzon FE (facturaelectronica@lospaticas.com, solo lectura) ---------------- */

function bz_tablas_ok(): bool
{
    return (int)ph_db()->query("SELECT COUNT(*) FROM sys.tables WHERE name IN ('FE_BUZON_CORREOS','FE_BUZON_DOCUMENTOS','FE_BUZON_ARCHIVOS')")->fetchColumn() === 3;
}

function bz_sin_tablas(): array
{
    return ['ok' => false, 'error' => 'Las tablas FE_BUZON_* no existen en esta BD (¿restauración nueva?). Ejecute: sudo -u www-data php8.5 panel-hacienda/buzon/buzon-sync.php --schema'];
}

function api_buzon_estado(): array
{
    $e = is_readable(BZ_ESTADO) ? (json_decode((string)file_get_contents(BZ_ESTADO), true) ?: []) : [];
    $log = is_readable(BZ_LOG) ? array_slice(file(BZ_LOG, FILE_IGNORE_NEW_LINES) ?: [], -30) : [];
    $enCurso = false;
    if ($fh = @fopen('/run/lock/paticas-buzon-fe.lock', 'r')) { $enCurso = !flock($fh, LOCK_SH | LOCK_NB); if (!$enCurso) flock($fh, LOCK_UN); fclose($fh); }
    $t = ph_systemctl_show('paticas-buzon-fe.timer', ['ActiveState', 'UnitFileState', 'NextElapseUSecRealtime', 'LastTriggerUSec']);
    return ['ok' => true, 'estado' => $e + ['estado' => 'nunca'], 'en_curso' => $enCurso, 'timer' => $t, 'tablas' => bz_tablas_ok(), 'log' => array_map('ph_mask', array_reverse($log))];
}

function api_buzon_sync(): array
{
    $out = []; $rc = 0;
    exec('/usr/bin/sudo -n /usr/bin/systemctl start --no-block paticas-buzon-fe.service 2>&1', $out, $rc);
    ph_accion("buzon: sincronizacion manual solicitada (rc=$rc)");
    usleep(800000);
    return ['ok' => $rc === 0, 'mensaje' => $rc === 0 ? 'Sincronización iniciada' : 'No se pudo iniciar: ' . implode(' ', $out)] + api_buzon_estado();
}

function bz_where(array &$p): string
{
    $w = ['1 = 1'];
    $q = trim((string)($_GET['q'] ?? ''));
    if ($q !== '') { $w[] = '(d.NOMBRE LIKE ? OR d.NIT_EMISOR LIKE ? OR d.CLAVE LIKE ? OR d.CONSECUTIVO LIKE ? OR d.NOMBRE_RECEPTOR LIKE ?)'; array_push($p, ...array_fill(0, 5, '%' . $q . '%')); }
    $tipo = (string)($_GET['tipo'] ?? '');
    if (in_array($tipo, ['FE', 'TE', 'NC', 'ND', 'FEC', 'FEE', 'REP'], true)) { $w[] = 'd.TIPO_DOC = ?'; $p[] = $tipo; }
    $est = (string)($_GET['estado'] ?? '');
    if (in_array($est, ['aceptado', 'aceptado_parcial', 'rechazado'], true)) { $w[] = 'd.ESTADO_HACIENDA = ?'; $p[] = $est; }
    elseif ($est === 'sin_respuesta') $w[] = 'd.ESTADO_HACIENDA IS NULL';
    if (($d = ph_date($_GET['desde'] ?? null))) { $w[] = 'd.FECHA_EMISION >= ?'; $p[] = $d . ' 00:00:00'; }
    if (($h = ph_date($_GET['hasta'] ?? null))) { $w[] = 'd.FECHA_EMISION < DATEADD(day, 1, CAST(? AS date))'; $p[] = $h; }
    if (($_GET['para'] ?? '') === '1') $w[] = 'd.PARA_LOS_PATICAS = 1';
    return implode(' AND ', $w);
}

function bz_fila(array $r): array
{
    return ['id' => (int)$r['ID'], 'clave' => $r['CLAVE'], 'tipo' => $r['TIPO_DOC'], 'consecutivo' => $r['CONSECUTIVO'], 'fecha' => $r['FECHA_EMISION'],
        'emisor' => ph_utf8($r['NOMBRE']), 'nit_emisor' => $r['NIT_EMISOR'], 'receptor' => ph_utf8($r['NOMBRE_RECEPTOR']), 'nit_receptor' => $r['NIT_RECEPTOR'],
        'para' => (bool)$r['PARA_LOS_PATICAS'], 'moneda' => $r['MONEDA'], 'total' => $r['TOTALCOMPROBANTE'] === null ? null : (float)$r['TOTALCOMPROBANTE'],
        'impuesto' => $r['TOTALIMPUESTO'] === null ? null : (float)$r['TOTALIMPUESTO'], 'estado' => $r['ESTADO_HACIENDA'] ?: 'sin_respuesta',
        'xml' => (bool)$r['TIENE_XML'], 'respuesta' => (bool)$r['TIENE_RESPUESTA'], 'pdf' => (bool)$r['TIENE_PDF']];
}

function bz_cols(): string { return 'd.ID, d.CLAVE, d.TIPO_DOC, d.CONSECUTIVO, CONVERT(varchar(19), d.FECHA_EMISION, 120) FECHA_EMISION, d.NOMBRE, d.NIT_EMISOR, d.NOMBRE_RECEPTOR, d.NIT_RECEPTOR,
    d.PARA_LOS_PATICAS, d.MONEDA, d.TOTALCOMPROBANTE, d.TOTALIMPUESTO, d.ESTADO_HACIENDA, d.TIENE_XML, d.TIENE_RESPUESTA, d.TIENE_PDF'; }

function api_buzon_docs(): array
{
    if (!bz_tablas_ok()) return bz_sin_tablas();
    $p = []; $w = bz_where($p);
    $size = min(100, max(10, (int)($_GET['size'] ?? 25))); $page = max(1, (int)($_GET['page'] ?? 1));
    $st = ph_db()->prepare("SELECT COUNT(*) FROM dbo.FE_BUZON_DOCUMENTOS d WHERE $w"); $st->execute($p); $total = (int)$st->fetchColumn();
    $off = ($page - 1) * $size;
    $st = ph_db()->prepare('SELECT ' . bz_cols() . " FROM dbo.FE_BUZON_DOCUMENTOS d WHERE $w ORDER BY d.FECHA_EMISION DESC, d.ID DESC OFFSET $off ROWS FETCH NEXT $size ROWS ONLY");
    $st->execute($p);
    return ['ok' => true, 'total' => $total, 'page' => $page, 'size' => $size, 'rows' => array_map('bz_fila', $st->fetchAll(PDO::FETCH_ASSOC))];
}

function api_buzon_kpis(): array
{
    if (!bz_tablas_ok()) return bz_sin_tablas();
    $db = ph_db();
    $mes = "d.FECHA_EMISION >= DATEFROMPARTS(YEAR(GETDATE()), MONTH(GETDATE()), 1)";
    $r = $db->query("SELECT COUNT(*) n, SUM(CASE WHEN d.ESTADO_HACIENDA = 'rechazado' THEN 1 ELSE 0 END) rech, SUM(CASE WHEN d.ESTADO_HACIENDA IS NULL THEN 1 ELSE 0 END) sinr,
        SUM(CASE WHEN d.PARA_LOS_PATICAS = 1 THEN 1 ELSE 0 END) para FROM dbo.FE_BUZON_DOCUMENTOS d WHERE $mes")->fetch(PDO::FETCH_ASSOC);
    $mon = $db->query("SELECT ISNULL(d.MONEDA, 'CRC') moneda, COUNT(*) n, SUM(CASE WHEN d.TIPO_DOC = 'NC' THEN -1 ELSE 1 END * ISNULL(d.TOTALCOMPROBANTE, 0)) total,
        SUM(CASE WHEN d.TIPO_DOC = 'NC' THEN -1 ELSE 1 END * ISNULL(d.TOTALIMPUESTO, 0)) impuesto FROM dbo.FE_BUZON_DOCUMENTOS d WHERE $mes AND d.TIPO_DOC IS NOT NULL GROUP BY ISNULL(d.MONEDA, 'CRC') ORDER BY 2 DESC")->fetchAll(PDO::FETCH_ASSOC);
    $tipos = $db->query("SELECT ISNULL(TIPO_DOC, '?') t, COUNT(*) n FROM dbo.FE_BUZON_DOCUMENTOS GROUP BY TIPO_DOC ORDER BY 2 DESC")->fetchAll(PDO::FETCH_KEY_PAIR);
    $g = $db->query("SELECT COUNT(*) n, CONVERT(varchar(10), MIN(FECHA_EMISION), 120) desde, CONVERT(varchar(10), MAX(FECHA_EMISION), 120) hasta FROM dbo.FE_BUZON_DOCUMENTOS")->fetch(PDO::FETCH_ASSOC);
    $c = $db->query("SELECT ESTADO, COUNT(*) n FROM dbo.FE_BUZON_CORREOS GROUP BY ESTADO")->fetchAll(PDO::FETCH_KEY_PAIR);
    return ['ok' => true, 'mes' => ['documentos' => (int)$r['n'], 'rechazados' => (int)$r['rech'], 'sin_respuesta' => (int)$r['sinr'], 'para_los_paticas' => (int)$r['para'],
        'por_moneda' => array_map(fn($m) => ['moneda' => $m['moneda'], 'n' => (int)$m['n'], 'total' => (float)$m['total'], 'impuesto' => (float)$m['impuesto']], $mon)],
        'total' => ['documentos' => (int)$g['n'], 'desde' => $g['desde'], 'hasta' => $g['hasta'], 'por_tipo' => array_map('intval', $tipos)],
        'correos' => array_map('intval', $c)];
}

function bz_ruta_segura(string $rel): ?string
{
    if ($rel === '' || str_contains($rel, '..') || str_contains($rel, "\0")) return null;
    $abs = realpath(BZ_BASE . '/' . ltrim($rel, '/'));
    $base = realpath(BZ_BASE);
    return $abs && $base && str_starts_with($abs, $base . '/') && is_file($abs) ? $abs : null;
}

function api_buzon_doc(): array
{
    if (!bz_tablas_ok()) return bz_sin_tablas();
    $id = (int)($_GET['id'] ?? 0);
    $st = ph_db()->prepare('SELECT d.*, CONVERT(varchar(19), d.FECHA_EMISION, 120) FECHA_EMISION_F, CONVERT(varchar(19), d.FECHA_INGRESO, 120) FECHA_INGRESO_F FROM dbo.FE_BUZON_DOCUMENTOS d WHERE d.ID = ?');
    $st->execute([$id]);
    $d = $st->fetch(PDO::FETCH_ASSOC);
    if (!$d) return ['ok' => false, 'error' => 'Documento no encontrado'];
    $d = array_map(fn($v) => is_string($v) ? ph_utf8($v) : $v, $d);
    $a = ph_db()->prepare("SELECT a.ID, a.CLASE, a.NOMBRE, a.ORIGEN_ZIP, a.RUTA, a.TAMANO, a.CORREO_ID, CONVERT(varchar(19), a.FECHA, 120) FECHA,
        c.REMITENTE, c.REMITENTE_EMAIL, c.PARA, c.ASUNTO, CONVERT(varchar(19), c.FECHA_CORREO, 120) FECHA_CORREO, CONVERT(varchar(19), c.FECHA_RECIBIDO, 120) FECHA_RECIBIDO,
        c.CARPETA, c.MESSAGE_ID FROM dbo.FE_BUZON_ARCHIVOS a LEFT JOIN dbo.FE_BUZON_CORREOS c ON c.ID = a.CORREO_ID WHERE a.CLAVE = ? ORDER BY a.ID");
    $a->execute([$d['CLAVE']]);
    $arch = []; $correos = []; $xml = null; $resp = null; $pdf = null;
    foreach ($a->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $r = array_map(fn($v) => is_string($v) ? ph_utf8($v) : $v, $r);
        $arch[] = ['id' => (int)$r['ID'], 'clase' => $r['CLASE'], 'nombre' => $r['NOMBRE'], 'zip' => $r['ORIGEN_ZIP'], 'tamano' => (int)$r['TAMANO'], 'fecha' => $r['FECHA'], 'correo_id' => (int)$r['CORREO_ID']];
        $correos[$r['CORREO_ID']] = ['id' => (int)$r['CORREO_ID'], 'remitente' => $r['REMITENTE'], 'email' => $r['REMITENTE_EMAIL'], 'para' => $r['PARA'], 'asunto' => $r['ASUNTO'],
            'fecha' => $r['FECHA_CORREO'], 'recibido' => $r['FECHA_RECIBIDO'], 'carpeta' => $r['CARPETA'], 'message_id' => $r['MESSAGE_ID']];
        $abs = bz_ruta_segura((string)$r['RUTA']);
        if ($abs && $r['CLASE'] === 'xml' && $xml === null) $xml = xml_pretty((string)file_get_contents($abs, false, null, 0, 2 * 1048576));
        if ($abs && $r['CLASE'] === 'respuesta' && $resp === null) $resp = xml_pretty((string)file_get_contents($abs, false, null, 0, 2 * 1048576));
        if ($abs && $r['CLASE'] === 'pdf' && $pdf === null) $pdf = (int)$r['ID'];
    }
    return ['ok' => true, 'doc' => $d, 'archivos' => $arch, 'correos' => array_values($correos), 'xml' => $xml, 'xml_respuesta' => $resp, 'pdf_id' => $pdf];
}

function api_buzon_correos(): array
{
    if (!bz_tablas_ok()) return bz_sin_tablas();
    $p = []; $w = ['1 = 1'];
    $est = (string)($_GET['estado'] ?? '');
    if (in_array($est, ['ok', 'sin_documentos', 'propio', 'duplicado', 'muy_grande', 'error'], true)) { $w[] = 'ESTADO = ?'; $p[] = $est; }
    $q = trim((string)($_GET['q'] ?? ''));
    if ($q !== '') { $w[] = '(REMITENTE LIKE ? OR ASUNTO LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
    $w = implode(' AND ', $w);
    $size = min(100, max(10, (int)($_GET['size'] ?? 25))); $page = max(1, (int)($_GET['page'] ?? 1)); $off = ($page - 1) * $size;
    $st = ph_db()->prepare("SELECT COUNT(*) FROM dbo.FE_BUZON_CORREOS WHERE $w"); $st->execute($p); $total = (int)$st->fetchColumn();
    $st = ph_db()->prepare("SELECT ID, CARPETA, REMITENTE, ASUNTO, CONVERT(varchar(19), FECHA_RECIBIDO, 120) RECIBIDO, TAMANO, ADJUNTOS, DOCUMENTOS, ESTADO, ERROR
        FROM dbo.FE_BUZON_CORREOS WHERE $w ORDER BY FECHA_RECIBIDO DESC, ID DESC OFFSET $off ROWS FETCH NEXT $size ROWS ONLY");
    $st->execute($p);
    $rows = array_map(fn($r) => ['id' => (int)$r['ID'], 'carpeta' => ph_utf8($r['CARPETA']), 'remitente' => ph_utf8($r['REMITENTE']), 'asunto' => ph_utf8($r['ASUNTO']),
        'recibido' => $r['RECIBIDO'], 'tamano' => (int)$r['TAMANO'], 'adjuntos' => (int)$r['ADJUNTOS'], 'documentos' => (int)$r['DOCUMENTOS'], 'estado' => $r['ESTADO'],
        'error' => ph_mask(ph_utf8($r['ERROR']))], $st->fetchAll(PDO::FETCH_ASSOC));
    return ['ok' => true, 'total' => $total, 'page' => $page, 'size' => $size, 'rows' => $rows];
}

function api_buzon_archivo(): never
{
    if (!bz_tablas_ok()) no_disponible('Tablas del buzón no disponibles');
    $st = ph_db()->prepare('SELECT CLASE, NOMBRE, RUTA, CLAVE FROM dbo.FE_BUZON_ARCHIVOS WHERE ID = ?');
    $st->execute([(int)($_GET['id'] ?? 0)]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    $abs = $r ? bz_ruta_segura((string)$r['RUTA']) : null;
    if (!$abs) no_disponible('Archivo no disponible');
    $pdf = $r['CLASE'] === 'pdf';
    $nombre = bz_nombre_seguro(($r['CLAVE'] ?: 'documento') . (['xml' => '.xml', 'respuesta' => '_respuesta.xml', 'receptor' => '_receptor.xml', 'pdf' => '.pdf'][$r['CLASE']] ?? '.bin'));
    ph_accion_log("buzon: archivo {$r['CLASE']} {$r['CLAVE']}");
    header('Content-Type: ' . ($pdf ? 'application/pdf' : 'application/xml; charset=utf-8'));
    header('Content-Disposition: ' . ($pdf && ($_GET['descargar'] ?? '') !== '1' ? 'inline' : 'attachment') . '; filename="' . $nombre . '"');
    header('Content-Length: ' . filesize($abs));
    readfile($abs);
    exit;
}
