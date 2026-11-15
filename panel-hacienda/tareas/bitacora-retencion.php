<?php
declare(strict_types=1);
/**
 * Limpieza por lotes de dbo.BITACORA (nunca BITACORA_HIST).
 * Sin --ejecutar solo informa cuántas filas entrarían.
 */
if (PHP_SAPI !== 'cli') { fwrite(STDERR, "solo CLI\n"); exit(1); }
require_once __DIR__ . '/../lib.php';
require_once __DIR__ . '/bitacora-retencion-lib.php';

$ejecutar = in_array('--ejecutar', $argv, true);
$c = ph_bitacora_retencion_leer();
$d = (int)$c['debug_dias'];
$o = (int)$c['otros_dias'];
$db = ph_db();
$st = $db->query("SELECT
    SUM(CASE WHEN TIPO = 'Debug' AND FECHA_HORA < DATEADD(day, -$d, GETDATE()) THEN 1 ELSE 0 END) AS debug_viejos,
    SUM(CASE WHEN ISNULL(TIPO,'') <> 'Debug' AND FECHA_HORA < DATEADD(day, -$o, GETDATE()) THEN 1 ELSE 0 END) AS otros_viejos
    FROM dbo.BITACORA");
$r = $st->fetch(PDO::FETCH_ASSOC) ?: [];
$nd = (int)($r['debug_viejos'] ?? 0);
$no = (int)($r['otros_viejos'] ?? 0);
echo date('Y-m-d H:i:s') . " retención DEBUG>$d días: $nd, resto>$o días: $no, total=" . ($nd+$no) . ($ejecutar ? " (ejecutar)\n" : " (solo informe, no se borra)\n");
if (!$ejecutar) exit(0);

$lote = 5000;
$borradas = 0;
# DEBUG first
while (true) {
    $n = $db->exec("DELETE TOP ($lote) FROM dbo.BITACORA WHERE TIPO = 'Debug' AND FECHA_HORA < DATEADD(day, -$d, GETDATE())");
    if ($n === false) { fwrite(STDERR, "error DELETE debug\n"); exit(2); }
    $n = (int)$n;
    $borradas += $n;
    echo date('Y-m-d H:i:s') . " debug lote=$n acumulado=$borradas\n";
    if ($n < $lote) break;
    usleep(200000);
}
while (true) {
    $n = $db->exec("DELETE TOP ($lote) FROM dbo.BITACORA WHERE ISNULL(TIPO,'') <> 'Debug' AND FECHA_HORA < DATEADD(day, -$o, GETDATE())");
    if ($n === false) { fwrite(STDERR, "error DELETE otros\n"); exit(2); }
    $n = (int)$n;
    $borradas += $n;
    echo date('Y-m-d H:i:s') . " otros lote=$n acumulado=$borradas\n";
    if ($n < $lote) break;
    usleep(200000);
}
echo date('Y-m-d H:i:s') . " fin, borradas=$borradas\n";
