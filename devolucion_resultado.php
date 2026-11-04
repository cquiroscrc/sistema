<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
include "menu.php";
if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
            $consecutivo = $_REQUEST['consecutivo'];      
    } 
    $vendedor = $_REQUEST['vendedor'];      
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
        $vendedor = $_REQUEST['vendedor'];      
    } 
    $cliente = $_REQUEST['cliente'];    
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
        $cliente = $_REQUEST['cliente'];      
    } 
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];      
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
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de consulta</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      
      <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <center><h1>Consulta de codigos</h1>
    <div class ="container">
     <br>
     <?php 
      echo '<a class= "agregarUsuario" href="devolucion_factura.php?consecutivo='.$consecutivo.
      '&vendedor='.$vendedor.'&cliente='.$cliente.'&fecha='.$fecha.'&subtotal='.$subtotal.'&descuento='
      .$descuento.'&impuesto='.$impuesto.'&total='.$total.'">
      <img width="48"  height="48" src= "img/back.png">Volver</a>';
     ?>
    
    <?php

  

    require_once("db.php");
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
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];
    } 

    if (substr($cod,0,5)=='1___ ') {
      $codigo=$cod;
    }
    else{
      $cant=stripos($cod,"_",0);
      if ($cant<>0) {
        $codigo= substr($cod,0,$cant);  
      }else{
        $codigo=$cod;
      }
    }
    $codigo= trim($codigo);
    $codigobusq = '';
    
    if ($familia==1) {
      $codigo='1___ '.$codigo;

    }
    //echo $codigo; exit;
    $suge=0;
    if ($codigo != ""){
    	$reglon=1;
      
      if (substr($codigo,0,1)==1 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==0 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==2) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==3) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==9) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }elseif (substr($codigo,0,1)==8) {
            $fam=substr($codigo,1,1);
            $des=substr($codigo,3,strlen($codigo));
           $sql="SELECT * FROM inventario WHERE codigo like '$fam%' 
           AND descripcion like '%$des%' order by descripcion";    
      }

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
        //echo $codigo;
        
        //echo substr($codigo,0,1);
        //echo $sql; exit;
            echo $vendedor;
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Costo</th><th>Venta</th>';
        
            echo '</tr>';
        while($arr = odbc_fetch_array($resultado)){
       

      //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $existencia=$arr["EXISTENCIA"];
          $costo=$arr["COSTO"];
          $venta=$arr["VENTA"];
          $potencia=$arr["potencia"];
          if ($potencia<>0) {
            $potencia=$potencia*100;
          }
          $venta=$venta+$potencia;
          $costodol=$arr["costodol"];
         


          
            echo '<td><a href="devolucion_codigo.php?consecutivo='.$consecutivo.'&sele='.$codigos.'&precioanterior='.$costo.'&cliente='.$cliente.'&vendedor='.$vendedor.'&fecha='.$fecha.'&subtotal='.$subtotal.'&descuento='.$descuento.'&impuesto='.$impuesto.'&total='.$total.'">'.$codigos.'</a></td>';
            echo '<td>'.$descripcion.'</td>';
            echo '<td>'.number_format($existencia,2).'</td></td><td>'.number_format(($costo),2).'</td><td>'.number_format(($venta),2).'</td></tr>';
         $reglon++;
          			
        }

    }

    ?>

    </div>
    
    
    </center>
    
   
    
   
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>