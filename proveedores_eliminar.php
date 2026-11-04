<?php
  session_start();
 require("db.php");

    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $id_proveedor = $_REQUEST['id'];        
    }

      $sql = "DELETE FROM proveedores WHERE codigo ='$id_proveedor'";
      //echo $sql; exit;
  if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    header("location: proveedores_resultado.php");
?>
