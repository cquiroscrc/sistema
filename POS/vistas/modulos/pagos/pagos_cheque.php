<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['cuentaBancaria']) && !empty($_REQUEST['cuentaBancaria'])){
          $cuentaBancaria = $_REQUEST['cuentaBancaria'];      
    } 
    if(isset($_REQUEST['cuentaContable']) && !empty($_REQUEST['cuentaContable'])){
          $cuentaContable = $_REQUEST['cuentaContable'];      
    }
    if(isset($_REQUEST['fechaChk']) && !empty($_REQUEST['fechaChk'])){
          $fechaChk = $_REQUEST['fechaChk'];      
    }
    if(isset($_REQUEST['numeroChk']) && !empty($_REQUEST['numeroChk'])){
          $numeroChk = $_REQUEST['numeroChk'];      
    }

  $sql="SELECT * FROM tmpCxP order by proveedor,nfac,fechaFact"; 
  $proveedor='';
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  foreach ($respuesta as $key => $arr) {
      $factura=$arr['nfac'];

      if ($proveedor=='') {
        $proveedor=$arr['proveedor'];
      }

      if ($proveedor!=$arr['proveedor']) {
        
        $proveedor=$arr['proveedor'];
        $numeroChk=$numeroChk+1;

      }  
    
    
    $sql_update="UPDATE tmpCxP SET Cuenta='$cuentaBancaria', CuentaC='$cuentaContable', fechaCKS='$fechaChk', chkAsociado='$numeroChk' WHERE nfac='$factura'";
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);
    
  }
  

?>
