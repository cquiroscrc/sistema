<?php
session_start();

require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if((isset($_REQUEST['id_empleado']))&&(!empty($_REQUEST['id_empleado']))){
    $id_empleado =$_REQUEST['id_empleado'];
	
}
if((isset($_REQUEST['fecha']))&&(!empty($_REQUEST['fecha']))){
    $fecha =$_REQUEST['fecha'];
}


$sql = "DELETE FROM vacaciones WHERE id_empleado='$id_empleado' AND fecha='$fecha'";

$ejecutar2 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

echo 'exito';