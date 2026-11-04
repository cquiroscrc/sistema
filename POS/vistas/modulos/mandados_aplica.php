<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");


if(isset($_REQUEST['motorizado']) && !empty($_REQUEST['motorizado'])){
  $motorizado=$_REQUEST['motorizado']; 
}


$sql= "UPDATE mandados SET aplicado='1' WHERE motorizado='$motorizado' AND aplicado is null";
//echo $sql;
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);


