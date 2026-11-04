<?php
/*
 * Panel de control del envio de comprobantes electronicos a Hacienda (api44).
 * Acceso protegido por nginx (auth_basic). Solo lectura salvo: "Reintentar" (marca para reenvio)
 * e Iniciar/Detener el timer systemd del envio (sudoers restringido).
 */
declare(strict_types=1);

const PH_UNIT        = 'paticas-hacienda-sender';
const PH_API44       = '/var/www/lospaticas/sistema/fe/API/api44';
const PH_SENDER_LOG  = PH_API44 . '/logs/sender.log';
const PH_ERRORS_LOG  = '/var/www/lospaticas/sistema/fe/API/api/errors/_errors.log';
const PH_PHP_LOG     = '/var/log/php/lospaticas-error.log';
const PH_DEDATA      = '/var/lib/lospaticas/fe/DEData';           // XML/PDF (copia de C:\Backups\DEData y destino de api44 tras el corte)
const PH_CORTE_SQL   = '/etc/lospaticas/corte-hacienda.sql';
const PH_DATA        = '/var/lib/lospaticas/panel';                // respaldos JSON y bitacora de acciones del panel
const PH_SYNC_LOG    = '/var/log/paticas-dedata-sync.log';
// Columnas de dbo.COMPAÑIAS que la correccion de rutas puede modificar
const RUTAS_COLS = '/^(IDP_(PROD|STAG)_(CERT_URL|RUTA_CERT|FILE_CERT)|XML_PATH|PDF_PATH|TEMPLATE_PATH)$/';
const PH_SYNC_BIN    = '/usr/local/sbin/paticas-dedata-sync';

const PH_IDLE_SEG    = 1800;      // cierre de sesion por inactividad (30 min)
const PH_MAX_SEG     = 43200;     // duracion maxima de una sesion (12 h)
const PH_FALLOS_MAX  = 5;         // intentos fallidos antes de bloquear
const PH_BLOQUEO_SEG = 900;       // bloqueo de 15 min por usuario y por IP
// Grupos de tbl_SEC_Grupos que pueden entrar. Vacio = cualquier usuario de tbl_SEC_Usuarios.
// Propuesta: [1] (01-Administradores); hoy los 7 usuarios son del grupo 1.
const PH_GRUPOS_PERMITIDOS = [];

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.gc_maxlifetime', (string)PH_MAX_SEG);
// Directorio propio: el cron phpsessionclean de Ubuntu borra /var/lib/php/sessions con el gc_maxlifetime global
// (1440 s = 24 min), lo que invalidaba el token CSRF de una pantalla de login abierta mas de ~24 min.
const PH_SESS_DIR = PH_DATA . '/sessions';
if (!is_dir(PH_SESS_DIR)) @mkdir(PH_SESS_DIR, 0700, true);
ini_set('session.save_path', PH_SESS_DIR);
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');
session_name('PANELHACIENDA');
session_set_cookie_params(['lifetime' => 0, 'path' => '/panel-hacienda/', 'httponly' => true, 'samesite' => 'Strict', 'secure' => true]);
session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
// Expiracion por inactividad / duracion maxima
if (!empty($_SESSION['usuario'])) {
    $now = time();
    if ($now - ($_SESSION['ultimo'] ?? 0) > PH_IDLE_SEG || $now - ($_SESSION['inicio'] ?? 0) > PH_MAX_SEG) {
        $u = $_SESSION['usuario'];
        $_SESSION = ['csrf' => bin2hex(random_bytes(32))];
        session_regenerate_id(true);
        $GLOBALS['ph_expirada'] = $u;
    } else {
        $_SESSION['ultimo'] = $now;
    }
}

function ph_user(): string { return (string)($_SESSION['usuario'] ?? ''); }
function ph_ip(): string { return (string)($_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '?'); }
/** Clave de bloqueo por origen: IPv4 completa o prefijo /64 en IPv6 (un mismo cliente rota dentro de su /64). */
function ph_ip_clave(): string
{
    $ip = ph_ip();
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) return substr(bin2hex(inet_pton($ip)), 0, 16) . '::/64';
    return $ip;
}

