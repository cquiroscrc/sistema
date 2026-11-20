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
    include "menu.php";
?>
<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resultado de consulta Cierres</title>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/anular.css">
<link rel="StyleSheet" href="css/calendar.css">
<link rel="icon" href="favicon.ico">
</head>

<body>
  

<?php
require_once("db.php");
//error_reporting(1);
if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
$fecha2 = $_REQUEST['Date2'];

}


echo '<br>';
echo '<a href="cierre_compras_mes.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
?>
 <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Reporte</a>
<br>
<br>   
<br>
  <?php



@$varsubtotal =0;
@$varimpuesto =0;
@$vardescuento =0;
@$vartotal =0;

$codigobusq = '';


if ($fecha1 != ""){
	
      

      $sql="SELECT c.Factura,c.proveedor,c.fecha,c.tipo,c.subtotal,c.impuesto,c.descuento,c.total, p.nombre 
      FROM compras c, Proveedores p WHERE fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  
      AND c.proveedor =  p.codigo order by proveedor,fecha";  

          if(($res = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
          



//ingresa nombres de cuadro
echo "<div id='muestra'>";
echo '<center>';
echo '<h2>Reporte de compras entre '.$fecha1.' y '.$fecha2.'</h2>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<th WIDTH=100><b>Fecha</b></th> ";
echo "<th WIDTH=100><b>Factura</b></th>";
echo "<th WIDTH=150><b>Subtotal</b></th> ";
echo "<th WIDTH=150><b>Impuesto</b></th> ";
echo "<th WIDTH=150><b>Descuento</b></th> ";
echo "<th WIDTH=150><b>Total</b></th> ";
echo "<th WIDTH=100><b>Tipo</b></th> ";
echo "<th WIDTH=100><b>Proveedor</b></th> ";
echo "<th WIDTH=250><b>Nombre</b></th> ";
echo "</tr> ";
echo "<tr> ";

$parcial_subtotal=0;
$parcial_impuesto=0;
$parcial_descuento=0;
$parcial_total=0;
$varproveeedor='';
$varnombre='';

while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro
  if ($varproveeedor=='') {
    $varproveeedor=$arr['proveedor'];
    $varnombre=$arr['nombre'];
  }

  if ($varproveeedor!=$arr['proveedor']) {
    echo '<th><b>Totales</th><th></th><th><b>'.number_format($parcial_subtotal,2).'</th><th><b>'.number_format($parcial_impuesto,2).'</th><th><b>'.number_format($parcial_descuento,2).'</th><th><b>'.number_format($parcial_total,2).'</th><th></th><th><b>'.$varproveeedor.'</th><th><b>'.$varnombre.'</th></tr>';
    $varproveeedor=$arr['proveedor']; 
    $varnombre=$arr['nombre'];
    $parcial_subtotal=0;
    $parcial_impuesto=0;
    $parcial_descuento=0;
    $parcial_total=0;
  }
  $fecha_sis=strtotime($arr['fecha']);
  echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
  echo "<td>".$arr["Factura"]."</td>";
  echo "<td>".number_format($arr["subtotal"],2)."</td>";
      $parcial_subtotal=$parcial_subtotal+ $arr['subtotal'];
      $varsubtotal = $varsubtotal + $arr["subtotal"];
  echo "<td>".number_format($arr["impuesto"],2)."</td>";
      $parcial_impuesto=$parcial_impuesto+$arr['impuesto'];
      $varimpuesto = $varimpuesto + $arr["impuesto"];
  echo "<td>".number_format($arr["descuento"],2)."</td>";
      $parcial_descuento=$parcial_descuento+$arr['descuento'];
      $vardescuento = $vardescuento + $arr["descuento"];
  echo "<td>".number_format($arr["total"],2)."</td>";
      $parcial_total=$parcial_total+$arr['total'];
      $vartotal= $vartotal + $arr["total"];
  echo "<td>".$arr["tipo"]."</td>";
  echo "<td>".$arr["proveedor"]."</td>";
  echo "<td>".$arr["nombre"]."</td>";
  echo "</tr> ";
			
}

echo '<th><b>Totales</th><th></th><th><b>'.number_format($parcial_subtotal,2).'</th><th><b>'.number_format($parcial_impuesto,2).'</th><th><b>'.number_format($parcial_descuento,2).'</th><th><b>'.number_format($parcial_total,2).'</th><th></th><th><b>'.$varproveeedor.'</th><th><b>'.$varnombre.'</th></tr>';
$fecha11=strtotime($fecha1);
$fecha22=strtotime($fecha2);
echo "<td><b> Fecha:</td>
<td><b>".date("d-m-Y", $fecha11)." --- ".date("d-m-Y", $fecha22)."</td><td><b>Subtotal: ".number_format($varsubtotal,2)."</td><td><b>Impuesto: ".number_format($varimpuesto,2)."</td><td><b>Descuento: ".number_format($vardescuento,2)."</td><td><b>Total: ".number_format($vartotal,2)."</td><td></td>
<td></td><td></td></tr>" ;

echo '</table>';
echo '</center>';
echo "</div>";
}

?>
<script src="js/bootstrap.min.js"></script>
</body>
</html>