<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura = $_REQUEST['Factura'];
    }   
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];
    }   
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
    }   

$sqlpedido="SELECT p.Codigo,i.DESCRIPCION,p.Proveedor,p.Factura,p.Cantidad,p.Etiquetas,p.Fecha,i.Ubicacion,p.Caja, substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1)WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))Else p.codigo END As Filtro FROM Pedidos as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE Factura='$Factura' ORDER BY Filtro";

$sqlcompra = "SELECT p.Codigo,i.DESCRIPCION,p.Proveedor,p.Factura,p.Cantidad,p.Etiquetas,p.Fecha,i.Ubicacion,p.Caja,substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1) WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 ))) WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))Else p.codigo END As Filtro FROM hmcompras as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE p.Factura='$Factura' ORDER BY Filtro";


          

          if ($tipo=='compra') {
            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcompra);
          }else{
            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlpedido);  
          }

          $i=1;
          foreach ($ejecutar as $key => $arr) {
            $codigos=$arr["Codigo"];
            $descripcion=$arr["DESCRIPCION"];           
            $proveedor=$arr["Proveedor"];
            $factura=$arr["Factura"];
            $cantidad=$arr["Cantidad"];
            $fecha=$arr["Fecha"];
            $ubicacion=$arr["Ubicacion"];
            $date_now = date('dmy');
            $detalle= $proveedor.'-'.$factura.'-'.$date_now.'-'.number_format($cantidad,0).'-'.$ubicacion;  
            $etiquetas=$arr["Etiquetas"];

            $imprimir='^XA^CF0,36^FO120,50^FDREPUESTOS LOS PATICAS^FS';

            $tamdescripcion=strlen ($codigos);
            $tamdescripcion=50-intval($tamdescripcion);
            $tamdescripcion=intval($tamdescripcion)/2;
            $tamdescripcion=$tamdescripcion*10;
            $pos=20+intval($tamdescripcion);

            $imprimir=$imprimir.'^FO'.$pos.',90^FD'.$codigos.'^FS';
            
            $tamdescripcion=strlen ($descripcion);

            if ($tamdescripcion<38) {
            
              $tamdescripcion=38-intval($tamdescripcion);
              $tamdescripcion=intval($tamdescripcion)/2;
              $tamdescripcion=$tamdescripcion*10;
              $pos=55+intval($tamdescripcion);
            
            }else{
              $pos=55;
            }

            $descripcion1= substr($descripcion,0,38);
            $imprimir=$imprimir.'^FO'.$pos.',130^CF0,25^FD'.$descripcion1.'^FS';
            
            if (strlen ($descripcion)<38) {
              $descripcion2='';
            }else{
              $descripcion2= substr($descripcion,39,76);
              $tamdescripcion=strlen ($descripcion2);

              if ($tamdescripcion<38) {
              
                $tamdescripcion=38-intval($tamdescripcion);
                $tamdescripcion=intval($tamdescripcion)/2;
                $tamdescripcion=$tamdescripcion*10;
                $pos=55+intval($tamdescripcion);
              
              }else{
                $pos=55;
              }
            }
            $imprimir=$imprimir.'^FO'.$pos.',160^FD'.$descripcion2.'^FS';
            $imprimir=$imprimir.'^FO150,193^CF0,23^FD'.$detalle.'^FS^XZ';
            for ($i=0; $i < $etiquetas ; $i++) {
             print_r($imprimir);

            }
            

          }

   



