<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
require("../modelos/conexion.php");
require("../controladores/utilitaria.controlador.php");
require("../modelos/utilitaria.modelo.php");




if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
  $ubicacion = $_REQUEST['ubicacion'];
}

$sqlactualiza="UPDATE inventario SET ubicacion='00X00' WHERE ubicacion='$ubicacion'";
$actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);
  
     