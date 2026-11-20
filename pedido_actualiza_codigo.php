<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['ID']) && !empty($_REQUEST['ID'])){
        $id_codigos = $_REQUEST['ID'];      
    } 
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
    if(isset($_REQUEST['marcador']) && !empty($_REQUEST['marcador'])){
          $marcador = $_REQUEST['marcador'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    } 

    $sql="SELECT * FROM inventario WHERE CODIGO ='$codigo'";  

          if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
          while($arr = odbc_fetch_array($resultado)){
          
              $anterior=$arr["VENTA"];
            
           } 

    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    $fecha=date("Y-m-d");
   //Percent = txtFields(1) / txtFields(0)  'Sub/Descuento/Lineas
  //NCost = txtCosto - (txtCosto * Percent)
   

      $sql_update="UPDATE Pedidos SET Codigo='$codigo', 
                                          Precio='$precio', 
                                          Cantidad='$cantidad',
                                          PrecioAnterior='$anterior'
                                          WHERE ID='$id_codigos'";
      
      //echo $sql_update; exit;
      
      if(($resultado = odbc_exec($id,$sql_update)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
        

    header("location: pedido_mostrar.php?fact=".$factura."&proveedor=".$proveedor."#".$marcador);
   
?>
