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
echo '<a href="cierre_contables_mes.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
?>

 <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime </a>
  <a href="#" onClick="location.href = '<?php echo "cierre_contable_excel.php?fecha1=".$fecha1."&fecha2=".$fecha2.""; ?>'"><img width="48"  height="48" src= "img/excel.png">Exportar</a>
<br>
<br>   
<br>
  <?php



@$varsubtotal =0;
@$varimpuesto =0;
@$vardescuento =0;
@$vartotal =0;

$codigobusq = '';



	
$sql="SELECT * FROM cierrediario WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'";

if(($res = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
            
          



//ingresa nombres de cuadro
echo "<div id='muestra'>";
echo '<center>';
echo '<h2>Reporte de contable entre las fechas '.$fecha1.' y '.$fecha2.'</h2>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<th WIDTH=100><b>Fecha</b></th> ";
echo "<th WIDTH=100><b>Ventas Contado</b></th>";
echo "<th WIDTH=150><b>Ventas Credito</b></th> ";
echo "<th WIDTH=150><b>Recibos</b></th> ";
echo "<th WIDTH=150><b>Descuentos</b></th> ";
echo "<th WIDTH=150><b>Repuestos</b></th> ";
echo "<th WIDTH=100><b>Papeleria</b></th> ";
echo "<th WIDTH=100><b>Combustible</b></th> ";
echo "<th WIDTH=250><b>Atencion</b></th> ";
echo "<th WIDTH=250><b>Encomiendas</b></th> ";
echo "<th WIDTH=250><b>Otros</b></th> ";
echo "<th WIDTH=250><b>Alfaro</b></th> ";
echo "<th WIDTH=250><b>Caja</b></th> ";
echo "<th WIDTH=250><b>Depositos</b></th> ";
echo "<th WIDTH=250><b>Diferencia</b></th> ";
echo "</tr> ";
echo "<tr> ";


$contado=0;
$credito=0;
$recibos=0;
$descuentos=0;
$repuestos=0;
$papeleria=0;
$combustible=0;
$atencion=0;
$encomiendas=0;
$otros=0;
$alfaro=0;
$caja=0;
$depositos=0;
$diferencia=0;
while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro
  
  $fecha_sis=strtotime($arr['Fecha']);
  echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
  echo "<td>".number_format($arr["I_VentasEfectivo"],2)."</td>";
  $contado=$contado+$arr["I_VentasEfectivo"];
  echo "<td>".number_format($arr["I_VentasCredito"],2)."</td>";
  $credito=$credito+$arr["I_VentasCredito"];      
  echo "<td>".number_format($arr["I_RecibosDinero"],2)."</td>";
  $recibos=$recibos+$arr["I_RecibosDinero"];
  echo "<td>".number_format($arr["E_Descuentos"],2)."</td>";
  $descuentos=$descuentos+$arr["E_Descuentos"];
  echo "<td>".number_format($arr["E_Repuestos"],2)."</td>";
  $repuestos=$repuestos+$arr["E_Repuestos"];
  echo "<td>".number_format($arr["E_Papeleria"],2)."</td>";
  $papeleria=$papeleria+$arr["E_Papeleria"];
  echo "<td>".number_format($arr["E_Combustible"],2)."</td>";
  $combustible=$combustible+$arr["E_Combustible"];
  echo "<td>".number_format($arr["E_AtencionPublico"],2)."</td>";
  $atencion=$atencion+$arr["E_AtencionPublico"];
  echo "<td>".number_format($arr["E_Encomiendas"],2)."</td>";
  $encomiendas=$encomiendas+$arr["E_Encomiendas"];
  echo "<td>".number_format($arr["E_Otros"],2)."</td>";
  $otros=$otros+$arr["E_Otros"];
  echo "<td>".number_format($arr["E_Alfaro"],2)."</td>";
  $alfaro=$alfaro+$arr["E_Alfaro"];
  echo "<td>".number_format($arr["T_Caja"],2)."</td>";
  $caja=$caja+$arr["T_Caja"];
  echo "<td>".number_format($arr["T_Depositos"],2)."</td>";
  $depositos=$depositos+$arr["T_Depositos"];
  if ($arr["T_FaltaSobra"]>0) {
    echo "<td style='color:green'><b>".number_format($arr["T_FaltaSobra"],2)."</td>";  
  }else{
    echo "<td style='color:red'><b>".number_format($arr["T_FaltaSobra"],2)."</td>";  
  }

  
  $diferencia=$diferencia+$arr['T_FaltaSobra'];
  echo "</tr> ";
			
}


echo '<th><b>Totales</th><th><b>'.number_format($contado,2).'</th><th><b>'.number_format($credito,2).'</th><th><b>'.number_format($recibos,2).'</th><th><b>'.number_format($descuentos,2).'</th><th><b>'.number_format($repuestos,2).'</th><th><b>'.number_format($papeleria,2).'</th><th><b>'.number_format($combustible,2).'</th><th><b>'.number_format($atencion,2).'</th><th><b>'.number_format($encomiendas,2).'</th><th><b>'.number_format($otros,2).'</th><th><b>'.number_format($alfaro,2).'</th><th><b>'.number_format($caja,2).'</th><th><b>'.number_format($depositos,2).'</th><th><b>'.number_format($diferencia,2).'</th></tr>';
echo '</table>';
echo '</center>';
echo "</div>";


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Cierre Contable';
</script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>