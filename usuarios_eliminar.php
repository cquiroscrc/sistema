<?php
  session_start();
 include("db.php");

    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $id_usuario = $_REQUEST['id'];        
    }
  if ($id_usuario<>'1') {
      $sql = "DELETE FROM acc_usuarios WHERE id ='$id_usuario'";
      //echo $sql; exit;
     
     if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));  

  }
      
   
    header("location: usuarios_resultado.php");
?>
