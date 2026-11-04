<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];
    }
    if(isset($_REQUEST['id_empleado']) && !empty($_REQUEST['id_empleado'])){
          $id_empleado = $_REQUEST['id_empleado'];
    }
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
        $cantidad = $_REQUEST['cantidad'];
    }

    $sql="INSERT INTO vacaciones (id_empleado,fecha,cantidad)
    values ('$id_empleado','$fecha','$cantidad')";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

  echo "exito";
