<?php
declare(strict_types=1);
/*
 * Reintento de envío de comprobantes (Comprobantes > Registro de envíos y detalle).
 * La "cola de envío" no es una tabla: es la consulta ProcesarPendientesEnvio del servicio (fe/API/api44/contrib/admin/admin.php):
 *   V_DOCUMENTO_ELECTRONICO v LEFT JOIN DOC_ELECTRONICO_PROCESADO p ON v.DOCUMENTO = p.NUMERO_DOC
 *   WHERE v.ACEPTA_DOC_ELECTRONICO='S' AND tipo FE/NC/ND/TE AND v.SAT no nulo AND v.FECHA >= FECHA_NORMATIVA
 *     AND ((p.ENVIADO IS NULL AND p.ACEPTADO IS NULL) OR p.ENVIADO='N') AND (p.ESTADO IS NULL OR p.ESTADO NOT IN ('rechazado','error'))
 * Un documento en 'error' queda fuera; reintentar = ESTADO 'reintentar' (+ ENVIADO 'N') y el próximo lote lo vuelve a enviar.
 * Los enviados sin respuesta (FECHATIMBRADO >= 2025-01-01) los consulta ProcesarPendientesRespuesta en cada lote: no se reenvían.
 */

const PH_SQL_REINTENTO = "UPDATE dbo.DOC_ELECTRONICO_PROCESADO SET ESTADO = 'reintentar', ENVIADO = 'N', ULTIMO_ERROR = LEFT(? + ISNULL(ULTIMO_ERROR, ''), 4000)
        WHERE TIPO_DOC = ? AND NUMERO_DOC = ? AND RTRIM(ESTADO) = 'error' AND ISNULL(ENVIADO, 'N') <> 'S' AND ISNULL(ACEPTADO, 'N') <> 'S'";
const PH_RESPUESTA_DESDE = '2025-01-01'; // ProcesarPendientesRespuesta solo consulta desde esta fecha de timbrado

function ph_servicio_activo(): bool
{
    static $a = null;
    return $a ??= (ph_systemctl_show(PH_UNIT . '.timer', ['ActiveState'])['ActiveState'] ?? '') === 'active';
}

/** FECHA_NORMATIVA de COMPAÑIAS tal como la interpreta SQL Server (AAAA-MM-DD). */
function ph_fecha_normativa(): string
{
    static $f = null;
    return $f ??= (string)ph_db()->query("SELECT CONVERT(varchar(10), CAST((SELECT TOP 1 FECHA_NORMATIVA FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI') AS datetime), 23)")->fetchColumn();
}

