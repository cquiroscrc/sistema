<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
          $factura = $_REQUEST['consecutivo'];      
    } 
    
     
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $id_codigos = $_REQUEST['codigo'];        
    }
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    } 
    if(isset($_REQUEST['subtotal']) && !empty($_REQUEST['subtotal'])){
          $subtotal = $_REQUEST['subtotal'];      
    } 
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
          $descuento = $_REQUEST['descuento'];      
    } 
    if(isset($_REQUEST['impuesto']) && !empty($_REQUEST['impuesto'])){
          $impuesto = $_REQUEST['impuesto'];      
    } 
    if(isset($_REQUEST['total']) && !empty($_REQUEST['total'])){
          $total = $_REQUEST['total'];      
    } 
    
  $sql = "DELETE FROM mcompras WHERE Codigo ='$id_codigos' AND Factura='$factura'";
    //echo $sql; exit;
   
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
   
      header("location: compras_factura.php?consecutivo=".$factura."&proveedor=".$proveedor."&subtotal=".$subtotal."&descuento=".$descuento."&impuesto=".$impuesto."&total=".$total."");
?>
