<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
      
    }
     //var_dump($codigo);
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************
      
    $sql="SELECT * FROM hpregeneral WHERE Factura = '$codigo' ORDER BY Id DESC";    

      //// *************** Ejecuto la consulta ******************
             
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        $data["data"][]= $res_busqueda;
        //var_dump($res_busqueda);
        echo json_encode($data);

