<?php
  session_start();
  include("db.php");


    $cod="XX";
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $cod = $_REQUEST['codigo'];
      $familia=0;
    }

    if(isset($_REQUEST['codigovw']) && !empty($_REQUEST['codigovw'])){
      $cod = $_REQUEST['codigovw'];
      $familia=0;
    }
    if(isset($_REQUEST['codigovwn']) && !empty($_REQUEST['codigovwn'])){
      $cod = $_REQUEST['codigovwn'];
      $familia=0;
    }
    if(isset($_REQUEST['codigovwf']) && !empty($_REQUEST['codigovwf'])){
      $cod = $_REQUEST['codigovwf'];
      $familia=1;
      
    }
    if(isset($_REQUEST['codigod']) && !empty($_REQUEST['codigod'])){
      $cod = $_REQUEST['codigod'];
      $familia=0;
    }
    if(isset($_REQUEST['codigoa']) && !empty($_REQUEST['codigoa'])){
      $cod = $_REQUEST['codigoa'];
      $familia=0;
    }
    if(isset($_REQUEST['codigov']) && !empty($_REQUEST['codigov'])){
      $cod = $_REQUEST['codigov'];
      $familia=0;
    }
    if(isset($_REQUEST['codigou']) && !empty($_REQUEST['codigou'])){
      $cod = $_REQUEST['codigou'];
      $familia=0;
    } 
    if(isset($_REQUEST['codigodes']) && !empty($_REQUEST['codigodes'])){
      $cod = '8'.$_REQUEST['codigodes'];
      $familia=0;
    } 
    $cant=stripos($cod,"_",0);
    
      if ($cant<>0) {
        $codigo= substr($cod,0,$cant);  
      }else{
        $codigo=$cod;
      }
    
    $codigo= trim($codigo);

  ///Normal

    
    if(isset($_REQUEST['cod']) && !empty($_REQUEST['cod'])){
          $cod = $_REQUEST['cod'];      
    } 
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion'];      
    } 
    if(isset($_REQUEST['tecnica']) && !empty($_REQUEST['tecnica'])){
          $tecnica = $_REQUEST['tecnica'];      
    } 
    $existencia = $_REQUEST['existencia'];    
    if(isset($_REQUEST['existencia']) && !empty($_REQUEST['existencia'])){
          $existencia = $_REQUEST['existencia'];      
    } 
      $costo = $_REQUEST['costo'];
    if(isset($_REQUEST['costo']) && !empty($_REQUEST['costo'])){
          $costo = $_REQUEST['costo'];      
    } 
    $venta = $_REQUEST['venta'];   
    if(isset($_REQUEST['venta']) && !empty($_REQUEST['venta'])){
          $venta = $_REQUEST['venta'];      
    } 
     $descuento = $_REQUEST['descuento'];   
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
          $descuento = $_REQUEST['descuento'];      
    } 
    $iv = $_REQUEST['iv'];      
    if(isset($_REQUEST['iv']) && !empty($_REQUEST['iv'])){
          $iv = $_REQUEST['iv'];      
    } 
    $proveedor = $_REQUEST['proveedor'];      
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    }
    $bodega = 1; 
    if(isset($_REQUEST['bodega']) && !empty($_REQUEST['bodega'])){
          $bodega = $_REQUEST['bodega']; 
    }
    if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
          $ubicacion = $_REQUEST['ubicacion']; 
    }
     if(isset($_REQUEST['act']) && !empty($_REQUEST['act'])){
          $act = $_REQUEST['act'];      
    } 
    if(isset($_REQUEST['marca']) && !empty($_REQUEST['marca'])){
          $marca = $_REQUEST['marca'];      
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

    $reserva=0;
    
      $sql = "INSERT INTO inventario (CODIGO,DESCRIPCION,DESC_ALT,EXISTENCIA,COSTO,VENTA,descuento,IV,proveedor,BODEGA,ubicacion,MARCA,UNIDAD_MEDIDA,CODIGO_IMPUESTO) values           
              ('".$codigo."','".$descripcion."','".$tecnica."','".$existencia."','".$costo."','".$venta."','".$descuento."','".$iv."','".$proveedor."','".$bodega."','".$ubicacion."','".$marca."','Unid','01')";
                        
    

    echo $sql;
   if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
            
    //while($arr = odbc_fetch_array($resultado)){ 

       
   
       header("location: mantenimiento_resultado.php?codigo=".$codigo);
   
?>
