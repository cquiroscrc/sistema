<?php

$hostname = 'localhost';
$dbuser = 'root';
$dbpassword = '';
$dbname = 'inventario';

/*
$hostname = 'localhost';
$dbuser = 'root';
$dbpassword = '';
$dbname = 'inventario';
*/
$db_link = mysql_connect(
                $hostname,
                $dbuser,
                $dbpassword) or
                die ('No se pudo conectar');
    mysql_select_db($dbname) or
        die('No se pudo conectar a BD');
?>


