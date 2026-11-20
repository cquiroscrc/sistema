<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    } 
    if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
          $factura = $_REQUEST['factura'];      
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

$factorcosto=0;
$factor=1;
$sugerido=$precio;
$procedencia='-';
$fecha=date("Y-m-d");
$sql_precio="SELECT VENTA FROM inventario WHERE CODIGO='$codigo'";

$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_precio);
foreach ($respuesta as $key => $value) {
  $precioanterior=$value['VENTA'];
}

$sql="INSERT INTO Pedidos (Factura,Codigo,Proveedor,Precio,Cantidad,factor,Sugerido,Procedencia, PrecioAnterior,Fecha,Caja,Etiquetas,FactorCosto)
values ('".$factura."','".$codigo."','".$proveedor."','".$precio."','".$cantidad."','".$factor."','".$sugerido."','".$procedencia."','".$precioanterior."','".$fecha."','".$factor."','".$cantidad."','".$factorcosto."')";

$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  

?>
