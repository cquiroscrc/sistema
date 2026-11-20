<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
    }

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura = $_REQUEST['Factura'];
    }
    
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************
      
    $sql="DELETE FROM hpregeneral WHERE codigo = '$codigo' and Factura = '$Factura'";    

      //// *************** Ejecuto la consulta ******************
    //var_dump($sql);         
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        $data["data"][]= $res_busqueda;
        //var_dump($res_busqueda);
        echo json_encode($data);

