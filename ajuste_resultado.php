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
    <br>
    <a href="ajuste.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
    <center><h1>Consulta de codigos</h1>
    <div class ="container">
     <br>
    
    <?php

  

    require_once("db.php");
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];
    } 
     //// *************** Incluye recibir ******************
      include "consulta_recibe.php";
      //// *************** Incluye recibir ******************
    $suge=0;
    if ($codigo != ""){
    	$reglon=1;
      
      //// *************** Incluye la consulta ******************
      include "consulta_resultado.php";
      //// *************** Incluye la consulta ******************

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
       echo "<h1>".$codigo."</h1>";      
      //ingresa nombres de cuadro
        //echo $codigo;
        
        //echo substr($codigo,0,1);
        //echo $sql; exit;
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Costo</th>
                    <th>Ubicacion</th>';
        
            echo '</tr>';
        while($arr = odbc_fetch_array($resultado)){
       

      //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $existencia=$arr["EXISTENCIA"];
          $venta=$arr["COSTO"];
          
          $costodol=$arr["costodol"];
          $proveedor=$arr["proveedor"];
          $desc_tecnica=$arr["DESC_ALT"];
          $ubicacion=$arr["ubicacion"];


          
            echo '<td><a href="ajuste_codigo.php?consecutivo='.$consecutivo.'&sele='.$codigos.'">'.$codigos.'</a></td>';
            echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
            echo '<td>'.number_format($existencia,2).'</td></td><td>'.number_format(($venta),2).'</td><td>'.$ubicacion.'</td></tr>';
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