<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");

    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2'];      
    } 
   
    if(isset($_REQUEST['recibo']) && !empty($_REQUEST['recibo'])){
          $recibo = $_REQUEST['recibo'];      
    }
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
          $cliente = $_REQUEST['cliente'];      
    }
    if ($_REQUEST['cliente']==0) {
      $cliente=$cliente = $_REQUEST['cliente'];
    }

  $sql = "DELETE FROM cxc WHERE NFac ='$recibo' AND IDCliente='$cliente'";
  //echo $sql; exit;
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
      
    
    
?>