function ph_require_login(): void
{
    if (ph_user() === '') {
        ph_json(['ok' => false, 'error' => isset($GLOBALS['ph_expirada']) ? 'La sesion expiro por inactividad' : 'No autenticado', 'csrf' => $_SESSION['csrf']], 401);
    }
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
    $s = preg_replace('/\beyJ[A-Za-z0-9_-]{8,}\.[A-Za-z0-9_-]{8,}\.[A-Za-z0-9_-]+/', 'eyJ***', $s);
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
        ph_json(['ok' => false, 'codigo' => 'csrf', 'error' => 'Solicitud invalida (CSRF). Recargue la pagina.', 'csrf' => $_SESSION['csrf']], 403);
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

function ph_accion_log(string $msg): void
{
    if (!is_dir(PH_DATA)) @mkdir(PH_DATA, 0700, true);
    @file_put_contents(PH_DATA . '/acciones.log', date('Y-m-d H:i:s') . ' [' . (ph_user() ?: '-') . '] ' . $msg . "\n", FILE_APPEND | LOCK_EX);
    error_log('panel-hacienda: ' . (ph_user() ?: '-') . ' ' . $msg);
}

/** Registra en dbo.BITACORA (SESSION_ID 'panel-hacienda'); nunca falla la peticion. */
function ph_bitacora(string $texto, string $nivel = 'Info', string $doc = '', string $tipo = ''): void
{
    try {
        ph_db()->prepare("INSERT INTO dbo.BITACORA (SESSION_ID, TIPO, TEXTO, FECHA_HORA, DOCUMENTO, TIPO_DOCUMENTO) VALUES ('panel-hacienda', ?, ?, SYSDATETIME(), ?, ?)")
            ->execute([$nivel, mb_substr('Panel: ' . $texto, 0, 4000), $doc, $tipo]);
    } catch (Throwable $e) {
        error_log('panel-hacienda: bitacora: ' . $e->getMessage());
    }
}

function ph_accion(string $msg, string $nivel = 'Info', string $doc = '', string $tipo = ''): void
{
    ph_accion_log($msg);
    ph_bitacora($msg . ' [usuario ' . (ph_user() ?: '-') . ', IP ' . ph_ip() . ']', $nivel, $doc, $tipo);
}

/* ---------------- Login contra dbo.tbl_SEC_Usuarios ---------------- */
function ph_fallos_archivo(): string { return PH_DATA . '/login-fallos.json'; }

/** Lee/actualiza contadores de fallos con bloqueo de archivo. $fn recibe el arreglo por referencia. */
function ph_fallos(callable $fn)
{
    if (!is_dir(PH_DATA)) @mkdir(PH_DATA, 0700, true);
    $fh = fopen(ph_fallos_archivo(), 'c+');
    flock($fh, LOCK_EX);
    $d = json_decode((string)stream_get_contents($fh), true) ?: [];
    $now = time();
    foreach ($d as $k => $v) if ($now - ($v['t'] ?? 0) > PH_BLOQUEO_SEG) unset($d[$k]);
    $r = $fn($d);
    ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($d)); fflush($fh);
    flock($fh, LOCK_UN); fclose($fh);
    @chmod(ph_fallos_archivo(), 0600);
    return $r;
}

