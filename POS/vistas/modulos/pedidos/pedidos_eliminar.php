<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['idcodigo']) && !empty($_REQUEST['idcodigo'])){
          $idcodigo = $_REQUEST['idcodigo'];
    }

$sql = "DELETE FROM Pedidos WHERE ID ='$idcodigo'";
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  

?>
