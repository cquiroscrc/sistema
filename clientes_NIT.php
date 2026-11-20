<?php
//session_start();
    require("db.php");

         
    $sql="SELECT * FROM clientes";  
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        $cliente=$arr['codigo'];
        $identificacion=$arr['cedula'];
        if (strlen($identificacion)===9) {
            $tipo='01';
          }elseif (strlen($identificacion)===10) {
            if (substr($identificacion, 0, 1)=='3') {
              $tipo='02';
            }else{
              $tipo='04';
            }
          }elseif (strlen($identificacion)===11) {
              $tipo='03';
          }elseif (strlen($identificacion)===12) {
              $tipo='03';
          }  
        // update cliente ***
          $sql_update = "UPDATE clientes SET TIPO_NIT='$tipo' WHERE codigo=$cliente";
         //echo $sql_update; 
         //exit;
         //if(($resultado_u = odbc_exec($id,$sql_update)) === false)
         //        die("Error en Select: ". odbc_errormsg($id));


      }

      $slq_consecutivo ="SELECT *  FROM hfacturas WHERE fechafact > '2018-09-01' and sat is null order by nfac";
        
          if(($res_consecutivo = odbc_exec($id,$slq_consecutivo)) === false)
              die("Error en Select: ". odbc_errormsg($id));
          
          while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
            $factura=rtrim($arr_consecutivo['nfac']);
            $fecha=$arr_consecutivo['fechafact'];
            if (substr($factura,0,1)=='D'){
              $consecutivo='0010000103' . substr($factura, -10);
            }else{
              $consecutivo='0010000101' . substr($factura, -10);
            }
            $consecutivo=rtrim($consecutivo);
            $pais='506';
            $nit='3101090358';  
            //$facturafinal=$casa_sucursal.$terminal.$cod_tipo.str_pad($consecutivo_fac, 10, "0", STR_PAD_LEFT) ; 

            $fecha =strtotime($fecha);
            
            $codigo_sat=  $pais.strftime( "%d%m%y", $fecha ).str_pad($nit, 12, "0", STR_PAD_LEFT).$consecutivo.'199999999';
            //echo '<script language="javascript">alert('.$codigo_sat.');</script>'; 
            $sql_act = "UPDATE hfacturas SET sat ='".$codigo_sat."', consecutivo = '".$consecutivo."' WHERE nfac ='".$arr_consecutivo['nfac']."'";
            //if(($res_act = odbc_exec($id,$sql_act)) === false)
            //   die("Error en Select: ". odbc_errormsg($id));
            //echo $sql_act;
            //echo "<br>";
          }


       // echo 'Registro Guardado';
       //header("location: index.php");
    
?>