<?php
  session_start();
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
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
      $fecha = $_REQUEST['fecha'];
    }



$sql_nombre="SELECT nombre FROM Proveedores WHERE codigo='$proveedor'";
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_nombre);
foreach ($respuesta as $key => $value) {
  $nombre=$value['nombre'];
}

$asunto=$nombre." - ".$factura;

$mensaje="<p>El pedido de ".$nombre." numero: ".$factura." </p><p>estará llegando aproximadamente ".$fecha."</p><p><br></p><p><a href=\"Pedido/pedido_".$factura.".pdf\" target=\"_blank\">Aqui encontraran el listado</a><br></p>";

$fecha_mensaje =date("Y-m-d h:i:s.m");

$sql="SELECT * FROM acc_usuarios";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$envia= $_SESSION["usuario"];
foreach ($ejecutar as $key => $valor) {
  $vaRecibir=$valor['usuario'];
  $inserta = "INSERT INTO Mensajes (Detalle,Envia,Recibe,Estado,Asunto,Fecha) Values ('$mensaje' ,'$envia','$vaRecibir','SL', '$asunto', '$fecha_mensaje')";

  $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);

}
