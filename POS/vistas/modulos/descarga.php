<?php


if (!isset($_GET['archivo']) || empty($_GET['archivo'])) {
    exit();
}
$ruta='../';
$ruta = $ruta.$_GET['archivo'];

$archivo = basename($_GET['archivo']);


if (is_file($ruta))
{
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename='.$archivo);
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.filesize($ruta));

readfile($ruta);
}else{
    exit();
}
?>