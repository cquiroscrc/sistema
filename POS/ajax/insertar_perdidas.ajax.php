<?php
    session_start();
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
    }

    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
      $descripcion = ucwords($_REQUEST['descripcion']);

    }
    $codigo=strtoupper($codigo);
    $descripcion=strtoupper($descripcion);

    $usuario=$_SESSION["usuario"];

    $sql="INSERT INTO tmpTras (codigo, descripcion, ubicacion) VALUES ('$codigo','$descripcion','$usuario')";

      //// *************** Ejecuto la consulta ******************
          
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

