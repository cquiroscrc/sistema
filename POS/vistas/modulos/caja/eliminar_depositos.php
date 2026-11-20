<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");

    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2'];      
    } 
    
    if(isset($_REQUEST['deposito']) && !empty($_REQUEST['deposito'])){
          $deposito = $_REQUEST['deposito'];      
    }
    if(isset($_REQUEST['cuenta']) && !empty($_REQUEST['cuenta'])){
          $cuenta = $_REQUEST['cuenta'];      
    }
    

  $sql = "DELETE FROM tbl_Bancos WHERE Documento ='$deposito' AND Cuenta='$cuenta'";
  //echo $sql; exit;

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
      
   
    
?>