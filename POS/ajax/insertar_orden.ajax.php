<?php

    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
      

    if((isset($_POST['empresa']))and(!empty($_POST['empresa']))){
        $empresa = strtoupper($_POST['empresa']);
    }

    if((isset($_POST['cantidad']))and(!empty($_POST['cantidad']))){
        $cantidad = $_POST['cantidad'];
    }

    if((isset($_POST['descripcion']))and(!empty($_POST['descripcion']))){
        $descripcion = strtoupper($_POST['descripcion']);
    }

    if((isset($_POST['consecutivo']))and(!empty($_POST['consecutivo']))){
        $consecutivo = strtoupper($_POST['consecutivo']);
    }


        $inserta = "INSERT INTO tmp_OC (empresa,cantidad,descripcion,consecutivo) Values ('$empresa' ,'$cantidad','$descripcion','$consecutivo')";

      

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);

      
?>