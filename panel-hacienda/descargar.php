<?php
// Descarga en streaming de respaldos de inventario. nginx lo atiende con el pool FPM panel-descargas
// (sin request_terminate_timeout), porque un .bak de ~12 GB tarda mas que el limite del pool general.
declare(strict_types=1);
require __DIR__ . '/lib.php';
require_once __DIR__ . '/tareas/tareas-lib.php';
try {
    ph_require_login();
    ph_respaldo_descargar();
} catch (Throwable $e) {
    error_log('panel-hacienda descargar: ' . $e->getMessage());
    if (!headers_sent()) ph_json(['ok' => false, 'error' => 'Error en la descarga'], 500);
}
