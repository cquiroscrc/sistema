<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        if ($cliente=='contado') {
          $cliente=0;
        }
        

          $csubtotal=0;
          $cimpuesto=0;
          $ctotal=0;
          if ($tipo=="frecuente") {
            $tiponum=0;
          }elseif ($tipo=="contado") {
            $tiponum=0; 
          }elseif ($tipo=="credito") {
            $tiponum=1;
          } 

          $time = time();
          $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time).".000";  

          $sqlrelleno="SELECT m.ID,m.Factura,m.Codigo,m.Vendedor,m.Precio,m.Cantidad,m.Fecha,i.DESCRIPCION,i.EXISTENCIA FROM tmp_factura m, inventario i WHERE Factura='".$consecutivo."' AND m.Codigo = I.CODIGO  ORDER BY ID";

                if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

                while($relleno = odbc_fetch_array($resultadorelleno)){
                  $codigo=$relleno["Codigo"];
                  $cantidad=$relleno["Cantidad"];
                  $existencia=$relleno["EXISTENCIA"];
                  $actual=$existencia-$cantidad;
                  $precio=$relleno["Precio"];
                  $totalcod=$cantidad*$precio;
                  $csubtotal=$csubtotal+$totalcod;
                  
                  $sql_Insert = "UPDATE INVENTARIO SET EXISTENCIA=".$actual." WHERE CODIGO='".$codigo."'"; 
          
                  //echo $sql_Insert; 
                  //echo "<br>";
                  //exit;
                  
                  if(($r_insert = odbc_exec($id,$sql_Insert)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

                  
                }

                // calculo de la factura
                
                $cimpuesto=($csubtotal-$descuento)*0.13;
                $ctotal=$csubtotal-$descuento+$cimpuesto+$mensajeria;
         
         
              
        
    
        $var_continua=true;  
      
   
?>
