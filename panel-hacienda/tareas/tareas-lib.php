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
    return [ph_fechas_local($timer), ph_fechas_local($svc), $log];
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
            'corriendo' => (($svc['ActiveState'] ?? '') === 'activating'),
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
    $palabras = ['enable' => 'ACTIVAR', 'disable' => 'DESACTIVAR', 'start' => 'EJECUTAR'];
    $verbos = ['enable' => 'HABILITADA', 'disable' => 'DESHABILITADA', 'start' => 'INICIADA'];
    if (!isset($defs[$id], $palabras[$acc])) return ['ok' => false, 'error' => 'Acción o tarea inválida'];
    $word = $palabras[$acc];
    if (($_POST['confirmar'] ?? '') !== $word) {
        ph_accion_log("tarea $id: $acc rechazado (sin palabra de confirmacion)");
        return ['ok' => false, 'error' => "Escriba $word para confirmar"];
    }
    $out = []; $rc = 0;
    exec('/usr/bin/sudo -n /usr/local/sbin/paticas-tareas-ctl ' . escapeshellarg($id) . ' ' . escapeshellarg($acc) . ' 2>&1', $out, $rc);
    $nombre = $defs[$id]['nombre'];
    ph_accion('tarea: ' . $nombre . ' ' . $verbos[$acc] . " (rc=$rc)", $rc === 0 ? 'Info' : 'Error');
    return ['ok' => $rc === 0, 'rc' => $rc, 'salida' => implode("\n", $out)];
}

function ph_log_normalizar(string $texto): array
{
    $texto = str_replace("\r", "\n", $texto);
    $bruto = preg_split("/\n/", $texto) ?: [];
    $limpio = [];
    $previo = false;
    $truncado = false;
    foreach ($bruto as $linea) {
        $linea = rtrim((string)$linea);
        if ($linea === '') continue;
        if (strlen($linea) > 500) {
            $linea = substr($linea, 0, 500) . '…';
            $truncado = true;
        }
        $avance = str_contains($linea, 'TRANSFERRING');
        if ($avance && $previo) array_pop($limpio);
        $limpio[] = $linea;
        $previo = $avance;
    }
    if (count($limpio) > 4000) {
        $limpio = array_slice($limpio, -4000);
        $truncado = true;
    }
    return [$limpio, $truncado];
}

function ph_log_archivo(string $path): array
{
    if (!is_file($path) || !is_readable($path)) return [[], false];
    $fh = fopen($path, 'rb');
    if ($fh === false) return [[], false];
    $tamano = filesize($path);
    $cortado = false;
    if (is_int($tamano) && $tamano > 1500000) {
        fseek($fh, -1500000, SEEK_END);
        fgets($fh);
        $cortado = true;
    }
    $texto = stream_get_contents($fh);
    fclose($fh);
    [$lineas, $truncado] = ph_log_normalizar(is_string($texto) ? $texto : '');
    return [$lineas, $cortado || $truncado];
}

function api_tarea_log(): array
{
    $id = (string)($_GET['id'] ?? '');
    $defs = [];
    foreach (ph_tareas_defs() as $d) $defs[$d['id']] = $d;
    if (!isset($defs[$id])) return ['ok' => false, 'error' => 'Tarea inválida'];
    $locales = [
        'caja_correo' => '/var/log/lospaticas/caja-correo.log',
        'mensajeria_update' => '/var/log/lospaticas/mensajeria-update.log',
        'prefacturas_update' => '/var/log/lospaticas/prefacturas-update.log',
    ];
    if (isset($locales[$id])) {
        [$lineas, $truncado] = ph_log_archivo($locales[$id]);
        return ['ok' => true, 'id' => $id, 'lineas' => $lineas, 'truncado' => $truncado];
    }
    $out = []; $rc = 0;
    exec('/usr/bin/sudo -n /usr/local/sbin/paticas-tareas-ctl ' . escapeshellarg($id) . ' log 2>&1', $out, $rc);
    if ($rc !== 0) return ['ok' => false, 'error' => 'No se pudo leer el registro', 'salida' => implode("\n", $out)];
    [$lineas, $truncado] = ph_log_normalizar(implode("\n", $out));
    return ['ok' => true, 'id' => $id, 'lineas' => $lineas, 'truncado' => $truncado];
}

