<?php
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    
        if ($cliente=='contado') {
          $cliente=0;
        }
        

          $csubtotal=$subtotal*-1;
          $cimpuesto=$impuesto*-1;
          $ctotal=$total*-1;
          $cdescuento=$descuento*-1;
          
         
          if ($tipo=="frecuente") {
            $tiponum=0;
          }elseif ($tipo=="contado") {
            $tiponum=0; 
          }elseif ($tipo=="credito") {
            $tiponum=1;
          } 
     
        // aqui ingresa los datos sobre FE 
        $pais='506';
        $nit='3101090358';  
        
        $facturafinal=$casa_sucursal.$terminal.$cod_tipo.str_pad($consecutivo_fac, 10, "0", STR_PAD_LEFT) ; 
        
        $ahora = time();
        $codigo_sat=  $pais.strftime( "%d%m%y", $ahora ).str_pad($nit, 12, "0", STR_PAD_LEFT).$facturafinal.'199999999';
        $fecha_documento=strftime( "%Y-%m-%d", $ahora ) ." ". date("H:i:s", $ahora).".000";
        
        
        $fecha_hg=strftime( "%Y-%m-%d", $fecha ) ." ". date("H:i:s", $ahora).".000";       
        $sql_Insert="INSERT INTO HFacturas(nfac,Nombre,Adicional,fechafact,fecha_documento,SubTotal,impuesto,descuento,Total,Tipo,proveedor,vendedor,estado,sat,consecutivo,referencia,motivo)
        values ('".$factura."','".$nombre."','','".$fecha_hg."','".$fecha_documento."','".$csubtotal."','".$cimpuesto."','".$cdescuento."','".$ctotal."','".$tipo."','".$cliente."','".$vendedor."','DEVOLUCION','".$codigo_sat."','".$facturafinal."','".$referencia_anular."','1')"; 
          
        //echo $sql_Insert; 
        //exit;
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Insert);
    
        $var_continua=true;  
      
   
?>
