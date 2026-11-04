<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  
  if((isset($_GET['salir']))and($_GET['salir']=='si')){
    session_destroy();
    header("location: ../index.php");
    die;
  }

?>
