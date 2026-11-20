<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
          $datos = $_REQUEST['datos'];      
    } 
    if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
          $factura = $_REQUEST['factura'];      
    } 
  $sql_consulta ="SELECT * FROM cxp WHERE Proveedor ='".$datos."' AND NFac ='".$factura."'";

  if(($res = odbc_exec($id,$sql_consulta)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  while($arr = odbc_fetch_array($res)){
    $nfac=$arr['NFac'];
    $proveedor=$arr['Proveedor'];
    $fechaFact=$arr['FechaFact'];
    $total=$arr['Total'];
    $cancelar=0;
    $cuentac=0;
    $subtotal=$arr['SubTotal'];
  }
  $sql = "INSERT INTO tmpCxP
            (nfac,proveedor,fechaFact,total,Cancelar,CuentaC,subtotal)
            values
            ('".$nfac."','".$proveedor."','".$fechaFact."','".$total."','".$Cancelar."','".$cuentac."','".$subtotal."')";
   
  if(($res = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

  header("location: proveedores_pagos_resultados.php?datos=".$datos); 
?>
