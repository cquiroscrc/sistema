<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

    include "menu.php";
?>
<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/anular.css">
<link rel="StyleSheet" href="css/calendar.css">
<link rel="icon" href="favicon.ico">
</head>

<body>
  
<br>
<a href="#" onClick="location.href = 'proveedores_pagos.php'"><img width="48"  height="48" src= "img/back.png">Volver</a>
<?php
require_once("db.php");
//error_reporting(1);

if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
  $datos = $_REQUEST['datos'];
}


//echo '<a href="clientes_rep.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
$sqln="SELECT * FROM Proveedores WHERE codigo='$datos'";  

    if(($res1 = odbc_exec($id,$sqln)) === false)
        die("Error en Select: ". odbc_errormsg($id));

while($arr1 = odbc_fetch_array($res1)){
  $nombre=$arr1['nombre'];
}

echo '<center><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;'.$datos.' - '.$nombre.'</h1>
      </center> '; 
?>


  <?php


@$varsaldo =0;
@$varsubtotal =0;
@$varimpuesto =0;
@$vardescuento =0;
@$vartotal =0;


$sql="SELECT * FROM cxp WHERE Proveedor='$datos' and Estado='Pendiente' order by FechaFact,NFac";  

    if(($res = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
      
          



//ingresa nombres de cuadro
echo '<center>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<th><b>Fecha</b></th> ";
echo "<th><b>Factura</b></th>";
echo "<th><b>Subtotal</b></th> ";
echo "<th><b>Impuesto</b></th> ";
echo "<th><b>Descuento</b></th> ";
echo "<th><b>Total</b></th> ";
echo "<th><b>Saldo</b></th> ";
echo "<th><b>Agregar</b></th> ";
echo "</tr> ";
echo "<tr> ";


while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro  
echo "<td>".$arr["FechaFact"]."</td> ";
echo "<td>".$arr["NFac"]."</td>";
$factura=$arr['NFac'];
echo "<td>".number_format($arr["SubTotal"],2)."</td>";
    $varsubtotal = $varsubtotal + $arr["SubTotal"];
echo "<td>".number_format($arr["Impuesto"],2)."</td>";
    $varimpuesto = $varimpuesto + $arr["Impuesto"];
echo "<td>".number_format($arr["Descuento"],2)."</td>";
    $vardescuento = $vardescuento + $arr["Descuento"];
echo "<td>".number_format($arr["Total"],2)."</td>";
    $vartotal= $vartotal + $arr["Total"];
    
    if ($arr["chkCancelacion"]==0) {
      $varsaldo= $varsaldo + $arr["Total"];  
    }
 
echo "<td style='color:blue'>".number_format($varsaldo,2)."</td>";
$esta='no';
$sql_tmp="SELECT * FROM tmpCxP WHERE proveedor='$datos' and nfac='$factura'";  

if(($res_tmp = odbc_exec($id,$sql_tmp)) === false)
    die("Error en Select: ". odbc_errormsg($id));

while($arr_tmp = odbc_fetch_array($res_tmp)){
  $esta='si';
}
if ($esta=='si') {
  echo '<td><a href="proveedores_pagos_excluir.php?datos='.$datos.'&factura='.$factura.'"><img width="32"  height="32" src= "img/del.png"></a></td></tr>';
}else{
  echo '<td><a href="proveedores_pagos_incluir.php?datos='.$datos.'&factura='.$factura.'"><img width="32"  height="32" src= "img/add.png"></a></td></tr>';  
}

echo "</tr> ";
			

}


echo "<td><strong>Subtotal: ".number_format($varsubtotal,2)."</td>
<td><strong>Impuesto: ".number_format($varimpuesto,2)."</td>
<td></td><td></td><td></td><td></td><td></td><td></td><tr>";
echo '<td><strong>Descuento: '.number_format($vardescuento,2).'</td>
<td><strong>Total: '.number_format($vartotal,2).'</strong></td>
<td></td><td></td><td></td><td></td><td></td><td></td></tr>';
echo '</table>';
echo '</center>';



?>
<script src="js/bootstrap.min.js"></script>
</body>
</html>