

<?php
  session_start();
  include("db.php");

    $mensajeria=0;
    $descuento=0;
    //$vendedor=0;
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];      
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
    $fecha=date("Y-m-d", $fecha_sis);

    $mensajeria=$mensajeria*1000;


    header("location: factura_nombre.php?consecutivo=".$consecutivo."&cliente=".$cliente."&descuento=".$descuento."&mensajeria=".$mensajeria."&vendedor=".$vendedor);    
    
  
     

  
   
?>