/** El documento cumple las condiciones base de la cola del servicio (vista, clave, receptor con FE, fecha normativa). */
function ph_cola_elegible(string $num): bool
{
    $st = ph_db()->prepare("SELECT COUNT(*) FROM V_DOCUMENTO_ELECTRONICO v WHERE v.DOCUMENTO = ? AND v.ACEPTA_DOC_ELECTRONICO = 'S'
        AND v.TIPO_DOCUMENTO IN ('FE','NC','ND','TE') AND NOT v.SAT IS NULL AND v.DOCUMENTO <> 'F-00000000027825'
        AND v.NIT NOT IN (SELECT RTRIM(NIT) FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI')
        AND v.FECHA >= (SELECT TOP 1 FECHA_NORMATIVA FROM dbo.[COMPAÑIAS] WHERE CONFIGURADA = 'SI')");
    $st->execute([$num]);
    return (int)$st->fetchColumn() > 0;
}

function ph_ymd($v): string
{
    $v = trim((string)$v);
    return preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ? substr($v, 0, 10) : '';
}

/** Regla sin consultas extra. $r: ESTADO, ENVIADO, ACEPTADO, FECHA_HORA, FECHATIMBRADO, TIENE_RESP. [puede, motivo] */
function ph_reintento_regla(array $r): array
{
    $e = strtolower(trim((string)($r['ESTADO'] ?? '')));
    $env = trim((string)($r['ENVIADO'] ?? '')) === 'S';
    $acep = trim((string)($r['ACEPTADO'] ?? '')) === 'S';
    $norm = ph_fecha_normativa();
    $f = ph_ymd($r['FECHA_HORA'] ?? '');
    $ft = ph_ymd($r['FECHATIMBRADO'] ?? '') ?: $f;
    if (str_starts_with($e, 'aceptado')) return [false, 'Ya fue aceptado por Hacienda'];
    if ($e === 'reintentar') return [false, 'Ya está en la cola de envío (marcado para reintento)'];
    if ($norm !== '' && $f !== '' && $f < $norm) return [false, "Documento anterior a la fecha normativa ($norm): el servicio de envío no procesa documentos de esa época"];
    if ($e === 'rechazado') return [false, 'Rechazado por Hacienda: esa clave no se puede reenviar; corríjalo con una nota de crédito o un documento nuevo'];
    if ($env) {
        if (!empty($r['TIENE_RESP'])) return [false, 'Ya fue enviado y Hacienda respondió'];
        if ($ft !== '' && $ft < PH_RESPUESTA_DESDE) return [false, 'Enviado sin respuesta antes de ' . PH_RESPUESTA_DESDE . ': el servicio ya no consulta su estado y no se puede reenviar con la misma clave'];
        return [false, 'Ya fue enviado: el servicio consulta la respuesta de Hacienda en cada lote'];
    }
    if ($acep) return [false, 'Marcado como aceptado'];
    if ($e !== 'error') return [false, 'Ya está en la cola de envío' . ($e !== '' ? " (estado $e)" : '')];
    return [true, 'Error local sin enviar (incluye el tope de fallos seguidos): se puede devolver a la cola de envío'];
}

/** Regla completa: además verifica la vista de la cola y que el servicio esté activo. */
function ph_reintento(array $r, string $num): array
{
    [$ok, $m] = ph_reintento_regla($r);
    if (!$ok) return [false, $m];
    if (!ph_cola_elegible($num)) return [false, 'No cumple las condiciones de la cola del servicio (sin clave, receptor sin factura electrónica o tipo no enviable)'];
    if (!ph_servicio_activo()) return [false, 'El servicio de envío está detenido: inícielo para poder reintentar'];
    return [true, $m];
}

/** POST tipo, numero [, validar=1]. Idempotente: UPDATE condicionado a ESTADO 'error'; no inserta filas. */

/** Al devolver un documento a la cola, reinicia el contador de fallos seguidos y oculta el aviso de tope. */
function ph_alerta_reset_fallos(string $tipo, string $num): void
{
    try {
        ph_db()->prepare("UPDATE dbo.BITACORA_ALERTA SET ACTIVA=0, VECES=0, ULTIMA_VEZ=SYSDATETIME()
            WHERE RTRIM(ISNULL(DOCUMENTO,'')) = ? AND RTRIM(ISNULL(TIPO_DOCUMENTO,'')) = ?
              AND CODIGO IN ('DOC_FALLO','DOC_FALLOS_MAX','DOC_PROCESO_DETENIDO','DOC_EXCEPCION','MAIL_SIN_CLAVE','MAIL_SIN_DIR_PDF','MAIL_SIN_DIR_XML')")
            ->execute([$num, $tipo]);
    } catch (Throwable $e) {
        // no impedir el reintento si falla el aviso
    }
}

function api_reintentar(): array
{
    $tipo = (string)($_POST['tipo'] ?? ''); $num = trim((string)($_POST['numero'] ?? ''));
    if (!in_array($tipo, ['FE', 'NC', 'ND', 'TE', 'FEC', 'FEE'], true) || !preg_match('/^[A-Za-z0-9\-]{1,30}$/', $num)) return ['ok' => false, 'error' => 'Documento inválido'];
    $st = ph_db()->prepare("SELECT RTRIM(ESTADO) ESTADO, ENVIADO, ACEPTADO, CONVERT(varchar(19), FECHA_HORA, 120) FECHA_HORA, RTRIM(FECHATIMBRADO) FECHATIMBRADO,
        CASE WHEN XML_RESPUESTA IS NULL OR XML_RESPUESTA = '' THEN 0 ELSE 1 END TIENE_RESP FROM dbo.DOC_ELECTRONICO_PROCESADO WHERE TIPO_DOC = ? AND NUMERO_DOC = ?");
    $st->execute([$tipo, $num]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if (!$r) return ['ok' => false, 'error' => "No existe $tipo $num en DOC_ELECTRONICO_PROCESADO"];
    [$puede, $motivo] = ph_reintento($r, $num);
    if (($_POST['validar'] ?? '') === '1')
        return ['ok' => true, 'validacion' => true, 'puede' => $puede, 'motivo' => $motivo, 'estado' => $r['ESTADO'], 'enviado' => $r['ENVIADO'], 'aceptado' => $r['ACEPTADO']];
    if (!$puede) {
        if (strtolower(trim((string)$r['ESTADO'])) === 'reintentar') return ['ok' => true, 'mensaje' => "$tipo $num ya estaba en la cola de envío"];
        return ['ok' => false, 'error' => $motivo];
    }
    $u = ph_db()->prepare(PH_SQL_REINTENTO);
    $u->execute(['[Reintento solicitado en panel por ' . ph_user() . ' ' . date('Y-m-d H:i:s') . '] ', $tipo, $num]);
    $n = $u->rowCount();
    if ($n === 1) {
        ph_alerta_reset_fallos($tipo, $num);
        ph_accion("reintento solicitado $tipo $num: devuelto a la cola de envío (ESTADO error -> reintentar)", 'Info', $num, $tipo);
        return ['ok' => true, 'mensaje' => "$tipo $num devuelto a la cola de envío; lo enviará el próximo lote"];
    }
    $st->execute([$tipo, $num]);
    $r2 = $st->fetch(PDO::FETCH_ASSOC) ?: [];
    if (strtolower(trim((string)($r2['ESTADO'] ?? ''))) === 'reintentar') return ['ok' => true, 'mensaje' => "$tipo $num ya estaba en la cola de envío"];
    return ['ok' => false, 'error' => 'No se actualizó: el estado cambió (' . ($r2['ESTADO'] ?? '?') . ')'];
}
