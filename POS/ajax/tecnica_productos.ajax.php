<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");


    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo =$_REQUEST['codigo']; 
    }

    if(isset($_REQUEST['tecnica']) && !empty($_REQUEST['tecnica'])){
      $tecnica =$_REQUEST['tecnica']; 
    }

    if(isset($_REQUEST['usuario']) && !empty($_REQUEST['usuario'])){
      $usuario =$_REQUEST['usuario']; 
    }
  
    $sql="UPDATE inventario SET DESC_ALT='$tecnica', Carro='$usuario' WHERE CODIGO='$codigo'";

    $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);  

    echo 'exito';
    
  
     