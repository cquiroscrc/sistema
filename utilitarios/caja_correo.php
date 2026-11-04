<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Deposito ya existe";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    //include "menu.php";
?>
<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resultado de Cierres</title>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/anular.css">
<link rel="StyleSheet" href="css/calendar.css">
<link rel="icon" href="favicon.ico">
</head>

<body>
  <style type="text/css">

  .botones{
    width:850px;
    height: 75px;
    padding:10px;
    background-color:#eee;
    border:1px solid #444;
  }
  .drecibos{
    width:300px;
    clear:left;
    float:left;
    border:1px solid #444;
    
  }
  .degresos{
    width:550px;
   
    float:left;
    border:1px solid #444;
    
  }
  .ddepositos{
    width:450px;
    float:left;
    border:1px solid #444;
  }
  #footer {
position: relative;
margin-top: -50px; /* altura en px del footer con valor negativo */
height: 50px; /* Altura del Footer en px*/
clear: both;
text-align: center;
color: #FFFFFF;
}

  </style>
  
  <?php
$fecha1=date("Y-m-d");
$fecha2=$fecha1;
require_once("db.php");

if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
$fecha2 = $_REQUEST['Date2'];

}
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
$tipo = $_REQUEST['tipo'];

}
$tipo='dia';
if ($tipo=='dia') {
  $fecha1=$fecha2;
}
@$varsubtotal =0;
@$varimpuesto =0;
@$vardescuento =0;
@$vartotal =0;
$codigobusq = '';

