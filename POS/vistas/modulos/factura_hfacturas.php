<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        
        

          $csubtotal=$subtotal;
          $cimpuesto=$impuesto;
          $ctotal=$total;
          $cdescuento=$descuento;
        
        if ($tiponum==1) {
        //entra 3 veces
          $tiponomx=1;
          
        }else{
          $tiponomx=0;
        }
         
            
        // aqui ingresa los datos sobre FE 
        $pais='506';
        $nit='3101090358';  
        $facturafinal=$casa_sucursal.$terminal.$cod_tipo.str_pad($consecutivo_fac, 10, "0", STR_PAD_LEFT) ; 
        
        // fecha/hora reales para Hacienda (clave + fecha_documento)
        $ahora = time();
        $codigo_sat=  $pais.strftime( "%d%m%y", $ahora ).str_pad($nit, 12, "0", STR_PAD_LEFT).$facturafinal.'199999999';
        $fecha_documento=strftime( "%Y-%m-%d", $ahora ) ." ". date("H:i:s", $ahora).".000";


        // fechafact: fecha de cierre contable (puede ser el día siguiente)
        $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $time).".000";       
        $sql_Insert="INSERT INTO HFacturas(nfac,Nombre,Adicional,fechafact,fecha_documento,SubTotal,impuesto,descuento,Total,Tipo,proveedor,vendedor,estado,sat,consecutivo,referencia,motivo,medio_pago)
        values ('".$factura."','".$nombre."','".$adicional."','".$fecha_hg."','".$fecha_documento."','".$csubtotal."','".$cimpuesto."','".$cdescuento."','".$ctotal."','".$tiponomx."','".$cliente."','".$vendedor."','APLICADA','".$codigo_sat."','".$facturafinal."','','','".$medio_pago."')"; 
        
        
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Insert);
    
        $var_continua=true;  
      
   
?>
