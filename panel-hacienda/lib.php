<?php
/*
 * Panel de control del envio de comprobantes electronicos a Hacienda (api44).
 * Acceso protegido por nginx (auth_basic). Solo lectura salvo: "Reintentar" (marca para reenvio)
 * e Iniciar/Detener el timer systemd del envio (sudoers restringido).
 */
declare(strict_types=1);

const PH_UNIT        = 'paticas-hacienda-sender';
const PH_GUARD       = '/etc/lospaticas/hacienda-sender.CORTE-REALIZADO';
const PH_API44       = '/var/www/lospaticas/sistema/fe/API/api44';
const PH_SENDER_LOG  = PH_API44 . '/logs/sender.log';
const PH_ERRORS_LOG  = '/var/www/lospaticas/sistema/fe/API/api/errors/_errors.log';
const PH_PHP_LOG     = '/var/log/php/lospaticas-error.log';

if (empty($_SERVER['REMOTE_USER'])) {           // nginx debe autenticar; si no, no se sirve nada
    http_response_code(403);
    exit('Acceso no autorizado');
}
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');

session_name('PANELHACIENDA');
session_set_cookie_params(['path' => '/panel-hacienda/', 'httponly' => true, 'samesite' => 'Strict',
    'secure' => (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')]);
session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
date_default_timezone_set('America/Costa_Rica');

function ph_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = require '/etc/lospaticas/db.php';
        $pdo = new PDO($c['pdo_dsn'], $c['user'], $c['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
    return $pdo;
}

function ph_db_info(): array
{
    $c = require '/etc/lospaticas/db.php';
    return ['host' => $c['host'], 'database' => $c['database']];
}

function ph_utf8(?string $s): string
{
    if ($s === null) return '';
    return mb_check_encoding($s, 'UTF-8') ? $s : mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
}

/** Oculta tokens, contrasenas, PIN, cryptoKey y cabeceras Authorization. */
function ph_mask(string $s): string
{
    $s = preg_replace('/\beyJ[A-Za-z0-9_-]{8,}\.[A-Za-z0-9_-]{8,}\.[A-Za-z0-9_-]+/', 'eyJ***(token oculto)', $s);
    $s = preg_replace('/(Authorization\s*[:=]\s*)(Bearer|Basic|Token)?\s*[^\s"\',;]+/i', '$1$2 ***', $s);
    $s = preg_replace('/((?:access|refresh|id)_token|token|password|passwd|contrase(?:n|ñ)a|pwd|pin(?:P12)?|_PIN|cryptoKey|crypto_key|client_secret|secret|apikey|api_key)(["\']?\s*(?:=>|[:=])\s*["\']?)([^"\'&\s,;<]+)/iu', '$1$2***', $s);
    $s = preg_replace('/[A-Za-z0-9+\/=]{400,}/', '[base64 omitido]', $s);
    return $s;
}

function ph_json($data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    exit;
}

function ph_require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) {
        ph_json(['ok' => false, 'error' => 'Solicitud invalida (CSRF)'], 403);
    }
}

function ph_date(?string $v): ?string
{
    $v = trim((string)$v);
    if ($v === '') return null;
    $v = str_replace('T', ' ', $v);
    return preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}(:\d{2}(\.\d{1,3})?)?)?$/', $v) ? $v : null;
}

function ph_nivel(string $line): string
{
    if (preg_match('/\b(fatal|error|excep|uncaught|rechaz)/i', $line)) return 'Error';
    if (preg_match('/\b(warning|deprecated|advertencia|notice)/i', $line)) return 'Warning';
    if (preg_match('/\binfo\b/i', $line)) return 'Info';
    return 'Debug';
}

function ph_systemctl_show(string $unit, array $props): array
{
    $cmd = '/usr/bin/systemctl show ' . escapeshellarg($unit) . ' --no-pager';
    foreach ($props as $p) $cmd .= ' -p ' . escapeshellarg($p);
    $out = [];
    exec($cmd . ' 2>/dev/null', $out);
    $r = [];
    foreach ($out as $l) { [$k, $v] = array_pad(explode('=', $l, 2), 2, ''); $r[$k] = $v; }
    return $r;
}

/**
 * Lee lineas de un archivo de log.
 *  - $since !== null: lineas nuevas desde el byte $since (modo "en vivo").
 *  - si no: hasta $limit lineas hacia atras desde $before (o el final).
 * Devuelve [lineas[offset=>texto], offsetInicio, offsetFin, tamano].
 */
function ph_tail(string $file, int $limit, ?int $before, ?int $since, callable $filter): array
{
    if (!is_readable($file)) return [[], 0, 0, -1];
    $size = filesize($file);
    $fh = fopen($file, 'rb');
    $out = [];
    if ($since !== null) {
        if ($since > $size) $since = 0;                       // archivo rotado
        $start = max($since, $size - 512 * 1024);
        fseek($fh, $start);
        $data = stream_get_contents($fh);
        $off = $start;
        foreach (explode("\n", $data) as $l) {
            $len = strlen($l) + 1;
            if ($l !== '' && $off + $len <= $size + 1 && $filter($l)) $out[$off] = $l;
            $off += $len;
        }
        fclose($fh);
        if (count($out) > $limit) $out = array_slice($out, -$limit, null, true);
        return [$out, $start, $size, $size];
    }
    $end = ($before === null || $before > $size) ? $size : max(0, $before);
    $pos = $end; $carry = ''; $scanned = 0;
    while ($pos > 0 && count($out) < $limit && $scanned < 8 * 1024 * 1024) {
        $chunk = min(65536, $pos); $pos -= $chunk; $scanned += $chunk;
        fseek($fh, $pos);
        $lines = explode("\n", fread($fh, $chunk) . $carry);
        $carry = $pos > 0 ? array_shift($lines) : '';
        $off = $pos > 0 ? $pos + strlen($carry) + 1 : 0;
        $tmp = [];
        foreach ($lines as $l) { if ($l !== '' && $filter($l)) $tmp[$off] = $l; $off += strlen($l) + 1; }
        $out = $tmp + $out;
    }
    fclose($fh);
    krsort($out);
    if (count($out) >= $limit) {
        $out = array_slice($out, 0, $limit, true);
        $cursor = min(array_keys($out));
    } else {
        $cursor = $pos > 0 ? $pos + strlen($carry) : 0;
    }
    return [$out, $cursor, $end, $size];
}
