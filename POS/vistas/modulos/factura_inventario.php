<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        

      $time = time();
      $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time).".000";  

      $$sqldatos="SELECT * FROM hpregeneral WHERE Factura ='$prefactura'";  
      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqldatos);

      for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {
        
        $codigo= $respuesta[$i]['codigo'];
        $cantidad=$respuesta[$i]['cantidad'];
        
        // Carga codigos y actualiza cantidades
        $sqlcodigo="SELECT CODIGO,EXISTENCIA FROM inventario WHERE CODIGO ='$codigo'";  
        $respuesta1 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcodigo);

        for ( $j = sizeof($respuesta1) - 1; $j >= 0; $j--) {
          $existencia=$respuesta1[$j]['EXISTENCIA'];

        }
       
        $actual=$existencia-$cantidad;

        $sql_Insert = "UPDATE INVENTARIO SET EXISTENCIA=".$actual." WHERE CODIGO='".$codigo."'"; 

        
        if ($codigo=='SP') {
          //No hace nada
        }else{
            $Actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Insert);
        }  
        
      }

            // calculo de la factura
            
           

    $var_continua=true;  
  
   
?>
