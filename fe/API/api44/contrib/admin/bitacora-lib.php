<?php
declare(strict_types=1);

/**
 * Bitácora del sender api44: niveles, RUN_ID, componente, código,
 * resumen por corrida, heartbeat y avisos deduplicados.
 */
const BITACORA_HEARTBEAT_SEG = 300;  // 5 minutos: un resumen aunque no haya novedad
const BITACORA_ALERTA_SEG = 1800;  // 30 minutos: reescribir el aviso si sigue activo
const BITACORA_ESTADO_ARCHIVO = '/var/lib/lospaticas/fe/sender-resumen.json';

function bitacora_run_id(): string
{
    global $BITACORA_RUN_ID;
    return (string)($BITACORA_RUN_ID ?? '');
}

function bitacora_run_iniciar(): void
{
    global $BITACORA_RUN_ID, $BITACORA_RUN;
    $BITACORA_RUN_ID = date('YmdHis') . '-' . bin2hex(random_bytes(8));
    $BITACORA_RUN = [
        'inicio' => microtime(true),
        'pendientes' => 0,
        'enviados' => 0,
        'aceptados' => 0,
        'rechazados' => 0,
        'omitidos' => 0,
        'omitidos_motivo' => [],
        'errores' => 0,
        'novedad' => false,
    ];
}

function bitacora_run_contar(string $campo, int $n = 1, string $motivo = ''): void
{
    global $BITACORA_RUN;
    if (!is_array($BITACORA_RUN ?? null) || !array_key_exists($campo, $BITACORA_RUN)) {
        return;
    }
    if (is_int($BITACORA_RUN[$campo])) {
        $BITACORA_RUN[$campo] += $n;
    }
    if ($campo === 'omitidos' && $motivo !== '') {
        $BITACORA_RUN['omitidos_motivo'][$motivo] = ($BITACORA_RUN['omitidos_motivo'][$motivo] ?? 0) + $n;
    }
}

function bitacora_run_novedad(): void
{
    global $BITACORA_RUN;
    if (is_array($BITACORA_RUN ?? null)) {
        $BITACORA_RUN['novedad'] = true;
    }
}

function bitacora_es_ruido(string $texto, string $nivel, string $codigo): bool
{
    if ($nivel !== 'Debug') {
        return false;
    }
    if ($codigo !== '' && in_array($codigo, ['TOKEN_OK', 'TOKEN_REFRESH', 'TOKEN_SAVE', 'RUN_BANNER', 'SIN_PENDIENTES', 'DOC_PROCESANDO'], true)) {
        return true;
    }
    $t = trim($texto);
    if ($t === '') {
        return true;
    }
    if (str_contains($t, 'PROCESAMIENTO DOCUMENTOS') || str_contains($t, 'TERMINA PROCESAMIENTO')) {
        return true;
    }
    if (str_starts_with($t, 'No se encontraron')) {
        return true;
    }
    if (preg_match('/^Procesando\s+/u', $t)) {
        return true;
    }
    if (stripos($t, 'access_token') !== false) {
        return true;
    }
    if (str_contains($t, 'Guardando token') || str_contains($t, 'cierra sesion con el IDP')) {
        return true;
    }
    return false;
}

