<?php
declare(strict_types=1);
require_once __DIR__ . '/caja-test.php';
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

/* ---------------- Tareas > Cierre de caja > Test ---------------- */
function api_caja_correo_test(): array
{
    session_write_close();
    $para = trim((string)($_POST['para'] ?? ''));
    $modo = (string)($_POST['modo'] ?? 'real') === 'muestra' ? 'muestra' : 'real';
    $fecha = trim((string)($_POST['fecha'] ?? ''));
    $r = lp_caja_test_enviar($para, $modo, $fecha, ph_user());
    $dest = lp_caja_test_validar($para) ? '(correo inválido)' : $para;
    $fe = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) ? " $fecha" : '';
    ph_accion('tareas: correo de PRUEBA del cierre de caja a ' . $dest . " ($modo$fe) -> " . ($r['ok'] ? 'enviado' : 'fallo: ' . mb_substr((string)$r['error'], 0, 200)), $r['ok'] ? 'Info' : 'Warning');
    return $r;
}