if ($fecha1 != ""){
  $sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  order by nfac";  
  if(($res = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));

  $varsubtotal=0;
  $varimpuesto=0;
  $vardescuento=0;
  $vartotal=0;
  $varsubtotalC=0;
  $varimpuestoC=0;
  $vardescuentoC=0;
  $vartotalC=0;
  $varmensajeria=0;
  $tempmensa=0;
  $tempmensaC=0;
  $totalrecibos=0;
  $totalegresos=0;
  $totalingresos=0;
  $totaldepositos=0;
  $totaldif=0;


  //ingresa nombres de cuadro

  while($arr = odbc_fetch_array($res)){
  //hacer los totales


    if ($arr["Tipo"]==0){
      $varsubtotal = $varsubtotal + $arr["subtotal"];
      $varimpuesto = $varimpuesto + $arr["impuesto"];
      $vardescuento = $vardescuento + $arr["descuento"];
      $vartotal= $vartotal + $arr["total"];
      $temptotal=$varsubtotal+$varimpuesto-$vardescuento;
      $tempmensa=$vartotal-$temptotal;
      

    }else{
      $varsubtotalC = $varsubtotalC + $arr["subtotal"];
      $varimpuestoC = $varimpuestoC + $arr["impuesto"];
      $vardescuentoC = $vardescuentoC + $arr["descuento"];
      $vartotalC= $vartotalC + $arr["total"];  
      $temptotalC=$varsubtotalC+$varimpuestoC-$vardescuentoC;
      $tempmensaC=$vartotalC-$temptotalC;
    }

    $varmensajeria=$tempmensa+$tempmensaC;
  			

  }
    //Datos de totales
  
  echo '<center>';
  echo '<h1>Cierre de Caja de :'.$fecha1.'</h1>';
  echo "<table border = '1'> ";
  echo "<tr> ";
  echo "<td><b>Subtotal</b></td> ";
  echo "<td><b>Descuento</b></td>";
  echo "<td><b>Impuesto</b></td> ";
  echo "<td><b>Total</b></td> ";
  echo "<td><b>Credito</b></td> ";
  echo "<td><b>Subtotal</b></td> ";
  echo "<td><b>Descuento</b></td> ";
  echo "<td><b>Impuesto</b></td> ";
  echo "<td><b>Total</b></td> ";
  echo "<td><b>Mensajeria</b></td> ";
  echo "</tr> ";
  echo "<tr> ";
  echo "</tr> ";
  echo "<td>".number_format($varsubtotal,2)."</td> ";
  echo "<td>".number_format($vardescuento,2)."</td>";
  echo "<td>".number_format($varimpuesto,2)."</td> ";
  echo "<td>".number_format($vartotal,2)."</td> ";
  echo "<td><b>Credito -></b></td> ";
  echo "<td>".number_format($varsubtotalC,2)."</td> ";
  echo "<td>".number_format($vardescuentoC,2)."</td> ";
  echo "<td>".number_format($varimpuestoC,2)."</td> ";
  echo "<td>".number_format($vartotalC,2)."</td> ";
  echo "<td>".number_format($varmensajeria,2)."</td> ";
  echo "</table>";
  echo "</br>";
 
 
  echo "</br>";
  // Recibos
  
  
  $sql_recibos="SELECT * FROM cxc WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and NFAC like'R-%' order by NFac";  

  if(($res_recibos = odbc_exec($id,$sql_recibos)) === false)
      die("Error en Select: ". odbc_errormsg($id));

  while($arr_recibos = odbc_fetch_array($res_recibos)){
    $fact=$arr_recibos["NFac"];
    $cliente=$arr_recibos["IDCliente"];
    $totalrecibos=$totalrecibos+$arr_recibos["Total"];
    
  }

 
  //$totalrecibos=$totalrecibos*-1;
  
  // Egresos
  
  
  $sql_egresos="SELECT e.Fecha, e.Documento, e.Tipo, e.Descripcion, e.Monto, t.Descripcion as egreso
  FROM tbl_Egresos e, tbl_TiposEgresos t
  WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'
  AND e.Tipo = t.Tipo order by e.Tipo,e.Documento";
  
  $e_descuentos=0;
  $e_repuestos=0;
  $e_papeleria=0;
  $e_combustible=0;
  $e_atencion=0;
  $e_encomiendas=0;
  $e_luz=0;
  $e_agua=0;
  $e_telefono=0;
  $e_otros=0;
  $e_alfaro=0;

  if(($res_egresos = odbc_exec($id,$sql_egresos)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    
  while($arr_egresos = odbc_fetch_array($res_egresos)){

    $documento=$arr_egresos["Documento"];
    $tipoE=$arr_egresos["Tipo"];
    
    //**** totales de engresos
    if ($tipoE==0) {
      $e_descuentos=$e_descuentos+ $arr_egresos['Monto'];
    }
    if ($tipoE==1) {
      $e_repuestos=$e_repuestos+ $arr_egresos['Monto'];
    }
    if ($tipoE==2) {
      $e_papeleria=$e_papeleria+ $arr_egresos['Monto'];
    }
    if ($tipoE==3) {
      $e_atencion=$e_atencion+ $arr_egresos['Monto'];
    }
    if ($tipoE==4) {
      $e_atencion=$e_atencion+ $arr_egresos['Monto'];
    }
    if ($tipoE==5) {
      $e_encomiendas=$e_encomiendas+ $arr_egresos['Monto'];
    }
    if ($tipoE==6) {
      $e_luz=$e_luz+ $arr_egresos['Monto'];
    }
    if ($tipoE==7) {
      $e_agua=$e_agua+ $arr_egresos['Monto'];
    }
    if ($tipoE==8) {
      $e_telefono=$e_telefono+ $arr_egresos['Monto'];
    }
    if ($tipoE==9) {
      $e_otros=$e_otros+ $arr_egresos['Monto'];
    }
    if ($tipoE==10) {
      $e_alfaro=$e_alfaro+ $arr_egresos['Monto'];
    }

    $totalegresos=$totalegresos+$arr_egresos["Monto"];
  }
  
  $totalingresos=$vartotal+$totalrecibos;
  
  // Depositos
  
 
  $sql_depositos="SELECT * FROM tbl_Bancos WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND Descripcion like 'Caja del %'  order by Cuenta, Documento";  

  if(($res_depositos = odbc_exec($id,$sql_depositos)) === false)
      die("Error en Select: ". odbc_errormsg($id));

  while($arr_depositos = odbc_fetch_array($res_depositos)){
    $ddocumento=$arr_depositos["Documento"];
    $cuenta=$arr_depositos["Cuenta"];
    
    $totaldepositos=$totaldepositos+$arr_depositos["Credito"];
  }
  

}

  echo '<div id="footer">';
  echo '<h1>Totales de cierre de caja de :'.$fecha1.'</h1>';
  echo '<a name="Totales" id="a"></a>';
  echo '<center>';
  echo "<table border = '1'> ";
  echo "<tr> ";
  echo "<td><b>Contado</b></td> ";
  echo "<td><b>Credito</b></td>";
  echo "<td><b>Recibos</b></td>";
  echo "<td><b>Descuentos</b></td>";
  echo "<td><b>Repuestos</b></td> ";
  echo "<td><b>Papeleria</b></td> ";
  echo "<td><b>Combustible</b></td> ";
  echo "<td><b>Atencion</b></td> ";
  echo "<td><b>Encomiendas</b></td> ";
  echo "<td><b>Luz</b></td> ";
  echo "<td><b>Agua</b></td> ";
  echo "<td><b>Telefono</b></td> ";
  echo "<td><b>Otros</b></td> ";
  echo "<td><b>Cierres Tarjetas</b></td> "; //******
  echo "</tr> ";
  echo "<tr> ";
  echo "<td>".number_format($vartotal,2)."</td> ";
  echo "<td>".number_format($vartotalC,2)."</td> ";
  echo "<td>".number_format($totalrecibos,2)."</td> ";
  echo "<td>".number_format($e_descuentos,2)."</td> ";
  echo "<td>".number_format($e_repuestos,2)."</td> ";
  echo "<td>".number_format($e_papeleria,2)."</td> ";
  echo "<td>".number_format($e_combustible,2)."</td> ";
  echo "<td>".number_format($e_atencion,2)."</td> ";
  echo "<td>".number_format($e_encomiendas,2)."</td> ";
  echo "<td>".number_format($e_luz,2)."</td> ";
  echo "<td>".number_format($e_agua,2)."</td> ";
  echo "<td>".number_format($e_telefono,2)."</td> ";
  echo "<td>".number_format($e_otros,2)."</td> ";
  echo "<td>".number_format($e_alfaro,2)."</td> ";

  echo "</table>";
  echo "<br>";
  echo "<table border = '1'> ";
  echo "<tr> ";
  echo "<td><b>Ingresos</b></td> ";
  echo "<td><b>Egresos</b></td>";
  echo "<td><b>Total Caja</b></td>";
  echo "<td><b>Depositos</b></td>";
  echo "<td><b>Diferencia</b></td> ";
  echo "</tr> ";
  echo "<tr> ";
  $totalcaja=$totalingresos-$totalegresos;
  echo '<td>'.number_format($totalingresos,2).'</td>';
  echo '<td>'.number_format($totalegresos,2).'</td> ';
  echo '<td>'.number_format($totalcaja,2).'</td> ';
  echo '<td>'.number_format($totaldepositos,2).'</td>';
  $totaldif=$totaldepositos-$totalingresos+$totalegresos;
  if ($totaldif<0) {
    
    echo '<td style="color:red"><b>'.number_format($totaldif,2).'</b></td>';
  }else{
    echo '<td style="color:green"><b>'.number_format($totaldif,2).'</b></td>';  
  }
  
  echo '</tr>';
  echo '</div>';
  echo '</table>';
  //echo $totalrecibos;
  $existe=0;

  $sql_existe = "SELECT * FROM cierrediario WHERE Fecha='$fecha2'";
  if(($res_existe = odbc_exec($id,$sql_existe)) === false)
        die("Error en Select: ". odbc_errormsg($id));

  while($arr = odbc_fetch_array($res_existe)){
    $existe=1;
  }

  function primer_mes() {
      $month = date('m');
      $year = date('Y');
      return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
  }

  function ultimo_mes() { 
      $month = date('m');
      $year = date('Y');
      $day = date("d", mktime(0,0,0, $month+1, 0, $year));
 
      return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
      };

  $inicio=primer_mes();      
 
  $fin=ultimo_mes();
  
  $valor1="SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                 .$inicio." 00:00:00.000' AND fechafact <= '".$fin." 23:59:00.000' AND impuesto <> 0";

  if(($res_valor1 = odbc_exec($id,$valor1)) === false)
        die("Error en Select: ". odbc_errormsg($id));

  while($arr7 = odbc_fetch_array($res_valor1)){
    $meta=$arr7["venta"];
  }


  $ano = date('Y');
  $mes = date('m');
  
 
  $sql_meta="SELECT * FROM tbl_metas WHERE mes =".$mes." AND ano=".$ano."";

  

  if(($res_valor1 = odbc_exec($id,$sql_meta)) === false)
        die("Error en Select: ". odbc_errormsg($id));

  while($arr7 = odbc_fetch_array($res_valor1)){
    $metamensual=$arr7["meta"];
    $metamensual=$metamensual*4;
  }
  

  $datos[1]=$fecha1;
  $datos[2]=$varsubtotal;
  $datos[3]=$vardescuento;
  $datos[4]=$varimpuesto;
  $datos[5]=$vartotal;
  $datos[6]=$varsubtotalC;
  $datos[7]=$vardescuentoC;
  $datos[8]=$varimpuestoC;
  $datos[9]=$vartotalC;
  $datos[10]=$varmensajeria;
  $datos[11]=$totalrecibos;
  $datos[12]=$e_descuentos;
  $datos[13]=$e_repuestos;
  $datos[14]=$e_papeleria;
  $datos[15]=$e_combustible;
  $datos[16]=$e_atencion;
  $datos[17]=$e_encomiendas;
  $datos[18]=$e_luz;
  $datos[19]=$e_agua;
  $datos[20]=$e_telefono;
  $datos[21]=$e_otros;
  $datos[22]=$e_alfaro;
  $datos[23]=$totalingresos;
  $datos[24]=$totalegresos;
  $datos[25]=$totalcaja;
  $datos[26]=$totaldepositos;
  $datos[27]=$totaldif;
  $datos[28]=$meta;
  $datos[29]=$metamensual;

  $dat=serialize($datos);
  $dat=urlencode($dat); 
  
  if ($totalingresos<>0) {
    header("location: http://lospaticas.com/cierres.php?datos=".$dat."");   
  }
  
?>
<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Cierre Correo';
</script> 
<script src="js/bootstrap.min.js"></script>
</body>
</html>