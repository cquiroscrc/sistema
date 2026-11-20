<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");

    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
        $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['codigoOrigen']) && !empty($_REQUEST['codigoOrigen'])){
        $codigoOrigen = $_REQUEST['codigoOrigen'];      
    } 
    

   //Update Hgeneral
  $sql_update_h="UPDATE hgeneral SET CODIGO='$codigo' WHERE CODIGO='$codigoOrigen'";
    if(($h_update = odbc_exec($id,$sql_update_h)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  echo $sql_update_h;

  // Update Codigo
  $sql_update_i="UPDATE Inventario SET CODIGO='$codigo' where CODIGO='$codigoOrigen'";
    if(($r_update = odbc_exec($id,$sql_update_i)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  echo $sql_update_i;

  header("location: pase_consulta_cambios.php");
    
?>
