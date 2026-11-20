<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo =$_REQUEST['codigo'];
    }

    if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
      $nombre = $_REQUEST['nombre'];
    }

    $sql="UPDATE Vendedores SET nombre='$nombre' WHERE codigo = '$codigo'";
    $esta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    echo $sql;


    