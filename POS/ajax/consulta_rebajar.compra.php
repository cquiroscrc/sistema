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
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];
    }
    
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************
      
    $sql="DELETE FROM mcompras WHERE Codigo = '$codigo' and Factura = '$Factura' and Proveedor='$proveedor'";    
      //// *************** Ejecuto la consulta ******************
    //var_dump($sql);         
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
       
       echo 'exito';

