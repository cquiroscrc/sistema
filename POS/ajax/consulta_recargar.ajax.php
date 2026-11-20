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
      
    $sql="SELECT h.codigo, h.cantidad, h.precio, h.codcliente, h.vendedor, i.DESCRIPCION, i.DESC_ALT FROM hpregeneral h, inventario i WHERE h.Factura = '$codigo' AND h.codigo = i.CODIGO ORDER BY h.Id DESC";    

      //// *************** Ejecuto la consulta ******************
             
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        $data["data"][]= $res_busqueda;
        //var_dump($res_busqueda);
        echo json_encode($data);

