<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    } 
    if(isset($_REQUEST['idcodigo']) && !empty($_REQUEST['idcodigo'])){
          $id_codigos = $_REQUEST['idcodigo'];
    }
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];
    }
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
          $cantidad = $_REQUEST['cantidad'];
    }
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $precio = $_REQUEST['monto'];
    }


$sql_precio="SELECT VENTA FROM inventario WHERE CODIGO='$codigo'";
$precioanterior=0;
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_precio);
foreach ($respuesta as $key => $value) {
  $precioanterior=$value['VENTA'];
}

$sql_update="UPDATE Pedidos SET Codigo='$codigo',Precio='$precio',Cantidad='$cantidad',PrecioAnterior='$precioanterior' WHERE ID='$id_codigos'";

$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);
  

?>
