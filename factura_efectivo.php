<?php
  session_start();
  include("db.php");

    $descuento=0;
    $mensajeria=0;
    $vendedor=0;
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];      
    } 
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
          $tipo = $_REQUEST['tipo'];      
    } 
    if(isset($_REQUEST['mensajeria']) && !empty($_REQUEST['mensajeria'])){
      $mensajeria = $_REQUEST['mensajeria'];  
    }
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
      $vendedor = $_REQUEST['vendedor'];  
    }
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    //$fecha=date("Y-m-d");
    $fecha=date("Y-m-d", $fecha_sis);

    $sqlrelleno="SELECT m.ID,m.Factura,m.Codigo,m.Vendedor,m.Precio,m.Cantidad,m.Fecha,i.DESCRIPCION
          FROM tmp_factura m, inventario i WHERE Factura='".$consecutivo."' AND m.Codigo = I.CODIGO  ORDER BY ID";

    if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    while($relleno = odbc_fetch_array($resultadorelleno)){
    
    $totalcod=$relleno['Cantidad']*$relleno['Precio'];
    $csubtotal=$csubtotal+$totalcod;
         
    }

    if ($tipo=='contado') {
      $descuento=$csubtotal*0.1;
    }
    if ($tipo=='frecuente') {
      $descuento=$csubtotal*0.15;
    }
    if ($tipo=='credito') {
      $descuento=$csubtotal*0.1;
    }
    



    header("location: factura_nombre.php?consecutivo=".$consecutivo."&cliente=".$cliente."&descuento=".$descuento."&mensajeria=".$mensajeria."&vendedor=".$vendedor);    
    
  
     

  
   
?>
