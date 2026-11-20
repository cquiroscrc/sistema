<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
        $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
        $proveedor = $_REQUEST['proveedor'];      
    } 
      if(isset($_REQUEST['subtotal']) && !empty($_REQUEST['subtotal'])){
        $subtotal = $_REQUEST['subtotal'];      
    }   
    $descuento=0;
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
        $descuento = $_REQUEST['descuento'];      
    } 
    $impuesto=0;
    if(isset($_REQUEST['impuesto']) && !empty($_REQUEST['impuesto'])){
        $impuesto = $_REQUEST['impuesto'];      
    } 
    if(isset($_REQUEST['total']) && !empty($_REQUEST['total'])){
        $total = $_REQUEST['total'];      
    } 

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion'];      
    } 
    if(isset($_REQUEST['precioanterior']) && !empty($_REQUEST['precioanterior'])){
          $precioanterior = $_REQUEST['precioanterior'];      
    } 
    if(isset($_REQUEST['preciosugerido']) && !empty($_REQUEST['preciosugerido'])){
          $preciosugerido = $_REQUEST['preciosugerido'];      
    } 
    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
          $precio = $_REQUEST['precio'];      
    } 
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
          $cantidad = $_REQUEST['cantidad'];      
    } 
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    $fecha=date("Y-m-d");
   //Percent = txtFields(1) / txtFields(0)  'Sub/Descuento/Lineas
  //NCost = txtCosto - (txtCosto * Percent)
    $porcentaje=$descuento / $subtotal;
    $costo=$preciosugerido-($preciosugerido*$porcentaje);
    $preciosugerido=$costo;

     $sqlInsert = "INSERT INTO mcompras
        (Factura,Codigo,Proveedor,Precio,Cantidad,PrecioAnterior,PrecioSugerido,Fecha)
        values
        ('".$consecutivo."','".$codigo."','".$proveedor."','".$precio."','".$cantidad."','".$precioanterior."','".$preciosugerido."','".$fecha."')";
      
      echo $sqlInsert;
      
      if(($resultadoInsert = odbc_exec($id,$sqlInsert)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
        $arrInsert = odbc_fetch_array($resultadoInsert);   

    header("location: dev_compras_factura.php?consecutivo=".$consecutivo."&proveedor=".$proveedor."&subtotal=".$subtotal."&descuento="
      .$descuento."&impuesto=".$impuesto."&total=".$total."");
   
?>
