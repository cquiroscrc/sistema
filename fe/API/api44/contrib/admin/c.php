<?php

// Configuration - Mover las constantes al nivel superior
const DB_CONNECTION_STRING = 'sqlsrv:Server=localhost;Database=Inventario';
const DB_USERNAME = 'sa';
const DB_PASSWORD = 'PLUMA';
const API_URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=';
const MAX_RETRIES = 3;
const LOG_DIR = __DIR__ . '/logs';
const LOG_FILE = LOG_DIR . '/hacienda_api.log';
const MAX_LOG_SIZE = 5 * 1024 * 1024; // 5MB
const MAX_LOG_FILES = 5;
const LOG_LEVEL = 'INFO';

// Command line options
$options = getopt("", ["reprocess-all", "help"]);
$reprocess_all = isset($options['reprocess-all']);

if (isset($options['help'])) {
    echo "Uso: php " . basename(__FILE__) . " [OPCIONES]\n\n";
    echo "Opciones:\n";
    echo "  --reprocess-all    Reprocesar todos los clientes, incluyendo aquellos con códigos existentes\n";
    echo "                     Use esto para migrar del catálogo CIIU3 a CIIU4\n";
    echo "  --help             Mostrar este mensaje de ayuda\n\n";
    echo "Ejemplos:\n";
    echo "  php " . basename(__FILE__) . "                    # Procesar solo clientes sin código de actividad\n";
    echo "  php " . basename(__FILE__) . " --reprocess-all    # Procesar TODOS los clientes (migración CIIU3 a CIIU4)\n\n";
    exit(0);
}

// Global variables
$request_count = 0;
$start_time = time();
$consecutive_429_count = 0;

// ============================================================================
// SISTEMA DE LOGGING MEJORADO
// ============================================================================

class Logger {
    private static $levels = [
        'DEBUG' => 0,
        'INFO' => 1,
        'WARNING' => 2,
        'ERROR' => 3,
        'CRITICAL' => 4
    ];

    private static function shouldLog($level) {
        return self::$levels[$level] >= self::$levels[LOG_LEVEL];
    }

    private static function ensureLogDirectory() {
        if (!file_exists(LOG_DIR)) {
            mkdir(LOG_DIR, 0755, true);
        }
    }

    private static function rotateLogIfNeeded() {
        if (!file_exists(LOG_FILE)) {
            return;
        }

        if (filesize(LOG_FILE) >= MAX_LOG_SIZE) {
            for ($i = MAX_LOG_FILES - 1; $i > 0; $i--) {
                $old = LOG_FILE . '.' . $i;
                $new = LOG_FILE . '.' . ($i + 1);
                if (file_exists($old)) {
                    if ($i == MAX_LOG_FILES - 1) {
                        unlink($old);
                    } else {
                        rename($old, $new);
                    }
                }
            }
            rename(LOG_FILE, LOG_FILE . '.1');
        }
    }

    public static function log($level, $message, $context = []) {
        if (!self::shouldLog($level)) {
            return;
        }

        try {
            self::ensureLogDirectory();
            self::rotateLogIfNeeded();

            $timestamp = date('Y-m-d H:i:s');
            $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
            $logLine = "[{$timestamp}] [{$level}] {$message}{$contextStr}\n";

            file_put_contents(LOG_FILE, $logLine, FILE_APPEND | LOCK_EX);

            if (php_sapi_name() === 'cli') {
                echo $logLine;
            }
        } catch (Exception $e) {
            error_log("Failed to write to log file: " . $e->getMessage());
        }
    }

    public static function debug($message, $context = []) {
        self::log('DEBUG', $message, $context);
    }

    public static function info($message, $context = []) {
        self::log('INFO', $message, $context);
    }

    public static function warning($message, $context = []) {
        self::log('WARNING', $message, $context);
    }

    public static function error($message, $context = []) {
        self::log('ERROR', $message, $context);
    }

    public static function critical($message, $context = []) {
        self::log('CRITICAL', $message, $context);
    }
}

// ============================================================================
// FUNCIONES DE BASE DE DATOS
// ============================================================================

