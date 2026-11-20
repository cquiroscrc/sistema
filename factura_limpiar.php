<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
          $factura = $_REQUEST['consecutivo'];      
    } 
    
  $sql = "DELETE FROM tmp_factura WHERE Factura='$factura'";
    //echo $sql; exit;
   
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
  $sql2 = "DELETE FROM hfacturas_tmp WHERE nfac ='$factura'";          
   if(($resultado2 = odbc_exec($id,$sql2)) === false)
         die("Error en Select: ". odbc_errormsg($id));
       
      header("location: factura_iniciar.php");
?>
