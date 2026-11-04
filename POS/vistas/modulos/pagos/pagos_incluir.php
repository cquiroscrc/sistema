<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    } 
    if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
          $factura = $_REQUEST['factura'];      
    }

  $sql_consulta ="SELECT * FROM cxp WHERE Proveedor ='".$proveedor."' AND NFac ='".$factura."'";

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consulta);
  foreach ($respuesta as $key => $arr) {
    $nfac=$arr['NFac'];
    $proveedor=$arr['Proveedor'];
    $fechaFact=$arr['FechaFact'];
    $total=$arr['Total'];
    $Cancelar=0;
    $cuentac=0;
    $subtotal=$arr['SubTotal'];
  }
  $sql = "INSERT INTO tmpCxP
            (nfac,proveedor,fechaFact,total,Cancelar,CuentaC,subtotal)
            values
            ('".$nfac."','".$proveedor."','".$fechaFact."','".$total."','".$Cancelar."','".$cuentac."','".$subtotal."')";
   
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  

?>
