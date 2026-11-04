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





?>
<a href="#" onClick="location.href = 'buscar_recibos.php'"><img width="48"  height="48" src= "img/back.png">Volver</a> 
<a href="#" onClick="window.print();"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>


<?php

      $sql="SELECT c.NFac, c.IDCliente, c.Fecha, c.Total, n.codigo, n.nombre FROM cxc c, clientes n WHERE c.NFac like 'R%$documento%' AND c.IDCliente= n.codigo ORDER BY c.Fecha,c.NFac";  

  //echo $sql; exit;

  //$sql="SELECT b.Fecha,b.Cuenta,b.Documento,b.Descripcion,b.Credito,b.Debito, c.Banco FROM tbl_Bancos b, tbl_Cuentas c WHERE b.Documento = '$documento' or b.Descripcion like '$descripcion' or b.Credito=$monto or b.Debito = $monto AND b.Cuenta= c.Cuenta ORDER BY b.Fecha,b.Descripcion";  



if(($res = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
            

//ingresa nombres de cuadro
echo "<div id='muestra'>";
echo '<center>';
echo '<h2>Busqueda de Recibo</h2>';
echo "<table border = '1'> ";
echo "<tr> ";
echo "<th WIDTH=100><b>Fecha</b></th> ";
echo "<th WIDTH=100><b>Documento</b></th> ";
echo "<th WIDTH=300><b>Nombre</b></th>";
echo "<th WIDTH=100><b>Cliente</b></th>";
echo "<th WIDTH=150><b>Monto</b></th> ";
echo "</tr> ";
echo "<tr> ";
$credito=0;
$debito=0;
while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro


  $fecha_sis=strtotime($arr['Fecha']);
  echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
  echo "<td>".$arr['NFac']."</td>";
  echo "<td>".$arr['nombre']."</td>";
  echo "<td>".$arr['IDCliente']."</td>";
  echo "<td>".number_format($arr['Total'],2)."</td>";
  echo "</tr> ";
  
 
			
}
echo "<th></th> ";
echo "<th></th>";
echo "<th></th>";
echo "<th></th>";
echo "<th></th>";
echo '</table>';
echo '</center>';
echo "</div>";


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Busqueda Recibo';
</script> 
<script src="js/bootstrap.min.js"></script>
</body>
</html>