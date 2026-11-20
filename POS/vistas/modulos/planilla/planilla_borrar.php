<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['planilla']) && !empty($_REQUEST['planilla'])){
        $planilla = $_REQUEST['planilla'];
    }
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];
    }

    $sql = "DELETE FROM rh_planilla where id_planilla ='$planilla' and fecha ='$fecha'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

  echo "exito";
