<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    }
   
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    } 

    if(isset($_REQUEST['pedido']) && !empty($_REQUEST['pedido'])){
          $pedido = $_REQUEST['pedido']; 
    }
    
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
          $cantidad = $_REQUEST['cantidad']; 
    }

 
  
  $sql= "SELECT * FROM hacer_pedido WHERE codigo='$codigo' AND pedido='$pedido'";  
  
  $existe='no';
  

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  foreach ($respuesta as $key => $value) {
    $existe="si";
  }

  if ($existe=='si') {
    $sql = "UPDATE hacer_pedido SET cantidad='$cantidad' WHERE codigo='$codigo' AND pedido='$pedido'";
  }else{
    $sql = "INSERT INTO hacer_pedido
            (codigo,cantidad,pedido)
            values
            ('".$codigo."','".$cantidad."','".$pedido."')";
    }
  
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    echo "exito";      

  
  