function ph_login(string $usuario, string $password): array
{
    $usuario = trim($usuario);
    if ($usuario === '' || $password === '' || mb_strlen($usuario) > 30 || strlen($password) > 64) return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos'];
    $kU = 'u:' . mb_strtolower($usuario); $kI = 'ip:' . ph_ip_clave();
    $bloq = ph_fallos(fn(array &$d) => max($d[$kU]['n'] ?? 0, $d[$kI]['n'] ?? 0) >= PH_FALLOS_MAX);
    if ($bloq) {
        ph_accion_log("login BLOQUEADO usuario=$usuario ip=" . ph_ip());
        return ['ok' => false, 'error' => 'Demasiados intentos fallidos. Intente de nuevo en 15 minutos.'];
    }
    // Mismo criterio que la tabla del sistema PATICAS: Username y Password (char, texto plano) recortados.
    // Usuario: comparacion de la BD (Modern_Spanish_CI_AS, sin distinguir mayusculas). Contraseña: exacta (hash_equals).
    $st = ph_db()->prepare('SELECT idUsuario, idGrupo, RTRIM(Descripcion) AS nombre, RTRIM(Username) AS usuario, RTRIM(Password) AS pw FROM dbo.tbl_SEC_Usuarios WHERE RTRIM(Username) = ?');
    $st->execute([$usuario]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    $ok = null;
    foreach ($rows as $r) {
        if (hash_equals((string)$r['pw'], $password)) { $ok = $r; break; }
    }
    unset($rows, $password);
    if ($ok && PH_GRUPOS_PERMITIDOS && !in_array((int)$ok['idGrupo'], PH_GRUPOS_PERMITIDOS, true)) {
        ph_accion_log("login RECHAZADO (grupo {$ok['idGrupo']} no permitido) usuario=$usuario ip=" . ph_ip());
        ph_bitacora("login rechazado por grupo: $usuario", 'Warning');
        return ['ok' => false, 'error' => 'Su usuario no tiene acceso a este panel'];
    }
    if (!$ok) {
        ph_fallos(function (array &$d) use ($kU, $kI) { foreach ([$kU, $kI] as $k) $d[$k] = ['n' => ($d[$k]['n'] ?? 0) + 1, 't' => time()]; });
        ph_accion_log("login FALLIDO usuario=$usuario ip=" . ph_ip());
        ph_bitacora("login fallido: $usuario (IP " . ph_ip() . ')', 'Warning');
        return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos'];
    }
    ph_fallos(function (array &$d) use ($kU) { unset($d[$kU]); });
    session_regenerate_id(true);
    $_SESSION['usuario'] = ph_utf8($ok['usuario']);
    $_SESSION['nombre'] = ph_utf8($ok['nombre']);
    $_SESSION['grupo'] = (int)$ok['idGrupo'];
    $_SESSION['inicio'] = $_SESSION['ultimo'] = time();
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    ph_accion('login correcto');
    return ['ok' => true, 'usuario' => $_SESSION['usuario'], 'nombre' => $_SESSION['nombre'], 'csrf' => $_SESSION['csrf']];
}

/**
 * Resuelve un archivo de DEData (PDF/XML) de forma segura: solo dentro de PH_DEDATA,
 * subcarpeta AAAA/MM/DD y nombre <clave>.pdf|.xml o RSP_<clave>.xml. Sin rutas relativas.
 */
function ph_dedata_file(?string $dbPath, ?string $fechaTimbrado, string $name): ?string
{
    if (!preg_match('/^(RSP_)?\d{50}\.(pdf|xml)$/', $name)) return null;
    $rels = [];
    $p = str_replace('\\', '/', trim((string)$dbPath));
    foreach (['/Backups/DEData', PH_DEDATA] as $pre) {
        if ($p !== '' && stripos($p, $pre) !== false) { $rels[] = substr($p, stripos($p, $pre) + strlen($pre)); break; }
    }
    if ($fechaTimbrado && ($t = strtotime($fechaTimbrado))) $rels[] = date('/Y/m/d', $t);
    $base = realpath(PH_DEDATA);
    if ($base === false) return null;
    foreach (array_unique($rels) as $rel) {
        if (!preg_match('#^(/\d{4}/\d{2}/\d{2})?/?$#', $rel)) continue;
        $f = realpath($base . rtrim($rel, '/') . '/' . $name);
        if ($f !== false && str_starts_with($f, $base . '/') && is_file($f)) return $f;
    }
    return null;
}
