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
$documento=null;
$descripcion=null;
$monto=null;

if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
  $documento = $_REQUEST['documento'];
  
} 
if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
  $descripcion = $_REQUEST['descripcion'];

}

if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
  $monto = $_REQUEST['monto'];

}




?>
<a href="#" onClick="location.href = 'buscar_egreso.php'"><img width="48"  height="48" src= "img/back.png">Volver</a> 
<a href="#" onClick="window.print();"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>


<?php
if($descripcion == NULL and $monto==NULL) {
      $sql="SELECT e.Tipo,e.Documento,e.Fecha,e.Monto,e.Descripcion,t.Tipo as tip, t.Descripcion as tdescripcion FROM tbl_Egresos e, tbl_TiposEgresos t WHERE e.Documento like '%$documento%' AND e.Tipo= t.Tipo ORDER BY e.Fecha,e.Descripcion";  

}

if($documento == NULL and $monto==NULL) {
      $sql="SELECT e.Tipo,e.Documento,e.Fecha,e.Monto,e.Descripcion,t.Tipo as tip, t.Descripcion as tdescripcion FROM tbl_Egresos e, tbl_TiposEgresos t WHERE e.Descripcion like '%$descripcion%' AND e.Tipo= t.Tipo ORDER BY e.Fecha,e.Descripcion";  

}

if($descripcion == NULL and $documento==NULL) {
      $sql="SELECT e.Tipo,e.Documento,e.Fecha,e.Monto,e.Descripcion,t.Tipo as tip, t.Descripcion as tdescripcion FROM tbl_Egresos e, tbl_TiposEgresos t WHERE e.Monto=$monto  AND e.Tipo= t.Tipo ORDER BY e.Fecha,e.Descripcion";  

  }

  //echo $sql; exit;

  //$sql="SELECT b.Fecha,b.Cuenta,b.Documento,b.Descripcion,b.Credito,b.Debito, c.Banco FROM tbl_Bancos b, tbl_Cuentas c WHERE b.Documento = '$documento' or b.Descripcion like '$descripcion' or b.Credito=$monto or b.Debito = $monto AND b.Cuenta= c.Cuenta ORDER BY b.Fecha,b.Descripcion";  



if(($res = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
            

//ingresa nombres de cuadro
echo "<div id='muestra'>";
echo '<center>';
echo '<h2>Busqueda de Egreso</h2>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<th WIDTH=100><b>Fecha</b></th> ";
echo "<th WIDTH=100><b>Documento</b></th> ";
echo "<th WIDTH=350><b>Descripcion</b></th>";
echo "<th WIDTH=100><b>Tipo</b></th>";
echo "<th WIDTH=250><b>Descrip Tipo</b></th> ";
echo "<th WIDTH=100><b>Monto</b></th> ";

echo "</tr> ";
echo "<tr> ";
$credito=0;
$debito=0;
while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro


  $fecha_sis=strtotime($arr['Fecha']);
  echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
  echo "<td>".$arr['Documento']."</td>";
  echo "<td>".$arr['Descripcion']."</td>";
  echo "<td>".$arr['Tipo']."</td>";
  echo "<td>".$arr['tdescripcion']."</td>";
  echo "<td style='color:blue'>".number_format($arr['Monto'],2)."</td>";  
  
  
  echo "</tr> ";
  
 
			
}
echo "<th></th> ";
echo "<th></th>";
echo "<th></th>";
echo "<th></th>";
echo "<th></th>";
echo "<th></th>";
echo '</table>';
echo '</center>';
echo "</div>";


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Busqueda Egreso';
</script> 
<script src="js/bootstrap.min.js"></script>
</body>
</html>