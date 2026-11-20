<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2'];      
    } 
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
          $tipo = $_REQUEST['tipo'];      
    }
    if(isset($_REQUEST['recibo']) && !empty($_REQUEST['recibo'])){
          $recibo = $_REQUEST['recibo'];      
    }
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
          $cliente = $_REQUEST['cliente'];      
    }
    if ($_REQUEST['cliente']==0) {
      $cliente=$cliente = $_REQUEST['cliente'];
    }

  $sql = "DELETE FROM cxc WHERE NFac ='$recibo' AND IDCliente='$cliente'";
  //echo $sql; exit;

    if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
      
    header("location: caja_resultado.php?Date2=".$Date2."&tipo=".$tipo);
    
?>