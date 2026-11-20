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

      
      echo '<a href="sucursal_consultas.php"><img width="48"  height="48" src= "img/back.png">Volver</a>'

     ?>
    
    <center><h1>Consulta de codigos Sucursal</h1>
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
                    <th>Suc</th>
                    <th>Ubicacion2</th>
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
          $existe='no';
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
          $esta_pedido=0;
          $ubica2="SINUB";
          $sql4="SELECT  cantidad, Ubicacion from tbl_exi_suc where codigo = '$codigos'";
          if(($suc = odbc_exec($id,$sql4)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr4 = odbc_fetch_array($suc)){
              $esta_pedido=$arr4["cantidad"];
              $ubica2=$arr4["Ubicacion"];
               if (isset($esta_pedido)) {
                  $existe='si';
               }else{
                  $existe='no';
               }
            }
          echo '<td>'.number_format($pedidos,2).'</td>';
          echo '<td style="color:blue">'.$ubica.'</td>';

         echo '<form action="sucursal_incluir.php" method="POST">';
              if ($esta_pedido==0) {
                echo '<td  style="color:black"><input type="text" id="sugerido"  name="sugerido" required autofocus value="'.$esta_pedido.'"></td>'; 
                echo '<td  style="color:green">00X00</td>';   
              }else{
                echo '<td  style="color:blue"><b><input type="text" id="sugerido"  name="sugerido" required autofocus value="'.$esta_pedido.'"></td>'; 
                echo '<td  style="color:green">'.$ubica2.'</td>';               
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



          echo '</tr>';

         $reglon++;
         $contador++;
          			
        }

    }

    ?>

    </div>
    
    
    </center>
    
   
    
<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Suc Resultado';
</script>     
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>