<?php

/**
 * Bridge HTTP minimo: SQL Server (ODBC) -> JSON para SiteGround.
 *
 * Compatible con PHP 7.0+ (XAMPP).
 *
 * URLs (con la carpeta en htdocs):
 *   http://IP:88/inventory-bridge/index.php?action=health
 *   http://IP:88/inventory-bridge/index.php?action=inventory
 *   http://IP:88/inventory-bridge/index.php?action=images
 *   http://IP:88/inventory-bridge/index.php?action=image&path=1/foto.jpg
 *   http://IP:88/inventory-bridge/index.php/health
 *   http://IP:88/inventory-bridge/index.php/inventory
 */

header('X-Content-Type-Options: nosniff');

$configFile = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
if (!is_file($configFile)) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(array('error' => 'Falta config.php. Copia config.example.php a config.php.'));
    exit;
}

$config = require $configFile;
$action = resolveAction();

if ($action === 'health') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('ok' => true, 'service' => 'paticas-inventory-bridge'));
    exit;
}

if ($action === 'inventory') {
    header('Content-Type: application/json; charset=utf-8');
    assertBearerToken(isset($config['token']) ? (string) $config['token'] : '');

    try {
        $rows = fetchInventoryRows($config['sql']);
        echo json_encode(array(
            'rows' => $rows,
            'count' => count($rows),
            'generated_at' => gmdate('c'),
        ), JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('error' => $e->getMessage()));
    }
    exit;
}

if ($action === 'images') {
    header('Content-Type: application/json; charset=utf-8');
    assertBearerToken(isset($config['token']) ? (string) $config['token'] : '');

    try {
        $files = listImageManifest(isset($config['images_root']) ? (string) $config['images_root'] : '');
        echo json_encode(array(
            'files' => $files,
            'count' => count($files),
            'generated_at' => gmdate('c'),
        ), JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('error' => $e->getMessage()));
    }
    exit;
}

if ($action === 'image') {
    assertBearerToken(isset($config['token']) ? (string) $config['token'] : '');

    $relative = isset($_GET['path']) ? (string) $_GET['path'] : '';
    $resolved = resolveSafeImagePath(
        isset($config['images_root']) ? (string) $config['images_root'] : '',
        $relative
    );

    if ($resolved === null) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(array('error' => 'Imagen no encontrada'));
        exit;
    }

    $mime = imageMimeFromPath($resolved);
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($resolved));
    header('Cache-Control: private, max-age=300');
    readfile($resolved);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
http_response_code(404);
echo json_encode(array(
    'error' => 'Not found',
    'hint' => 'Usa ?action=health, ?action=inventory, ?action=images o ?action=image&path=',
));

/**
 * @return string
 */
function resolveAction()
{
    if (isset($_GET['action']) && $_GET['action'] !== '') {
        return strtolower(trim((string) $_GET['action']));
    }

    $pathInfo = isset($_SERVER['PATH_INFO']) ? (string) $_SERVER['PATH_INFO'] : '';
    if ($pathInfo !== '') {
        $part = strtolower(trim($pathInfo, '/'));
        if ($part !== '') {
            return $part;
        }
    }

    $uriPath = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    $uriPath = is_string($uriPath) ? $uriPath : '/';
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME']) : '';
    $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    if ($base !== '' && $base !== '/' && strpos($uriPath, $base) === 0) {
        $uriPath = substr($uriPath, strlen($base));
    }

    $uriPath = strtolower(trim(str_replace('\\', '/', (string) $uriPath), '/'));
    $uriPath = preg_replace('#^index\.php/?#', '', $uriPath);
    $uriPath = trim((string) $uriPath, '/');

    if ($uriPath === '' || $uriPath === 'index.php') {
        return 'inventory';
    }

    return $uriPath;
}

/**
 * @param string $expected
 * @return void
 */
function assertBearerToken($expected)
{
    if ($expected === '') {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(array('error' => 'Token no configurado en config.php'));
        exit;
    }

    $header = '';
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $header = (string) $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $header = (string) $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['HTTP_X_INVENTORY_TOKEN'])) {
        $header = 'Bearer ' . (string) $_SERVER['HTTP_X_INVENTORY_TOKEN'];
    }

    // Apache/CGI a veces no pasa Authorization; permitir ?token= como respaldo.
    if ($header === '' && isset($_GET['token'])) {
        $header = 'Bearer ' . (string) $_GET['token'];
    }

    if (!preg_match('/^Bearer\s+(\S+)$/i', $header, $m)) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(array('error' => 'Falta Authorization: Bearer'));
        exit;
    }

    if (!hash_equals($expected, $m[1])) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(array('error' => 'Token invalido'));
        exit;
    }
}

/**
 * @param array $sql
 * @return array
 */
function fetchInventoryRows($sql)
{
    $pdo = makePdo($sql);
    $query = sprintf(
        'SELECT [%s] AS sku, [%s] AS name, [%s] AS quantity, [%s] AS unit, [%s] AS unit_price, [%s] AS tax_rate FROM [%s]',
        $sql['sku_column'],
        $sql['name_column'],
        $sql['qty_column'],
        $sql['unit_column'],
        $sql['price_column'],
        isset($sql['tax_column']) ? $sql['tax_column'] : 'IV',
        $sql['table']
    );

    $raw = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    $out = array();

    foreach ($raw as $row) {
        $sku = trim(toUtf8(isset($row['sku']) ? (string) $row['sku'] : ''));
        if ($sku === '') {
            continue;
        }

        $out[] = array(
            'sku' => $sku,
            'name' => toUtf8(isset($row['name']) ? (string) $row['name'] : $sku),
            'quantity' => (float) (isset($row['quantity']) ? $row['quantity'] : 0),
            'unit' => isset($row['unit']) ? trim(toUtf8((string) $row['unit'])) : null,
            'unit_price' => (float) (isset($row['unit_price']) ? $row['unit_price'] : 0),
            'tax_rate' => (float) (isset($row['tax_rate']) ? $row['tax_rate'] : 0),
        );
    }

    return $out;
}

