<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        

          $time = time();
          $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time).".000";  

          $$sqldatos="SELECT * FROM hgeneral WHERE Factura ='$documento'";  

            if(($res_datos = odbc_exec($id,$sqldatos)) === false)
                die("Error en Select: ". odbc_errormsg($id));

            while($arr_d = odbc_fetch_array($res_datos)){
                  $codigo= $arr_d['codigo'];
                  $cantidad=$arr_d['cantidad'];
                  
                  // Carga codigos y actualiza cantidades
                  $sqlcodigo="SELECT CODIGO,EXISTENCIA FROM inventario WHERE CODIGO ='$codigo'";  

                  if(($res_codigo = odbc_exec($id,$sqlcodigo)) === false)
                      die("Error en Select: ". odbc_errormsg($id));

                  while($arr_codigo = odbc_fetch_array($res_codigo)){
                    $existencia=$arr_codigo['EXISTENCIA'];

                  }
                 
                  $actual=$existencia+$cantidad; 
                  $sql_Insert = "UPDATE INVENTARIO SET EXISTENCIA=".$actual." WHERE CODIGO='".$codigo."'"; 
          
                  //echo $sql_Insert; 
                  //echo "<br>";
                  //exit;
                  if ($codigo=='SP') {
                    //No hace nada
                  }else{
                    if(($r_insert = odbc_exec($id,$sql_Insert)) === false)
                      die("Error en Select: ". odbc_errormsg($id));
                  }  
                  
                }

                // calculo de la factura
                
               
    
        $var_continua=true;  
      
   
?>
