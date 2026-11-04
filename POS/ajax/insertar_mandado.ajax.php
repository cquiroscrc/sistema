<?php
date_default_timezone_set("America/Costa_Rica");
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
      

    if((isset($_POST['envia']))and(!empty($_POST['envia']))){
        $envia = $_POST['envia'];
    }

    if((isset($_POST['factura']))and(!empty($_POST['factura']))){
        $factura = $_POST['factura'];
    }

    if((isset($_POST['monto']))and(!empty($_POST['monto']))){
        $monto = $_POST['monto'];
    }

    if((isset($_POST['fecha']))and(!empty($_POST['fecha']))){
        $fecha = $_POST['fecha'];
        $time = time();
        $fecha_hg= $fecha ." ". date("H:i:s", $time).".000";
    }
    
    if((isset($_POST['lugar']))and(!empty($_POST['lugar']))){
        $lugar = strtoupper($_POST['lugar']);
    }

    if((isset($_POST['cantidad']))and(!empty($_POST['cantidad']))){
        $cantidad = $_POST['cantidad'];
    }
    if((isset($_POST['motorizado']))and(!empty($_POST['motorizado']))){
        $motorizado = $_POST['motorizado'];
    }


        $inserta = "INSERT INTO mandados (envia,factura,monto,fecha,lugar,cantidad,motorizado) Values ('$envia' ,'$factura','$monto','$fecha_hg', '$lugar', $cantidad, $motorizado)";

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);


      
?>