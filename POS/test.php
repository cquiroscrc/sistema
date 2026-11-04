<?php
header('Content-Type: text/plain');
echo "Ruta real: " . realpath(__DIR__) . "\n";
echo "Usuario: " . get_current_user() . "\n";
echo "Permisos: ";
var_dump(is_readable(__FILE__));
?>