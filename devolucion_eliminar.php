<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
        $consecutivo = $_REQUEST['consecutivo'];      
    } 
    $vendedor = $_REQUEST['vendedor'];      
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
        $vendedor = $_REQUEST['vendedor'];      
    } 
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];      
    } 
    $cliente = $_REQUEST['cliente'];    
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
        $cliente = $_REQUEST['cliente'];      
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

  $sql = "DELETE FROM mcompras WHERE codigo ='$codigo' AND Proveedor='$vendedor'";
    //echo $sql; exit;
   
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
   
      header("location: devolucion_factura.php?consecutivo=".$consecutivo."&vendedor=".$vendedor."&cliente=".$cliente."&fecha=".$fecha."&subtotal=".$subtotal."&descuento="
      .$descuento."&impuesto=".$impuesto."&total=".$total."");
?>
