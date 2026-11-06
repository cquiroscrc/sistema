<?php
declare(strict_types=1);
/** Tareas programadas del corte (caja/mensajeria/prefacturas en .106 y respaldo en .100). */

function ph_tareas_defs(): array
{
    return [
        ['id' => 'caja_correo', 'nombre' => 'Cierre de caja (correo)', 'descripcion' => 'utilitarios/caja_correo.php · lee hfacturas/cxc/egresos/bancos y redirige a cierres.php para enviar el correo', 'hora' => '17:15', 'servidor' => 'pat-php-app01 (.106)'],
        ['id' => 'mensajeria_update', 'nombre' => 'Actualizar mensajería', 'descripcion' => 'utilitarios/mensajeria_update.php · restablece existencias de códigos 9SM, 9ENCOMIENDA, 9IMPRESIONZL, 9SERVDIGITALZL y 9TM', 'hora' => '17:28', 'servidor' => 'pat-php-app01 (.106)'],
        ['id' => 'prefacturas_update', 'nombre' => 'Limpiar prefacturas', 'descripcion' => 'utilitarios/prefacturas_update.php · DELETE de hprefacturas, hpregeneral y tmp_devol', 'hora' => '17:48', 'servidor' => 'pat-php-app01 (.106)'],
        ['id' => 'backup_inventario', 'nombre' => 'Respaldo BD inventario', 'descripcion' => 'BACKUP DATABASE inventario WITH COMPRESSION, CHECKSUM + RESTORE VERIFYONLY (retención 14 días)', 'hora' => '18:00', 'servidor' => 'pat-sql-web01 (.100)'],
    ];
}

function ph_tareas_parse(string $text): array
{
    $timer = []; $svc = []; $log = []; $sec = 'timer';
    foreach (preg_split('/\r\n|\n/', $text) as $l) {
        if ($l === '---SERVICE---') { $sec = 'svc'; continue; }
        if ($l === '---LOG---') { $sec = 'log'; continue; }
        if ($sec === 'log') { if ($l !== '') $log[] = $l; continue; }
        if ($l !== '' && str_contains($l, '=')) {
            [$k, $v] = explode('=', $l, 2);
            if ($sec === 'timer') $timer[$k] = $v; else $svc[$k] = $v;
        }
    }
    return [$timer, $svc, $log];
}

function api_tareas(): array
{
    $out = [];
    foreach (ph_tareas_defs() as $d) {
        $lines = []; $rc = 0;
        exec('/usr/bin/sudo -n /usr/local/sbin/paticas-tareas-ctl ' . escapeshellarg($d['id']) . ' status 2>&1', $lines, $rc);
        [$timer, $svc, $log] = ph_tareas_parse(implode("\n", $lines));
        $enabled = (($timer['UnitFileState'] ?? '') === 'enabled');
        $item = $d + [
            'enabled' => $enabled,
            'timer' => $timer,
            'servicio' => $svc,
            'log' => array_values(array_slice($log, -40)),
        ];
        if ($rc !== 0 && !$timer) $item['log_error'] = 'No se pudo leer el estado (rc=' . $rc . ')';
        $out[] = $item;
    }
    return ['ok' => true, 'tareas' => $out];
}

function api_tarea(): array
{
    $id = (string)($_POST['id'] ?? '');
    $acc = (string)($_POST['accion'] ?? '');
    $defs = [];
    foreach (ph_tareas_defs() as $d) $defs[$d['id']] = $d;
    if (!isset($defs[$id]) || !in_array($acc, ['enable', 'disable'], true)) return ['ok' => false, 'error' => 'Acción o tarea inválida'];
    $word = $acc === 'enable' ? 'ACTIVAR' : 'DESACTIVAR';
    if (($_POST['confirmar'] ?? '') !== $word) {
        ph_accion_log("tarea $id: $acc rechazado (sin palabra de confirmacion)");
        return ['ok' => false, 'error' => "Escriba $word para confirmar"];
    }
    $out = []; $rc = 0;
    exec('/usr/bin/sudo -n /usr/local/sbin/paticas-tareas-ctl ' . escapeshellarg($id) . ' ' . escapeshellarg($acc) . ' 2>&1', $out, $rc);
    $nombre = $defs[$id]['nombre'];
    ph_accion('tarea: ' . $nombre . ' ' . ($acc === 'enable' ? 'HABILITADA' : 'DESHABILITADA') . " (rc=$rc)", $rc === 0 ? 'Info' : 'Error');
    return ['ok' => $rc === 0, 'rc' => $rc, 'salida' => implode("\n", $out)];
}
