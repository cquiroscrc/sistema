<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    include "menu.php";
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de consulta</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> 
      <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script>
      
      <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <br>
    <a href="consulta_simple.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
    <center><h1>Consulta de codigos</h1>
    <div class ="container">
     
    <?php

  

    //require_once("db.php");
    include("conectarDB.php");
    $cod="XX";
    $familia=2;
    
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

      $resultado = mysql_query($sql);
      if (!$resultado) {
        $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";    
      die($mensaje);
      }

      //if(($resultado = odbc_exec($id,$sql)) === false)
      //        die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
        //echo $cod; echo '<br>';
        //echo substr($codigo,0,1);
        //echo $sql; exit;
            
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Venta</th>
                    <th>Feria</th>';
                    echo'<th>Transito</th>
                    <th>Ubicacion</th>';
                    echo '<th>Imagen</th>';
            echo '</tr>';
        //while($arr = odbc_fetch_array($resultado)){
        while ($arr = mysql_fetch_assoc($resultado)) {
      

      //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $existencia=$arr["EXISTENCIA"];
          $costo=$arr["COSTO"];
          $venta=($arr["VENTA"]+100)*1.13;
          $costodol=$arr["costodol"];
          $proveedor=$arr["proveedor"];
          $ubicacion=$arr["ubicacion"];
          $desc_tecnica=$arr["DESC_ALT"];
          
          /// Dia del vocho 21% descuento
            $d_vocho=($arr['VENTA']+100)*0.21;
            $d_vocho=($arr['VENTA']+100)-$d_vocho;
            $d_vocho=$d_vocho*1.13;
            //$feria=$arr["Venta"]/1.12;
            $feria=$d_vocho;
            /// Dia del vocho
            
            $div_feria=$feria/500;
            $div_feria=round($div_feria);
            $feria=$div_feria*500;
          
          
            echo '<td>'.$codigos.'</td>';
            echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
            echo '<td>'.number_format($existencia,2).'</td><td style="color:green"><b>'.number_format($venta,2).'</b></td>';
            echo '<td style="color:red">'.number_format($feria,2).'</td>';
            
          
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";

          $pedi = mysql_query($sql3);
          if (!$pedi) {
            $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";    
          die($mensaje);
          }

          //if(($pedi = odbc_exec($id,$sql3)) === false)
          //    die("Error en Select: ". odbc_errormsg($id));  

            //while($arr3 = odbc_fetch_array($pedi)){
            while ($arr3 = mysql_fetch_assoc($pedi)) {
              $pedidos=$arr3["pedido"];
            }
          
          echo '<td>'.number_format($pedidos,2).'</td><td>'.$ubicacion.'</td>';
          // ingresar imagen************
          $path='subida_cat/';

          if (substr($codigos,0,1)==1) {
            $directorio='1/';
          }
          if (substr($codigos,0,1)==2) {
            $directorio='2/';
          }
          if (substr($codigos,0,1)==3) {
            $directorio='3/';
          }
          if (substr($codigos,0,1)==9) {
            $directorio='9/';
          }
          if (substr($codigos,0,1)==0) {
            $directorio='0/';
          }
          $codigos1=rtrim($codigos);
          if (substr($codigos,-2)=='EU') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
             
          } 
          if (substr($codigos,-2)=='GR') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }
          if (substr($codigos,-2)=='TW') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
             
          }
          if (substr($codigos,-2)=='JP') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }
          if (substr($codigos,-2)=='ZL') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }

          $foto=substr ( $codigos1 , 1 );
          $foto=rtrim($foto);

          if (substr($foto,0,1)==' ') {
                //echo 'FOTOS';
                $foto=substr($foto, 2, strlen($foto));
              }
              
          $filename = $path.$directorio.$foto.".jpg";
          $archivo=$filename;

          if (file_exists($filename)) {
              $archivo=$filename;
          } else {
              $archivo=$path."inf.png";
          }

          echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';

          echo '</tr>';
         $reglon++;
          			
        }

    }

    ?>

    </div>
    
    
    </center>
    
   <script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>
    
    <script>// poner Hora
      var myVar = setInterval(function(){myTimer()},1000);
      function myTimer() {
      var d = new Date();
      document.getElementById("demo").innerHTML = d.toLocaleTimeString();
      }
    </script>
    <script type="text/javascript" language="JavaScript">
      document.title = 'Sistema / Consulta simple';
    </script>
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>