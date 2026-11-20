<?php
  session_start();
 require("db.php");

    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $id_telefono = $_REQUEST['id'];        
    }

      $sql = "DELETE FROM tbl_TEL_Numeros WHERE id ='$id_telefono'";
      //echo $sql; exit;
  if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    header("location: telefono_resultado.php");
?>
