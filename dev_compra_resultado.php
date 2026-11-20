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
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
        $proveedor = $_REQUEST['proveedor'];      
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
    <?php 
      echo '<a  href="dev_compras_factura.php?consecutivo='.$consecutivo.
      '&proveedor='.$proveedor.'&subtotal='.$subtotal.'&descuento='
      .$descuento.'&impuesto='.$impuesto.'&total='.$total.'">
      <img width="48"  height="48" src= "img/back.png">Volver</a>';
     ?>
    <center><h1><span class="glyphicon glyphicon-share" aria-hidden="true"></span>&nbsp;Consulta de codigos</h1> 
    <div class ="container">
     <br>
     
    
    <?php

  

    require_once("db.php");
    //// *************** Incluye recibir ******************
    include "consulta_recibe.php";
    //// *************** Incluye recibir ******************

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
      
      //// *************** Incluye la consulta ******************
      include "consulta_resultado.php";
      //// *************** Incluye la consulta ******************

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
        //echo $codigo;
        
        //echo substr($codigo,0,1);
        //echo $sql; exit;
        echo "<h1>".$codigo."</h1>"; 
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
          $costodol=$arr["costodol"];
         


          
            echo '<td><a href="dev_compras_codigo.php?consecutivo='.$consecutivo.'&sele='.$codigos.'&precioanterior='.$costo.'&proveedor='.$proveedor.'&subtotal='.$subtotal.'&descuento='.$descuento.'&impuesto='.$impuesto.'&total='.$total.'">'.$codigos.'</a></td>';
            echo '<td>'.$descripcion.'</td>';
            echo '<td>'.number_format($existencia,2).'</td></td><td>'.number_format(($costo),2).'</td><td>'.number_format(($venta),2).'</td></tr>';
         $reglon++;
          			
        }

    }

    ?>

    </div>
    
    
    </center>
    
   
    
    <script>// poner Hora
      var myVar = setInterval(function(){myTimer()},1000);
      function myTimer() {
      var d = new Date();
      document.getElementById("demo").innerHTML = d.toLocaleTimeString();
      }
    </script>
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>