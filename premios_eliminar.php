<?php
  session_start();
  include("db.php");

    
    
  $sql = "DELETE FROM tbl_premios";
  //echo $sql; exit;

    if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
      
    header("location: premios_resultado.php");
    
?>