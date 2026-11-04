<?php
  session_start();
  include("db.php");
   date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    //$fecha=date("Y-m-d");
    $fecha=date("Y-m-d 00:00:00.000", $fecha_sis);

    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
          $factura = $_REQUEST['consecutivo'];      
    } 
    
     
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $id_codigos = $_REQUEST['codigo'];        
    }
    
    
  $sql = "DELETE FROM tmp_factura WHERE Codigo ='$id_codigos' AND Factura='$factura'";
    //echo $sql; exit;
   
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));

  $existe=false;
  $sql_e="SELECT * FROM tmp_factura WHERE Factura='$factura'";
    if(($resultado_e = odbc_exec($id,$sql_e)) === false)
         die("Error en Select: ". odbc_errormsg($id));
    $tmp_subtotal=0;
    $tmp_impuesto=0;
    $tmp_total=0;

    while($arr_con = odbc_fetch_array($resultado_e)){
            $existe=true;
            $tmp_subtotal=$tmp_subtotal+($arr_con["Precio"]*$arr_con["Cantidad"]);
            $tmp_impuesto=$tmp_subtotal*0.13;
            $tmp_total=$tmp_subtotal+$tmp_impuesto;

    } 
    if ($existe==true) {
              //actualiza totales
        $sql_u = "UPDATE hfacturas_tmp SET subtotal='$tmp_subtotal',impuesto='$tmp_impuesto',total='$tmp_total',fechafact='$fecha' WHERE Nfac=$factura";
            
            if(($resultado_u = odbc_exec($id,$sql_u)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
    }else{
              // elimina factura
        $sql_d = "DELETE FROM hfacturas_tmp WHERE nfac='$factura'";
        //echo $sql; exit;
   
        if(($resultado_d= odbc_exec($id,$sql_d)) === false)
         die("Error en Select: ". odbc_errormsg($id));

    }        
   
      header("location: factura_nueva.php?consecutivo=".$factura."");
?>