function get_db_connection($retry = true) {
    $max_attempts = 3;
    $attempt = 0;

    while ($attempt < $max_attempts) {
        try {
            $conn = new PDO(DB_CONNECTION_STRING, DB_USERNAME, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_TIMEOUT, 30);

            Logger::debug("Conexión a base de datos establecida", ['intento' => $attempt + 1]);
            return $conn;
        } catch (PDOException $e) {
            $attempt++;
            Logger::error("Falló la conexión a base de datos", [
                'intento' => $attempt,
                'error' => $e->getMessage(),
                'codigo' => $e->getCode()
            ]);

            if ($attempt >= $max_attempts) {
                if ($retry) {
                    Logger::critical("Se alcanzó el máximo de intentos de conexión. Esperando 60s antes de reintentar.");
                    sleep(60);
                    return get_db_connection(false);
                } else {
                    Logger::critical("La conexión a base de datos falló permanentemente. El servicio no puede continuar.");
                    throw $e;
                }
            }
            sleep(5 * $attempt);
        }
    }

    return null;
}

function log_request($conn, $response_text, $http_status, $status_text, $content_length, $content_type, $server, $date, $error_message) {
    $max_attempts = 3;
    $attempt = 0;

    while ($attempt < $max_attempts) {
        try {
            $query = "INSERT INTO ApiRequestLog (RequestTime, ResponseText, HttpStatus, StatusText, ContentLength, ContentType, ServerHeader, DateHeader, ErrorMessage)
                      VALUES (GETDATE(), ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $response_text,
                $http_status,
                $status_text,
                $content_length,
                $content_type,
                $server,
                $date,
                $error_message
            ]);
            Logger::debug("Petición API registrada en base de datos");
            return true;
        } catch (PDOException $e) {
            $attempt++;
            Logger::error("Falló el registro de petición en base de datos", [
                'intento' => $attempt,
                'error' => $e->getMessage()
            ]);

            if ($attempt >= $max_attempts) {
                Logger::warning("No se pudo registrar la petición después del máximo de intentos. Continuando...");
                return false;
            }

            try {
                $conn = get_db_connection();
            } catch (Exception $reconnectError) {
                Logger::error("Falló la reconexión a base de datos", ['error' => $reconnectError->getMessage()]);
                return false;
            }
            sleep(2);
        }
    }

    return false;
}

function log_not_found($conn, $identificacion) {
    $max_attempts = 3;
    $attempt = 0;

    while ($attempt < $max_attempts) {
        try {
            $query = "IF NOT EXISTS (SELECT 1 FROM ClientesNoEncontrados WHERE Identificacion = ?)
                        INSERT INTO ClientesNoEncontrados (Identificacion, FechaIntento) VALUES (?, GETDATE())
                      ELSE
                        UPDATE ClientesNoEncontrados SET FechaIntento = GETDATE() WHERE Identificacion = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$identificacion, $identificacion, $identificacion]);
            Logger::info("No se encontró la identificación en Hacienda", ['identificacion' => $identificacion]);
            return true;
        } catch (PDOException $e) {
            $attempt++;
            Logger::error("Falló el registro de cliente no encontrado", [
                'identificacion' => $identificacion,
                'intento' => $attempt,
                'error' => $e->getMessage()
            ]);

            if ($attempt >= $max_attempts) {
                return false;
            }
            sleep(2);
        }
    }

    return false;
}

