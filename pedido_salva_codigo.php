<?php
  session_start();
  include("db.php");

    
    if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
        $factura = $_REQUEST['fact'];      
    } 
   if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
          $precio = $_REQUEST['precio'];      
    } 
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
          $cantidad = $_REQUEST['cantidad'];      
    } 
     if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    } 

    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    $fecha=date("Y-m-d");

    $sql_prove="SELECT Proveedor FROM Pedidos WHERE Factura='$factura'";
    
      if(($prov = odbc_exec($id,$sql_prove)) === false)
        die("Error en Select: ". odbc_errormsg($id));
            
      while($arr_prove = odbc_fetch_array($prov)){
        $proveedor=$arr_prove['Proveedor'];      
      }
    
    $factor=1;
    $sugerido=$precio;
    $procedencia='-';

    $sql_precio="SELECT VENTA FROM inventario WHERE CODIGO='$codigo'";
    
      if(($pre = odbc_exec($id,$sql_precio)) === false)
        die("Error en Select: ". odbc_errormsg($id));
            
      while($arr_precio = odbc_fetch_array($pre)){
        
        $precioanterior=$arr_precio['VENTA'];
      }
    
    $factorcosto=0;
   
  
     $sql_Insert="INSERT INTO Pedidos (Factura,Codigo,Proveedor,Precio,Cantidad,factor,Sugerido,Procedencia,
      PrecioAnterior,Fecha,Caja,Etiquetas,FactorCosto)
        values ('".$factura."','".$codigo."','".$proveedor."','".$precio."','".$cantidad."','".$factor."','".$sugerido."','".$procedencia."','".$precioanterior."','".$fecha."','".$factor."','".$cantidad."','".$factorcosto."')"; 
      
      //echo $sql_Insert; exit;
      
      if(($r_insert = odbc_exec($id,$sql_Insert)) === false)
        die("Error en Select: ". odbc_errormsg($id));
  

    header("location: pedido_mostrar.php?fact=".$factura."&proveedor=".$proveedor);
   
?>
