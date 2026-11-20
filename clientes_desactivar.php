<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
      $datos = $_REQUEST['datos'];
    }
    
    

    $sql = "UPDATE clientes  SET comentario='**DESACTIVADO**' 
                      WHERE codigo='$datos'";

   //echo $sql;
   //exit ;               
    if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));




header("location: clientes_activar.php");  
  
?>