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
<link rel="icon" href="img/favicon.ico">
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
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
$tipo = $_REQUEST['tipo'];

}
if ($tipo=='dia') {
  $fecha1=$fecha2;
}

echo '<br>';
echo '<a href="rep_facturas_dia.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
$data = file_get_contents("http://apis.gometa.org/status/status.json");
$products = json_decode($data, true);

$errores=$products['api-prod']['lasterror'];
$statushacienda=$products['api-prod']['status'];
$actualizacion=$products['api-prod']['last_update'];
echo "<br>";

echo "<center>";
echo "<table> ";
echo "<tr> ";
echo"<td><img width='48'  height='48' src= 'img/hacienda.jpg'></td>";
echo "<td><b>Ultima actualización</b></td> ";
echo "<td style='color:blue'>".$actualizacion."</td> ";
echo "<td><b>Ultimo error</b></td>";
echo "<td style='color:red'>".$errores."</td>";
echo "<td><b>Estado Hacienda</b></td>";
if ($statushacienda=='OK') {
echo "<td><img width='48'  height='48' src= 'img/check.png'> </td>";
}
if ($statushacienda=='BAD') {
echo "<td><img width='48'  height='48' src= 'img/cancel-icon.png'></td>";
}
echo "</tr></table> ";
echo "</center>";  

echo "<br>";


@$varsubtotal =0;
@$varimpuesto =0;
@$vardescuento =0;
@$vartotal =0;

$codigobusq = '';


if ($fecha1 != ""){
	

      //$sql="SELECT * FROM DOC_ELECTRONICO_PROCESADO WHERE FECHA_HORA BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  order by FECHA_HORA DESC";  

      $sql="SELECT D.FECHA_HORA,D.NUMERO_DOC,D.EMAIL_RECEPTOR,D.TIPO_DOC,D.ESTADO,D.TOTALCOMPROBANTE,D.ENVIADO,D.ACEPTADO,D.ULTIMO_ERROR,D.FECHA_HORA,F.nfac,F.Nombre,F.vendedor FROM DOC_ELECTRONICO_PROCESADO D, hfacturas F WHERE D.FECHA_HORA BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND D.NUMERO_DOC=F.nfac order by D.FECHA_HORA DESC";
       
          if(($res = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
        



//ingresa nombres de cuadro
echo '<center>';
echo "<H1> REPORTE DE FACTURAS DEL DIA ".$fecha1."</H1>";
echo "<table border = '1'> ";
echo "<tr> ";
echo "<td><b>Id</b></td> ";
echo "<td><b>Fecha</b></td> ";
echo "<td><b>Factura</b></td>";
echo "<td><b>Nombre</b></td>";
echo "<td><b>Vend</b></td>";
echo "<td><b>Correo</b></td>";
echo "<td><b>Tipo</b></td> ";
echo "<td><b>Total</b></td> ";
echo "<td><b>Estado</b></td> ";
echo "<td><b>Env</b></td> ";
echo "<td><b>Acep</b></td> ";
echo "<td><b>Error</b></td> ";

echo "</tr> ";
echo "<tr> ";
$id_f=1;

while($arr = odbc_fetch_array($res)){
    //ingresa datos en cuadro
    echo "<td>".$id_f."</td> ";  
    echo "<td>".$arr["FECHA_HORA"]."</td> ";
    echo "<td>".$arr["NUMERO_DOC"]."</td>";
    echo "<td>".$arr["Nombre"]."</td>";
    echo "<td>".$arr["vendedor"]."</td>";

   
    if ($arr["EMAIL_RECEPTOR"]=="facturaelectronica@lospaticas.com") {
      echo "<td style='color:grey'>Correo Local</td>";
    }else{

      echo "<td style='color:blue'>".$arr["EMAIL_RECEPTOR"]."</td>";
    }


    //echo "<td>".$arr["TIPO_DOC"]."</td>";
    if ($arr["TIPO_DOC"]==="FE") {
      echo "<td style='color:green'>".$arr["TIPO_DOC"]."</td>";
    }
    if ($arr["TIPO_DOC"]==="NC") {
      echo "<td style='color:purple'>".$arr["TIPO_DOC"]."</td>";
    }
    echo "<td>".number_format($arr["TOTALCOMPROBANTE"],2)."</td>";

    if ($arr["ESTADO"]=="aceptado") {
      echo "<td style='color:green'>".$arr["ESTADO"]."</td>";
    } 
    if ($arr["ESTADO"]=="aceptado-previo") {
      echo "<td style='color:blue'>".$arr["ESTADO"]."</td>";
    }
    if ($arr["ESTADO"]=="VACIO") {
      echo "<td style='color:red'>".$arr["ESTADO"]."</td>";
    }
    if($arr["ESTADO"]=="rechazado"){
      echo "<td style='color:red'>".$arr["ESTADO"]."</td>";
    }

    //echo "<td>".$arr["ESTADO"]."</td>";
    echo "<td>".$arr["ENVIADO"]."</td>";
    echo "<td>".$arr["ACEPTADO"]."</td>";
    echo "<td>".$arr["ULTIMO_ERROR"]."</td>";
        


    echo "</tr> ";
    			
    $id_f=$id_f+1;
}

echo '</table>';
echo '</center>';

}
$self = "rep_facturas_resultado.php"; //Obtenemos la página en la que nos encontramos
//header("refresh:10; url=$self"); //Refrescamos cada 300 segundos
$self=$self."?Date1=".$fecha1."&Date2=".$fecha2."&tipo=dia";
echo $self;
echo "<META HTTP-EQUIV='refresh' CONTENT='240; URL=".$self."'>"; 
//header("Refresh: 5; url=".$self);


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / FE Resultado';
</script> 
<script src="js/bootstrap.min.js"></script>

</body>
</html>