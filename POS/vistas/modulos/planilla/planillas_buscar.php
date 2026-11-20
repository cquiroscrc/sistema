<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
    $id_empleado = $_REQUEST['id'];
} 

$sql="SELECT * FROM rh_empleados WHERE id_empleado='$id_empleado'";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

echo utf8_decode(json_encode($ejecutar));