function bitacora_clasificar(string $texto, string $nivel, string $componente, string $codigo): array
{
    $t = trim($texto);
    if (stripos($t, 'fechafact') !== false && stripos($t, 'posterior a hoy') !== false) {
        $codigo = $codigo !== '' ? $codigo : 'DOC_FECHA_FUTURA';
        $componente = $componente !== '' ? $componente : 'envio';
        if ($nivel === 'Debug') {
            $nivel = 'Warning';
        }
    } elseif (stripos($t, 'clave SAT tiene fecha') !== false && stripos($t, 'posterior') !== false) {
        $codigo = $codigo !== '' ? $codigo : 'DOC_CLAVE_FECHA_FUTURA';
        $componente = $componente !== '' ? $componente : 'envio';
        if ($nivel === 'Debug') {
            $nivel = 'Warning';
        }
    } elseif (stripos($t, 'recibido correctamente') !== false) {
        $codigo = $codigo !== '' ? $codigo : 'DOC_RECIBIDO';
        $componente = $componente !== '' ? $componente : 'envio';
        if ($nivel === 'Debug') {
            $nivel = 'Info';
        }
    } elseif (stripos($t, 'pendiente de ser procesado') !== false) {
        $codigo = $codigo !== '' ? $codigo : 'DOC_ENVIADO';
        $componente = $componente !== '' ? $componente : 'envio';
        if ($nivel === 'Debug') {
            $nivel = 'Info';
        }
    } elseif (preg_match('/\brechaz/i', $t) && (stripos($t, 'hacienda') !== false || stripos($t, 'documento') !== false || stripos($t, 'MensajeHacienda') !== false)) {
        $codigo = $codigo !== '' ? $codigo : 'DOC_RECHAZADO';
        $componente = $componente !== '' ? $componente : 'respuesta';
        if ($nivel === 'Debug') {
            $nivel = 'Warning';
        }
    } elseif (preg_match('/\baceptad/i', $t) && (stripos($t, 'hacienda') !== false || stripos($t, 'documento') !== false)) {
        $codigo = $codigo !== '' ? $codigo : 'DOC_ACEPTADO';
        $componente = $componente !== '' ? $componente : 'respuesta';
        if ($nivel === 'Debug') {
            $nivel = 'Info';
        }
    } elseif ($nivel === 'Debug' && preg_match('/\b(Error|Exception|falló)\b/i', $t)) {
        $nivel = 'Error';
        $codigo = $codigo !== '' ? $codigo : 'ERROR';
    }
    if ($componente === '' && (stripos($t, 'token') !== false || stripos($t, 'IDP') !== false)) {
        $componente = 'token';
    }
    if ($componente === '' && (stripos($t, 'correo') !== false || stripos($t, 'mensaje') !== false && stripos($t, 'enviado al cliente') !== false)) {
        $componente = 'recepcion';
    }
    return [$nivel, $componente, $codigo];
}

function bitacora_alerta_clave(string $codigo, string $documento, string $tipoDoc): string
{
    return mb_substr($codigo . '|' . $tipoDoc . '|' . $documento, 0, 200);
}

/**
 * @return array{0:bool,1:int} [debe_escribir, veces]
 */
