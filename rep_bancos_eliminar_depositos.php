<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2'];      
    } 
    if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
          $Date1 = $_REQUEST['Date1'];      
    } 
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
          $tipo = $_REQUEST['tipo'];      
    }
    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $deposito = $_REQUEST['documento'];      
    }
    if(isset($_REQUEST['cuenta']) && !empty($_REQUEST['cuenta'])){
          $cuenta = $_REQUEST['cuenta'];      
    }
    

  $sql = "DELETE FROM tbl_Bancos WHERE Documento ='$deposito' AND Cuenta='$cuenta'";
  //echo $sql; exit;

    if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
      
    header("location: rep_bancos_resultados.php?Date2=".$Date2."&datos=".$cuenta."&Date1=".$Date1."");
    
?>