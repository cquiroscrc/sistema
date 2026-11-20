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
<a href="#" onClick="location.href = 'clientes_activar.php'"><img width="48"  height="48" src= "img/back.png">Volver</a>
<?php
require_once("db.php");
//error_reporting(1);

if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
  $datos = $_REQUEST['datos'];
}


//echo '<a href="clientes_rep.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
$sqln="SELECT * FROM clientes WHERE codigo='$datos'";  

    if(($res1 = odbc_exec($id,$sqln)) === false)
        die("Error en Select: ". odbc_errormsg($id));

while($arr1 = odbc_fetch_array($res1)){
  $nombre=$arr1['nombre'];
  $activado = $arr1["comentario"];
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


$sql="SELECT * FROM cxc WHERE IDCliente='$datos'  order by Fecha,Nfac";  

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
echo "</tr> ";
echo "<tr> ";


while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro
echo "<td>".$arr["Fecha"]."</td> ";
echo "<td>".$arr["NFac"]."</td>";
echo "<td>".$arr["SubTotal"]."</td>";
    $varsubtotal = $varsubtotal + $arr["SubTotal"];
echo "<td>".$arr["Impuesto"]."</td>";
    $varimpuesto = $varimpuesto + $arr["Impuesto"];
echo "<td>".$arr["Descuento"]."</td>";
    $vardescuento = $vardescuento + $arr["Descuento"];
echo "<td>".$arr["Total"]."</td>";
    $vartotal= $vartotal + $arr["Total"];
    $varsaldo= $varsaldo + $arr["Total"];
echo "<td style='color:blue'>".number_format($varsaldo,2)."</td>";

  
echo "</tr> ";
			

}


  echo "<td><strong>Subtotal: ".number_format($varsubtotal,2)."</td>
  <td><strong>Impuesto: ".number_format($varimpuesto,2)."</td>
  <td></td><td></td><td></td><td></td><td></td><tr>";
  echo '<td><strong>Descuento: '.number_format($vardescuento,2).'</td>
  <td><strong>Total: '.number_format($vartotal,2).'</strong></td>
  <td></td><td></td><td></td><td></td><td></td></tr>';
  echo '</table>';
if ($activado=="**DESACTIVADO**") {
  //no muestra boton
}else{
echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea desactivar el cliente?\")) 
        location.href=\"clientes_desactivar.php?datos=".$datos."\";
        '><img width='48'  height='48' src= 'img/eliminar.png'>Desactivar Cliente</a></td>";          
        echo '</div>';  
}
echo '</center>';
?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Clientes Activar';
</script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>