<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }

    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
      $fecha2 = $_REQUEST['Date2'];
    }

    include "menu.php";
     require("db.php");
?>
<!DOCTYPE>
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
    
    <!--
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a> -->
    <a href="ventas_dia.php"><img width="48"  height="48" src= "img/back.png">Volver</a>
    <?php 
      echo '<a href="ventas_imprime.php?Date2='.$fecha2.'"><img width="48"  height="48" src= "img/PDF.png">Generar PDF</a>';
     ?>
    <center><h1>Consulta de codigos vendidos el dia</h1>
    <div id ="muestra">
     
    <?php

           
      echo '<center><table border =1>
        <tr class="encabezadoRegistro">
        <th WIDTH=250>Codigo</th>
        <th WIDTH=200>Descripcion</th>
        <th>Cantidad</th>
        <th>Movimiento</th>
        <th>Ubicacion</th>
        <th>Existencia</th>
        </th>';
      echo '</tr>';
      $fecha2 = strtotime ($fecha2 ) ;
      $fecha = date('Y-m-d',$fecha2);
      //$newdate = strtotime ( '+30 day' , strtotime ( $fecha ) ) ;
      //$newdate = date ( 'Y-m-d h:i:s.m' , $newdate );


      $sql="SELECT h.fecha, h.codigo, h.cantidad, h.movimiento, i.DESCRIPCION,i.ubicacion, i.existencia,
        substring(h.CODIGO,1,1),substring(h.CODIGO,6,3 ),substring(h.CODIGO,10,15),Case substring(h.CODIGO, 1, 1)
        WHEN '0' then substring(h.CODIGO,1,1)+substring(h.CODIGO,5,4 )+substring(h.CODIGO,9,4 )+' '+substring(h.CODIGO,1,4 )+' '+ltrim(rtrim(substring(h.CODIGO,13,12 )))
        WHEN '1' then substring(h.CODIGO,1,1)+substring(h.CODIGO,5,4 )+substring(h.CODIGO,9,4 )+' '+substring(h.CODIGO,1,4 )+' '+ltrim(rtrim(substring(h.CODIGO,13,12 )))      
        Else h.CODIGO END As Filtro from HGENERAL h, inventario i where fecha > '".$fecha." 00:00:00.000'  
        and fecha < '".$fecha ." 23:59:59.999'  AND h.codigo= i.CODIGO AND h.CODIGO <> 'SP' AND h.codigo <> '9SM' order by Filtro";
          
     //echo $sql;
     //exit;
      
      if(($pedi = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));  

      while($arr = odbc_fetch_array($pedi)){
        $codigos=$arr["codigo"];
        $descripcion=$arr["DESCRIPCION"];
        $cantidad=$arr["cantidad"];
        $movimiento=$arr["movimiento"];
        $ubica=$arr["ubicacion"];
        $existencia= $arr["existencia"];
              
        echo '<td>'.$codigos.'</td>';
        echo '<td>'.$descripcion.'</td>';
        echo '<td>'.number_format($cantidad,2).'</td>';
        echo '<td>'.$movimiento.'</td>';
        echo '<td>'.$ubica.'</td>';
        echo '<td>'.number_format($existencia,2).'</td>';
        echo '</tr>';
      }

    ?>
    

    </div>
    
    
    </center>
    
   
  <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / V Diarias';
  </script>    
    
  <script src="js/bootstrap.min.js"></script> 
</body>
</html>