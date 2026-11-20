<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    include "menu.php";

if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $Date1 = $_REQUEST['Date1'];        
} 

if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
  $Date2 = $_REQUEST['Date2'];
}

$c_vw=0;
$c_d=0;
$c_v=0;
$c_a=0;
$c_u=0;
$v_vw=0;
$v_d=0;
$v_v=0;
$v_a=0;
$v_u=0;
$e_vw=0;
$e_d=0;
$e_v=0;
$e_a=0;
$e_u=0;
$i_vw=0;
$i_d=0;
$i_v=0;
$i_a=0;
$i_u=0;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de consulta</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <br>
    <?php 
    echo '<a href="consulta_ventas_mes_totales.php?Date1='.$Date1.'&Date2='.$Date2.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
     ?>
  
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Tabla</a>

    
    
  <?php

  

    require_once("db.php");

    
    $sql ="SELECT h.CODIGO ,MAX(h.FECHA)as fecha,sum(h.cantidad) as cantidad ,h.precio,i.existencia,i.costo,i.DESCRIPCION,
      substring(h.codigo,1,1),substring(h.codigo,6,3 ),substring(h.codigo,10,15),Case substring(h.codigo, 1, 1)
      WHEN '0' then substring(h.codigo,1,1)+substring(h.codigo,5,4 )+substring(h.codigo,9,4 )+' '+substring(h.codigo,1,4 )+' '+ltrim(rtrim(substring(h.codigo,13,12 )))
      WHEN '1' then substring(h.codigo,1,1)+substring(h.codigo,5,4 )+substring(h.codigo,9,4 )+' '+substring(h.codigo,1,4 )+' '+ltrim(rtrim(substring(h.codigo,13,12 )))      
      Else h.codigo END As Filtro  FROM HGENERAL h  inner  join inventario i on h.codigo = i.codigo where h.fecha >= '$Date1' and h.fecha <='$Date2'
      and h.movimiento='Venta' GROUP BY h.CODIGO,i.codigo,i.existencia,i.costo,i.DESCRIPCION,h.precio
      order by Filtro";

    
  
    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    //ingresa nombres de cuadro
    echo "<div id='muestra' class ='container'> ";
    echo '<center><table border =0>
          <tr class="encabezadoRegistro">
          <th WIDTH=250>Codigo</th>
          <th WIDTH=200>Descripcion</th>
          <th>Existencia</th>
          <th>Venta</th>
          <th>Costo</th>
          <th>Cantidad</th>
          <th>fecha</th>';
    echo '</tr>';

    while($arr = odbc_fetch_array($resultado)){
       

    //ingresa datos en cuadro
          
      echo '<td>'.$arr["CODIGO"].'</td>';
      $codigo=substr($arr["CODIGO"],0,1);
            
      if ($codigo =='1') {
        $e_vw=$e_vw+$arr["cantidad"];
        $i_vw++;
        $c_vw=$c_vw+($arr["costo"]*$arr["cantidad"]);
        $v_vw=$v_vw+($arr["precio"]*$arr["cantidad"]);
      }
      if ($codigo =='2') {
        $e_d=$e_d+$arr["cantidad"];
        $i_d++;
        $c_d=$c_d+($arr["costo"]*$arr["cantidad"]);
        $v_d=$v_d+($arr["precio"]*$arr["cantidad"]);
      }
      if ($codigo =='3') {
        $e_u=$e_u+$arr["cantidad"];
        $i_u++;
        $c_u=$c_u+($arr["costo"]*$arr["cantidad"]);
        $v_u=$v_u+($arr["precio"]*$arr["cantidad"]);
      }
      if ($codigo =='0') {
        $e_v=$e_v+$arr["cantidad"];
        $i_v++;
        $c_v=$c_v+($arr["costo"]*$arr["cantidad"]);
        $v_v=$v_v+($arr["precio"]*$arr["cantidad"]);
      }
      if ($codigo =='9') {
        $e_a=$e_a+$arr["cantidad"];
        $i_a++;
        $c_a=$c_a+($arr["costo"]*$arr["cantidad"]);
        $v_a=$v_a+($arr["precio"]*$arr["cantidad"]);
      }
    
      echo '<td>'.$arr["DESCRIPCION"].'</td>';
      echo '<td>'.number_format($arr["existencia"],2).'</td></td><td style="color:green"><b>'.number_format(($arr["precio"]),2).'</b></td><td style="color:red">'.number_format($arr["costo"],2).'</td>';
      echo '<td>'.$arr["cantidad"].'</td>';
      $fecha_sis=strtotime($arr['fecha']);
      
      echo '<td>'.date("d-m-Y", $fecha_sis).'</td></tr>';
        
  }       
  ?>
  
    
<script type="text/javascript">
function mostrar(){
document.getElementById('muestra').style.display = 'block';}
</script>
<script type="text/javascript">
function ocultar(){
document.getElementById('muestra').style.display = 'none';}
</script>

<script src="js/bootstrap.min.js"></script>


</body>
</html>