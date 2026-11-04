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
if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
$datos = $_REQUEST['datos'];

}


echo '<br>';
echo '<a href="Ventas_mes_clientes.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
?>
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
	

      $sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  AND nombre='$datos' order by nfac";  

          if(($res = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
          



//ingresa nombres de cuadro
echo '<center>';
echo '<h2>'.$datos.'</h2>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<td><b>Fecha</b></td> ";
echo "<td><b>Factura</b></td>";
echo "<td><b>Subtotal</b></td> ";
echo "<td><b>Impuesto</b></td> ";
echo "<td><b>Descuento</b></td> ";
echo "<td><b>Total</b></td> ";
echo "<td><b>Tipo</b></td> ";
echo "<td><b>Estado</b></td> ";
echo "<td><b>Cliente</b></td> ";
echo "<td><b>Nombre</b></td> ";
echo "<td><b>Vendedor</b></td> ";
echo "</tr> ";
echo "<tr> ";


while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro
echo "<td>".$arr["fechafact"]."</td> ";
echo "<td>".$arr["nfac"]."</td>";
echo "<td>".$arr["subtotal"]."</td>";
    $varsubtotal = $varsubtotal + $arr["subtotal"];
echo "<td>".$arr["impuesto"]."</td>";
    $varimpuesto = $varimpuesto + $arr["impuesto"];
echo "<td>".$arr["descuento"]."</td>";
    $vardescuento = $vardescuento + $arr["descuento"];
echo "<td>".$arr["total"]."</td>";
    $vartotal= $vartotal + $arr["total"];
if ($arr["Tipo"]==0){
    echo "<td>Contado</td>";
}else{
    echo "<td>Credito</td>";
}
if ($arr["estado"]=="ANULADA   ") {
  echo "<td style='color:red'>".$arr["estado"]."</td>";
}elseif ($arr["estado"]=="DEVOLUCION") {
  echo "<td style='color:orange'>".$arr["estado"]."</td>";  
}elseif ($arr["estado"]=="APLICADA  ") {
  echo "<td style='color:blue'>".$arr["estado"]."</td>";  
}
  



echo "<td>".$arr["proveedor"]."</td>";
echo "<td>".$arr["Nombre"]."</td>";
echo "<td>".$arr["vendedor"]."</td>";
echo "</tr> ";
			

}

echo "<td><strong> Fecha  :".$fecha1." --- ".$fecha2."</td>
<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
<td></td><td></td></tr>" ;
echo "<td><strong>Subtotal: ".number_format($varsubtotal,2)."</td>
<td><strong>Impuesto: ".number_format($varimpuesto,2)."</td>
<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><tr>";
echo '<td><strong>Descuento: '.number_format($vardescuento,2).'</td>
<td><strong>Total: '.number_format($vartotal,2).'</strong></td>
<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
<td></td><td></td></tr>';
echo '</table>';
echo '</center>';

}

?>
<script src="js/bootstrap.min.js"></script>
</body>
</html>