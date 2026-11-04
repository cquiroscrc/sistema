<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
      
    }
     
     $codigo ='F-'.sprintf("%014s", $codigo);

      //// *************** Incluye la consulta ******************
      
    $sql="SELECT h.Factura,h.fecha,h.codigo, h.cantidad, h.precio, i.DESCRIPCION,  f.subtotal,f.impuesto,f.descuento,f.total,f.Tipo,f.estado,f.Nombre,f.adicional,f.sat,f.consecutivo, v.codigo AS codven, v.nombre AS nombreven, c.codigo AS codclie, c.nombre AS nomclie FROM hgeneral h, inventario i, hfacturas f, Vendedores v, clientes c WHERE h.codigo = i.CODIGO AND h.Factura = f.nfac AND h.codcliente = c.codigo AND FORMAT(cast( h.vendedor as numeric),'###')= FORMAT(cast( v.codigo as numeric),'###') AND h.Factura = 'F-00000000024688'";    

      //// *************** Ejecuto la consulta ******************
             
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        $data["data"][]= $res_busqueda;
        //var_dump($res_busqueda);
        echo json_encode($data);

