<?php
date_default_timezone_set("America/Costa_Rica");
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
      

   
    if((isset($_POST['factura']))and(!empty($_POST['factura']))){
        $factura = $_POST['factura'];
    }

    if((isset($_POST['fecha']))and(!empty($_POST['fecha']))){
        $fecha = $_POST['fecha'];
    }
    
    if((isset($_POST['motorizado']))and(!empty($_POST['motorizado']))){
        $motorizado = $_POST['motorizado'];
    }

    $sql="SELECT * FROM mandados WHERE factura ='$factura' and fecha='$fecha' and motorizado='$motorizado'";  
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($ejecutar as $key => $value) {
        $lugar=$value['lugar'];
      }

    $lugar=$lugar ."/ NULA";

    $sql = "UPDATE mandados  SET cantidad=0 , lugar='$lugar' 
                      WHERE factura ='$factura' and fecha='$fecha' and motorizado='$motorizado'";  


    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);


      
?>