function update_client($conn, $identificacion, $actividad_principal) {
    $max_attempts = 3;
    $attempt = 0;

    while ($attempt < $max_attempts) {
        try {
            $query = "DECLARE @id NVARCHAR(50) = ?;
                      IF EXISTS (SELECT 1 FROM Clientes WHERE cedula = @id)
                      BEGIN
                          UPDATE CLIENTES
                          SET actividad_comercial = ?
                          WHERE cedula = @id;

                          DELETE FROM ClientesNoEncontrados WHERE Identificacion = @id;
                      END
                      ELSE
                      BEGIN
                          IF NOT EXISTS (SELECT 1 FROM ClientesNoEncontrados WHERE Identificacion = @id)
                          BEGIN
                              INSERT INTO ClientesNoEncontrados (Identificacion, FechaIntento)
                              VALUES (@id, GETDATE());
                          END
                          ELSE
                          BEGIN
                              UPDATE ClientesNoEncontrados
                              SET FechaIntento = GETDATE()
                              WHERE Identificacion = @id;
                          END
                      END";
            $stmt = $conn->prepare($query);
            $stmt->execute([$identificacion, $actividad_principal]);
            Logger::info("Cliente actualizado exitosamente", [
                'identificacion' => $identificacion,
                'actividad' => $actividad_principal
            ]);
            return true;
        } catch (PDOException $e) {
            $attempt++;
            Logger::error("Falló la actualización del cliente", [
                'identificacion' => $identificacion,
                'intento' => $attempt,
                'error' => $e->getMessage()
            ]);

            if ($attempt >= $max_attempts) {
                return false;
            }
            sleep(2);
        }
    }

    return false;
}

// ============================================================================
// FUNCIONES DE API
// ============================================================================

function fetch_api($identificacion) {
    global $request_count, $start_time, $consecutive_429_count;

    $retries = 0;
    while ($retries < MAX_RETRIES) {
        try {
            $ch = curl_init();
            if ($ch === false) {
                throw new Exception("Falló la inicialización de cURL");
            }

            curl_setopt($ch, CURLOPT_URL, API_URL . $identificacion);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            $response = curl_exec($ch);

            if ($response === false) {
                $curl_error = curl_error($ch);
                $curl_errno = curl_errno($ch);
                curl_close($ch);
                throw new Exception("cURL error ({$curl_errno}): {$curl_error}");
            }

            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $content_length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD) ?: 'Unknown';
            $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'Unknown';
            $headers = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
            $server = '';
            $date = '';

            foreach (explode("\r\n", $headers) as $header) {
                if (stripos($header, 'Server:') === 0) {
                    $server = trim(substr($header, strlen('Server:')));
                } elseif (stripos($header, 'Date:') === 0) {
                    $date = trim(substr($header, strlen('Date:')));
                }
            }

            curl_close($ch);
            $request_count++;

            if ($http_status == 429) {
                $retries++;
                $consecutive_429_count++;

                if ($consecutive_429_count == 1) {
                    $wait_time = 5;
                } elseif ($consecutive_429_count == 2) {
                    $wait_time = 10;
                } else {
                    $wait_time = 5;
                }

                Logger::warning("Límite de tasa alcanzado (429), contador consecutivo: {$consecutive_429_count}, esperando {$wait_time}s, reintento {$retries}/" . MAX_RETRIES, [
                    'identificacion' => $identificacion
                ]);
                sleep($wait_time);
                continue;
            }

            if ($http_status == 200) {
                $consecutive_429_count = 0;
                Logger::debug("Petición API exitosa", ['identificacion' => $identificacion]);
                return [
                    'body' => $body,
                    'http_status' => $http_status,
                    'status_text' => 'OK',
                    'content_length' => $content_length,
                    'content_type' => $content_type,
                    'server' => $server,
                    'date' => $date,
                    'error' => null
                ];
            }

            $consecutive_429_count = 0;
            Logger::warning("Petición API falló con estado {$http_status}", [
                'identificacion' => $identificacion
            ]);

            return [
                'body' => '',
                'http_status' => $http_status,
                'status_text' => 'Error',
                'content_length' => 'Unknown',
                'content_type' => 'Unknown',
                'server' => $server ?: 'Unknown',
                'date' => $date ?: 'Unknown',
                'error' => 'HTTP Error ' . $http_status
            ];

        } catch (Exception $e) {
            $retries++;
            Logger::error("Excepción en petición API, reintento {$retries}/" . MAX_RETRIES, [
                'identificacion' => $identificacion,
                'error' => $e->getMessage()
            ]);

            if ($retries >= MAX_RETRIES) {
                break;
            }
            sleep(5 * $retries);
        }
    }

    Logger::error("Petición API falló después de todos los reintentos", ['identificacion' => $identificacion]);
    return [
        'body' => '',
        'http_status' => -1,
        'status_text' => 'Error',
        'content_length' => 'Unknown',
        'content_type' => 'Unknown',
        'server' => 'Unknown',
        'date' => 'Unknown',
        'error' => 'Muchas consultas fallidas, se agotaron los reintentos'
    ];
}

