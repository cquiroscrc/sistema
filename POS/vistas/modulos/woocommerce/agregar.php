<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    }

    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
        $precio = $_REQUEST['precio'];
    }

    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion'];
          $descripcion= strtolower($descripcion);
          $descripcion = ucwords($descripcion);      
    }
   
 
  
  $sql= "SELECT * FROM woocommerce WHERE codigo='$codigo'";  
  
  $existe='no';
  

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  foreach ($respuesta as $key => $value) {
    $existe="si";
  }

  if ($existe=='si') {
    
  }else{
    $sql = "INSERT INTO woocommerce
            (codigo,descripcion,precio)
            values
            ('".$codigo."','".$descripcion."','".$precio."')";
    }
    echo $sql;
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    echo "exito";      

  
  