<?php
declare(strict_types=1);
/*
 * Configuracion > Respaldos: dias a mantener en local (pat-sql-web01:/var/opt/mssql/backup) y en MEGA.
 * Se guarda en .100 (/etc/lospaticas/backup-retencion.conf) por paticas-backup-ctl retencion-set (enteros 1-365).
 * Guardar no borra nada: la limpieza la hacen paticas-backup-inventario / paticas-backup-mega en la proxima corrida,
 * y solo sobre inventario_YYYYMMDD_HHMM.bak.
 */
function ph_retencion_leer(): array
{
    [$rc, $out, $err] = ph_backup_ctl('retencion');
    if ($rc !== 0) return ['ok' => false, 'error' => 'No se pudo leer la retención en pat-sql-web01 (' . ($err ?: "rc=$rc") . ')'];
    $v = [];
    foreach (preg_split('/\n/', trim($out)) as $l) {
        $c = explode("\t", trim($l));
        if (count($c) === 2 && ctype_digit($c[1])) $v[$c[0]] = (int)$c[1];
    }
    if (!isset($v['LOCAL'], $v['MEGA'])) return ['ok' => false, 'error' => 'Respuesta inválida de pat-sql-web01'];
    return ['ok' => true, 'local' => $v['LOCAL'], 'mega' => $v['MEGA'], 'actualizado' => isset($v['ACTUALIZADO']) ? date('Y-m-d H:i', $v['ACTUALIZADO']) : null];
}

function ph_retencion_dias(string $x): ?int
{
    $x = trim($x);
    if (!preg_match('/^\d{1,3}$/', $x)) return null;
    $n = (int)$x;
    return $n >= 1 && $n <= 365 ? $n : null;
}

function api_config_respaldos(): array
{
    return ph_retencion_leer();
}

function api_config_respaldos_guardar(): array
{
    $l = ph_retencion_dias((string)($_POST['local'] ?? ''));
    $m = ph_retencion_dias((string)($_POST['mega'] ?? ''));
    if ($l === null || $m === null) return ['ok' => false, 'error' => 'Los días deben ser números enteros entre 1 y 365'];
    $antes = ph_retencion_leer();
    [$rc, , $err] = ph_backup_ctl('retencion-set', "$l:$m");
    if ($rc !== 0) {
        ph_accion_log('configuracion: retención de respaldos no guardada: ' . ($err ?: "rc=$rc"));
        return ['ok' => false, 'error' => 'No se pudo guardar (' . ($err ?: "rc=$rc") . ')'];
    }
    $t = fn(string $k, int $n) => !empty($antes['ok']) && $antes[$k] !== $n ? "{$antes[$k]} → $n" : (string)$n;
    ph_accion("configuracion: retención de respaldos local {$t('local', $l)} días, MEGA {$t('mega', $m)} días (aplica en la próxima corrida)");
    return ph_retencion_leer() + ['mensaje' => 'Guardado. Se aplica en la próxima corrida del respaldo; no se borró nada ahora.'];
}
