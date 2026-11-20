<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        if ($cliente=='contado') {
          $cliente=0;
        }
        

          $csubtotal=0;
          $cimpuesto=0;
          $ctotal=0;
          $sqlrelleno="SELECT m.ID,m.Factura,m.Codigo,m.Vendedor,m.Precio,m.Cantidad,m.Fecha,i.DESCRIPCION
            FROM tmp_factura m, inventario i WHERE Factura='".$consecutivo."' AND m.Codigo = I.CODIGO  ORDER BY ID";

                if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

                while($relleno = odbc_fetch_array($resultadorelleno)){
                 
                  $totalcod=$relleno['Cantidad']*$relleno['Precio'];
                  $csubtotal=$csubtotal+$totalcod;
                  
                }

                // calculo de la factura
                
                $cimpuesto=($csubtotal-$descuento)*0.13;
                $ctotal=$csubtotal-$descuento+$cimpuesto+$mensajeria;
         
          if ($tipo=="frecuente") {
            $tiponum=0;
          }elseif ($tipo=="contado") {
            $tiponum=0; 
          }elseif ($tipo=="credito") {
            $tiponum=1;
          } 
        $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time).".000";       
        $sql_Insert="INSERT INTO HFacturas(nfac,Nombre,Adicional,fechafact,SubTotal,impuesto,descuento,Total,Tipo,proveedor,vendedor,estado)
          values ('".$factura."','".$nombre."','','".$fecha_hg."','".$csubtotal."','".$cimpuesto."','".$descuento."','".$ctotal."','".$tiponum."','".$cliente."','".$vendedor."','APLICADA')"; 
          
        //echo $sql_Insert; 
        //exit;
        
        if(($r_insert = odbc_exec($id,$sql_Insert)) === false)
          die("Error en Select: ". odbc_errormsg($id));
    
        $var_continua=true;  
      
   
?>