/**
 * @param string $root
 * @return array
 */
function listImageManifest($root)
{
    $root = rtrim(str_replace('\\', '/', $root), '/');
    if ($root === '' || !is_dir($root)) {
        throw new RuntimeException('images_root no existe o no esta configurada.');
    }

    $allowed = array(
        '.jpg' => true,
        '.jpeg' => true,
        '.png' => true,
        '.gif' => true,
        '.webp' => true,
        '.bmp' => true,
    );

    $out = array();
    $entries = @scandir($root);
    if (!is_array($entries)) {
        throw new RuntimeException('No se pudo leer images_root.');
    }

    sort($entries);

    foreach ($entries as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue;
        }
        if (strlen($folder) !== 1) {
            continue;
        }

        $folderPath = $root . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($folderPath)) {
            continue;
        }

        $files = @scandir($folderPath);
        if (!is_array($files)) {
            continue;
        }

        sort($files);

        foreach ($files as $filename) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            $full = $folderPath . DIRECTORY_SEPARATOR . $filename;
            if (!is_file($full)) {
                continue;
            }

            $ext = strtolower(strrchr($filename, '.'));
            if ($ext === '' || !isset($allowed[$ext])) {
                continue;
            }

            $out[] = array(
                'path' => $folder . '/' . $filename,
                'folder' => $folder,
                'filename' => $filename,
                'size' => (int) filesize($full),
                'mtime' => (int) filemtime($full),
            );
        }
    }

    return $out;
}

/**
 * @param string $root
 * @param string $relative
 * @return string|null
 */
function resolveSafeImagePath($root, $relative)
{
    $root = rtrim($root, "/\\");
    if ($root === '' || !is_dir($root)) {
        return null;
    }

    $relative = str_replace('\\', '/', trim($relative));
    if ($relative === '' || strpos($relative, '..') !== false) {
        return null;
    }

    $parts = explode('/', $relative);
    if (count($parts) !== 2) {
        return null;
    }

    $folder = $parts[0];
    $filename = $parts[1];
    if (strlen($folder) !== 1 || $filename === '' || strpos($filename, '/') !== false) {
        return null;
    }

    $allowed = array(
        '.jpg' => true,
        '.jpeg' => true,
        '.png' => true,
        '.gif' => true,
        '.webp' => true,
        '.bmp' => true,
    );
    $ext = strtolower(strrchr($filename, '.'));
    if ($ext === '' || !isset($allowed[$ext])) {
        return null;
    }

    $realRoot = realpath($root);
    $candidate = $root . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;
    $realFile = realpath($candidate);

    if ($realRoot === false || $realFile === false || !is_file($realFile)) {
        return null;
    }

    $rootPrefix = strtolower(rtrim(str_replace('\\', '/', $realRoot), '/')) . '/';
    $filePath = strtolower(str_replace('\\', '/', $realFile));
    if (strpos($filePath, $rootPrefix) !== 0 && $filePath !== rtrim($rootPrefix, '/')) {
        return null;
    }

    return $realFile;
}

/**
 * @param string $path
 * @return string
 */
function imageMimeFromPath($path)
{
    $ext = strtolower(strrchr($path, '.'));
    $map = array(
        '.jpg' => 'image/jpeg',
        '.jpeg' => 'image/jpeg',
        '.png' => 'image/png',
        '.gif' => 'image/gif',
        '.webp' => 'image/webp',
        '.bmp' => 'image/bmp',
    );

    if (isset($map[$ext])) {
        return $map[$ext];
    }

    if (function_exists('mime_content_type')) {
        $detected = @mime_content_type($path);
        if (is_string($detected) && $detected !== '') {
            return $detected;
        }
    }

    return 'application/octet-stream';
}

/**
 * @param array $sql
 * @return PDO
 */
function makePdo($sql)
{
    $host = $sql['host'];
    $port = $sql['port'];
    $db = $sql['database'];
    $user = $sql['username'];
    $pass = $sql['password'];

    $drivers = array(
        'ODBC Driver 18 for SQL Server',
        'ODBC Driver 17 for SQL Server',
        'ODBC Driver 13 for SQL Server',
        'SQL Server',
    );
    $errors = array();

    foreach ($drivers as $driver) {
        try {
            $dsn = 'odbc:Driver={' . $driver . '};Server=' . $host . ',' . $port . ';Database=' . $db . ';Encrypt=no;TrustServerCertificate=yes';

            return new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (Exception $e) {
            $errors[] = $driver . ': ' . $e->getMessage();
        }
    }

    throw new RuntimeException('ODBC fail: ' . implode(' | ', $errors));
}

/**
 * @param string $value
 * @return string
 */
function toUtf8($value)
{
    if ($value === '') {
        return $value;
    }

    if (function_exists('iconv')) {
        $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $value);
        if (is_string($converted) && $converted !== '') {
            return $converted;
        }
    }

    if (function_exists('mb_convert_encoding')) {
        $converted = @mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
        if (is_string($converted)) {
            return $converted;
        }
    }

    return $value;
}
