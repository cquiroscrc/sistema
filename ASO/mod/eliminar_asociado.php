<?php
  require("../cnx/conexion.php");
  require("../cnx/utilitaria.controlador.php");
  require("../cnx/utilitaria.modelo.php");

  
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
        $id = $_REQUEST['id'];
    }

    $sql = "DELETE FROM ASO_Asociados where Id ='$id'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

  echo "EXITO";
