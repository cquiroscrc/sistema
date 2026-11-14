
/* ---------------- Configuracion > Correo (SMTP) ---------------- */
function api_config_correo(): array
{
    return ['ok' => true] + lp_smtp_publico();
}

function api_config_correo_guardar(): array
{
    $r = lp_smtp_guardar($_POST, ph_user());
    if ($r['ok']) {
        $p = lp_smtp_publico();
        ph_accion('configuracion: correo SMTP guardado (' . $p['host'] . ':' . $p['puerto'] . ' ' . $p['cifrado'] . ', usuario ' . ($p['usuario'] ?: '-') . ', remitente ' . $p['remitente'] . '; cambios: ' . (implode(', ', $r['cambios']) ?: 'ninguno') . ')');
        return ['ok' => true] + $p;
    }
    ph_accion_log('configuracion: correo SMTP no guardado: ' . $r['error']);
    return $r;
}

function api_config_correo_probar(): array
{
    session_write_close();
    $r = lp_smtp_probar($_POST);
    ph_accion('configuracion: prueba de conexion SMTP ' . trim((string)($_POST['host'] ?? '')) . ':' . (int)($_POST['puerto'] ?? 0) . ' -> ' . ($r['ok'] ? 'OK' : 'fallo: ' . mb_substr((string)$r['error'], 0, 200)), $r['ok'] ? 'Info' : 'Warning');
    return $r;
}
