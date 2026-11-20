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

  $sql = "UPDATE tbl_Cuentas set Banco='$banco', Cuenta='$cuenta',Descripcion='$descripcion',Moneda='$moneda' WHERE Cuenta='$cuenta'";

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

  echo "exito";
