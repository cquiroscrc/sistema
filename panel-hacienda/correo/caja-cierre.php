<?php
declare(strict_types=1);
/*
 * Cierre de caja OFICIAL por correo (tarea programada paticas-caja-correo.service, 17:15).
 * Reemplaza la ruta vieja caja_correo.php → redirección a lospaticas.com/cierres.php → mail().
 * - Formato nuevo: utilitarios/caja_correo_nuevo.php (lp_cierre_calcular + lp_cierre_correo), igual que el botón Test,
 *   pero sin la franja PRUEBA. Solo lectura de la BD (SELECT).
 * - Envío: SMTP de panel-hacienda > Configuración > Correo (PHPMailer 6.12, lp_enviar_correo).
 * - Destinatarios: lista del modal Configuración de la tarea (/var/lib/lospaticas/panel/caja-destinatarios.json).
 *   Sin lista válida no se envía (no hay destinatario fijo).
 * - Salida: una línea de texto para el log; código de salida ≠ 0 si falla.
 * Uso (solo CLI):
 *   php8.5 caja-cierre.php [--fecha=AAAA-MM-DD] [--dry-run=/ruta/salida.html]
 *   --dry-run arma el correo y lo guarda en el archivo, sin enviar nada.
 */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

require_once __DIR__ . '/correo-lib.php';
require_once __DIR__ . '/caja-test.php';

const LP_CIERRE_DESTINATARIOS = '/var/lib/lospaticas/panel/caja-destinatarios.json';

function lp_cierre_salir(int $rc, string $msg): void
{
    fwrite($rc === 0 ? STDOUT : STDERR, $msg . "\n");
    exit($rc);
}

/** Lista válida del modal Configuración (sin respaldo fijo). */
function lp_cierre_destinatarios(): array
{
    $j = is_readable(LP_CIERRE_DESTINATARIOS) ? json_decode((string)file_get_contents(LP_CIERRE_DESTINATARIOS), true) : null;
    $l = is_array($j) && isset($j['destinatarios']) && is_array($j['destinatarios']) ? $j['destinatarios'] : [];
    $ok = [];
    foreach ($l as $d)
        if (is_string($d) && !preg_match('/[\s,;<>]/', $d = trim($d)) && filter_var($d, FILTER_VALIDATE_EMAIL)) $ok[strtolower($d)] = $d;
    return array_values($ok);
}

function lp_cierre_texto(array $c): string
{
    $f = fn($k) => number_format((float)$c[$k], 2, '.', ',');
    return "Cierre de caja {$c['fecha']}\n"
        . "Contado: {$f('contado_total')}\nCrédito: {$f('credito_total')}\nRecibos: {$f('recibos')}\n"
        . "Ingresos: {$f('ingresos')}\nEgresos: {$f('egresos')}\nTotal en caja: {$f('caja')}\nDepósitos: {$f('depositos')}\nDiferencia: {$f('diferencia')}\n";
}

$op = getopt('', ['fecha:', 'dry-run:']);
$fecha = is_string($op['fecha'] ?? null) ? trim($op['fecha']) : '';
$dry = is_string($op['dry-run'] ?? null) ? $op['dry-run'] : null;
if ($fecha === '') { $tz = new DateTimeZone('America/Costa_Rica'); $fecha = (new DateTime('now', $tz))->format('Y-m-d'); }

try {
    [$c] = lp_caja_test_datos('real', $fecha);   // lectura de la BD, sin modificar nada
} catch (Throwable $e) {
    lp_cierre_salir(1, "ERROR cierre $fecha: no se pudieron leer los datos: " . preg_replace('/\s+/', ' ', $e->getMessage()));
}
$html = lp_cierre_correo($c);
$asunto = 'Cierre de hoy ' . $c['fecha'];
$para = lp_cierre_destinatarios();

if ($dry !== null) {
    if (@file_put_contents($dry, $html) === false) lp_cierre_salir(1, "ERROR cierre {$c['fecha']}: no se pudo escribir $dry");
    lp_cierre_salir(0, "SIMULACIÓN cierre {$c['fecha']}: HTML en $dry (" . strlen($html) . ' bytes), asunto "' . $asunto . '", ingresos '
        . number_format((float)$c['ingresos'], 2, '.', ',') . ', se enviaría a ' . count($para) . ' destinatario(s): ' . (implode(', ', $para) ?: '(ninguno)') . '. No se envió nada.');
}

// Igual que la ruta vieja: sin ingresos en el día no se manda cierre.
if ((float)$c['ingresos'] == 0.0) lp_cierre_salir(0, "Cierre {$c['fecha']} sin ingresos: no se envía correo");
if (!$para) lp_cierre_salir(2, "ERROR cierre {$c['fecha']}: no hay destinatarios válidos en Tareas programadas → Cierre de caja → Configuración");
$cfg = lp_smtp_leer();
if (empty($cfg['host']) || empty($cfg['remitente'])) lp_cierre_salir(2, "ERROR cierre {$c['fecha']}: falta configurar el servidor o el remitente en Configuración → Correo");

$r = lp_enviar_correo($para, $asunto, $html, lp_cierre_texto($c));
if (!$r['ok']) lp_cierre_salir(3, "ERROR cierre {$c['fecha']}: no se pudo enviar: " . mb_substr(preg_replace('/\s+/', ' ', (string)$r['error']), 0, 300));
lp_cierre_salir(0, "Cierre {$c['fecha']} enviado a " . count($para) . ' destinatario(s)');
