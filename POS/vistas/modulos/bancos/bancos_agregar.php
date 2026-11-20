<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");
    $montoi=0;
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
        $tipo = $_REQUEST['tipo'];
    }
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
        $Date2 = $_REQUEST['Date2'];
    }
    if(isset($_REQUEST['cuentas']) && !empty($_REQUEST['cuentas'])){
          $cuentas = $_REQUEST['cuentas'];      
    }
    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
        $documento = $_REQUEST['documento'];
    }
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto'];      
    }
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion'];
    }
    if(isset($_REQUEST['cuenta2']) && !empty($_REQUEST['cuenta2'])){
        $cuenta2 = $_REQUEST['cuenta2'];
    }
    if(isset($_REQUEST['montoi']) && !empty($_REQUEST['montoi'])){
        $montoi = $_REQUEST['montoi'];
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

      if ($cuenta2=='ninguna') {

          if ($tipo=='tingreso') {
              $ttipo=0;
              $ingreso=$monto;
              $egreso=0;
          }elseif ($tipo=='oingreso') {
              $ttipo=0;
              $ingreso=$monto;
              $egreso=0;
          }elseif ($tipo=='tegreso') {
              $ttipo=1;
              $ingreso=0;
              $egreso=$monto;
          }elseif ($tipo=='oegreso') {
              $ttipo=1;
              $ingreso=0;
              $egreso=$monto;
          }

          $sql = "INSERT INTO tbl_Bancos (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC)
            values ('".$Date2."','".$cuentas."','".$documento."','".$descripcion."','".$ingreso."','".$egreso."','1900-01-01 00:00:00.000','0','','0','')";

          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          echo "exito";
      }else{

          $sql_salida = "INSERT INTO tbl_Bancos (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC)
            values ('".$Date2."','".$cuentas."','".$documento."','".$descripcion."','0','".$monto."','1900-01-01 00:00:00.000','0','','0','')";

          $sql_entrada = "INSERT INTO tbl_Bancos (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC)
            values ('".$Date2."','".$cuenta2."','".$documento."','".$descripcion."','".$montoi."','0','1900-01-01 00:00:00.000','0','','0','')";

          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_salida);
          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_entrada);
          echo "exito";
      }


  }
  
  