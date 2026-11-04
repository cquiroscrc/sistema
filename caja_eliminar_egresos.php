<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2'];      
    } 
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
          $tipo = $_REQUEST['tipo'];      
    }
    if(isset($_REQUEST['egreso']) && !empty($_REQUEST['egreso'])){
          $egreso = $_REQUEST['egreso'];      
    }
    if(isset($_REQUEST['tegreso']) && !empty($_REQUEST['tegreso'])){
          $tegreso = $_REQUEST['tegreso'];      
    }
    if ($_REQUEST['tegreso']==0) {
      $tegreso=$tegreso = $_REQUEST['tegreso'];
    }

  $sql = "DELETE FROM tbl_Egresos WHERE Documento ='$egreso' AND Tipo='$tegreso'";
  //echo $sql; exit;

    if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
      
    header("location: caja_resultado.php?Date2=".$Date2."&tipo=".$tipo);
    
?>