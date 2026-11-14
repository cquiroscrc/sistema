<?php
declare(strict_types=1);
/*
 * Configuracion SMTP de salida (panel-hacienda > Configuracion > Correo) y envio reutilizable.
 * - Config: /var/lib/lospaticas/panel/smtp.json (www-data, 0600), fuera del webroot.
 * - Contrasena cifrada en reposo (sodium secretbox); la clave esta aparte en /etc/lospaticas/smtp.key
 *   (root:www-data 0640). La contrasena nunca se devuelve al navegador ni se registra.
 * - Uso futuro (p. ej. utilitarios/cierres.php):
 *     require_once '/var/www/lospaticas/sistema/panel-hacienda/correo/correo-lib.php';
 *     $r = lp_enviar_correo(['a@lospaticas.com'], 'Asunto', '<p>html</p>');   // ['ok'=>bool, 'error'=>?]
 */

const LP_SMTP_CFG = '/var/lib/lospaticas/panel/smtp.json';
const LP_SMTP_KEY = '/etc/lospaticas/smtp.key';
const LP_PHPMAILER = '/var/www/lospaticas/sistema/fe/API/api44/tools/phpmailer/vendor/phpmailer/phpmailer/src';
const LP_SMTP_CIFRADOS = ['starttls', 'ssl', 'ninguno'];

function lp_smtp_clave(): string
{
    $b = is_readable(LP_SMTP_KEY) ? trim((string)file_get_contents(LP_SMTP_KEY)) : '';
    $k = $b !== '' ? base64_decode($b, true) : false;
    if ($k === false || strlen($k) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) throw new RuntimeException('Falta la clave de cifrado SMTP (' . LP_SMTP_KEY . ')');
    return $k;
}

function lp_smtp_cifrar(string $plano): string
{
    $n = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    return 'v1:' . base64_encode($n . sodium_crypto_secretbox($plano, $n, lp_smtp_clave()));
}

function lp_smtp_descifrar(string $c): string
{
    if (!str_starts_with($c, 'v1:')) return '';
    $b = base64_decode(substr($c, 3), true);
    if ($b === false || strlen($b) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) return '';
    $p = sodium_crypto_secretbox_open(substr($b, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), substr($b, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), lp_smtp_clave());
    return $p === false ? '' : $p;
}

/** Config cruda guardada (contrasena aun cifrada). */
function lp_smtp_leer(): array
{
    $j = is_readable(LP_SMTP_CFG) ? json_decode((string)file_get_contents(LP_SMTP_CFG), true) : null;
    return is_array($j) ? $j : [];
}

/** Config para mostrar en el panel: sin contrasena. */
function lp_smtp_publico(): array
{
    $c = lp_smtp_leer();
    return [
        'configurado' => !empty($c['host']),
        'host' => $c['host'] ?? '', 'puerto' => (int)($c['puerto'] ?? 587), 'cifrado' => $c['cifrado'] ?? 'starttls',
        'usuario' => $c['usuario'] ?? '', 'remitente' => $c['remitente'] ?? '', 'remitente_nombre' => $c['remitente_nombre'] ?? '',
        'password_guardada' => !empty($c['password']),
        'actualizado' => $c['actualizado'] ?? null, 'actualizado_por' => $c['actualizado_por'] ?? null,
    ];
}

/** Valida datos del formulario. Devuelve [config_sin_password, errores]. */
function lp_smtp_validar(array $in): array
{
    $e = [];
    $host = trim((string)($in['host'] ?? ''));
    if ($host === '' || strlen($host) > 253 || !(filter_var($host, FILTER_VALIDATE_IP) || preg_match('/^(?=.{1,253}$)([a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/i', $host))) $e[] = 'Servidor inválido';
    $puerto = (int)($in['puerto'] ?? 0);
    if ($puerto < 1 || $puerto > 65535) $e[] = 'Puerto inválido';
    $cif = (string)($in['cifrado'] ?? '');
    if (!in_array($cif, LP_SMTP_CIFRADOS, true)) $e[] = 'Cifrado inválido';
    $usuario = trim((string)($in['usuario'] ?? ''));
    if (strlen($usuario) > 254 || preg_match('/[\r\n]/', $usuario)) $e[] = 'Usuario inválido';
    $rem = trim((string)($in['remitente'] ?? ''));
    if (!filter_var($rem, FILTER_VALIDATE_EMAIL)) $e[] = 'Remitente (From) inválido';
    $nom = trim((string)($in['remitente_nombre'] ?? ''));
    if (mb_strlen($nom) > 100 || preg_match('/[\r\n]/', $nom)) $e[] = 'Nombre del remitente inválido';
    if (strlen((string)($in['password'] ?? '')) > 256) $e[] = 'Contraseña demasiado larga';
    return [['host' => strtolower($host), 'puerto' => $puerto, 'cifrado' => $cif, 'usuario' => $usuario, 'remitente' => $rem, 'remitente_nombre' => $nom], $e];
}

