<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2'];      
    }
    if(isset($_REQUEST['egreso']) && !empty($_REQUEST['egreso'])){
          $egreso = $_REQUEST['egreso'];      
    }
    if(isset($_REQUEST['tegreso']) && !empty($_REQUEST['tegreso'])){
          $tegreso = $_REQUEST['tegreso'];      
    }
    if ($_REQUEST['tegreso']==0) {
      $tegreso=$tegreso = $_REQUEST['tegreso'];
    }

  $sql = "DELETE FROM tbl_Egresos WHERE Documento ='$egreso' AND Tipo='$tegreso'";
  //echo $sql; exit;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    
?>