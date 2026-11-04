<?php
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura = $_REQUEST['Factura'];
    }
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];
    }
    
  
      //// *************** Incluye la consulta ******************

    $sql="SELECT * FROM mcompras WHERE  Factura = '$Factura' and Proveedor='$proveedor'";    
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
      foreach ($respuesta as $key => $value) {
          echo 'TERMINAR';
       }

      
    $sql="SELECT * FROM compras WHERE  Factura = '$Factura' and proveedor='$proveedor'";    
      //// *************** Ejecuto la consulta ******************
    //var_dump($sql);         
    //echo $sql;
      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
      foreach ($respuesta as $key => $value) {
          echo 'ENTRO';
       }
    


