<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
require("../cnx/conexion.php");
require("../cnx/utilitaria.controlador.php");
require("../cnx/utilitaria.modelo.php");

if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
  $id =$_REQUEST['id']; 

}

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

$sqlactualiza="UPDATE ASO_Asociados SET Nombre='$nombre', Cedula='$cedula', Telefono='$telefono', Correo='$correo', Inicio='$inicio' 
WHERE Id=$id";
$actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);

echo "EXITO";
  
     