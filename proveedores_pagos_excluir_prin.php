<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
          $datos = $_REQUEST['datos'];      
    } 
    if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
          $factura = $_REQUEST['factura'];      
    } 
  
  $sql = "DELETE FROM tmpCxP WHERE nfac ='$factura' AND proveedor='$datos' ";
  

  if(($res = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

  header("location: proveedores_pagos.php"); 
?>