function get_identificaciones($reprocess_all = false) {
    try {
        $conn = get_db_connection();

        if ($reprocess_all) {
            $query = "SELECT cedula FROM CLIENTES";
            Logger::info("Obteniendo TODOS los clientes para reprocesar (migración CIIU3 a CIIU4)");
        } else {
            $query = "SELECT cedula FROM CLIENTES WHERE LEN(isnull(actividad_comercial,''))=0 or actividad_comercial='000000' order by createdate desc";
            Logger::info("Obteniendo solo clientes sin código de actividad ordenados por fecha mas reciente de creacion");
        }

        $stmt = $conn->query($query);
        $identificaciones = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $conn = null;
        Logger::info("Identificaciones obtenidas", [
            'cantidad' => count($identificaciones),
            'reprocesar_todos' => $reprocess_all
        ]);
        return $identificaciones;
    } catch (Exception $e) {
        Logger::critical("Falló la obtención de identificaciones", ['error' => $e->getMessage()]);
        return [];
    }
}
function process_identificacion($identificacion) {
    try {
        $conn = get_db_connection();

        // Separador visual para inicio de procesamiento
        Logger::info("========================================");
        Logger::info("Procesando identificacion", ['identificacion' => $identificacion]);

        $response = fetch_api($identificacion);
        $response_text = $response['body'];
        $http_status = $response['http_status'];
        $status_text = $response['status_text'];
        $content_length = $response['content_length'];
        $content_type = $response['content_type'];
        $server = $response['server'];
        $date = $response['date'];
        $error_message = $response['error'];

        log_request($conn, $response_text, $http_status, $status_text, $content_length, $content_type, $server, $date, $error_message);

        if ($http_status == 200) {
            $data = json_decode($response_text, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['nombre'])) {
                $actividad_principal = null;
                foreach ($data['actividades'] ?? [] as $actividad) {
                    if ($actividad['tipo'] === 'P') {
                        $actividad_principal = $actividad['codigo'];
                        break;
                    }
                }
                if ($actividad_principal) {
                    update_client($conn, $identificacion, $actividad_principal);
                    Logger::info("✓ ÉXITO: Cliente actualizado correctamente", [
                        'identificacion' => $identificacion,
                        'actividad' => $actividad_principal
                    ]);
                } else {
                    Logger::warning("⚠ No se encontró actividad principal", ['identificacion' => $identificacion]);
                    log_not_found($conn, $identificacion);
                }
            } else {
                $error_message = json_last_error() === JSON_ERROR_NONE ? 'JSON no contiene el campo "nombre"' : 'Respuesta JSON inválida: ' . json_last_error_msg();
                Logger::warning("⚠ Respuesta API inválida", [
                    'identificacion' => $identificacion,
                    'error' => $error_message
                ]);
                log_not_found($conn, $identificacion);
            }
        } else {
            Logger::warning("✗ Cliente no encontrado en API Hacienda", [
                'identificacion' => $identificacion,
                'http_status' => $http_status
            ]);
            log_not_found($conn, $identificacion);
        }

        $conn = null;
        Logger::info("Finalizado procesamiento para: {$identificacion}");
        Logger::info("========================================\n");

        return true;

    } catch (Exception $e) {
        Logger::error("✗ ERROR: Excepción procesando identificación", [
            'identificacion' => $identificacion,
            'error' => $e->getMessage(),
            'traza' => $e->getTraceAsString()
        ]);

        try {
            $conn = get_db_connection();
            log_not_found($conn, $identificacion);
            log_request($conn, '', -1, 'Error', 'Unknown', 'Unknown', 'Unknown', 'Unknown', $e->getMessage());
            $conn = null;
        } catch (Exception $logError) {
            Logger::critical("Falló el registro del error en base de datos", ['error' => $logError->getMessage()]);
        }

        Logger::info("Finalizado procesamiento para: {$identificacion} (CON ERRORES)");
        Logger::info("========================================\n");

        return false;
    }
}
// ============================================================================
// PROCESO PRINCIPAL
// ============================================================================

