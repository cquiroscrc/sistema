<?php 
    require("../cnx/conexion.php");
    require("../cnx/utilitaria.controlador.php");
    require("../cnx/utilitaria.modelo.php");

if(isset($_REQUEST['asociado']) && !empty($_REQUEST['asociado'])){
    $id = $_REQUEST['asociado'];
} 

$sql="SELECT * FROM ASO_Asociados WHERE Id=$id";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

echo utf8_decode(json_encode($ejecutar));