/** Guarda la config. password '' = conservar la guardada; borrar_password=1 la elimina. */
function lp_smtp_guardar(array $in, string $quien): array
{
    [$cfg, $err] = lp_smtp_validar($in);
    if ($err) return ['ok' => false, 'error' => implode('. ', $err)];
    $prev = lp_smtp_leer();
    $pw = (string)($in['password'] ?? '');
    if (($in['borrar_password'] ?? '') === '1') $cfg['password'] = '';
    elseif ($pw !== '') $cfg['password'] = lp_smtp_cifrar($pw);
    else $cfg['password'] = $prev['password'] ?? '';
    $cfg['actualizado'] = date('Y-m-d H:i:s');
    $cfg['actualizado_por'] = $quien;
    $dir = dirname(LP_SMTP_CFG);
    if (!is_dir($dir)) @mkdir($dir, 0700, true);
    $tmp = LP_SMTP_CFG . '.tmp' . getmypid();
    $old = umask(0077);
    $ok = file_put_contents($tmp, json_encode($cfg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", LOCK_EX) !== false && rename($tmp, LP_SMTP_CFG);
    umask($old);
    if (!$ok) { @unlink($tmp); return ['ok' => false, 'error' => 'No se pudo guardar la configuración']; }
    @chmod(LP_SMTP_CFG, 0600);
    $cambios = [];
    foreach (['host', 'puerto', 'cifrado', 'usuario', 'remitente', 'remitente_nombre'] as $k) if ((string)($prev[$k] ?? '') !== (string)$cfg[$k]) $cambios[] = $k;
    if (($prev['password'] ?? '') !== $cfg['password']) $cambios[] = $cfg['password'] === '' ? 'contraseña eliminada' : 'contraseña cambiada';
    return ['ok' => true, 'cambios' => $cambios];
}

function lp_smtp_cargar_phpmailer(): void
{
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer', false)) return;
    foreach (['Exception', 'SMTP', 'PHPMailer'] as $c) require_once LP_PHPMAILER . "/$c.php";
}

/** PHPMailer listo para enviar con la config (guardada o la del formulario). Lanza excepcion si no hay config. */
function lp_smtp_mailer(?array $cfg = null): \PHPMailer\PHPMailer\PHPMailer
{
    lp_smtp_cargar_phpmailer();
    $c = $cfg ?? lp_smtp_leer();
    if (empty($c['host'])) throw new RuntimeException('Correo SMTP no configurado');
    $m = new \PHPMailer\PHPMailer\PHPMailer(true);
    $m->isSMTP();
    $m->Host = $c['host'];
    $m->Port = (int)$c['puerto'];
    $m->Timeout = 15;
    $m->CharSet = 'UTF-8';
    $m->SMTPDebug = 0;
    if ($c['cifrado'] === 'ssl') { $m->SMTPSecure = 'ssl'; $m->SMTPAutoTLS = false; }
    elseif ($c['cifrado'] === 'starttls') { $m->SMTPSecure = 'tls'; }
    else { $m->SMTPSecure = ''; $m->SMTPAutoTLS = false; }
    $pw = isset($c['password_plano']) ? (string)$c['password_plano'] : (!empty($c['password']) ? lp_smtp_descifrar((string)$c['password']) : '');
    if ($c['usuario'] !== '' || $pw !== '') { $m->SMTPAuth = true; $m->Username = $c['usuario']; $m->Password = $pw; }
    if (!empty($c['remitente'])) $m->setFrom($c['remitente'], (string)($c['remitente_nombre'] ?? ''), false);
    return $m;
}

/** Probar conexion: conecta, EHLO, STARTTLS/SSL y AUTH; luego QUIT. No envia ningun correo. */
function lp_smtp_probar(array $in): array
{
    [$cfg, $err] = lp_smtp_validar($in);
    if ($err) return ['ok' => false, 'error' => implode('. ', $err)];
    $pw = (string)($in['password'] ?? '');
    if ($pw !== '') $cfg['password_plano'] = $pw; else $cfg['password'] = lp_smtp_leer()['password'] ?? '';
    $t0 = microtime(true);
    try {
        $m = lp_smtp_mailer($cfg);
        $ok = $m->smtpConnect();
        $m->smtpClose();
        $ms = (int)round((microtime(true) - $t0) * 1000);
        return $ok ? ['ok' => true, 'mensaje' => 'Conexión correcta' . ($m->SMTPAuth ? ' y autenticación aceptada' : ' (sin autenticación)') . " en {$ms} ms. No se envió ningún correo."]
                   : ['ok' => false, 'error' => 'No se pudo conectar'];
    } catch (Throwable $e) {
        $msg = preg_replace('/\s+/', ' ', $e->getMessage());
        return ['ok' => false, 'error' => 'Falló: ' . mb_substr($msg, 0, 300)];
    }
}

/** Envia un correo HTML con la config guardada. $para: lista de direcciones. Devuelve ['ok'=>bool,'error'=>?]. */
function lp_enviar_correo(array $para, string $asunto, string $html, string $texto = ''): array
{
    try {
        $m = lp_smtp_mailer();
        if (!$m->From) throw new RuntimeException('Falta el remitente');
        $n = 0;
        foreach ($para as $d) if (is_string($d) && filter_var(trim($d), FILTER_VALIDATE_EMAIL)) { $m->addAddress(trim($d)); $n++; }
        if (!$n) throw new RuntimeException('Sin destinatarios válidos');
        $m->Subject = $asunto;
        $m->isHTML(true);
        $m->Body = $html;
        $m->AltBody = $texto !== '' ? $texto : trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
        $m->send();
        return ['ok' => true];
    } catch (Throwable $e) {
        error_log('lp_enviar_correo: ' . $e->getMessage());
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}
