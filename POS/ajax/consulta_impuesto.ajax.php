<?php

    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

      
    $sql="SELECT valor FROM tbl_Utiles WHERE IDUtilidad =4";    

      //// *************** Ejecuto la consulta ******************
             
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        
        echo $respuesta[0]['valor'];
