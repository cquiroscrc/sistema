<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");

    
    if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
        $factura = $_REQUEST['fact'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
        $proveedor = $_REQUEST['proveedor'];      
    } 
    
    
    if(isset($_REQUEST['total']) && !empty($_REQUEST['total'])){
        $total = $_REQUEST['total'];      
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

  //echo $sqlinsertcompra .'<br>';  
  if(($r_compra = odbc_exec($id,$sqlinsertcompra)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  // Insertar CXP
    $sql_cxp="INSERT INTO cxp (NFac,Proveedor,FechaFact,Tipo,DiasCredito,FechaLimite,SubTotal,Descuento,Impuesto,Total,Saldo,Estado,chkCancelacion)
    values ('".$factura."','".$proveedor."','".date("Y-m-d h:i:s.m")."','".$tipo."','".$dias."','".$newdate."','".$subtotal."','".$descuento."','".$impuesto."','".$total."','".$total."','".$estado."','0')"; 
  //echo $sql_cxp.'<br>';

  if(($r_cxp = odbc_exec($id,$sql_cxp)) === false)
    die("Error en Select: ". odbc_errormsg($id));
 
  //entra el ciclo

  $sql = "SELECT * FROM Pedidos WHERE Factura='$factura' AND Proveedor='$proveedor'";
  
 // echo $sql .'<br>';  
  if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  while($arr = odbc_fetch_array($resultado)){ 
    $codigo=$arr['Codigo'];
    $cantidad=$arr['Cantidad'];
    $precio=$arr['Precio']; // Valor en $
    $precioanterior=$arr['PrecioAnterior']; // Anterior
    $preciosugerido=$arr['Sugerido'];  // Precio Venta
    $factor=$arr['factor'];
    $costo=$precio*$factor;
    
    //echo $codigo.'<br>';
    
    $sql_inventario="SELECT * FROM inventario WHERE CODIGO = '$codigo'";
    
    if(($r_inventario = odbc_exec($id,$sql_inventario)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  
    while($a_inventario = odbc_fetch_array($r_inventario)){ 
      $existencia=$a_inventario['EXISTENCIA'];

      //echo $existencia.'<br>';
    }

    //Update existencias 
      $actual=(int)$existencia+(int)$cantidad;
      $sql_update="UPDATE inventario SET EXISTENCIA='$actual', 
                                          VENTA='$preciosugerido', 
                                          COSTO='$costo', costodol='$precio', factorcosto='$factor',
                                          proveedor='$proveedor' WHERE CODIGO='$codigo'";
    //  echo $sql_update.'<br>';
   
    if(($r_update = odbc_exec($id,$sql_update)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    //Insert Hgeneral

    $sql_insert="INSERT INTO hgeneral 
    (Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,
      precio,estado) values 
    ('".$factura."','".date("Y-m-d h:i:s.m")."','".$codigo."','".$cantidad."','PEDIDO','".$tipo."',
      '".$proveedor."','1','999','1','".$existencia."','".$actual."','".$precio."','Aplicado')";
    //echo $sql_insert.'<br>';
     
    if(($r_insert = odbc_exec($id,$sql_insert)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
  //Insertar hmcompras
   $sql_hmcompras="INSERT INTO hmcompras (Factura,Codigo,Proveedor,Tipo,Precio,Cantidad,PrecioAnterior,PrecioSugerido,TotalLinea,Fecha) values
   ('".$factura."','".$codigo."','".$proveedor."','".$tipo."','".$precio."','".$cantidad."','".$precioanterior."','".$preciosugerido."','".$preciosugerido*$cantidad."','".date("Y-m-d h:i:s.m")."')";
   //echo $sql_hmcompras.'<br>';

   if(($r_hmcompras = odbc_exec($id,$sql_hmcompras)) === false)
      die("Error en Select: ". odbc_errormsg($id));


  } //Fin de while       

$sql_eliminar="DELETE FROM Pedidos where Factura='$factura' AND Proveedor='$proveedor'";
//echo $sql_eliminar; exit;

if(($r_eliminar = odbc_exec($id,$sql_eliminar)) === false)
      die("Error en Select: ". odbc_errormsg($id));


    // Quitar comentario al final
 header("location: pedido_cargar.php");
    
?>