/* ---------------- Respaldos de inventario: archivos locales (.100) y MEGA ---------------- */
const PH_RESPALDO_RE = '/^inventario_\d{8}_\d{4}\.bak$/';
const PH_CTL = '/usr/local/sbin/paticas-tareas-ctl';

/** Ejecuta paticas-tareas-ctl backup_inventario <accion> [nombre] por sudo; devuelve [rc, stdout, stderr]. */
function ph_backup_ctl(string $accion, string $nombre = ''): array
{
    $cmd = ['/usr/bin/sudo', '-n', PH_CTL, 'backup_inventario', $accion];
    if ($nombre !== '') $cmd[] = $nombre;
    $p = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (!is_resource($p)) return [127, '', 'no se pudo ejecutar'];
    $out = (string)stream_get_contents($pipes[1]);
    $err = (string)stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    return [proc_close($p), $out, trim($err)];
}

function ph_respaldo_origen(string $o): string
{
    return in_array($o, ['local', 'mega'], true) ? $o : '';
}

/** Lista archivos de un origen: ['ok', 'configurado', 'error', 'archivos' => [[nombre, bytes, fecha]]]. */
function ph_respaldo_listar(string $origen): array
{
    if ($origen === 'mega') {
        [$rc, $out] = ph_backup_ctl('mega-status');
        if (trim($out) !== 'OK') {
            $motivo = preg_replace('/^NO_CONFIGURADO\s*/', '', trim($out)) ?: 'sin respuesta de pat-sql-web01';
            return ['ok' => false, 'configurado' => false, 'error' => 'MEGA no configurado: ' . $motivo, 'archivos' => []];
        }
    }
    [$rc, $out, $err] = ph_backup_ctl($origen === 'mega' ? 'mega-files' : 'files');
    if ($rc !== 0) return ['ok' => false, 'configurado' => true, 'error' => 'No se pudo listar (' . ($err ?: "rc=$rc") . ')', 'archivos' => []];
    $arch = [];
    foreach (preg_split('/\n/', trim($out)) as $l) {
        $c = explode("\t", trim($l));
        if (count($c) !== 3 || !preg_match(PH_RESPALDO_RE, $c[0]) || !ctype_digit($c[1])) continue;
        $ts = (int)floor((float)$c[2]);
        $arch[] = ['nombre' => $c[0], 'bytes' => (int)$c[1], 'fecha' => $ts > 0 ? date('Y-m-d H:i:s', $ts) : ''];
    }
    usort($arch, fn($a, $b) => strcmp($b['nombre'], $a['nombre']));
    return ['ok' => true, 'configurado' => true, 'archivos' => $arch];
}

function api_respaldos(): array
{
    $o = ph_respaldo_origen((string)($_GET['origen'] ?? ''));
    if ($o === '') return ['ok' => false, 'error' => 'Origen inválido'];
    session_write_close();   // MEGA puede tardar; no bloquear otras peticiones
    return ['origen' => $o] + ph_respaldo_listar($o);
}

