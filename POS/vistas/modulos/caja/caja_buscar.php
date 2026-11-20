<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");
 
    if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
          $factura = $_REQUEST['factura']; 
    }

  $factura ='F-'.sprintf("%014s",   $factura);
  $sql= "SELECT * FROM hfacturas WHERE nfac='".$factura."'";  


  
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    $data["data"][]= $respuesta;
    echo json_encode($data);
  
  
  