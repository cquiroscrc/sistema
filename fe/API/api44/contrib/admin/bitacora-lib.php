<?php
declare(strict_types=1);

/**
 * Bitácora del sender api44: niveles, RUN_ID, componente, código,
 * resumen por corrida, heartbeat y avisos deduplicados.
 */
const BITACORA_HEARTBEAT_SEG = 2700;
const BITACORA_ALERTA_SEG = 21600;
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
            }
            if ($codigo === 'DOC_ACEPTADO') {
                bitacora_run_contar('aceptados');
                bitacora_alerta_desactivar('DOC_FECHA_FUTURA', $Documento, $TipoDoc);
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
    $texto = sprintf(
        'Corrida %s · %d pendientes · %d enviados · %d omitidos%s · %d errores · %s',
        $hhmm,
        (int)$BITACORA_RUN['pendientes'],
        (int)$BITACORA_RUN['enviados'],
        (int)$BITACORA_RUN['omitidos'],
        $motTxt,
        (int)$BITACORA_RUN['errores'],
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
