<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");
    $Etiquetas=0;

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura =$_REQUEST['Factura']; 
    }

    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor =$_REQUEST['proveedor']; 
    }

    if(isset($_REQUEST['Codigo']) && !empty($_REQUEST['Codigo'])){
      $Codigo =$_REQUEST['Codigo']; 
    }
    echo $Codigo;
    if(isset($_REQUEST['Etiquetas']) && !empty($_REQUEST['Etiquetas'])){
      $Etiquetas = $_REQUEST['Etiquetas'];
    }
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
    }
    if(isset($_REQUEST['ID']) && !empty($_REQUEST['ID'])){
        $idproducto = $_REQUEST['ID'];
    }


  
    $sqlcompra="UPDATE hmcompras SET Etiquetas='$Etiquetas' WHERE Codigo='$Codigo' AND Factura='$Factura'";

    $sqlpedido="UPDATE Pedidos SET Etiquetas='$Etiquetas' WHERE ID='$idproducto' AND Factura='$Factura'";

    if ($tipo=='compra') {
      $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcompra);
      echo $sqlcompra;
    }else{
      $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlpedido);
      //echo $sqlpedido;
       // echo $tipo;
    }

  
     