<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        

          $time = time();
          $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time);   

           // Carga Hgeneral y hacer carga de los codigos de la factura
            $sqldatos="SELECT * FROM hgeneral WHERE Factura ='$documento'";  

            if(($res_datos = odbc_exec($id,$sqldatos)) === false)
                die("Error en Select: ". odbc_errormsg($id));

            while($arr_d = odbc_fetch_array($res_datos)){
                  $codigo= $arr_d['codigo'];
                  $cantidad=$arr_d['cantidad'];
                  $tiponum=$arr_d['tipo'];
                  $cliente=$arr_d['codcliente'];
                  $vendedor=$arr_d['vendedor'];
                  $precio=$arr_d['precio'];

                  $sqlcodigo="SELECT CODIGO,EXISTENCIA FROM inventario WHERE CODIGO ='$codigo'";  

                  if(($res_codigo = odbc_exec($id,$sqlcodigo)) === false)
                      die("Error en Select: ". odbc_errormsg($id));

                  while($arr_codigo = odbc_fetch_array($res_codigo)){
                    $existencia=$arr_codigo['EXISTENCIA'];

                  }
                  if ($codigo=='SP') {
                   $existencia=1;
                   $actual=1;
                  }else{
                    $actual=$existencia+$cantidad;
                  }
                  $sql_Insert="INSERT INTO hgeneral(Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,precio,estado) values ('".$factura."','".$fecha_hg."','".$codigo."',".$cantidad.",'DEVOLUCION','".$tiponum."','".$cliente."','".$maquina."','".$vendedor."',1,".$existencia.",".$actual.",".$precio.",'APLICADA')";

                  //echo $sql_Insert; 
                  //echo "<br>";
                  //exit;

                  if(($r_insert = odbc_exec($id,$sql_Insert)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

                  
                }

        
    
        $var_continua=true;  
      
   
?>
