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
$datos='0';
if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
$datos = $_REQUEST['datos'];

}


echo '<br>';
echo '<a href="egresos_mes.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
?>

 <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime </a>
<br>
<br>   
<br>
<?php


if ($datos=='todos') {
  $sql="SELECT e.Tipo, e.Documento, e.Fecha, e.Monto, e.Descripcion, t.Descripcion as descripciontipo FROM tbl_Egresos e, tbl_TiposEgresos t WHERE e.Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND e.Tipo= t.Tipo ORDER BY e.Tipo";  
}else{
  $sql="SELECT e.Tipo, e.Documento, e.Fecha, e.Monto, e.Descripcion, t.Descripcion as descripciontipo FROM tbl_Egresos e, tbl_TiposEgresos t WHERE e.Tipo ='$datos' AND e.Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND e.Tipo= t.Tipo ORDER BY e.Tipo";  
}


if(($res = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
            
          



//ingresa nombres de cuadro
echo "<div id='muestra'>";
echo '<center>';
echo '<h2>Reporte de egresos entre las fechas '.$fecha1.' y '.$fecha2.'</h2>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<th WIDTH=100><b>Fecha</b></th> ";
echo "<th WIDTH=100><b>Documento</b></th>";
echo "<th WIDTH=150><b>Monto</b></th> ";
echo "<th WIDTH=150><b>Descripcion</b></th> ";
echo "<th WIDTH=150><b>Tipo</b></th> ";
echo "<th WIDTH=150><b>Egreso</b></th> ";
echo "</tr> ";
echo "<tr> ";
$vartipo='';
$varnombre='';
$monto=0;
$montoparcial=0;
while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro

  if ($vartipo=='') {
    $varnombre=$arr['descripciontipo'];
    $vartipo=$arr['Tipo'];
  }

  if ($vartipo!=$arr['Tipo']) {
    echo "<th></th> ";
    echo "<th></th>";
    echo "<th>".number_format($montoparcial,2)."</th>";
    echo "<th></th>";
    echo "<th>".$vartipo."</th>";  
    echo "<th>".$varnombre."</th>";
    echo "</tr> ";  
    $montoparcial=0;
    $varnombre=$arr['descripciontipo'];
    $vartipo=$arr['Tipo'];
  }

  $fecha_sis=strtotime($arr['Fecha']);
  echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
  echo "<td>".$arr['Documento']."</td>";
  echo "<td>".number_format($arr['Monto'],2)."</td>";
  $monto=$monto+$arr['Monto'];
  $montoparcial=$montoparcial+$arr['Monto'];
  echo "<td>".$arr['Descripcion']."</td>";
  echo "<td>".$arr['Tipo']."</td>";  
  echo "<td>".$arr['descripciontipo']."</td>";
  echo "</tr> ";
  
 
			
}
echo "<th></th> ";
echo "<th></th>";
echo "<th>".number_format($montoparcial,2)."</th>";
echo "<th></th>";
echo "<th>".$vartipo."</th>";  
echo "<th>".$varnombre."</th>";
echo "</tr> ";  


echo '</table>';
echo '</center>';
echo "</div>";


?>
<script src="js/bootstrap.min.js"></script>
</body>
</html>