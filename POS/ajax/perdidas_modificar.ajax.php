<?php
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    $ubicacion='';
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
    }

    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
      $descripcion = $_REQUEST['descripcion'];
    }

    if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
        $ubicacion = $_REQUEST['ubicacion'];
    }
        
    $codigo=strtoupper($codigo);
    $descripcion=strtoupper($descripcion);

      //// *************** Incluye la consulta ******************
    $sql="UPDATE tmpTras SET ubicacion='$ubicacion', codigo='$codigo',descripcion='$descripcion' WHERE codigo='$codigo'";

      //// *************** Ejecuto la consulta ******************

      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        
