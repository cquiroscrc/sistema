<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
require("../cnx/conexion.php");
require("../cnx/utilitaria.controlador.php");
require("../cnx/utilitaria.modelo.php");



if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
  $nombre =$_REQUEST['nombre']; 
  $nombre=ucwords($nombre);
}

if(isset($_REQUEST['cedula']) && !empty($_REQUEST['cedula'])){
  $cedula =$_REQUEST['cedula']; 
}

if(isset($_REQUEST['telefono']) && !empty($_REQUEST['telefono'])){
  $telefono = $_REQUEST['telefono'];
}

if(isset($_REQUEST['correo']) && !empty($_REQUEST['correo'])){
  $correo = $_REQUEST['correo'];
}

if(isset($_REQUEST['inicio']) && !empty($_REQUEST['inicio'])){
  $inicio = $_REQUEST['inicio'];
}

$sqlactualiza="INSERT INTO ASO_Asociados (Nombre, Cedula, Telefono, Correo, Inicio) VALUES ('".$nombre."','".$cedula."','".$telefono."','".$correo."','".$inicio."')";
$actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);

echo "EXITO";
  
     