<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
          $descuento = $_REQUEST['descuento'];      
    } 
    
  $sql = "UPDATE tbl_Utiles SET valor='$descuento' WHERE IDUtilidad =2";
  //echo $sql; exit;

    if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
      
    header("location: usuarios_resultado.php");
    
?>