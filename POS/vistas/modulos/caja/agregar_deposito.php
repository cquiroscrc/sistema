<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['cuentas']) && !empty($_REQUEST['cuentas'])){
          $cuentas = $_REQUEST['cuentas'];      
    }
    if ($_REQUEST['cuentas']==0) {
      $cuentas = $_REQUEST['cuentas'];
    } 
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto'];      
    } 
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2']; 
    }
    
    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $documento = $_REQUEST['documento']; 
    }
 
  $sql= "SELECT * FROM tbl_Bancos WHERE Cuenta='".$cuentas."' AND Documento='".$documento."'";  
  $existe='no';

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  foreach ($respuesta as $key => $value) {
    $existe="si";
  }
 //echo $sql2; 
  if ($existe=='si') {
     echo "existe";
  }
  
  if ($existe=='no') {
        

    $sql = "INSERT INTO tbl_Bancos
            (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC)
            values
            ('".$Date2."','".$cuentas."','".$documento."','Caja del ".$Date2."','".$monto."','0','1900-01-01 00:00:00.000','0','','0','')";
  
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    echo "exito";      
  }
  
  