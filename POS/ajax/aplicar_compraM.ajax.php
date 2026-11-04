<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");


    
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
        $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
        $proveedor = $_REQUEST['proveedor'];      
    } 
    if(isset($_REQUEST['subtotal']) && !empty($_REQUEST['subtotal'])){
        $subtotal = $_REQUEST['subtotal'];      
    }   
    $descuento=0;
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
        $descuento = $_REQUEST['descuento'];      
    } 
    $impuesto=0;
    if(isset($_REQUEST['impuesto']) && !empty($_REQUEST['impuesto'])){
        $impuesto = $_REQUEST['impuesto'];      
    } 
    if(isset($_REQUEST['total']) && !empty($_REQUEST['total'])){
        $total = $_REQUEST['total'];

    }
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];      
        $fecha= $fecha.' 00:00:00.000';
    }
    echo $fecha.'**FECHA**';
    $id_acceso=$_SESSION["id"];

    if ($proveedor==1) {
      $tipo='Contado';
      $dias='0';
      $newdate =$fecha.' 00:00:00.000';
      
      $estado='Cancelada';  
    } else{
      $tipo='Credito';
      $dias='30';
      //$fecha = date("Y-m-d h:i:s.m");
      $newdate = strtotime ( '+30 day' , strtotime ( $fecha ) ) ;
      $newdate = date ( 'Y-m-d h:i:s.m' , $newdate );
      $estado='Pendiente';  
    }

    if ($proveedor==9) {
      $tipo='Contado';
      $dias='0';
      $newdate = strtotime ( $fecha)  ;
      $fecha = strtotime ( $fecha)  ;
      
      $estado='Cancelada';  
    }


   
    
    //insertar la compra
  $sqlinsertcompra="INSERT INTO compras (Factura, proveedor,fecha,tipo,subtotal,impuesto,descuento,total) values
    ('".$consecutivo."','".$proveedor."','".$fecha."','".$tipo."','".$subtotal."','".$impuesto."','".$descuento."','".$total."')";

  //echo $sqlinsertcompra .'<br>';  
  $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlinsertcompra);
  
  // Insertar CXP
    $sql_cxp="INSERT INTO cxp (NFac,Proveedor,FechaFact,Tipo,DiasCredito,FechaLimite,SubTotal,Descuento,Impuesto,Total,Saldo,Estado,chkCancelacion)
    values ('".$consecutivo."','".$proveedor."','".$fecha."','".$tipo."','".$dias."','".$newdate."','".$subtotal."','".$descuento."','".$impuesto."','".$total."','".$total."','".$estado."','0')"; 
  //echo $sql_cxp.'<br>';

  $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cxp);
  

  

    
?>
