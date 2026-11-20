<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
      
    }
     
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************
      
    $sql="SELECT c.Codigo, c.Cantidad, c.Precio, c.PrecioSugerido,c.Proveedor, i.DESCRIPCION, i.DESC_ALT FROM mcompras c, inventario i WHERE c.Factura = '$codigo' AND C.Codigo = i.CODIGO ORDER BY C.ID DESC";    
   
      //// *************** Ejecuto la consulta ******************
             
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        $data["data"][]= $res_busqueda;
        //var_dump($res_busqueda);
        echo json_encode($data);

