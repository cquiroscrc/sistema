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
    <style type="text/css">

    .botones{
        width:850px;
        height: 75px;
        padding:15px;
        background-color:#eee;
        border:1px solid #444;
    }
    input{
        
        background-color:white;
        width: 50px;
    }
    </style>
    <br>
    <?php 

      if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
        $ubicacion = $_REQUEST['ubicacion'];        
      } 

      
      echo '<a href="eventos_consultas.php"><img width="48"  height="48" src= "img/back.png">Volver</a>'

     ?>
    
    <center><h1>Consulta de codigos Eventos</h1>
    <div class ="container">
     
    <?php

  

    require_once("db.php");

    $cod="XX";
    $familia=2;
    
    
    
     //// *************** Incluye recibir ******************
      include "consulta_recibe.php";
      //// *************** Incluye recibir ******************
    
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
        //echo $cod; echo '<br>';
        //echo substr($codigo,0,1);
        //echo $sql; exit;
         echo "<h1>".$codigo."</h1>";   
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Venta</th>
                    <th>Transito</th>
                    <th>Ubicacion</th>
                    <th>Imagen</th>
                    <th>Cantidad</th>
                    <th>Agregar</th>
                    </th>';
        
            echo '</tr>';

            $marcador1='';
            $marcador2='inicio';
            $marcador3='inicio';
            $contador=1;

        while($arr = odbc_fetch_array($resultado)){
       

      //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $existencia=$arr["EXISTENCIA"];
          $costo=$arr["COSTO"];
          $venta=$arr["VENTA"];
          $costodol=$arr["costodol"];
          $proveedor=$arr["proveedor"];
          $ubica=$arr["ubicacion"];
          $reserva=$arr["Reserva"];
          

          if ($marcador1=='') {
            $marcador=$codigos;
            $marcador1='inicio';
          }
          
          if ($contador==2) {
            $marcador1=$marcador2;
            $marcador2=$marcador3;
            $marcador3=$codigos;
            
          }
          if ($contador==3) {
            $marcador1=$marcador2;
            $marcador2=$marcador3;
            $marcador3=$codigos;
            $contador=1;
          }

          
            echo '<td><a name="'.$codigos.'" id="a"></a>'.$codigos.'</td>';
            echo '<td>'.$descripcion.'</td>';
            echo '<td>'.number_format($existencia,2).'</td></td><td style="color:green"><b>'.number_format(($venta+ 100),2).'</b></td>';
          
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
          if(($pedi = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=$arr3["pedido"];
            }
            $existe='no';
            $esta_pedido=0;
            $sql_pedido = "SELECT codigo, cantidad FROM tbl_TrasOUT where codigo='$codigos'";
            if(($r_pedi = odbc_exec($id,$sql_pedido)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($row = odbc_fetch_array($r_pedi)){
              $existe='si';
              $esta_pedido= $row['cantidad'];
            }
          
          echo '<td>'.number_format($pedidos,2).'</td>';
          echo '<td style="color:blue">'.$ubica.'</td>';
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
          if (substr($codigos,0,1)==4) {
            $directorio='4/';
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
          $filename = $path.$directorio.$foto.".jpg";
          $archivo=$filename;

          if (file_exists($filename)) {
              $archivo=$filename;
          } else {
              $archivo=$path."inf.png";
          }

          echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';


          
          echo '<form action="eventos_incluir.php" method="POST">';
              if ($esta_pedido==0) {
                echo '<td  style="color:black"><input type="text" id="sugerido"  name="sugerido" required autofocus value="'.$esta_pedido.'"></td>';    
              }else{
                echo '<td  style="color:blue"><b><input type="text" id="sugerido"  name="sugerido" required autofocus value="'.$esta_pedido.'"></td>';                
              }
              echo '<input type="hidden" id="existe"  name="existe" required autofocus value="'.$existe.'">';
              echo '<input type="hidden" id="codigo"  name="codigo" required autofocus value="'.$codigos.'">';
              echo '<input type="hidden" id="cod"  name="cod" required autofocus value="'.$codigo.'">' ;            
              echo '<input type="hidden" id="marcador"  name="marcador" required autofocus value="'.$marcador1.'">' ;           
              if ($esta_pedido==0) {
                echo '<td><button  type="submit">OK</button></td>';
              }else{
                echo '<td style="color:red"><button  type="submit">OK</button></td>';
              }
              echo '</form>';
              echo '<tr>';

         $reglon++;
         $contador++;
          			
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
   
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Reserva Resultado';
</script>    
    
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>