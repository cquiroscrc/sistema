<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        
          
          $time = time();
          $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time);   

           // Carga Hgeneral y hacer carga de los codigos de la factura
            $sqldatos="SELECT * FROM hpregeneral WHERE Factura ='$prefactura'";  

            $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqldatos);

            for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {
            
                  $codigo= $respuesta[$i]['codigo'];
                  $cantidad=$respuesta[$i]['cantidad'];
                  //$tiponum=$arr_d['tipo'];
                  $precio=$respuesta[$i]['precio'];

                  $sqlcodigo="SELECT CODIGO,EXISTENCIA FROM inventario WHERE CODIGO ='$codigo'";  

                  $respuesta1 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcodigo);

                  for ( $j = sizeof($respuesta1) - 1; $j >= 0; $j--) {
                    $existencia=$respuesta1[$j]['EXISTENCIA'];


                  }
                  if ($codigo=='SP') {
                   $existencia=1;
                   $actual=1;
                  }else{
                    $actual=$existencia-$cantidad;
                  }
                  $sql_Insert="INSERT INTO hgeneral(Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,precio,estado) values ('".$factura."','".$fecha_hg."','".$codigo."',".$cantidad.",'Venta','".$tipo."','".$cliente."','".$maquina."','".$vendedor."',1,".$existencia.",".$actual.",".$precio.",'APLICADA')";

                  //echo $sql_Insert; 
                  //echo "<br>";
                  //exit;

                  $Inserta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Insert);

                  
                }

        
    
        $var_continua=true;  
      
   
?>
