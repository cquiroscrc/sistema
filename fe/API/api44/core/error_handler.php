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
    throw new ErrorException($message, 0, $level, $file, $line);
});

//Para cuando es error grave de php y se sale de todo
register_shutdown_function(function () {
        $error = error_get_last();
    if ($error !== null) {
        $e = new ErrorException(
            $error['message'], 0, $error['type'], $error['file'], $error['line']
        );
        myExceptionHandler($e);
    }
});
