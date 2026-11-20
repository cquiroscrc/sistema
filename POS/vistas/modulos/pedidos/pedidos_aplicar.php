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
    if(isset($_REQUEST['total']) && !empty($_REQUEST['total'])){
      $total = $_REQUEST['total'];
      $total= (float)$total;
      $subtotal = $_REQUEST['total'];
      $impuesto = 0;
      $descuento = 0;
    }
if ($proveedor==1) {
  $tipo='Contado';
  $dias='0';
  $newdate = date("Y-m-d h:i:s.m");

  $estado='Cancelada';
} else{
  $tipo='Credito';
  $dias='30';
  $fecha = date("Y-m-d h:i:s.m");
  $newdate = strtotime ( '+30 day' , strtotime ( $fecha ) ) ;
  $newdate = date ( 'Y-m-d h:i:s.m' , $newdate );
  $estado='Pendiente';
}



    //insertar la compra
    $sqlinsertcompra="INSERT INTO compras (Factura, proveedor,fecha,tipo,subtotal,impuesto,descuento,total) values
    ('".$factura."','".$proveedor."','".date("Y-m-d h:i:s.m")."','".$tipo."','".$subtotal."','".$impuesto."','".$descuento."','".$total."')";
    echo $sqlinsertcompra;
    //exit;
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlinsertcompra);



//echo $sqlinsertcompra;

// Insertar CXP
$sql_cxp="INSERT INTO cxp (NFac,Proveedor,FechaFact,Tipo,DiasCredito,FechaLimite,SubTotal,Descuento,Impuesto,Total,Saldo,Estado,chkCancelacion)
    values ('".$factura."','".$proveedor."','".date("Y-m-d h:i:s.m")."','".$tipo."','".$dias."','".$newdate."','".$subtotal."','".$descuento."','".$impuesto."','".$total."','".$total."','".$estado."','0')";
echo $sql_cxp.'<br>';
//exit;
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cxp);


$sql = "SELECT * FROM Pedidos WHERE Factura='$factura' AND Proveedor='$proveedor'";
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

foreach ($respuesta as $key => $arr) {
  $codigo=$arr['Codigo'];
  $cantidad=$arr['Cantidad'];
  $precio=$arr['Precio']; // Valor en $
  $precioanterior=$arr['PrecioAnterior']; // Anterior
  $preciosugerido=$arr['Sugerido'];  // Precio Venta
  $factor=$arr['factor'];
  $costo=floatval($precio)*floatval($factor);

  $sql_inventario="SELECT * FROM inventario WHERE CODIGO = '$codigo'";
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_inventario);
  foreach ($respuesta as $key => $a_inventario) {
    $existencia=$a_inventario['EXISTENCIA'];
    echo $existencia.'<br>';
  }

  //Update existencias
  $actual=(int)$existencia+(int)$cantidad;
  $sql_update="UPDATE inventario SET EXISTENCIA='$actual',VENTA='$preciosugerido',COSTO='$costo', costodol='$precio', factorcosto='$factor',
    proveedor='$proveedor' WHERE CODIGO='$codigo'";
  echo $sql_update.'<br>';
  //exit;
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);

  //Insert Hgeneral

  $sql_insert="INSERT INTO hgeneral 
    (Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,
      precio,estado) values 
    ('".$factura."','".date("Y-m-d h:i:s.m")."','".$codigo."','".$cantidad."','PEDIDO','".$tipo."',
      '".$proveedor."','1','999','1','".$existencia."','".$actual."','".$precio."','Aplicado')";
  echo $sql_insert.'<br>';
  //exit;
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_insert);

  //Insertar hmcompras
  $sql_hmcompras="INSERT INTO hmcompras (Factura,Codigo,Proveedor,Tipo,Precio,Cantidad,PrecioAnterior,PrecioSugerido,TotalLinea,Fecha) values
   ('".$factura."','".$codigo."','".$proveedor."','".$tipo."','".$precio."','".$cantidad."','".$precioanterior."','".$preciosugerido."','".$preciosugerido*$cantidad."','".date("Y-m-d h:i:s.m")."')";
  echo $sql_hmcompras.'<br>';
  //exit;

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_hmcompras);

} //Fin foreach

$sql_eliminar="DELETE FROM Pedidos where Factura='$factura' AND Proveedor='$proveedor'";
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_eliminar);
echo $sql_eliminar.'<br>';
//exit;
