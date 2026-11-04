<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
      $descuento =$_REQUEST['descuento'];
    }

    $sql = "UPDATE tbl_Utiles SET Valor='$descuento' WHERE IDUtilidad =2";

    $esta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);


    