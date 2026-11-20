<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['cod']) && !empty($_REQUEST['cod'])){
          $cod = $_REQUEST['cod'];      
    } 
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion'];      
    } 
    if(isset($_REQUEST['tecnica']) && !empty($_REQUEST['tecnica'])){
          $tecnica = $_REQUEST['tecnica'];      
    } 
    if(isset($_REQUEST['existencia']) && !empty($_REQUEST['existencia'])){
          $existencia = $_REQUEST['existencia'];      
    } 
    if(isset($_REQUEST['costo']) && !empty($_REQUEST['costo'])){
          $costo = $_REQUEST['costo'];      
    } 
    if(isset($_REQUEST['venta']) && !empty($_REQUEST['venta'])){
          $venta = $_REQUEST['venta'];      
    } 
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
          $descuento = $_REQUEST['descuento'];      
    } 
    if(isset($_REQUEST['iv']) && !empty($_REQUEST['iv'])){
          $iv = $_REQUEST['iv'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    }
    if(isset($_REQUEST['bodega']) && !empty($_REQUEST['bodega'])){
          $bodega = $_REQUEST['bodega']; 
    }
    if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
          $ubicacion = $_REQUEST['ubicacion']; 
    }
     if(isset($_REQUEST['act']) && !empty($_REQUEST['act'])){
          $act = $_REQUEST['act'];      
    } 
    ///  para arriba modificar y actualizar
    // para abajo inserta
   
    if(isset($_REQUEST['costodol']) && !empty($_REQUEST['costodol'])){
          $costodol = $_REQUEST['costodol'];      
    } 
    if(isset($_REQUEST['factorcosto']) && !empty($_REQUEST['factorcosto'])){
          $factorcosto = $_REQUEST['factorcosto'];      
    } 
    if(isset($_REQUEST['factorprecio']) && !empty($_REQUEST['factorprecio'])){
          $factorprecio = $_REQUEST['factorprecio'];      
    } 
    if(isset($_REQUEST['fpanterior']) && !empty($_REQUEST['fpanterior'])){
          $fpanterior = $_REQUEST['fpanterior'];      
    } 
    if(isset($_REQUEST['potencia']) && !empty($_REQUEST['potencia'])){
          $potencia = $_REQUEST['potencia'];      
    } 
    if(isset($_REQUEST['carro']) && !empty($_REQUEST['carro'])){
          $carro = $_REQUEST['carro'];      
    } 
    if(isset($_REQUEST['reserva']) && !empty($_REQUEST['reserva'])){
          $reserva = $_REQUEST['reserva'];      
    } 

    if ($act=0) {
      $sql = "INSERT INTO hacer_pedido
        (codigo,cantidad,proveedor,pedido)
        values
        ('".$codigo."','".$cantidad."','".$s_proveedor."','".$s_pedido."')";
    
    }else{
      $sql = "UPDATE inventario SET CODIGO='".$codigo."',
                        DESCRIPCION='".$descripcion."',
                        DESC_ALT='".$tecnica."'
                    WHERE CODIGO='".$codigo."'";
                        
    }

   // echo $sql; exit;
   if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
            
    while($arr = odbc_fetch_array($resultado)){ 

       
   }
       header("location: descripcion_resultado.php?codigo=".$cod);
   
?>
