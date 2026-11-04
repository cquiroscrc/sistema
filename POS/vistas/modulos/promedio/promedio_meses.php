<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['mes']) && !empty($_REQUEST['mes'])){
          $mes = $_REQUEST['mes'];
    }

    $sql = "UPDATE tbl_Utiles SET Valor = '".$mes."' WHERE IDUtilidad =5";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    echo "exito";
    echo $sql;

  
  