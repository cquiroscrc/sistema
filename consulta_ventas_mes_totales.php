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

$dc_vw=0;
$dc_d=0;
$dc_v=0;
$dc_a=0;
$dc_u=0;
$dv_vw=0;
$dv_d=0;
$dv_v=0;
$dv_a=0;
$dv_u=0;
$de_vw=0;
$de_d=0;
$de_v=0;
$de_a=0;
$de_u=0;
$di_vw=0;
$di_d=0;
$di_v=0;
$di_a=0;
$di_u=0;
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
    <a href="fechas_ventas_mes.php"><img width="48"  height="48" src= "img/back.png">Volver</a> 
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Totales</a>
    
  <?php
  echo '<a href="consulta_ventas_mes_productos.php?Date1='.$Date1.'&Date2='.$Date2.'"><img width="48"  height="48" src= "img/imprimir.png">Ver Tabla</a>';          
  

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
    
    
    while($arr = odbc_fetch_array($resultado)){
    
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
          
  }       
  ?>
  
  </div>
  <div id="muestra">
    <center><h1>Codigos vendidos y devueltos en el mes entre <?php echo $Date1; ?> y  <?php echo $Date2; ?></h1>
      <center><table border =1>
          <tr class="encabezadoRegistro">
          <th WIDTH=250>Marca  -  Ventas</th>
          <th WIDTH=150>Cantidades</th>
          <th WIDTH=150>Costo</th>
          <th WIDTH=150>Venta</th>
          <th WIDTH=150>Items</th>
        </tr>
    <?php 
        
        echo "<td><b>Volkswagen</td><td>".number_format($e_vw,2)."</td><td>".number_format($c_vw,2)."</td><td>".number_format($v_vw,2)."</td><td>".number_format($i_vw,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Daihatsu</td><td>".number_format($e_d,2)."</td><td>".number_format($c_d,2)."</td><td>".number_format($v_d,2)."</td><td>".number_format($i_d,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Accesorios</td><td>".number_format($e_a,2)."</td><td>".number_format($c_a,2)."</td><td>".number_format($v_a,2)."</td><td>".number_format($i_a,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Volvo</td><td>".number_format($e_v,2)."</td><td>".number_format($c_v,2)."</td><td>".number_format($v_v,2)."</td><td>".number_format($i_v,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Usados</td><td>".number_format($e_u,2)."</td><td>".number_format($c_u,2)."</td><td>".number_format($v_u,2)."</td><td>".number_format($i_u,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Totales</td><td><b>".number_format($e_vw+$e_d+$e_a+$e_v+$e_u,2) ."</td><td><b>".number_format($c_vw+$c_d+$c_a+$c_v+$c_u,2)."</td><td><b>".number_format($v_vw+$v_d+$v_a+$v_v+$v_u,2)."</td><td><b>".number_format($i_vw+$i_d+$i_a+$i_v+$i_u,2)."</td>";
        echo '</tr>'; 
        echo "<br>";
        /*
        $sqldevol ="SELECT h.CODIGO ,MAX(h.FECHA)as fecha,sum(h.cantidad) as cantidad ,h.precio,i.existencia,i.costo,i.DESCRIPCION,
      substring(h.codigo,1,1),substring(h.codigo,6,3 ),substring(h.codigo,10,15),Case substring(h.codigo, 1, 1)
      WHEN '0' then substring(h.codigo,1,1)+substring(h.codigo,5,4 )+substring(h.codigo,9,4 )+' '+substring(h.codigo,1,4 )+' '+ltrim(rtrim(substring(h.codigo,13,12 )))
      WHEN '1' then substring(h.codigo,1,1)+substring(h.codigo,5,4 )+substring(h.codigo,9,4 )+' '+substring(h.codigo,1,4 )+' '+ltrim(rtrim(substring(h.codigo,13,12 )))      
      Else h.codigo END As Filtro  FROM HGENERAL h  inner  join inventario i on h.codigo = i.codigo where h.fecha >= '$Date1' and h.fecha <='$Date2'
      and h.movimiento='DEVOLUCION' GROUP BY h.CODIGO,i.codigo,i.existencia,i.costo,i.DESCRIPCION,h.precio
      order by Filtro";
    */
      $sqldevol="SELECT h.CODIGO ,MAX(h.FECHA)as fecha,sum(h.cantidad) as cantidad ,
      h.movimiento,h.precio,i.existencia,i.costo,i.DESCRIPCION FROM HGENERAL h inner join inventario i on 
      h.codigo = i.codigo where h.fecha >= '$Date1' and h.fecha <='$Date2' and h.movimiento='Devolucion' 
      GROUP BY h.CODIGO,i.codigo,i.existencia,i.costo,i.DESCRIPCION,h.precio, h.movimiento";
  
    if(($resultadodevol = odbc_exec($id,$sqldevol)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    
    while($arrdevol = odbc_fetch_array($resultadodevol)){
      $codigod=substr($arrdevol["CODIGO"],0,1);
            
      if ($codigod =='1') {
        $de_vw=$de_vw+$arrdevol["cantidad"];
        $di_vw++;
        $dc_vw=$dc_vw+($arrdevol["costo"]*$arrdevol["cantidad"]);
        $dv_vw=$dv_vw+($arrdevol["precio"]*$arrdevol["cantidad"]);
      }
      if ($codigod =='2') {
        $de_d=$de_d+$arrdevol["cantidad"];
        $di_d++;
        $dc_d=$dc_d+($arrdevol["costo"]*$arrdevol["cantidad"]);
        $dv_d=$dv_d+($arrdevol["precio"]*$arrdevol["cantidad"]);
      }
      if ($codigod =='3') {
        $de_u=$de_u+$arrdevol["cantidad"];
        $di_u++;
        $dc_u=$dc_u+($arrdevol["costo"]*$arrdevol["cantidad"]);
        $dv_u=$dv_u+($arrdevol["precio"]*$arrdevol["cantidad"]);
      }
      if ($codigod =='0') {
        $de_v=$de_v+$arrdevol["cantidad"];
        $di_v++;
        $dc_v=$dc_v+($arrdevol["costo"]*$arrdevol["cantidad"]);
        $dv_v=$dv_v+($arrdevol["precio"]*$arrdevol["cantidad"]);
      }
      if ($codigod =='9') {
        $de_a=$de_a+$arrdevol["cantidad"];
        $di_a++;
        $dc_a=$dc_a+($arrdevol["costo"]*$arrdevol["cantidad"]);
        $dv_a=$dv_a+($arrdevol["precio"]*$arrdevol["cantidad"]);
        
      }
    }

    ?>
    
      <center><table border =1>
          <tr class="encabezadoRegistro">
          <th WIDTH=250>Marca  -  Devoluciones</th>
          <th WIDTH=150>Cantidades</th>
          <th WIDTH=150>Costo</th>
          <th WIDTH=150>Venta</th>
          <th WIDTH=150>Items</th>
        </tr>
       <?php 
        
        echo "<td><b>Volkswagen</td><td>".number_format($de_vw,2)."</td><td>".number_format($dc_vw,2)."</td><td>".number_format($dv_vw,2)."</td><td>".number_format($di_vw,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Daihatsu</td><td>".number_format($de_d,2)."</td><td>".number_format($dc_d,2)."</td><td>".number_format($dv_d,2)."</td><td>".number_format($di_d,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Accesorios</td><td>".number_format($de_a,2)."</td><td>".number_format($dc_a,2)."</td><td>".number_format($dv_a,2)."</td><td>".number_format($di_a,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Volvo</td><td>".number_format($de_v,2)."</td><td>".number_format($dc_v,2)."</td><td>".number_format($dv_v,2)."</td><td>".number_format($di_v,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Usados</td><td>".number_format($de_u,2)."</td><td>".number_format($dc_u,2)."</td><td>".number_format($dv_u,2)."</td><td>".number_format($di_u,2)."</td>";
        echo '</tr>'; 
        echo "<td><b>Totales</td><td><b>".number_format($de_vw+$de_d+$de_a+$de_v+$de_u,2) ."</td><td><b>".number_format($dc_vw+$dc_d+$dc_a+$dc_v+$dc_u,2)."</td><td><b>".number_format($dv_vw+$dv_d+$dv_a+$dv_v+$dv_u,2)."</td><td><b>".number_format($di_vw+$di_d+$di_a+$di_v+$di_u,2)."</td>";
        echo '</tr>'; 
        echo "<br>";
        ?>
    </center>    
  </div>  
    


<script src="js/bootstrap.min.js"></script>


</body>
</html>