<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    //
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
      
    }else{
      $codigo=0;
    }
     //var_dump($codigo);
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************
      
    $sql="SELECT * FROM clientes WHERE codigo = '$codigo'";    

      //// *************** Ejecuto la consulta ******************
             
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        $data["data"][]= $res_busqueda;
        //var_dump($res_busqueda);
        echo json_encode($data);

