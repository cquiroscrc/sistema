<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
  
  $codigolimpio= ltrim($codigo) ; 
  
  //UPDATE inventario SET CODIGO = '204479 87204 TW' WHERE CODIGO LIKE '204479 87204 TW% 
  
  $sql = "UPDATE inventario SET CODIGO ='$codigolimpio' WHERE CODIGO LIKE '$codigo%'";
  //echo $sql; exit;

    if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
      
    header("location: espacios_blancos.php");
    
?>