<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura =$_REQUEST['Factura']; 
    }

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];      
    }
    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
      $precio = $_REQUEST['precio']; 
    }

    //var_dump($codigo);
    $codigo=strtoupper($codigo);  
    //// *************** Incluye la consulta ******************
    
    $sqlesta="SELECT VENTA, potencia FROM inventario WHERE CODIGO = '$codigo'";
    $esta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlesta);
    foreach ($esta as $key => $value) {

          $venta=$value["VENTA"]+($value["potencia"]*100);
          
          if ($precio>$venta) {

                $sqlactualiza="UPDATE hpregeneral SET precio=$precio WHERE Factura='$Factura' AND CODIGO = '$codigo'";
                $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);

                echo 'OK';
 
              
          }else{
                $sqlactualiza="UPDATE hpregeneral SET precio=$venta WHERE Factura='$Factura' AND CODIGO = '$codigo'";
                $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);
              echo 'error';
          }
           
    }

    