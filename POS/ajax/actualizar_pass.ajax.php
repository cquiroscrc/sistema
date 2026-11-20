<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");


    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $id =$_REQUEST['id']; 
    }

    if(isset($_REQUEST['pass']) && !empty($_REQUEST['pass'])){
      $password = $_REQUEST['pass'];
    }

    
    $sqlactualiza="UPDATE acc_usuarios SET password='$password' WHERE id='$id'";
    
    $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);
  
     