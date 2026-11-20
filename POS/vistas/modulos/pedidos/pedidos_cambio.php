<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");
    $precio=0;

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura =$_REQUEST['Factura']; 
    }

    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor =$_REQUEST['proveedor']; 
    }

    if(isset($_REQUEST['Codigo']) && !empty($_REQUEST['Codigo'])){
      $Codigo =$_REQUEST['Codigo']; 
    }

    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
      $precio = $_REQUEST['precio'];
    }

    $sqlpedido="UPDATE Pedidos SET Sugerido='$precio' WHERE Codigo='$Codigo' AND Factura='$Factura' AND Proveedor='$proveedor'";

    $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlpedido);


  
     