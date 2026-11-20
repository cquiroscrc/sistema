<?php
  session_start();
include("db.php");

    if(isset($_REQUEST['pedidos']) && !empty($_REQUEST['pedidos'])){
      $pedidos = $_REQUEST['pedidos'];        
    }

      $sql = "DELETE FROM hacer_pedido WHERE pedido ='$pedidos'";
  	//echo $sql; exit;
  	if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
             
      header("location: eliminar_pedidos.php");
    
?>
?>
