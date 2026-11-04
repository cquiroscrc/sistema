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

    $sql="UPDATE Pedidos SET Etiquetas=0 WHERE Factura='$factura' AND Proveedor='$proveedor'"; 
  //echo $sql_cxp.'<br>';

  if(($r_cxp = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  

    // Quitar comentario al final
 header("location: pedido_cargar.php");
    
?>
