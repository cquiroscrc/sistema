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
$motorizado=$_SESSION['profile']['nombre']; 
if(isset($_REQUEST['mensajero']) && !empty($_REQUEST['mensajero'])){
  $motorizado=$_REQUEST['mensajero']; 
}

echo '<br>';
echo '<a href="reporte_mandados.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
?>
 <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Reporte</a>
<?php
  echo '<a href="reporte_mandados_imprime.php?Date1='.$fecha1.'&Date2='.$fecha2.'&motorizado='.$motorizado.'"><img width="48"  height="48" src= "img/imprimir.png">Facturar</a> ';
  ?>

<br>
<br>   
<br>
  <?php

  $sqlmensa = "SELECT * FROM mensajeros WHERE id='$motorizado'";

   if(($resmensa = odbc_exec($id,$sqlmensa)) === false)
  die("Error en Select: ". odbc_errormsg($id));

while($arrmensa = odbc_fetch_array($resmensa)){
      $nombremensa=$arrmensa['nombre'];
      $monto=$arrmensa['monto'];
      $mandado=$arrmensa['mandado'];
}


if ($fecha1 != ""){
	
      

      $sql="SELECT * FROM mandados WHERE fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and motorizado ='$motorizado'
      order by motorizado,fecha ASC";  

          if(($res = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
          


//echo $sql; exit; 

//ingresa nombres de cuadro
echo "<div id='muestra'>";
echo '<center>';
echo '<h2>Reporte de Mandados entre '.$fecha1.' y '.$fecha2.'</h2>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<th WIDTH=170><b>Fecha</b></th> ";
echo "<th WIDTH=100><b>Factura</b></th>";
echo "<th WIDTH=150><b>Monto</b></th> ";
echo "<th WIDTH=300><b>Lugar</b></th> ";
echo "<th WIDTH=50><b>Cantidad</b></th> ";
echo "<th WIDTH=50><b>Envia</b></th> ";
echo "<th WIDTH=100><b>Motorizado</b></th> ";
echo "<th WIDTH=100><b>Eliminar</b></th> ";

echo "</tr> ";
echo "<tr> ";

$mandados=0;
$unidades=0;
$subtotal=0;
while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro
  

  $fecha_sis=strtotime($arr['fecha']);
  $fecha=$arr['fecha'];
  echo "<td>".date("d-m-Y H:m:s", $fecha_sis)."</td> ";
  $factura=$arr["factura"];
  echo "<td>".$factura."</td>";
  echo "<td>".number_format($arr["monto"],2)."</td>";
  echo "<td>".$arr["lugar"]."</td>";
  $cantidad=$arr["cantidad"];
  echo "<td>".number_format($cantidad,2)."</td>";
      $mandados=$mandados+$arr['cantidad'];
      $subtotal=$subtotal+($arr['cantidad']-$mandado);
  echo "<td>".$arr["envia"]."</td>";
  $motorizado=$arr["motorizado"];
  echo "<td>".$motorizado."</td>";
  echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar el mandado?\")) 
      location.href=\"salvar_mandado.php?factura=".$factura."&Date2=".$fecha."&motorizado=".$motorizado."\";
      '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
  echo "</tr> ";
  if ($cantidad==0) {
    //no suma
  }else{
    $unidades++;      
  }
	
}

echo '<th><b>Mandados</th><th><b>'.number_format($mandados,2).'</th><th><b>Unidades</th><th><b>'.number_format($unidades,2).'</th><th><b>Total</th><th><b>'.number_format($subtotal,2).'</th><th></th><th></th></tr>';

echo '</table>';
echo '</center>';
echo "</div>";
}

?>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Reporte Mandados';
</script> 
<script src="js/bootstrap.min.js"></script>
</body>
</html>