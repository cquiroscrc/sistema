<?php
  session_start();
  include("db.php");

    $mensajeria=0;
    //$vendedor=0;
    $descuento=0;

    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];  
      if ($cliente =='contado' ) {
        $cliente=0;     
      }
        $sqlcliente="SELECT * FROM clientes WHERE codigo = ".$cliente."";
              
          if(($r_cliente = odbc_exec($id,$sqlcliente)) === false)
              die("Error en Select: ". odbc_errormsg($id));

            while($res_cliente = odbc_fetch_array($r_cliente)){
              $nombre = $res_cliente['nombre']; 
          }
          if ($cliente > 10000) {
            // Credito
            $tipo='credito';
          }
          if ($cliente < 10000) {
            // frecuente
            $tipo='frecuente';
          }
          if ($cliente == 0) {
            // contado
            $tipo='contado';
          } 
          if ($cliente ==0 ) {
            $cliente='contado';     
          }      
    } 
    
    if(isset($_REQUEST['mensajeria']) && !empty($_REQUEST['mensajeria'])){
      $mensajeria = $_REQUEST['mensajeria'];  
    }
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
      $descuento = $_REQUEST['descuento'];  
    }
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
      $vendedor = $_REQUEST['vendedor'];  
    }    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    //$fecha=date("Y-m-d");
    //$fecha=date("Y-m-d", $fecha_sis);

    //echo $vendedor;
    //exit;
   // validar el monto del descuento

    $sqlrelleno="SELECT m.ID,m.Factura,m.Codigo,m.Vendedor,m.Precio,m.Cantidad,m.Fecha,i.DESCRIPCION
          FROM tmp_factura m, inventario i WHERE Factura='".$consecutivo."' AND m.Codigo = I.CODIGO  ORDER BY ID";

    if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    while($relleno = odbc_fetch_array($resultadorelleno)){
    
      $totalcod=$relleno['Cantidad']*$relleno['Precio'];
      $csubtotal=$csubtotal+$totalcod;
           
    }
    $cumple='no';
    if (($tipo=='contado') and ($descuento<$csubtotal*0.1)) {
      $cumple='si';
    }
    if (($tipo=='frecuente') and ($descuento<$csubtotal*0.15)){
      $cumple='si';
    }
    if (($tipo=='credito') and ($descuento<$csubtotal*0.1)){
      $cumple='si';
    }  
    if ($cumple=='no') {
      $descuento=0;
    }

    header("location: factura_nombre.php?consecutivo=".$consecutivo."&cliente=".$cliente."&descuento=".$descuento."&mensajeria=".$mensajeria."&vendedor=".$vendedor);    
    
  
     

  
   
?>