function api_respaldo_borrar(): array
{
    $o = ph_respaldo_origen((string)($_POST['origen'] ?? ''));
    $n = (string)($_POST['nombre'] ?? '');
    $sinc = ($_POST['sincronizar'] ?? '') === '1';
    if ($o === '' || !preg_match(PH_RESPALDO_RE, $n)) return ['ok' => false, 'error' => 'Archivo inválido'];
    if (($_POST['confirmar'] ?? '') !== 'ELIMINAR') {
        ph_accion_log("respaldo: borrado de $o/$n rechazado (sin palabra de confirmacion)");
        return ['ok' => false, 'error' => 'Escriba ELIMINAR para confirmar'];
    }
    $etq = ['local' => 'local (pat-sql-web01)', 'mega' => 'MEGA'];
    $res = [];
    $orden = [$o];
    if ($sinc) $orden[] = $o === 'local' ? 'mega' : 'local';
    foreach ($orden as $i => $org) {
        if ($i > 0) {
            // Solo si existe un archivo con el mismo nombre en la otra pestaña
            $l = ph_respaldo_listar($org);
            if (!$l['ok']) { $res[] = ['origen' => $org, 'ok' => false, 'mensaje' => $l['error']]; continue; }
            if (!in_array($n, array_column($l['archivos'], 'nombre'), true)) { $res[] = ['origen' => $org, 'ok' => true, 'mensaje' => 'no existía']; continue; }
        }
        [$rc, $out, $err] = ph_backup_ctl($org === 'mega' ? 'mega-delete' : 'delete', $n);
        $ok = $rc === 0;
        ph_accion("respaldo: ELIMINADO $n en " . $etq[$org] . ($sinc ? ' (borrado sincronizado)' : '') . ($ok ? '' : " — fallo rc=$rc " . mb_substr($err, 0, 200)), $ok ? 'Info' : 'Error');
        $res[] = ['origen' => $org, 'ok' => $ok, 'mensaje' => $ok ? 'eliminado' : ($err ?: "rc=$rc")];
        if (!$ok && $i === 0) break;
    }
    ph_espacio_invalidar();
    return ['ok' => $res[0]['ok'], 'resultados' => $res, 'error' => $res[0]['ok'] ? null : 'No se pudo eliminar: ' . $res[0]['mensaje']];
}

/** Descarga en streaming (descargar.php, pool FPM sin limite de tiempo). No carga el archivo en memoria. */
function ph_respaldo_descargar(): never
{
    $o = ph_respaldo_origen((string)($_GET['origen'] ?? ''));
    $n = (string)($_GET['nombre'] ?? '');
    if ($o === '' || !preg_match(PH_RESPALDO_RE, $n)) ph_json(['ok' => false, 'error' => 'Archivo inválido'], 400);
    $l = ph_respaldo_listar($o);
    $tam = null;
    foreach ($l['archivos'] as $a) if ($a['nombre'] === $n) $tam = $a['bytes'];
    if ($tam === null) ph_json(['ok' => false, 'error' => $l['ok'] ? 'El archivo no existe' : $l['error']], 404);
    ph_accion("respaldo: descarga iniciada $n desde " . ($o === 'mega' ? 'MEGA' : 'local') . " ($tam bytes)");
    session_write_close();   // no bloquear otras peticiones del panel durante la descarga
    @set_time_limit(0);
    ignore_user_abort(true);
    while (ob_get_level() > 0) ob_end_clean();
    $cmd = ['/usr/bin/sudo', '-n', PH_CTL, 'backup_inventario', $o === 'mega' ? 'mega-download' : 'download', $n];
    $p = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['file', '/dev/null', 'w']], $pipes);
    if (!is_resource($p)) ph_json(['ok' => false, 'error' => 'No se pudo iniciar la descarga'], 500);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $n . '"');
    header('Content-Length: ' . $tam);
    header('X-Accel-Buffering: no');
    $enviado = 0;
    while (!feof($pipes[1])) {
        $b = fread($pipes[1], 1 << 20);
        if ($b === false || $b === '') { if (feof($pipes[1])) break; continue; }
        echo $b; flush();
        $enviado += strlen($b);
        if (connection_aborted()) break;
    }
    $cortada = connection_aborted() || $enviado < $tam;
    if (!feof($pipes[1])) proc_terminate($p);
    fclose($pipes[1]);
    $rc = proc_close($p);
    ph_accion_log("respaldo: descarga $n " . ($cortada ? 'INTERRUMPIDA' : 'completa') . " bytes=$enviado de $tam rc=$rc");
    exit;
}

