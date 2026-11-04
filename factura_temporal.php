<?php
  
  include("db.php");

    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
          $factura = $_REQUEST['consecutivo'];      
    } 
    
   
  $sql = "DELETE FROM tmp_factura WHERE Factura='$factura'";
    //echo $sql; exit;
   
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
  $var_continua=true;    
?>
