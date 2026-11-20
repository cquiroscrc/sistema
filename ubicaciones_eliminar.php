<?php
  session_start();
 require("db.php");

    if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
      $ubicacion = $_REQUEST['ubicacion'];        
    }

      $sql = "UPDATE inventario SET ubicacion='00X00' WHERE UBICACION ='$ubicacion'";
      //echo $sql; exit;
  if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    header("location: ubicaciones_consultas.php?ubicacion=".$ubicacion);
?>
