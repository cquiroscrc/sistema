<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    } 
    if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
          $factura = $_REQUEST['factura'];      
    }

   $sql = "DELETE FROM tmpCxP WHERE nfac ='$factura' AND proveedor='$proveedor' ";

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  ?>
