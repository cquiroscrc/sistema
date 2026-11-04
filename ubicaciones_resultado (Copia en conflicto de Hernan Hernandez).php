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

      
      echo '<a href="ubicaciones_consultas.php?ubicacion='.$ubicacion.'"><img width="48"  height="48" src= "img/back.png">Volver</a>'

     ?>
    
    <center><h1>Consulta de codigos / Ubicacion <?php echo $ubicacion; ?></h1>
    <div class ="container">
     
    <?php

  

    require_once("db.php");

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
if(isset($_REQUEST['codigoo']) && !empty($_REQUEST['codigoo'])){
      $cod = $_REQUEST['codigoo'];
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
elseif (substr($codigo,0,1)==4) {
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
        //echo $cod; echo '<br>';
        //echo substr($codigo,0,1);
        //echo $sql; exit;
            
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Venta</th>
                    <th>Costo</th>
                    <th>Transito</th>
                    <th>Ubicacion</th>
                    <th></th>';
        
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
            echo '<td>'.number_format($existencia,2).'</td></td><td style="color:green"><b>'.number_format(($venta+ 100),2).'</b></td><td style="color:red">'.number_format($costo,2).'</td>';
          
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
          if(($pedi = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=$arr3["pedido"];
            }
          
          echo '<td>'.number_format($pedidos,2).'</td>';
          echo '<td style="color:blue">'.$ubica.'</td>';

          echo '<form action="ubicaciones_actualiza.php" method="POST">';                
          echo '<input type="hidden" id="codigo"  name="codigo" required autofocus value="'.$codigos.'">';
          echo '<input type="hidden" id="cod"  name="cod" required autofocus value="'.$codigo.'">' ;
          echo '<input type="hidden" id="ubicacion"  name="ubicacion" required autofocus value="'.$ubicacion.'">' ;          
          echo '<input type="hidden" id="marcador"  name="marcador" required autofocus value="'.$marcador1.'">' ;          
          echo '<td><button  type="submit">Agregar</button></td>';
          echo '</form>';



          echo '</tr>';

         $reglon++;
         $contador++;
          			
        }

    }

    ?>

    </div>
    
    
    </center>
    
   
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ubicaciones Resultado';
</script>    
    
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>
