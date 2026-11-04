<?php
/**
 * Migraciones de BD del panel-hacienda (usa /etc/lospaticas/db.php).
 *   sudo -u www-data php8.5 /var/www/lospaticas/sistema/panel-hacienda/db/migrar.php --estado
 *   sudo -u www-data php8.5 /var/www/lospaticas/sistema/panel-hacienda/db/migrar.php --aplicar
 * Solo para pruebas: --base=NOMBRE (otra BD del mismo servidor; requiere permisos sobre ella)
 *                    --esquema=NOMBRE (crea los objetos en ese esquema en vez de dbo; el esquema debe existir).
 * Codigo de salida: 0 ok/sin pendientes, 2 hay pendientes (--estado), 1 error.
 */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/migrar-lib.php';

$opt = getopt('', ['estado', 'aplicar', 'base:', 'esquema:']);
$c = require '/etc/lospaticas/db.php';
$dsn = $c['pdo_dsn'];
if (isset($opt['base'])) {
    if (!preg_match('/^[A-Za-z0-9_]{1,100}$/', $opt['base'])) { fwrite(STDERR, "--base invalida\n"); exit(1); }
    $dsn = preg_replace('/Database=[^;]*/i', 'Database=' . $opt['base'], $dsn);
}
if (isset($opt['esquema'])) {
    if (!preg_match('/^[a-z][a-z0-9_]{0,60}$/', $opt['esquema']) || $opt['esquema'] === 'dbo') { fwrite(STDERR, "--esquema invalido\n"); exit(1); }
    $GLOBALS['MIG_ESQ'] = $opt['esquema'];
}
try {
    $db = new MigPDO($dsn, $c['user'], $c['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $bd = $db->query('SELECT @@SERVERNAME + \' / \' + DB_NAME()')->fetchColumn() . ($GLOBALS['MIG_ESQ'] !== '' ? ' (esquema de prueba ' . $GLOBALS['MIG_ESQ'] . ')' : '');
    echo "BD: $bd\n";
    if (isset($opt['aplicar'])) {
        $u = 'cli:' . (function_exists('posix_geteuid') ? (posix_getpwuid(posix_geteuid())['name'] ?? '?') : (getenv('USER') ?: '?')) . (getenv('SUDO_USER') ? ' (sudo ' . getenv('SUDO_USER') . ')' : '');
        mig_log("aplicar en $bd por $u");
        [$hechas, $err] = mig_aplicar($db, $u);
        echo $hechas ? 'Aplicadas: ' . implode(', ', $hechas) . "\n" : "Nada pendiente.\n";
        if ($err) { echo "$err\n"; exit(1); }
    }
    $pend = 0;
    foreach (mig_estado($db) as $e) {
        $p = !$e['aplicada'] || $e['faltantes'];
        $pend += $p ? 1 : 0;
        printf("  %-40s %s%s%s\n", $e['nombre'], $e['aplicada'] ? "aplicada {$e['fecha']} ({$e['usuario']})" : 'PENDIENTE',
            $e['modificada'] ? '  [archivo modificado despues de aplicarse]' : '', $e['faltantes'] ? '  FALTAN: ' . implode(', ', $e['faltantes']) : '');
    }
    echo $pend ? "$pend pendiente(s)\n" : "Todo aplicado.\n";
    exit(isset($opt['aplicar']) || !$pend ? 0 : 2);
} catch (Throwable $x) {
    mig_log('ERROR ' . $x->getMessage());
    echo 'ERROR: ' . $x->getMessage() . "\n";
    exit(1);
}
