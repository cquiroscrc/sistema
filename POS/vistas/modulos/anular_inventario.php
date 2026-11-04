<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        

          $time = time();
          $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time).".000";  

          $sqldatos="SELECT * FROM hgeneral WHERE Factura ='$documento'";  
          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqldatos);

              foreach ($respuesta as $key => $arr) {
                  $codigo= $arr['codigo'];
                  $cantidad=$arr['cantidad'];
                  
                  // Carga codigos y actualiza cantidades
                  $sqlcodigo="SELECT CODIGO,EXISTENCIA FROM inventario WHERE CODIGO ='$codigo'";  
                  $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcodigo);

                  foreach ($ejecutar as $key => $value) {
                    $existencia=$value['EXISTENCIA'];

                  }
                 
                  $actual=$existencia+$cantidad; 
                  $sql_Insert = "UPDATE INVENTARIO SET EXISTENCIA=".$actual." WHERE CODIGO='".$codigo."'"; 
          
                  echo $sql_Insert; 
                  //echo "<br>";
                  //exit;
                  if ($codigo=='SP') {
                    //No hace nada
                  }else{
                    $consulta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Insert);
                  }  
                  
                }

                // calculo de la factura
                
               
    
        $var_continua=true;  
      
   
?>
