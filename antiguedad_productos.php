<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    include "menu.php";

if(isset($_REQUEST['estudio']) && !empty($_REQUEST['estudio'])){
  $estudio = $_REQUEST['estudio'];        
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
    echo '<a href="antiguedad_resultado.php?estudio='.$estudio.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
     ?>
  
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Tabla</a>

    
    
  <?php

  

    require_once("db.php");

    $fecha_dia = date('Y-m-d');
    $fecha_b = strtotime ( '-1 year' , strtotime ( $fecha_dia ) ) ;
    $fecha_b = date ( 'Y-m-d' , $fecha_b );

    $fecha_dia = date('Y-m-d');
    $fecha_a = strtotime ( '-6 month' , strtotime ( $fecha_dia ) ) ;
    $fecha_a = date ( 'Y-m-d' , $fecha_a );


    $sql ="SELECT max(h.fecha) as fechamax ,h.codigo,i.DESCRIPCION,i.EXISTENCIA,i.COSTO,(i.COSTO*i.EXISTENCIA) as totalcosto,
    Case substring(h.codigo, 1, 1)
    WHEN '0' then (SUBSTRING(h.codigo,1,1))+(SUBSTRING(h.codigo,5,4 ))+(SUBSTRING(h.codigo,9,4 ))+' '+(SUBSTRING(h.codigo,1,4 ))+' '+(ltrim(rtrim(SUBSTRING(h.codigo,13,12 ))))
    WHEN '1' then (SUBSTRING(h.codigo,1,1))+(SUBSTRING(h.codigo,5,4 ))+(SUBSTRING(h.codigo,9,4 ))+' '+(SUBSTRING(h.codigo,1,4 ))+' '+(ltrim(rtrim(SUBSTRING(h.codigo,13,12 ))))
    ELSE h.codigo END as FILTRO
    FROM hgeneral h,inventario i WHERE h.codigo=i.CODIGO AND EXISTENCIA >0 GROUP BY h.codigo,i.DESCRIPCION,i.EXISTENCIA,i.COSTO order by FILTRO" ; 

    
  
    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));


    
    //ingresa nombres de cuadro
    echo "<div id='muestra' class ='container'> ";
    echo '<center><table border =1>
          <tr class="encabezadoRegistro">
          <th WIDTH=250>Codigo</th>
          <th WIDTH=200>Descripcion</th>
          <th>Existencia</th>
          <th>Costo</th>
          <th>fecha</th>';
    echo '</tr>';

    while($arr = odbc_fetch_array($resultado)){
    $fechamax=$arr['fechamax'];   

    //ingresa datos en cuadro
      if ($estudio=='1') {
        if ($fechamax>$fecha_a) {
          echo '<td>'.$arr["codigo"].'</td>';    
          echo '<td>'.$arr["DESCRIPCION"].'</td>';
          echo '<td>'.number_format($arr["EXISTENCIA"],2).'</td></td><td style="color:red">'.number_format($arr["COSTO"],2).'</td>';      
          $fechamax=strtotime ($fechamax);
          echo '<td>'.date("d-m-Y", $fechamax).'</td></tr>';    
        }

      }elseif ($estudio=='2') {
        if ($fechamax>$fecha_b && $fechamax<$fecha_a ) {
         echo '<td>'.$arr["codigo"].'</td>';    
          echo '<td>'.$arr["DESCRIPCION"].'</td>';
          echo '<td>'.number_format($arr["EXISTENCIA"],2).'</td></td><td style="color:red">'.number_format($arr["COSTO"],2).'</td>';      
          $fechamax=strtotime ($fechamax);
          echo '<td>'.date("d-m-Y", $fechamax).'</td></tr>';    
        }
      }elseif ($estudio=='3') {
        if ($fechamax<$fecha_b) {
          echo '<td>'.$arr["codigo"].'</td>';    
          echo '<td>'.$arr["DESCRIPCION"].'</td>';
          echo '<td>'.number_format($arr["EXISTENCIA"],2).'</td></td><td style="color:red">'.number_format($arr["COSTO"],2).'</td>';      
          $fechamax=strtotime ($fechamax);
          echo '<td>'.date("d-m-Y", $fechamax).'</td></tr>';    
        } 
      }
    }

    if ($estudio=='4') {
    //hacer consulta completa
    $sql_no ="SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,COSTO,
    Case substring(CODIGO, 1, 1)
    WHEN '0' then (SUBSTRING(CODIGO,1,1))+(SUBSTRING(CODIGO,5,4 ))+(SUBSTRING(CODIGO,9,4 ))+' '+(SUBSTRING(CODIGO,1,4 ))+' '+(ltrim(rtrim(SUBSTRING(CODIGO,13,12 ))))
    WHEN '1' then (SUBSTRING(CODIGO,1,1))+(SUBSTRING(CODIGO,5,4 ))+(SUBSTRING(CODIGO,9,4 ))+' '+(SUBSTRING(CODIGO,1,4 ))+' '+(ltrim(rtrim(SUBSTRING(CODIGO,13,12 ))))
    ELSE CODIGO END as FILTRO
    from inventario where CODIGO not in (select distinct codigo from hgeneral) and EXISTENCIA >0 ORDER BY FILTRO";
      
      if(($resultado_no = odbc_exec($id,$sql_no)) === false)
        die("Error en Select: ". odbc_errormsg($id));
      
      while($arr = odbc_fetch_array($resultado_no)){    
        echo '<td>'.$arr["CODIGO"].'</td>';    
        echo '<td>'.$arr["DESCRIPCION"].'</td>';
        echo '<td>'.number_format($arr["EXISTENCIA"],2).'</td></td><td style="color:red">'.number_format($arr["COSTO"],2).'</td>';      
        echo '<td>'.number_format($arr['VENTA'],2).'</td></tr>';    

      }
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