// ============================================================================
// PROCESO PRINCIPAL
// ============================================================================

function main() {
    global $reprocess_all;

    Logger::info("=== Servicio iniciado ===", ['reprocesar_todos' => $reprocess_all]);

    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            Logger::critical("Error fatal detectado", [
                'tipo' => $error['type'],
                'mensaje' => $error['message'],
                'archivo' => $error['file'],
                'linea' => $error['line']
            ]);
        }
    });

    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $level = 'ERROR';
        if ($errno === E_WARNING || $errno === E_USER_WARNING) {
            $level = 'WARNING';
        }

        Logger::log($level, "Error PHP: {$errstr}", [
            'archivo' => $errfile,
            'linea' => $errline,
            'errno' => $errno
        ]);

        return true;
    });

    try {
        $identificaciones = get_identificaciones($reprocess_all);

        if (empty($identificaciones)) {
            Logger::info("No hay identificaciones para procesar. Finalizando.");
            return;
        }

        $total = count($identificaciones);
        $processed = 0;
        $errors = 0;

        if ($reprocess_all) {
            Logger::info("Iniciando REPROCESAMIENTO COMPLETO - migración CIIU3 a CIIU4", [
                'total_clientes' => $total,
                'advertencia' => 'Esto actualizará TODOS los clientes incluyendo aquellos con códigos existentes'
            ]);

            echo "\n!!! ADVERTENCIA: Ejecutando en modo REPROCESAR TODOS !!!\n";
            echo "Esto actualizará TODOS los {$total} clientes, incluyendo aquellos con códigos existentes.\n";
            echo "Iniciando en 5 segundos... (Presiona Ctrl+C para cancelar)\n\n";
            for ($i = 5; $i > 0; $i--) {
                echo "{$i}...\n";
                sleep(1);
            }
            echo "¡Iniciando ahora!\n\n";
        }

        foreach ($identificaciones as $index => $identificacion) {
            try {
                // Mostrar progreso general antes de procesar cada cliente
                $progressInfo = sprintf(
                    "PROGRESO: %d/%d (%.1f%%) | Errores: %d | Cliente actual: %s",
                    $processed + 1,
                    $total,
                    (($processed + 1) / $total) * 100,
                    $errors,
                    $identificacion
                );

                Logger::info($progressInfo);

                $result = process_identificacion((string)$identificacion);
                $processed++;

                if (!$result) {
                    $errors++;
                }

                // Resumen después del procesamiento
                $successRate = $processed > 0 ? round((($processed - $errors) / $processed) * 100, 1) : 0;
                Logger::info("RESUMEN PARCIAL", [
                    'procesados' => $processed,
                    'total' => $total,
                    'errores' => $errors,
                    'tasa_exito' => $successRate . '%',
                    'restantes' => $total - $processed
                ]);

                // Pausa pequeña entre clientes para mejor legibilidad
                if ($processed % 10 == 0) {
                    Logger::info("--- Pausa de 2 segundos cada 10 clientes ---");
                    sleep(2);
                }

            } catch (Exception $e) {
                $errors++;
                Logger::error("✗ EXCEPCIÓN NO MANEJADA en loop principal", [
                    'identificacion' => $identificacion,
                    'error' => $e->getMessage()
                ]);
                Logger::info("========================================\n");
                sleep(5);
            }
        }

        Logger::info("=== Servicio completado ===", [
            'total_procesados' => $processed,
            'total_errores' => $errors,
            'tasa_exito' => round((($processed - $errors) / $processed) * 100, 2) . '%',
            'modo' => $reprocess_all ? 'REPROCESAMIENTO COMPLETO (CIIU3→CIIU4)' : 'SOLO CLIENTES NUEVOS'
        ]);

    } catch (Exception $e) {
        Logger::critical("Excepción fatal en función main", [
            'error' => $e->getMessage(),
            'traza' => $e->getTraceAsString()
        ]);

        sleep(10);
    }
}



// Execute main function
if (php_sapi_name() === 'cli' || basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    main();
}

?>