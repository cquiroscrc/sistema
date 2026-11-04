<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['marcador']) && !empty($_REQUEST['marcador'])){
          $marcador = $_REQUEST['marcador'];      
    } 
    
  $sql = "UPDATE inventario SET reserva=0 WHERE CODIGO ='$codigo'";
  //echo $sql; exit;

    if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
      
    header("location: reserva_consultas.php#".$marcador."");
    
?>