function bitacora_alerta_registrar(string $codigo, string $mensaje, string $documento, string $tipoDoc, int $intervaloSeg = BITACORA_ALERTA_SEG): array
{
    global $dbConn;
    if (!$dbConn || $codigo === '') {
        return [true, 1];
    }
    $clave = bitacora_alerta_clave($codigo, $documento, $tipoDoc);
    try {
        $st = $dbConn->prepare('SELECT VECES, MENSAJE, ULTIMO_LOG, ACTIVA FROM dbo.BITACORA_ALERTA WHERE CLAVE = ?');
        $st->execute([$clave]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        $ahora = date('Y-m-d H:i:s');
        if (!$r) {
            $dbConn->prepare('INSERT INTO dbo.BITACORA_ALERTA (CLAVE, CODIGO, DOCUMENTO, TIPO_DOCUMENTO, MENSAJE, PRIMERA_VEZ, ULTIMA_VEZ, ULTIMO_LOG, VECES, ACTIVA) VALUES (?,?,?,?,?,?,?,?,1,1)')
                ->execute([$clave, $codigo, $documento !== '' ? $documento : null, $tipoDoc !== '' ? $tipoDoc : null, mb_substr($mensaje, 0, 1000), $ahora, $ahora, $ahora]);
            return [true, 1];
        }
        $veces = (int)$r['VECES'] + 1;
        $cambio = trim((string)$r['MENSAJE']) !== trim($mensaje) || !(int)$r['ACTIVA'];
        $ultimo = strtotime((string)$r['ULTIMO_LOG']) ?: 0;
        $escribir = $cambio || (time() - $ultimo) >= $intervaloSeg;
        $dbConn->prepare('UPDATE dbo.BITACORA_ALERTA SET VECES=?, MENSAJE=?, ULTIMA_VEZ=?, ULTIMO_LOG=?, ACTIVA=1' . ($escribir ? '' : '') . ' WHERE CLAVE=?');
        // ULTIMO_LOG only advances when we write a new bitácora line
        if ($escribir) {
            $dbConn->prepare('UPDATE dbo.BITACORA_ALERTA SET VECES=?, MENSAJE=?, ULTIMA_VEZ=?, ULTIMO_LOG=?, ACTIVA=1 WHERE CLAVE=?')
                ->execute([$veces, mb_substr($mensaje, 0, 1000), $ahora, $ahora, $clave]);
        } else {
            $dbConn->prepare('UPDATE dbo.BITACORA_ALERTA SET VECES=?, ULTIMA_VEZ=?, ACTIVA=1 WHERE CLAVE=?')
                ->execute([$veces, $ahora, $clave]);
        }
        return [$escribir, $veces];
    } catch (Throwable $e) {
        error_log('bitacora_alerta_registrar: ' . $e->getMessage());
        return [true, 1];
    }
}

function bitacora_alerta_desactivar(string $codigo, string $documento, string $tipoDoc = ''): void
{
    global $dbConn;
    if (!$dbConn || $codigo === '' || $documento === '') {
        return;
    }
    try {
        $dbConn->prepare('UPDATE dbo.BITACORA_ALERTA SET ACTIVA=0, ULTIMA_VEZ=SYSDATETIME() WHERE CLAVE=?')
            ->execute([bitacora_alerta_clave($codigo, $documento, $tipoDoc)]);
    } catch (Throwable $e) {
        error_log('bitacora_alerta_desactivar: ' . $e->getMessage());
    }
}

function Bitacora(
    string $sessionID,
    string $Texto,
    string $Documento = '',
    string $TipoDoc = '',
    string $nivel = 'Debug',
    bool $mostrarConsola = true,
    string $componente = '',
    string $codigo = ''
): bool {
    if ($sessionID === '' || $Texto === '') {
        return false;
    }
    $nivelesValidos = ['Debug', 'Info', 'Warning', 'Error'];
    if (!in_array($nivel, $nivelesValidos, true)) {
        $nivel = 'Debug';
    }
    [$nivel, $componente, $codigo] = bitacora_clasificar($Texto, $nivel, $componente, $codigo);

    try {
        global $dbConn;
        $textoLimitado = mb_substr(normalizarTextoBitacora($Texto), 0, 4000);
        if ($mostrarConsola && PHP_SAPI === 'cli') {
            $timestamp = date('d/m/Y H:i:s');
            $docInfo = $Documento !== '' ? "|$Documento|" : '';
            echo "[$timestamp] [$nivel] $docInfo $textoLimitado\n";
        }
        if (bitacora_es_ruido($Texto, $nivel, $codigo)) {
            return true;
        }
        if (!$dbConn) {
            error_log("Bitacora: No hay conexión a BD - SessionID: $sessionID, Texto: $textoLimitado");
            return false;
        }
        $sql = 'INSERT INTO dbo.BITACORA (SESSION_ID, TIPO, TEXTO, FECHA_HORA, DOCUMENTO, TIPO_DOCUMENTO, RUN_ID, COMPONENTE, CODIGO)
                VALUES (:sessionID, :Tipo, :Texto, SYSDATETIME(), :Documento, :TipoDoc, :RunId, :Componente, :Codigo)';
        $stm = $dbConn->prepare($sql);
        $ok = $stm->execute([
            ':sessionID' => $sessionID,
            ':Tipo' => $nivel,
            ':Texto' => $textoLimitado,
            ':Documento' => $Documento,
            ':TipoDoc' => $TipoDoc,
            ':RunId' => bitacora_run_id() !== '' ? bitacora_run_id() : null,
            ':Componente' => $componente !== '' ? $componente : null,
            ':Codigo' => $codigo !== '' ? $codigo : null,
        ]);
        if ($ok && in_array($nivel, ['Info', 'Warning', 'Error'], true)) {
            bitacora_run_novedad();
            if ($nivel === 'Error') {
                bitacora_run_contar('errores');
            }
            if ($codigo === 'DOC_ENVIADO' || $codigo === 'DOC_RECIBIDO') {
                bitacora_run_contar('enviados');
                bitacora_alerta_reset_fallos($Documento, $TipoDoc);
            }
            if ($codigo === 'DOC_ACEPTADO') {
                bitacora_run_contar('aceptados');
                bitacora_alerta_desactivar('DOC_FECHA_FUTURA', $Documento, $TipoDoc);
                bitacora_alerta_reset_fallos($Documento, $TipoDoc);
            }
            if ($codigo === 'DOC_RECHAZADO') {
                bitacora_run_contar('rechazados');
            }
        }
        return $ok;
    } catch (PDOException $ex) {
        $errorMsg = "Bitacora PDO Error: {$ex->getMessage()} - SessionID: $sessionID, Documento: $Documento, Tipo: $TipoDoc";
        if (PHP_SAPI === 'cli') {
            echo '[' . date('d/m/Y H:i:s') . "] [ERROR] $errorMsg\n";
        }
        error_log($errorMsg);
        if (function_exists('grace_debug')) {
            grace_debug($errorMsg);
        }
        return false;
    } catch (Exception $ex) {
        $errorMsg = "Bitacora General Error: {$ex->getMessage()} - SessionID: $sessionID";
        if (PHP_SAPI === 'cli') {
            echo '[' . date('d/m/Y H:i:s') . "] [ERROR] $errorMsg\n";
        }
        error_log($errorMsg);
        return false;
    }
}

function bitacora_motivo_corto(string $codigo, string $mensaje): string
{
    return match ($codigo) {
        'DOC_FECHA_FUTURA', 'DOC_CLAVE_FECHA_FUTURA' => 'fecha futura',
        default => ($mensaje !== '' ? mb_substr($mensaje, 0, 40) : $codigo),
    };
}

function bitacora_run_cerrar(string $sessionID): void
{
    global $BITACORA_RUN;
    if (!is_array($BITACORA_RUN ?? null)) {
        return;
    }
    $dur = microtime(true) - (float)$BITACORA_RUN['inicio'];
    $durTxt = $dur < 10 ? (number_format($dur, 1, '.', '') . ' s') : ((string)(int)round($dur) . ' s');
    $motivos = $BITACORA_RUN['omitidos_motivo'] ?? [];
    $motTxt = $motivos ? (' (' . implode(', ', array_keys($motivos)) . ')') : '';
    $hhmm = date('H:i');
    $etapas = $BITACORA_RUN['etapas_error'] ?? [];
    $etTxt = $etapas ? (' (etapa ' . implode(', ', $etapas) . ')') : '';
    $texto = sprintf(
        'Corrida %s · %d pendientes · %d enviados · %d omitidos%s · %d errores%s · %s',
        $hhmm,
        (int)$BITACORA_RUN['pendientes'],
        (int)$BITACORA_RUN['enviados'],
        (int)$BITACORA_RUN['omitidos'],
        $motTxt,
        (int)$BITACORA_RUN['errores'],
        $etTxt,
        $durTxt
    );
    $novedad = !empty($BITACORA_RUN['novedad'])
        || (int)$BITACORA_RUN['enviados'] > 0
        || (int)$BITACORA_RUN['errores'] > 0
        || (int)$BITACORA_RUN['aceptados'] > 0
        || (int)$BITACORA_RUN['rechazados'] > 0;
    $escribir = $novedad;
    if (!$escribir) {
        $ultimo = 0;
        if (is_readable(BITACORA_ESTADO_ARCHIVO)) {
            $j = json_decode((string)file_get_contents(BITACORA_ESTADO_ARCHIVO), true);
            $ultimo = (int)($j['ultimo_resumen'] ?? 0);
        }
        $escribir = (time() - $ultimo) >= BITACORA_HEARTBEAT_SEG;
    }
    if ($escribir) {
        Bitacora($sessionID, $texto, '', '', 'Info', true, 'envio', 'RUN_RESUMEN');
        @file_put_contents(
            BITACORA_ESTADO_ARCHIVO,
            json_encode(['ultimo_resumen' => time(), 'texto' => $texto, 'run_id' => bitacora_run_id()], JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
        @chmod(BITACORA_ESTADO_ARCHIVO, 0640);
    }
}

const BITACORA_MAX_FALLOS_DOC_DEF = 10;
const BITACORA_MAX_FALLOS_CONF = '/var/lib/lospaticas/fe/sender.conf';
const BITACORA_CODIGOS_FALLO_RESET = [
    'DOC_FALLO', 'DOC_FALLOS_MAX', 'DOC_PROCESO_DETENIDO', 'DOC_EXCEPCION',
    'MAIL_SIN_CLAVE', 'MAIL_SIN_DIR_PDF', 'MAIL_SIN_DIR_XML',
];

function bitacora_max_fallos_doc(?int $forzar = null): int
{
    static $n = null;
    if ($forzar !== null) {
        $n = max(1, min(100, $forzar));
        return $n;
    }
    if ($n !== null) {
        return $n;
    }
    $n = BITACORA_MAX_FALLOS_DOC_DEF;
    if (function_exists('params_get')) {
        $p = params_get('max_fallos_doc');
        if ($p !== null && $p !== '' && ctype_digit((string)$p)) {
            $v = (int)$p;
            if ($v >= 1 && $v <= 100) {
                $n = $v;
                return $n;
            }
        }
    }
    if (is_readable(BITACORA_MAX_FALLOS_CONF)) {
        foreach (file(BITACORA_MAX_FALLOS_CONF, FILE_IGNORE_NEW_LINES) ?: [] as $l) {
            if (preg_match('/^MAX_FALLOS_DOC=(\d{1,3})\s*$/', $l, $m)) {
                $v = (int)$m[1];
                if ($v >= 1 && $v <= 100) {
                    $n = $v;
                }
            }
        }
    }
    return $n;
}

function bitacora_es_omitido_fecha_futura(string $codigo, string $mensaje = ''): bool
{
    if (in_array($codigo, ['DOC_FECHA_FUTURA', 'DOC_CLAVE_FECHA_FUTURA'], true)) {
        return true;
    }
    return stripos($mensaje, 'fechafact') !== false || stripos($mensaje, 'posterior a hoy') !== false;
}

function bitacora_run_etapa_error(string $etapa): void
{
    global $BITACORA_RUN;
    bitacora_run_contar('errores');
    bitacora_run_novedad();
    if (!is_array($BITACORA_RUN ?? null)) {
        return;
    }
    $BITACORA_RUN['etapas_error'] = $BITACORA_RUN['etapas_error'] ?? [];
    $BITACORA_RUN['etapas_error'][] = $etapa;
}

function bitacora_alerta_reset(string $codigo, string $documento, string $tipoDoc = ''): void
{
    global $dbConn;
    if (!$dbConn || $codigo === '' || $documento === '') {
        return;
    }
    try {
        $dbConn->prepare('UPDATE dbo.BITACORA_ALERTA SET ACTIVA=0, VECES=0, ULTIMA_VEZ=SYSDATETIME() WHERE CLAVE=?')
            ->execute([bitacora_alerta_clave($codigo, $documento, $tipoDoc)]);
    } catch (Throwable $e) {
        error_log('bitacora_alerta_reset: ' . $e->getMessage());
    }
}

function bitacora_alerta_reset_fallos(string $documento, string $tipoDoc): void
{
    global $dbConn;
    if (!$dbConn || $documento === '') {
        return;
    }
    try {
        $in = implode(',', array_fill(0, count(BITACORA_CODIGOS_FALLO_RESET), '?'));
        $st = $dbConn->prepare("UPDATE dbo.BITACORA_ALERTA SET ACTIVA=0, VECES=0, ULTIMA_VEZ=SYSDATETIME()
            WHERE RTRIM(ISNULL(DOCUMENTO,'')) = ? AND RTRIM(ISNULL(TIPO_DOCUMENTO,'')) = ? AND CODIGO IN ($in)");
        $st->execute(array_merge([$documento, $tipoDoc], BITACORA_CODIGOS_FALLO_RESET));
    } catch (Throwable $e) {
        error_log('bitacora_alerta_reset_fallos: ' . $e->getMessage());
    }
}

function bitacora_alerta_veces(string $codigo, string $documento, string $tipoDoc): int
{
    global $dbConn;
    if (!$dbConn || $codigo === '' || $documento === '') {
        return 0;
    }
    try {
        $st = $dbConn->prepare('SELECT VECES, ACTIVA FROM dbo.BITACORA_ALERTA WHERE CLAVE=?');
        $st->execute([bitacora_alerta_clave($codigo, $documento, $tipoDoc)]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (!$r || !(int)$r['ACTIVA']) {
            return 0;
        }
        return (int)$r['VECES'];
    } catch (Throwable $e) {
        return 0;
    }
}

/** True si el correo de este documento ya alcanzó el tope y no debe reintentarse cada minuto. */
function bitacora_correo_agotado(string $documento, string $tipoDoc): bool
{
    $max = bitacora_max_fallos_doc();
    foreach (['MAIL_SIN_CLAVE', 'MAIL_SIN_DIR_PDF', 'MAIL_SIN_DIR_XML'] as $c) {
        if (bitacora_alerta_veces($c, $documento, $tipoDoc) >= $max) {
            return true;
        }
    }
    return false;
}

/**
 * Marca ESTADO=error (ENVIADO=N) para sacarlo de la cola, solo si no está enviado/aceptado/rechazado
 * y no es fecha futura. Inserta una fila mínima si aún no existe. No toca XML ni claves.
 */
function bitacora_marcar_error_envio(string $documento, string $tipoDoc, string $motivo): bool
{
    global $dbConn;
    if (!$dbConn || $documento === '' || $tipoDoc === '') {
        return false;
    }
    $motivo = mb_substr($motivo, 0, 500);
    try {
        $st = $dbConn->prepare('SELECT RTRIM(ISNULL(ENVIADO,\'N\')) ENVIADO, RTRIM(ISNULL(ACEPTADO,\'N\')) ACEPTADO, RTRIM(ISNULL(ESTADO,\'\')) ESTADO
            FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE NUMERO_DOC=? AND TIPO_DOC=?');
        $st->execute([$documento, $tipoDoc]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $e = strtolower(trim((string)$r['ESTADO']));
            if ($r['ENVIADO'] === 'S' || $r['ACEPTADO'] === 'S' || str_starts_with($e, 'aceptado') || $e === 'rechazado') {
                return false;
            }
            $u = $dbConn->prepare("UPDATE dbo.DOC_ELECTRONICO_PROCESADO
                SET ESTADO='error', ENVIADO='N', ERROR_LOCAL='S', ERROR_HACIENDA='N', ULTIMO_ERROR=?
                WHERE NUMERO_DOC=? AND TIPO_DOC=?
                  AND ISNULL(ENVIADO,'N')<>'S' AND ISNULL(ACEPTADO,'N')<>'S'
                  AND RTRIM(ISNULL(ESTADO,'')) NOT IN ('rechazado')
                  AND RTRIM(ISNULL(ESTADO,'')) NOT LIKE 'aceptado%'");
            $u->execute([$motivo, $documento, $tipoDoc]);
            return $u->rowCount() > 0;
        }
        $ins = $dbConn->prepare("INSERT INTO dbo.DOC_ELECTRONICO_PROCESADO
            (TIPO_DOC, NUMERO_DOC, NIT_EMISOR, GLN_EMISOR, NIT_RECEPTOR, GLN_RECEPTOR,
             ENVIADO, ACEPTADO, ESTADO, ERROR_LOCAL, ERROR_HACIENDA, ULTIMO_ERROR, EMAIL_ENVIADO)
            SELECT RTRIM(v.TIPO_DOCUMENTO), RTRIM(v.DOCUMENTO),
                   LEFT(ISNULL(RTRIM(c.NIT),''),40), LEFT(ISNULL(RTRIM(c.GLN),''),13),
                   LEFT(ISNULL(RTRIM(v.NIT),''),40), LEFT(ISNULL(RTRIM(v.GLN),''),13),
                   'N', 'N', 'error', 'S', 'N', ?, 'NO'
            FROM dbo.V_DOCUMENTO_ELECTRONICO v
            CROSS JOIN (SELECT TOP 1 NIT, GLN FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA='SI') c
            WHERE RTRIM(v.DOCUMENTO)=? AND RTRIM(v.TIPO_DOCUMENTO)=?
              AND NOT EXISTS (
                  SELECT 1 FROM dbo.DOC_ELECTRONICO_PROCESADO p
                  WHERE p.NUMERO_DOC=RTRIM(v.DOCUMENTO) AND p.TIPO_DOC=RTRIM(v.TIPO_DOCUMENTO)
              )");
        $ins->execute([$motivo, $documento, $tipoDoc]);
        return $ins->rowCount() > 0;
    } catch (Throwable $e) {
        error_log('bitacora_marcar_error_envio: ' . $e->getMessage());
        return false;
    }
}

/**
 * Registra un fallo de un documento (aviso deduplicado).
 * Si $puedeMarcarError y alcanza el tope, lo saca de la cola con ESTADO=error (Reintentar lo devuelve).
 * No aplica a fecha futura. No marca error si el documento ya fue enviado o aceptado.
 * @return array{0:int,1:bool} [veces, capado]
 */
function bitacora_doc_fallo(
    string $sessionID,
    string $documento,
    string $tipoDoc,
    string $codigo,
    string $mensaje,
    string $componente = 'envio',
    bool $puedeMarcarError = true
): array {
    if ($documento === '' || bitacora_es_omitido_fecha_futura($codigo, $mensaje)) {
        return [0, false];
    }
    $max = bitacora_max_fallos_doc();
    [$escribir, $veces] = bitacora_alerta_registrar($codigo, $mensaje, $documento, $tipoDoc);
    if ($escribir && function_exists('Bitacora')) {
        $texto = $mensaje;
        if ($veces > 1) {
            $texto .= " (intento $veces, último " . date('H:i') . ')';
        }
        Bitacora($sessionID, $texto, $documento, $tipoDoc, 'Warning', true, $componente, $codigo);
    }

    $capado = false;
    if ($puedeMarcarError) {
        [, $seguidos] = bitacora_alerta_registrar('DOC_FALLO', $mensaje, $documento, $tipoDoc);
        if ($seguidos >= $max) {
            $msgCap = "Tras $max fallos seguidos se sacó de la cola de envío. Use Reintentar en el panel cuando esté corregido. Último error: $mensaje";
            if (bitacora_marcar_error_envio($documento, $tipoDoc, $msgCap)) {
                $capado = true;
                bitacora_alerta_registrar('DOC_FALLOS_MAX', $msgCap, $documento, $tipoDoc);
                if (function_exists('Bitacora')) {
                    Bitacora($sessionID, $msgCap, $documento, $tipoDoc, 'Error', true, $componente, 'DOC_FALLOS_MAX');
                }
            }
        }
    }
    return [$veces, $capado];
}

