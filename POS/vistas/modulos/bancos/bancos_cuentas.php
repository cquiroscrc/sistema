<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['banco']) && !empty($_REQUEST['banco'])){
        $banco = $_REQUEST['banco'];
    }
    if(isset($_REQUEST['cuenta']) && !empty($_REQUEST['cuenta'])){
        $cuenta = $_REQUEST['cuenta'];
    }
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion'];
    }
    if(isset($_REQUEST['moneda']) && !empty($_REQUEST['moneda'])){
        $moneda = $_REQUEST['moneda'];
    }
 
  $sql= "SELECT * FROM tbl_Cuentas WHERE Cuenta='".$cuenta."'";
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


         $sql = "INSERT INTO tbl_Cuentas (Banco,Cuenta,Descripcion,Saldo,Moneda)
            values ('".$banco."','".$cuenta."','".$descripcion."','0','".$moneda."')";

          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          echo "exito";



  }
  
  