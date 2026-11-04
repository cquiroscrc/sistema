<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        

          $time = time();
          $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time);   

           // Carga Hgeneral y hacer carga de los codigos de la factura
            $sqldatos="SELECT * FROM hgeneral WHERE Factura ='$documento'";  

            $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqldatos);

           foreach ($respuesta as $key => $arr) {
                  $codigo= $arr['codigo'];
                  $cantidad=$arr['cantidad'];
                  $tiponum=$arr['tipo'];
                  $cliente=$arr['codcliente'];
                  $vendedor=$arr['vendedor'];
                  $precio=$arr['precio'];

                  $sqlcodigo="SELECT CODIGO,EXISTENCIA FROM inventario WHERE CODIGO ='$codigo'";  

                  $respuesta1 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcodigo);

                  foreach ($respuesta1 as $key => $value) {
                    $existencia=$value['EXISTENCIA'];

                  }

                  if ($codigo=='SP') {
                   $existencia=1;
                   $actual=1;
                  }else{
                    $actual=$existencia+$cantidad;
                  }
                  $sql_Insert="INSERT INTO hgeneral(Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,precio,estado) values ('".$factura."','".$fecha_hg."','".$codigo."',".$cantidad.",'DEVOLUCION','".$tiponum."','".$cliente."','".$maquina."','".$vendedor."',1,".$existencia.",".$actual.",".$precio.",'APLICADA')";

                  echo $sql_Insert; 
                  //echo "<br>";
                  //exit;

                 $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Insert);
                  
                }

        
    
        $var_continua=true;  
      
   
?>
