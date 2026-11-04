<?php
  session_start();
 require("db.php");

    if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
      $id_cliente = $_REQUEST['datos'];        
    }
      $sql_update="UPDATE cxc SET IDCliente = '0' WHERE IDCliente='$id_cliente'";
      if(($resultado_u = odbc_exec($id,$sql_update)) === false)
      	die("Error en Select: ". odbc_errormsg($id));			


      $sql = "DELETE FROM clientes WHERE codigo ='$id_cliente'";
      //echo $sql; exit;
  	  if(($resultado = odbc_exec($id,$sql)) === false)
      	die("Error en Select: ". odbc_errormsg($id));

    header("location: clientes_elimina.php");
?>
