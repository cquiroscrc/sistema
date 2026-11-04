<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['pedido']) && !empty($_REQUEST['pedido'])){
          $pedido = $_REQUEST['pedido']; 
          
    }
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
          $p_id = $_REQUEST['id']; 
          
    }

  $sql = "DELETE FROM hacer_pedido WHERE codigo ='$codigo' AND pedido='$pedido' AND id='$p_id'";
    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
        
      header("location: consultas_pedido_ver.php?pedidos=".$pedido);
   
?>
