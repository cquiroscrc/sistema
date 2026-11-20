<?php
date_default_timezone_set("America/Costa_Rica");
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
      

    if((isset($_POST['Envia']))and(!empty($_POST['Envia']))){
        $envia = $_POST['Envia'];
    }

    if((isset($_POST['Recibe']))and(!empty($_POST['Recibe']))){
        $recibe = $_POST['Recibe'];
    }

    if((isset($_POST['mensaje']))and(!empty($_POST['mensaje']))){
        $mensaje = $_POST['mensaje'];
    }
    if((isset($_POST['asunto']))and(!empty($_POST['asunto']))){
        $asunto = $_POST['asunto'];
    }

    if ($recibe=='todos') {
        $sql="SELECT * FROM acc_usuarios";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        foreach ($ejecutar as $key => $valor) {
            $vaRecibir=$valor['usuario'];
            $fecha =date("Y-m-d h:i:s.m");
            $inserta = "INSERT INTO Mensajes (Detalle,Envia,Recibe,Estado,Asunto,Fecha) Values ('$mensaje' ,'$envia','$vaRecibir','SL', '$asunto', '$fecha')";

            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);

        }
    }else{
        $fecha =date("Y-m-d h:i:s.m");
        $inserta = "INSERT INTO Mensajes (Detalle,Envia,Recibe,Estado,Asunto,Fecha) Values ('$mensaje' ,'$envia','$recibe','SL', '$asunto', '$fecha')";

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);
        //echo $inserta;

    }


?>