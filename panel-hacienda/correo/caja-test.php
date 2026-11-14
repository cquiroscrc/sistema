<?php
declare(strict_types=1);
/*
 * Correo de PRUEBA del cierre de caja (botón "Test" en Tareas programadas).
 * Usa el formato nuevo de utilitarios/caja_correo_nuevo.php (lp_cierre_calcular / lp_cierre_muestra / lp_cierre_correo).
 * Solo lectura: lp_cierre_calcular hace SELECT; no se ejecuta la tarea, no hay UPDATE/DELETE, no se usa la redirección
 * de caja_correo.php ni mail(). Envía por el SMTP de Configuración → Correo (lp_enviar_correo).
 */
const LP_CAJA_NUEVO = '/var/www/lospaticas/sistema/utilitarios/caja_correo_nuevo.php';
const LP_CAJA_DB = '/var/www/lospaticas/sistema/utilitarios/db.php';
const LP_CAJA_TEST_RL = '/var/lib/lospaticas/panel/caja-test.json';
const LP_CAJA_TEST_ESPERA = 30;      // segundos entre pruebas
const LP_CAJA_TEST_POR_HORA = 10;

function lp_caja_test_formato(): void
{
    if (function_exists('lp_cierre_correo')) return;
    if (!is_file(LP_CAJA_NUEVO)) throw new RuntimeException('No se encontró el formato nuevo del cierre (utilitarios/caja_correo_nuevo.php)');
    $tz = date_default_timezone_get();
    // Solo define funciones y constantes: lp_cierre_responder() corre únicamente cuando es el script principal.
    require_once LP_CAJA_NUEVO;
    date_default_timezone_set($tz);
    foreach (['lp_cierre_calcular', 'lp_cierre_muestra', 'lp_cierre_correo'] as $f)
        if (!function_exists($f)) throw new RuntimeException("El formato nuevo no define $f()");
}

/** [cierre, nota]. $modo 'real' (lectura de la BD, fecha o hoy) o 'muestra'. */
function lp_caja_test_datos(string $modo, string $fecha): array
{
    lp_caja_test_formato();
    $tz = date_default_timezone_get();
    date_default_timezone_set('America/Costa_Rica');
    try {
        if ($modo === 'muestra') return [lp_cierre_muestra(), 'Datos de muestra (cifras del 5 de noviembre de 2026), no son de hoy'];
        $hoy = date('Y-m-d');
        if ($fecha === '') $fecha = $hoy;
        $p = explode('-', $fecha);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !checkdate((int)$p[1], (int)$p[2], (int)$p[0]) || $fecha > $hoy)
            throw new RuntimeException('Fecha inválida (AAAA-MM-DD, no futura)');
        $loc = setlocale(LC_CTYPE, '0');
        ob_start();
        try { require LP_CAJA_DB; } finally { ob_end_clean(); }
        try {
            if (empty($id)) throw new RuntimeException('No se pudo abrir la base del cierre');
            $c = lp_cierre_calcular($id, $fecha);
        } finally {
            if (!empty($id)) @odbc_close($id);
            if (is_string($loc)) setlocale(LC_CTYPE, $loc);
        }
        return [$c, "Datos reales del $fecha, leídos sin modificar nada"];
    } finally {
        date_default_timezone_set($tz);
    }
}

function lp_caja_test_html(array $c, string $nota, string $quien): string
{
    $html = lp_cierre_correo($c);
    $barra = '<div style="padding:14px 20px;background-color:#a53231;color:#ffffff;text-align:center;font-family:Verdana,Geneva,sans-serif;">'
        . '<div style="font-size:15px;letter-spacing:0.14em;font-weight:bold;">PRUEBA · NO ES EL CIERRE OFICIAL</div>'
        . '<div style="padding-top:6px;font-size:13px;">' . htmlspecialchars("Enviado desde panel-hacienda por $quien. $nota.", ENT_QUOTES, 'UTF-8') . '</div>'
        . '</div>';
    $r = preg_replace_callback('/<body([^>]*)>/', fn($m) => $m[0] . $barra, $html, 1);
    $r = is_string($r) ? $r : $html;
    return str_replace('<title>Cierre de caja', '<title>PRUEBA – Cierre de caja', $r);
}

