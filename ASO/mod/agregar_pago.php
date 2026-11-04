<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
require("../cnx/conexion.php");
require("../cnx/utilitaria.controlador.php");
require("../cnx/utilitaria.modelo.php");


if(isset($_REQUEST['asociado']) && !empty($_REQUEST['asociado'])){
  $codigo =$_REQUEST['asociado']; 
}

if(isset($_REQUEST['pago']) && !empty($_REQUEST['pago'])){
  $pago = $_REQUEST['pago'];
}

if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
  $fecha = $_REQUEST['fecha'];
}

$sqlactualiza="INSERT INTO ASO_Cuotas (Id,Monto,Saldo,Fecha) VALUES (".$codigo.",".$pago.",0,'".$fecha."')";
$actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);

echo "EXITO";
  
     