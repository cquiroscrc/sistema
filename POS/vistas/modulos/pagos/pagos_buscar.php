<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");
 
    if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
          $factura = $_REQUEST['factura']; 
    }


  $sql= "SELECT * FROM cxp WHERE NFac like'%".$factura."'";
  //echo $sql;

  
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    $data["data"][]= $respuesta;
    echo json_encode($data);
  
  
  