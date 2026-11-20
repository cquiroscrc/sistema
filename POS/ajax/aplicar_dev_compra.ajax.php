<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");


    
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
        $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
        $proveedor = $_REQUEST['proveedor'];      
    } 
    if(isset($_REQUEST['subtotal']) && !empty($_REQUEST['subtotal'])){
        $subtotal = $_REQUEST['subtotal']; 
        $subtotal= $subtotal*-1;

    }   
    $descuento=0;
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
        $descuento = $_REQUEST['descuento'];      
        $descuento = $descuento *-1;
    } 
    $impuesto=0;
    if(isset($_REQUEST['impuesto']) && !empty($_REQUEST['impuesto'])){
        $impuesto = $_REQUEST['impuesto'];      
        $impuesto = $impuesto *-1;
    } 
    if(isset($_REQUEST['total']) && !empty($_REQUEST['total'])){
        $total = $_REQUEST['total'];      
        $total = $total*-1;
    }

    $id_acceso=$_SESSION["id"];

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

    if ($proveedor==9) {
      $tipo='Contado';
      $dias='0';
      $newdate = strtotime ( $fecha_compra)  ;
      $fecha = strtotime ( $fecha_compra)  ;
      
      $estado='Cancelada';  
    }


   
    
    //insertar la compra
  $sqlinsertcompra="INSERT INTO compras (Factura, proveedor,fecha,tipo,subtotal,impuesto,descuento,total) values
    ('".$consecutivo."','".$proveedor."','".date("Y-m-d h:i:s.m")."','".$tipo."','".$subtotal."','".$impuesto."','".$descuento."','".$total."')";

  //echo $sqlinsertcompra .'<br>';  
  $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlinsertcompra);
  
  // Insertar CXP
    $sql_cxp="INSERT INTO cxp (NFac,Proveedor,FechaFact,Tipo,DiasCredito,FechaLimite,SubTotal,Descuento,Impuesto,Total,Saldo,Estado,chkCancelacion)
    values ('".$consecutivo."','".$proveedor."','".date("Y-m-d h:i:s.m")."','".$tipo."','".$dias."','".$newdate."','".$subtotal."','".$descuento."','".$impuesto."','".$total."','".$total."','".$estado."','0')"; 
  //echo $sql_cxp.'<br>';

  $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cxp);
  //entra el ciclo

  $sql = "SELECT * FROM mcompras WHERE Factura='$consecutivo' AND Proveedor='$proveedor'";
  
  //echo $sql .'<br>';  
  $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  foreach ($ejecutar as $key => $arr) { 
    $codigo=$arr['Codigo'];
    $cantidad=$arr['Cantidad'];
    $precio=$arr['Precio'];
    $precioanterior=$arr['PrecioAnterior'];
    $preciosugerido=$arr['PrecioSugerido'];
    
   //echo $codigo.'<br>';
    
    $sql_inventario="SELECT * FROM inventario WHERE CODIGO = '$codigo'";
    
     $ejecutar2 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_inventario);
     foreach ($ejecutar2 as $key => $a_inventario) { 
        $existencia=$a_inventario['EXISTENCIA'];

   //echo $existencia.'<br>';
     }//existencia

    //Update existencias 
      $actual=(int)$existencia-(int)$cantidad;
      $sql_update="UPDATE inventario SET EXISTENCIA='$actual', 
                                          VENTA='$precio', 
                                          COSTO='$preciosugerido', 
                                          proveedor='$proveedor' WHERE CODIGO='$codigo'";
  // echo $sql_update.'<br>';
   
   $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);
    
    //Insert Hgeneral

    $sql_insert="INSERT INTO hgeneral 
    (Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,
      precio,estado) values 
    ('".$consecutivo."','".date("Y-m-d h:i:s.m")."','".$codigo."','".$cantidad."','COMPRA','".$tipo."',
      '".$proveedor."','1','".$id_acceso."','1','".$existencia."','".$actual."','".$precio."','Aplicado')";
   //echo $sql_insert.'<br>';
     
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_insert);
    
  //Insertar hmcompras
   $sql_hmcompras="INSERT INTO hmcompras (Factura,Codigo,Proveedor,Tipo,Precio,Cantidad,PrecioAnterior,PrecioSugerido,TotalLinea,Fecha) values
   ('".$consecutivo."','".$codigo."','".$proveedor."','".$tipo."','".$precio."','".$cantidad."','".$precioanterior."','".$preciosugerido."','".$preciosugerido*$cantidad."','".date("Y-m-d h:i:s.m")."')";
   //echo $sql_hmcompras.'<br>';

   $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_hmcompras);


  } //Fin de while       

$sql_eliminar="DELETE FROM mcompras where Factura='$consecutivo' AND Proveedor='$proveedor'";

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_eliminar);


    
?>
