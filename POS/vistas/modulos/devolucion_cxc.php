<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        if ($cliente=='contado') {
          $cliente=0;
        }
        if ($cliente > 10000) {
            // Credito
            $diascredito=30;

            $fec_vencimi=   date('Y-m-d', strtotime('+1 month'))." ". date("H:i:s", $time).".000";
        }else{
            $diascredito=0;
            $fec_vencimi= strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time).".000";
        }

          $csubtotal=$subtotal*-1;
          $cimpuesto=$impuesto*-1;
          $ctotal=$total*-1;
          $cdescuento=$descuento*-1;
                
          

        $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time).".000";        
        $sql_Insert="INSERT INTO CxC(Nfac,IDcliente,fecha,diascredito,fechalimite,SubTotal,impuesto,descuento,Total,saldo,Estado)
          values ('".$factura."','".$cliente."','".$fecha_hg."','".$diascredito."','".$fec_vencimi."','".$csubtotal."','".$cimpuesto."','".$cdescuento."','".$ctotal."','".$ctotal."','Pendiente')"; 
          
        //echo $sql_Insert; 
        //exit;
        
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Insert);
        $var_continua=true;  
      
   
?>