function lp_caja_test_texto(array $c, string $nota): string
{
    $f = fn($k) => number_format((float)$c[$k], 2, '.', ',');
    return "PRUEBA – NO ES EL CIERRE OFICIAL\n$nota.\n\nCierre de caja {$c['fecha']}\n"
        . "Contado: {$f('contado_total')}\nCrédito: {$f('credito_total')}\nRecibos: {$f('recibos')}\n"
        . "Ingresos: {$f('ingresos')}\nEgresos: {$f('egresos')}\nTotal en caja: {$f('caja')}\nDepósitos: {$f('depositos')}\nDiferencia: {$f('diferencia')}\n";
}

/** Límite global: una prueba cada 30 s y 10 por hora. Devuelve null si hay cupo (y lo consume) o el motivo. */
function lp_caja_test_cupo(): ?string
{
    $old = umask(077);
    $fp = @fopen(LP_CAJA_TEST_RL, 'c+');
    umask($old);
    if (!$fp) return 'No se pudo verificar el límite de envíos';
    try {
        flock($fp, LOCK_EX);
        $l = json_decode((string)stream_get_contents($fp), true);
        $now = time();
        $l = array_values(array_filter(is_array($l) ? $l : [], fn($t) => is_int($t) && $t > $now - 3600));
        if ($l && $now - max($l) < LP_CAJA_TEST_ESPERA) return 'Espere ' . (LP_CAJA_TEST_ESPERA - ($now - max($l))) . ' s antes de enviar otra prueba';
        if (count($l) >= LP_CAJA_TEST_POR_HORA) return 'Se alcanzó el límite de ' . LP_CAJA_TEST_POR_HORA . ' correos de prueba por hora';
        $l[] = $now;
        ftruncate($fp, 0); rewind($fp); fwrite($fp, json_encode($l));
        return null;
    } finally {
        flock($fp, LOCK_UN); fclose($fp);
    }
}

function lp_caja_test_validar(string $para): ?string
{
    if ($para === '' || strlen($para) > 254 || preg_match('/[\s,;<>]/', $para) || !filter_var($para, FILTER_VALIDATE_EMAIL)) return 'Correo destino inválido';
    return null;
}

function lp_caja_test_enviar(string $para, string $modo, string $fecha, string $quien): array
{
    $para = trim($para);
    if ($e = lp_caja_test_validar($para)) return ['ok' => false, 'error' => $e];
    $modo = $modo === 'muestra' ? 'muestra' : 'real';
    $cfg = lp_smtp_leer();
    if (empty($cfg['host']) || empty($cfg['remitente'])) return ['ok' => false, 'error' => 'Configure primero el servidor y el remitente en Configuración → Correo'];
    try { [$c, $nota] = lp_caja_test_datos($modo, trim($fecha)); }
    catch (Throwable $e) { return ['ok' => false, 'error' => $e->getMessage()]; }
    if ($e = lp_caja_test_cupo()) return ['ok' => false, 'error' => $e];
    $r = lp_enviar_correo([$para], 'Prueba – Cierre de caja (panel-hacienda) ' . $c['fecha'], lp_caja_test_html($c, $nota, $quien), lp_caja_test_texto($c, $nota));
    if (!$r['ok']) return ['ok' => false, 'error' => 'No se pudo enviar: ' . mb_substr(preg_replace('/\s+/', ' ', (string)$r['error']), 0, 300)];
    return ['ok' => true, 'mensaje' => "Correo de prueba enviado a $para. $nota.", 'fecha' => $c['fecha'], 'modo' => $modo];
}
