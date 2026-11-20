<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");

    
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
        $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
        $proveedor = $_REQUEST['proveedor']; 
        if (strlen($proveedor)>5) {
                $proveedor = strstr($proveedor, ' ', true); // Desde PHP 5.3.0
            }       
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
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
        $fecha_compra = $_REQUEST['Date2'];      
    }
    
   


    if ($proveedor==1) {
      $tipo='Contado';
      $dias='0';
      $newdate = strtotime ( $fecha_compra)  ;
      $fecha = strtotime ( $fecha_compra)  ;
      
      $estado='Cancelada';  
    } else{
      $tipo='Credito';
      $dias='30';
      $newdate = strtotime ( $fecha_compra)  ;
      $fecha = strtotime ( '+30 day' , strtotime ( $fecha_compra ) ) ;
      //$newdate = date ( 'Y-m-d h:i:s.m' , $newdate );
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
    ('".$consecutivo."','".$proveedor."','".date("Y-m-d h:i:s.m",$newdate)."','".$tipo."','".$subtotal."','".$impuesto."','".$descuento."','".$total."')";

  if(($r_compra = odbc_exec($id,$sqlinsertcompra)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  // Insertar CXP
    $sql_cxp="INSERT INTO cxp (NFac,Proveedor,FechaFact,Tipo,DiasCredito,FechaLimite,SubTotal,Descuento,Impuesto,Total,Saldo,Estado,chkCancelacion)
    values ('".$consecutivo."','".$proveedor."','".date("Y-m-d h:i:s.m",$newdate)."','".$tipo."','".$dias."','".date("Y-m-d h:i:s.m",$fecha)."','".$subtotal."','".$descuento."','".$impuesto."','".$total."','".$total."','".$estado."','0')"; 

  if(($r_cxp = odbc_exec($id,$sql_cxp)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  //entra el ciclo

  
 header("location: temp_compras.php");
    
?>
