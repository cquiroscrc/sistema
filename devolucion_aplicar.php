<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");

    
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
        $consecutivo = $_REQUEST['consecutivo'];      
    } 
    $vendedor = $_REQUEST['vendedor'];      
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
        $vendedor = $_REQUEST['vendedor'];      
    } 
    $cliente = $_REQUEST['cliente'];    
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
        $cliente = $_REQUEST['cliente'];      
    } 
    if(isset($_REQUEST['subtotal']) && !empty($_REQUEST['subtotal'])){
        $subtotal = $_REQUEST['subtotal'];   
        $subtotal=$subtotal*-1;   
    }   
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];      
    }
    $descuento=0;
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
        $descuento = $_REQUEST['descuento'];      
        $descuento= $descuento*-1;
    } 
    $impuesto=0;
    if(isset($_REQUEST['impuesto']) && !empty($_REQUEST['impuesto'])){
        $impuesto = $_REQUEST['impuesto'];  
        $impuesto=$impuesto*-1;

    } 
    if(isset($_REQUEST['total']) && !empty($_REQUEST['total'])){
        $total = $_REQUEST['total']; 
        $total= $total*-1;    
    }
    
    if ($cliente==0) {
      $tipo=0;
      $dias=0;
      $estado='DEVOLUCION';
      $newdate=$fecha;
    } else{
      $tipo=1;
      $dias=30;
      $newdate = strtotime ( '+30 day' , strtotime ( $fecha ) ) ;
      $newdate = date ( 'Y-m-d h:i:s.m' , $newdate );
      $estado='DEVOLUCION';

      $sql_cxc="INSERT INTO cxc (IDCliente,NFac,IDCompañia,Fecha,DiasCredito,FechaLimite,SubTotal,Impuesto,Descuento,Total,Saldo,Estado)
        values ('".$cliente."','".$consecutivo."','".$compa."','".$fecha."','".$dias."','".$newdate."','".$subtotal."','".$impuesto."','".$descuento."','".$total."','".$total."','".$estado."')"; 
      
      if(($r_cxc = odbc_exec($id,$sql_cxc)) === false)
        die("Error en Select: ". odbc_errormsg($id));
  
      echo $sql_cxc .'<br>';  

    }
    
    $compa=1;
    
    //insertar devolucion
  $sqlinsertdevolucion="INSERT INTO hfacturas (nfac, fechafact,subtotal,impuesto,descuento,total,Tipo,estado,proveedor,vendedor) values
    ('".$consecutivo."','".$fecha."','".$subtotal."','".$impuesto."','".$descuento."','".$total."','".$tipo."','".$estado."','".$cliente."','".$vendedor."')";
  
  if(($r_devolucion = odbc_exec($id,$sqlinsertdevolucion)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  echo $sqlinsertdevolucion .'<br>';  



  //entra el ciclo

  $sql = "SELECT * FROM mcompras WHERE Factura='$consecutivo' AND Proveedor='$vendedor'";
  
  echo $sql .'<br>';  
  if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  while($arr = odbc_fetch_array($resultado)){ 
    $codigo=$arr['Codigo'];
    $cantidad=$arr['Cantidad'];
    $precio=$arr['Precio'];
    $precioanterior=$arr['PrecioAnterior'];
    $preciosugerido=$arr['PrecioSugerido'];
    
    echo $codigo.'<br>';
    
    $sql_inventario="SELECT * FROM inventario WHERE CODIGO = '$codigo'";
    
    if(($r_inventario = odbc_exec($id,$sql_inventario)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  
    while($a_inventario = odbc_fetch_array($r_inventario)){ 
      $existencia=$a_inventario['EXISTENCIA'];

      echo $existencia.'<br>';
    }//existencia

    //Update existencias 
      $actual=(int)$existencia+(int)$cantidad;
      $sql_update="UPDATE inventario SET EXISTENCIA='$actual' WHERE CODIGO='$codigo'";
      echo $sql_update.'<br>';
   
    if(($r_update = odbc_exec($id,$sql_update)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    //Insert Hgeneral

    $sql_insert="INSERT INTO hgeneral 
    (Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,
      precio,estado) values 
    ('".$consecutivo."','".$fecha."','".$codigo."','".$cantidad."','DEVOLUCION','".$tipo."',
      '".$cliente."','1','".$vendedor."','1','".$existencia."','".$actual."','".$precio."','APLICADA')";
    echo $sql_insert.'<br>';
     
    if(($r_insert = odbc_exec($id,$sql_insert)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
 
  } //Fin de while       

$sql_eliminar="DELETE FROM mcompras where Factura='$consecutivo' AND Proveedor='$vendedor'";

if(($r_eliminar = odbc_exec($id,$sql_eliminar)) === false)
      die("Error en Select: ". odbc_errormsg($id));


    // Quitar comentario al final
 header("location: devolucion_ingreso.php");
 
?>
