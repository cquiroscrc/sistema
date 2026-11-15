<?php
declare(strict_types=1);
/* Retención de BITACORA (no toca BITACORA_HIST). Guardar no borra nada. */
const PH_BITACORA_RETENCION = '/var/lib/lospaticas/panel/bitacora-retencion.conf';

function ph_bitacora_retencion_leer(): array
{
    $d = 7; $o = 90;
    if (is_readable(PH_BITACORA_RETENCION)) {
        foreach (file(PH_BITACORA_RETENCION, FILE_IGNORE_NEW_LINES) ?: [] as $l) {
            if (preg_match('/^DEBUG_DIAS=(\d{1,4})\s*$/', $l, $m)) $d = (int)$m[1];
            if (preg_match('/^OTROS_DIAS=(\d{1,4})\s*$/', $l, $m)) $o = (int)$m[1];
        }
    }
    return ['ok' => true, 'debug_dias' => $d, 'otros_dias' => $o, 'archivo' => PH_BITACORA_RETENCION];
}

function ph_bitacora_retencion_validar(string $x, int $min, int $max): ?int
{
    if (!preg_match('/^\d{1,4}$/', trim($x))) return null;
    $n = (int)$x;
    return $n >= $min && $n <= $max ? $n : null;
}

function api_config_bitacora(): array
{
    $c = ph_bitacora_retencion_leer();
    try {
        $d = (int)$c['debug_dias']; $o = (int)$c['otros_dias'];
        $st = ph_db()->query("SELECT
            SUM(CASE WHEN TIPO = 'Debug' AND FECHA_HORA < DATEADD(day, -$d, GETDATE()) THEN 1 ELSE 0 END) AS debug_viejos,
            SUM(CASE WHEN ISNULL(TIPO,'') <> 'Debug' AND FECHA_HORA < DATEADD(day, -$o, GETDATE()) THEN 1 ELSE 0 END) AS otros_viejos,
            COUNT(*) AS total
            FROM dbo.BITACORA");
        $r = $st->fetch(PDO::FETCH_ASSOC) ?: [];
        $c['debug_viejos'] = (int)($r['debug_viejos'] ?? 0);
        $c['otros_viejos'] = (int)($r['otros_viejos'] ?? 0);
        $c['total'] = (int)($r['total'] ?? 0);
        $c['mensaje'] = 'Con estos valores se borrarían ' . number_format($c['debug_viejos'] + $c['otros_viejos']) . ' filas de BITACORA (DEBUG con más de ' . $d . ' días + el resto con más de ' . $o . ' días). El histórico no se toca. El temporizador está deshabilitado; no se borra nada hasta que lo actives.';
    } catch (Throwable $e) {
        $c['error'] = $e->getMessage();
    }
    return $c;
}

function api_config_bitacora_guardar(): array
{
    $d = ph_bitacora_retencion_validar((string)($_POST['debug_dias'] ?? ''), 1, 3650);
    $o = ph_bitacora_retencion_validar((string)($_POST['otros_dias'] ?? ''), 1, 3650);
    if ($d === null || $o === null) return ['ok' => false, 'error' => 'Los días deben ser números enteros (DEBUG 1-3650, resto 1-3650)'];
    $txt = "DEBUG_DIAS=$d\nOTROS_DIAS=$o\nACTUALIZADO=" . time() . "\n";
    if (@file_put_contents(PH_BITACORA_RETENCION, $txt, LOCK_EX) === false) {
        return ['ok' => false, 'error' => 'No se pudo guardar la configuración'];
    }
    @chmod(PH_BITACORA_RETENCION, 0640);
    ph_accion("configuracion: retención de bitácora DEBUG $d días, resto $o días (no se borró nada; el temporizador sigue deshabilitado)");
    return api_config_bitacora() + ['ok' => true];
}
