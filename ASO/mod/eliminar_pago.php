<?php
  require("../cnx/conexion.php");
  require("../cnx/utilitaria.controlador.php");
  require("../cnx/utilitaria.modelo.php");

  
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
        $id = $_REQUEST['id'];
    }

    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
        $monto = $_REQUEST['monto'];
    }

    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];
    }

    $sql = "DELETE FROM ASO_Cuotas where Id ='$id' AND Monto='$monto' AND Fecha='$fecha'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

  echo "EXITO";