/* ---------------- Cierre de caja: destinatarios del correo ---------------- */
const PH_CAJA_CFG = PH_DATA . '/caja-destinatarios.json';
const PH_CAJA_DEFECTO = ['hhernandezv@lospaticas.com'];

function ph_caja_destinatarios(): array
{
    $j = is_readable(PH_CAJA_CFG) ? json_decode((string)file_get_contents(PH_CAJA_CFG), true) : null;
    $l = is_array($j) && isset($j['destinatarios']) && is_array($j['destinatarios']) ? array_values(array_filter($j['destinatarios'], 'is_string')) : [];
    return [$l ?: PH_CAJA_DEFECTO, is_array($j) ? $j : []];
}

function api_caja_config(): array
{
    [$l, $j] = ph_caja_destinatarios();
    return ['ok' => true, 'destinatarios' => $l, 'actualizado' => $j['actualizado'] ?? null, 'usuario' => $j['usuario'] ?? null,
        'predeterminado' => PH_CAJA_DEFECTO, 'tarea_habilitada' => trim((string)shell_exec('/usr/bin/systemctl is-enabled paticas-caja-correo.timer 2>/dev/null')) === 'enabled'];
}

function api_caja_config_guardar(): array
{
    $crudo = preg_split('/[\s,;]+/', (string)($_POST['destinatarios'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $lista = []; $malos = [];
    foreach ($crudo as $e) {
        $e = trim($e);
        if (strlen($e) > 254 || !filter_var($e, FILTER_VALIDATE_EMAIL)) { $malos[] = $e; continue; }
        $lista[strtolower($e)] = $e;
    }
    if ($malos) return ['ok' => false, 'error' => 'Correos inválidos: ' . implode(', ', array_slice($malos, 0, 5))];
    if (!$lista) return ['ok' => false, 'error' => 'Indique al menos un destinatario'];
    if (count($lista) > 20) return ['ok' => false, 'error' => 'Máximo 20 destinatarios'];
    [$antes] = ph_caja_destinatarios();
    $nueva = array_values($lista);
    $datos = ['destinatarios' => $nueva, 'actualizado' => date('Y-m-d H:i:s'), 'usuario' => ph_user()];
    if (!is_dir(PH_DATA)) @mkdir(PH_DATA, 0700, true);
    $tmp = PH_CAJA_CFG . '.tmp' . getmypid();
    if (file_put_contents($tmp, json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX) === false || !rename($tmp, PH_CAJA_CFG)) {
        @unlink($tmp);
        return ['ok' => false, 'error' => 'No se pudo guardar la configuración'];
    }
    @chmod(PH_CAJA_CFG, 0640);
    ph_accion('cierre de caja: destinatarios ' . implode(', ', $antes) . ' -> ' . implode(', ', $nueva));
    return ['ok' => true, 'destinatarios' => $nueva, 'actualizado' => $datos['actualizado'], 'usuario' => $datos['usuario']];
}

/* ---------------- Espacio en disco y respaldos (Resumen) ---------------- */
const PH_ESPACIO_CACHE = PH_DATA . '/espacio-cache.json';
const PH_ESPACIO_TTL_100 = 120;    // df/du de pat-sql-web01 por SSH
const PH_ESPACIO_TTL_MEGA = 600;   // MEGA es lento: 10 min

function ph_df_local(string $ruta): ?array
{
    $o = trim((string)shell_exec('/usr/bin/df -B1 --output=target,size,used,avail ' . escapeshellarg($ruta) . ' 2>/dev/null | tail -1'));
    $c = preg_split('/\s+/', $o);
    if (count($c) !== 4 || !ctype_digit($c[1])) return null;
    return ['montaje' => $c[0], 'total' => (int)$c[1], 'usado' => (int)$c[2], 'libre' => (int)$c[3]];
}

function ph_espacio_100(): array
{
    [$rc, $out, $err] = ph_backup_ctl('espacio');
    if ($rc !== 0) return ['ok' => false, 'error' => $err ?: "rc=$rc"];
    $r = ['ok' => true];
    foreach (preg_split('/\n/', trim($out)) as $l) {
        $c = explode("\t", trim($l));
        if ($c[0] === 'DISK' && count($c) === 5) $r['disco'] = ['montaje' => $c[1], 'total' => (int)$c[2], 'usado' => (int)$c[3], 'libre' => (int)$c[4]];
        if ($c[0] === 'BACKUP' && count($c) === 3) $r['respaldos'] = ['archivos' => (int)$c[1], 'bytes' => (int)$c[2]];
        if ($c[0] === 'CARPETA' && count($c) === 2) $r['carpeta_bytes'] = (int)$c[1];
    }
    return $r;
}

function ph_espacio_mega(): array
{
    $l = ph_respaldo_listar('mega');
    if (!$l['ok']) return ['ok' => false, 'configurado' => $l['configurado'], 'error' => $l['error']];
    $r = ['ok' => true, 'configurado' => true, 'archivos' => count($l['archivos']), 'bytes' => array_sum(array_column($l['archivos'], 'bytes'))];
    [$rc, $out] = ph_backup_ctl('mega-espacio');
    if (preg_match('/USED STORAGE:\s*(\d+)\s+[\d.]+%\s+of\s+(\d+)/', $out, $m)) {
        $r['usado'] = (int)$m[1]; $r['total'] = (int)$m[2]; $r['libre'] = max(0, (int)$m[2] - (int)$m[1]);
    }
    if (preg_match('/file versions:\s*(\d+)/', $out, $m)) $r['versiones'] = (int)$m[1];
    return $r;
}

function api_espacio(): array
{
    session_write_close();
    $cache = is_readable(PH_ESPACIO_CACHE) ? (json_decode((string)file_get_contents(PH_ESPACIO_CACHE), true) ?: []) : [];
    $ahora = time(); $cambio = false;
    foreach (['s100' => [PH_ESPACIO_TTL_100, 'ph_espacio_100'], 'mega' => [PH_ESPACIO_TTL_MEGA, 'ph_espacio_mega']] as $k => [$ttl, $fn]) {
        if (!isset($cache[$k]['t']) || $ahora - (int)$cache[$k]['t'] > $ttl) {
            $cache[$k] = ['t' => $ahora, 'd' => $fn()];
            $cambio = true;
        }
    }
    if ($cambio) {
        $tmp = PH_ESPACIO_CACHE . '.tmp' . getmypid();
        if (file_put_contents($tmp, json_encode($cache), LOCK_EX) !== false) rename($tmp, PH_ESPACIO_CACHE);
    }
    $s100 = $cache['s100']['d']; $mega = $cache['mega']['d'];
    $leido = fn(string $k) => date('Y-m-d H:i:s', (int)$cache[$k]['t']);
    $discos = [];
    $discos[] = ['servidor' => 'pat-sql-web01 (.100)', 'leido' => $leido('s100')] + ($s100['disco'] ?? ['error' => $s100['error'] ?? 'sin datos']);
    $l106 = ph_df_local('/');
    $discos[] = ['servidor' => 'pat-php-app01 (.106)', 'leido' => date('Y-m-d H:i:s')] + ($l106 ?? ['error' => 'sin datos']);
    return ['ok' => true, 'discos' => $discos, 'respaldos' => [
        'local' => ['leido' => $leido('s100'), 'carpeta_bytes' => $s100['carpeta_bytes'] ?? null] + ($s100['respaldos'] ?? ['error' => $s100['error'] ?? 'sin datos']),
        'mega' => ['leido' => $leido('mega')] + $mega,
    ]];
}

/** Tras borrar archivos, invalida la cache del Resumen. */
function ph_espacio_invalidar(): void { @unlink(PH_ESPACIO_CACHE); }
