<?php


error_reporting(E_ALL);

function myExceptionHandler($e)
{
    Bitacora("",$e);
    grace_error($e);
    error_log($e);
    http_response_code(500);
    if (ini_get('display_errors')) {
        echo $e;
    } else {
        echo "<h1>Error interno del sistema</h1>
              Un error interno ha ocurrido, reintente por favor.<br>";
    }
}

set_exception_handler('myExceptionHandler');

set_error_handler(function ($level, $message, $file = '', $line = 0) {
    // PHP 8.x: E_DEPRECATED no debe abortar el proceso (solo se registra en el log de PHP)
    if ($level === E_DEPRECATED || $level === E_USER_DEPRECATED) {
        return false;
    }
    throw new ErrorException($message, 0, $level, $file, $line);
});

//Para cuando es error grave de php y se sale de todo
register_shutdown_function(function () {
        $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        $e = new ErrorException(
            $error['message'], 0, $error['type'], $error['file'], $error['line']
        );
        myExceptionHandler($e);
    }
});
