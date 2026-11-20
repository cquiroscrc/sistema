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
<title>Resultado de bancos</title>
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




?>
<a href="#" onClick="location.href = 'rep_bancos.php'"><img width="48"  height="48" src= "img/back.png">Volver</a> 
<a href="#" onClick="window.print();"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>
<a href="#" onClick="location.href = '<?php echo "rep_bancos_excel.php?cuenta=".$datos."&fecha1=".$fecha1."&fecha2=".$fecha2.""; ?>'"><img width="48"  height="48" src= "img/excel.png">Exportar</a>
<!--
 <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime </a>
-->
<?php


  $sql="SELECT b.Fecha,b.Cuenta,b.Documento,b.Descripcion,b.Credito,b.Debito, c.Banco FROM tbl_Bancos b, tbl_Cuentas c WHERE b.Cuenta ='$datos' AND b.Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND b.Cuenta= c.Cuenta ORDER BY b.Fecha,b.Descripcion";  



if(($res = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
            

//ingresa nombres de cuadro
echo "<div id='muestra'>";
echo '<center>';
echo '<h2>Reporte de bancos entre las fechas '.$fecha1.' y '.$fecha2.'</h2>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<th WIDTH=100><b>Fecha</b></th> ";
echo "<th WIDTH=100><b>Cuenta</b></th> ";
echo "<th WIDTH=150><b>Nombre</b></th>";
echo "<th WIDTH=100><b>Documento</b></th>";
echo "<th WIDTH=450><b>Descripcion</b></th> ";
echo "<th WIDTH=100><b>Credito</b></th> ";
echo "<th WIDTH=100><b>Debito</b></th> ";
echo "<th WIDTH=100><b>Eliminar</b></th> ";
echo "</tr> ";
echo "<tr> ";
$credito=0;
$debito=0;
while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro


  $fecha_sis=strtotime($arr['Fecha']);
  echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
  echo "<td>".$arr['Cuenta']."</td>";
  echo "<td>".$arr['Banco']."</td>";
  echo "<td>".$arr['Documento']."</td>";
  echo "<td>".$arr['Descripcion']."</td>";
  if ($arr['Credito']>0) {
    echo "<td style='color:blue'>".number_format($arr['Credito'],2)."</td>";  
  }else{
    echo "<td>".number_format($arr['Credito'],2)."</td>";
  }
  if ($arr['Debito']>0) {
    echo "<td style='color:red'>".number_format($arr['Debito'],2)."</td>";  
  }else{
    echo "<td>".number_format($arr['Debito'],2)."</td>";
  }
  $credito=$credito+$arr['Credito'];
  $vdocumento=$arr['Documento'];
  $vcuenta=$arr['Cuenta'];
  $debito=$debito+$arr['Debito'];
  echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar este movimiento?\")) 
      location.href=\"rep_bancos_eliminar_depositos.php?documento=".$vdocumento."&cuenta=".$vcuenta."&Date2=".$fecha2."&Date1=".$fecha1."\";
      '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";

  echo "</tr> ";
  
 
			
}
echo "<th></th> ";
echo "<th></th>";
echo "<th></th>";
echo "<th></th>";
echo "<th></th>";
echo "<th>".number_format($credito,2)."</th>";
echo "<th>".number_format($debito,2)."</th>";
echo "<th></th>";
echo "</tr> ";  


echo '</table>';
echo '</center>';
echo "</div>";


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Reporte Banco';
</script> 
<script src="js/bootstrap.min.js"></script>
</body>
</html>