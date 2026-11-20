<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");


          
   
$codcliente=0;


    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura =$_REQUEST['Factura']; 
    }

    
    if(isset($_REQUEST['codcliente']) && !empty($_REQUEST['codcliente'])){
      $codcliente = $_REQUEST['codcliente'];
      
    }
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
      $vendedor = $_REQUEST['vendedor'];
      
    }

    $consulta ="SELECT descuento FROM clientes WHERE codigo=$codcliente";
    $resultado=ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);
    foreach ($resultado as $key => $arr3) {   
              $ValDesc=$arr3["descuento"];
              
            }
    
    $sqlactualiza="UPDATE hpregeneral SET codcliente=$codcliente, vendedor='$vendedor' WHERE Factura='$Factura'";

      $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);
  
     echo $ValDesc; 
 

    //// *************** Recibe codigo ******************