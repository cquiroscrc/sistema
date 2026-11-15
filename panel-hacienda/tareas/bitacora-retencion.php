<?php
declare(strict_types=1);
/**
 * Retención de dbo.BITACORA: mueve (copia y luego borra, verificado) las filas
 * más antiguas que el plazo configurado hacia dbo.BITACORA_HIST.
 * Nunca elimina filas sin haberlas copiado. Nunca depura ni borra BITACORA_HIST.
 * Nunca toca tablas de documentos.
 * Sin --ejecutar solo informa cuántas filas se moverían.
 */
if (PHP_SAPI !== 'cli') { fwrite(STDERR, "solo CLI\n"); exit(1); }
require_once __DIR__ . '/../lib.php';
require_once __DIR__ . '/bitacora-retencion-lib.php';

$ejecutar = in_array('--ejecutar', $argv, true);
$c = ph_bitacora_retencion_leer();
$d = (int)$c['debug_dias'];
$o = (int)$c['otros_dias'];
$db = ph_db();
$cols = 'SESSION_ID, TIPO, TEXTO, FECHA_HORA, DOCUMENTO, TIPO_DOCUMENTO, RUN_ID, COMPONENTE, CODIGO';

$st = $db->query("SELECT
    SUM(CASE WHEN TIPO = 'Debug' AND FECHA_HORA < DATEADD(day, -$d, GETDATE()) THEN 1 ELSE 0 END) AS debug_viejos,
    SUM(CASE WHEN ISNULL(TIPO,'') <> 'Debug' AND FECHA_HORA < DATEADD(day, -$o, GETDATE()) THEN 1 ELSE 0 END) AS otros_viejos
    FROM dbo.BITACORA");
$r = $st->fetch(PDO::FETCH_ASSOC) ?: [];
$nd = (int)($r['debug_viejos'] ?? 0);
$no = (int)($r['otros_viejos'] ?? 0);
echo date('Y-m-d H:i:s') . " retención (mover a HIST) DEBUG>$d días: $nd, resto>$o días: $no, total=" . ($nd+$no) . ($ejecutar ? " (ejecutar)\n" : " (solo informe, no se mueve nada)\n");
if (!$ejecutar) exit(0);

$lote = 5000;

function ph_bitacora_mover_lote(PDO $db, string $cols, int $lote, string $where, string $etiqueta): int
{
    $movidas = 0;
    while (true) {
        $db->beginTransaction();
        try {
            $db->exec('IF OBJECT_ID(\'tempdb..#lote_bitacora\') IS NOT NULL DROP TABLE #lote_bitacora');
            $db->exec(
                "SELECT TOP ($lote) %%physloc%% AS rid, $cols
                 INTO #lote_bitacora
                 FROM dbo.BITACORA WITH (ROWLOCK, UPDLOCK, READPAST)
                 WHERE $where
                 ORDER BY FECHA_HORA, SESSION_ID, DOCUMENTO"
            );
            $nLote = (int)$db->query('SELECT COUNT(*) FROM #lote_bitacora')->fetchColumn();
            if ($nLote === 0) {
                $db->commit();
                break;
            }
            $nIns = (int)$db->exec("INSERT INTO dbo.BITACORA_HIST ($cols) SELECT $cols FROM #lote_bitacora");
            if ($nIns !== $nLote) {
                $db->rollBack();
                fwrite(STDERR, date('Y-m-d H:i:s') . " $etiqueta: copiadas=$nIns distinto de lote=$nLote, se revierte\n");
                exit(2);
            }
            $nDel = (int)$db->exec(
                'DELETE b FROM dbo.BITACORA b INNER JOIN #lote_bitacora l ON b.%%physloc%% = l.rid'
            );
            if ($nDel !== $nLote) {
                $db->rollBack();
                fwrite(STDERR, date('Y-m-d H:i:s') . " $etiqueta: borradas=$nDel distinto de lote=$nLote, se revierte (HIST no queda a medias: se deshace la copia)\n");
                exit(2);
            }
            $db->commit();
            $movidas += $nLote;
            echo date('Y-m-d H:i:s') . " $etiqueta lote=$nLote acumulado=$movidas\n";
            if ($nLote < $lote) break;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            fwrite(STDERR, date('Y-m-d H:i:s') . " $etiqueta error: " . $e->getMessage() . "\n");
            exit(2);
        }
        usleep(200000);
    }
    return $movidas;
}

$whereDebug = "TIPO = 'Debug' AND FECHA_HORA < DATEADD(day, -$d, GETDATE())";
$whereOtros = "ISNULL(TIPO,'') <> 'Debug' AND FECHA_HORA < DATEADD(day, -$o, GETDATE())";
$movidas = 0;
$movidas += ph_bitacora_mover_lote($db, $cols, $lote, $whereDebug, 'debug');
$movidas += ph_bitacora_mover_lote($db, $cols, $lote, $whereOtros, 'otros');
echo date('Y-m-d H:i:s') . " fin, movidas a BITACORA_HIST=$movidas\n";
