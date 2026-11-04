<?php
session_start();
//date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Deposito ya existe";
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
<title>Resultado de Cierres</title>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/anular.css">
<link rel="StyleSheet" href="css/calendar.css">
<link rel="icon" href="img/favicon.ico">
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
require_once("db.php");
echo '<a name="Inicio" id="a"></a>';
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

$data = file_get_contents("http://apis.gometa.org/tdc/tdc.json");
$tc = json_decode($data, true);
$venta=0; $compra=0;
$venta=$tc['venta'];
$compra=$tc['compra'];

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
  echo '<h1>Cierre de caja de :'.$fecha1.'</h1>';
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
  echo "<td><b>TC</b></td> ";
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
  echo "<td>".number_format($compra-3,0)."</td> ";
  echo "</table>";
  echo "</br>";
  //botones
  echo '<div class="botones">';
  echo '<a href="#Totales"><img width="48"  height="48" src= "img/downdb.png">Ver Totales</a>';
  echo '<a href="cierres.php?Date2='.$fecha2.'&tipo=dia"><img width="48"  height="48" src= "img/cierre.png">Ver facturas</a>';
  echo '<a href="cambio_fecha.php"><img width="48"  height="48" src= "img/fecha.png">Cambio de Fecha</a>';
  echo '<a target="_blank" href="cierre_dia_reporte_fact.php?Date1='.$fecha2.'&Date2='.$fecha2.'"><img width="48"  height="48" src= "img/facts.png">Reporte Facturas</a>';
  echo '<a target="_blank" href="cierre_dia_reporte_totales.php?Date2='.$fecha2.'"><img width="48"  height="48" src= "img/cierre.png">Reporte Cierre</a>';
  echo '</div>';
  echo "</br>";
  // Recibos
  echo '<div class="drecibos">';
  echo '<h2>Recibos</h2>';
  echo '<center>';
  echo "<table border = '1'> ";
  echo "<tr> ";
  echo '<td><a href="cierre_dia_ingresar_recibo.php?Date2='.$fecha2.'&tipo=dia"><img width="32"  height="32" src= "img/add.png"></a></td>';
  echo "<td><b># Recibo</b></td> ";
  echo "<td><b>Cliente</b></td>";
  echo "<td><b>Monto</b></td> ";
  echo "</tr> ";
  echo "<tr> ";
  
  $sql_recibos="SELECT * FROM cxc WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and NFAC like'R-%' order by NFac";  

  if(($res_recibos = odbc_exec($id,$sql_recibos)) === false)
      die("Error en Select: ". odbc_errormsg($id));

  while($arr_recibos = odbc_fetch_array($res_recibos)){
    $fact=$arr_recibos["NFac"];
    $cliente=$arr_recibos["IDCliente"];
    echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar el recibo?\")) 
      location.href=\"cierre_dia_eliminar_recibos.php?cliente=".$cliente."&Date2=".$fecha2."&tipo=dia&recibo=".$fact."\";
      '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";

    echo '<td>'.$fact.'</td> ';
    echo '<td>'.$cliente.'</td>';
    echo '<td>'.number_format($arr_recibos["Total"],2).'</td>';
    echo '</tr>';
    $totalrecibos=$totalrecibos+$arr_recibos["Total"];
    
  }

  echo '<td></td><td></td><td><b>Total Recibos</b></td><td><b>'.number_format($totalrecibos,2).'</b></td></tr>';
  echo "</table>";
  echo "</br>";
  echo '</div>';
  $totalrecibos=$totalrecibos*-1;
  
  // Egresos
  echo '<div class="degresos">';
  echo '<h2>Egresos</h2>';
  echo '<center>';
  echo "<table border = '1'> ";
  echo "<tr> ";
  echo '<td><a href="cierre_dia_ingresar_egresos.php?Date2='.$fecha2.'&tipo=dia"><img width="32"  height="32" src= "img/add.png"></a></td>';
  echo "<td><b># Egreso</b></td> ";
  echo "<td><b>Tipo</b></td>";
  echo "<td><b>Egreso Descripcion</b></td>";
  echo "<td><b>Descripcion</b></td>";
  echo "<td><b>Monto</b></td> ";
  echo "</tr> ";
  echo "<tr> ";
  
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
    echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar el egreso?\")) 
      location.href=\"cierre_dia_eliminar_egresos.php?tegreso=".$tipoE."&Date2=".$fecha2."&tipo=dia&egreso=".$documento."\";
      '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
    echo '<td>'.$documento.'</td> ';
    echo '<td>'.$tipoE.'</td>';
    echo '<td>'.$arr_egresos["egreso"].'</td>';
    echo '<td>'.$arr_egresos["Descripcion"].'</td>';
    echo '<td>'.number_format($arr_egresos["Monto"],2).'</td>';
    echo '</tr>';
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
      $e_combustible=$e_combustible+ $arr_egresos['Monto'];
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
  echo '<td></td><td></td><td><b>Total Egresos</b></td><td><b>'.number_format($totalegresos,2).'</b></td><td></td><td></td></tr>';
  echo "</table>";
  echo "</br>";
  echo '</div>';

  $totalingresos=$vartotal+$totalrecibos;
  
  // Depositos
  echo '<div class="ddepositos">';
  echo '<h2>Depositos</h2>';
  echo '<center>';
  echo "<table border = '1'> ";
  echo "<tr> ";
  echo '<td><a href="cierre_dia_ingresar_deposito.php?Date2='.$fecha2.'&tipo=dia"><img width="32"  height="32" src= "img/add.png"></a></td>';
  echo "<td><b># Deposito</b></td> ";
  echo "<td><b>Cuenta</b></td>";
  echo "<td><b>Descripcion</b></td>";
  echo "<td><b>Monto</b></td> ";
  echo "</tr> ";
  echo "<tr> ";
 
  $sql_depositos="SELECT * FROM tbl_Bancos WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND Descripcion like 'Caja del %'  order by Cuenta, Documento";  

  if(($res_depositos = odbc_exec($id,$sql_depositos)) === false)
      die("Error en Select: ". odbc_errormsg($id));

  while($arr_depositos = odbc_fetch_array($res_depositos)){
    $ddocumento=$arr_depositos["Documento"];
    $cuenta=$arr_depositos["Cuenta"];
    echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar el deposito?\")) 
      location.href=\"cierre_dia_eliminar_depositos.php?cuenta=".$cuenta."&Date2=".$fecha2."&tipo=dia&deposito=".$ddocumento."\";
      '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
    
    echo '<td>'.$ddocumento.'</td> ';
    echo '<td>'.$cuenta.'</td>';
    echo '<td>'.$arr_depositos["Descripcion"].'</td>';
    echo '<td>'.number_format($arr_depositos["Credito"],2).'</td>';
    echo '</tr>';
    $totaldepositos=$totaldepositos+$arr_depositos["Credito"];
  }
  echo '<td></td><td></td><td><b>Total Depositos</b></td><td><b>'.number_format($totaldepositos,2).'</b></td><td></td></tr>';
  echo "</table>";
  echo "</br>";
  echo '</div>';
  echo "</br>";
  echo "</br>";

}

  echo '<div id="footer">';
  echo '<a href="#Inicio"><img width="48"  height="48" src= "img/up.png">Inicio</a>';
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
  echo "<td><b>Alfaro</b></td> "; //******
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

  if ($existe==1) {

    $sql_actualiza="UPDATE cierrediario SET I_VentasEfectivo ='".$vartotal."',
                  I_VentasCredito ='".$vartotalC."', I_RecibosDinero='".$totalrecibos."',
                  E_Descuentos='".$e_descuentos."', E_Repuestos='".$e_repuestos."',
                  E_Papeleria='".$e_papeleria."', E_Combustible='".$e_combustible."',
                  E_AtencionPublico='".$e_atencion."', E_Encomiendas='".$e_encomiendas."',
                  E_Luz='".$e_luz."',E_Agua='".$e_agua."',E_Telefono='".$e_telefono."',
                  E_Otros='".$e_otros."',E_Alfaro='".$e_alfaro."',T_Caja='".$totalcaja."',
                  T_Depositos='".$totaldepositos."',T_FaltaSobra='".$totaldif."'
                WHERE Fecha ='".$fecha2."'";

    if(($res_actualiza = odbc_exec($id,$sql_actualiza)) === false)
      die("Error en Select: ". odbc_errormsg($id));
            
 
  }
  if ($existe==0) {

    $sql_cierre="INSERT INTO cierrediario 
        (Fecha,I_VentasEfectivo,I_VentasCredito,I_RecibosDinero,E_Descuentos,E_Repuestos,E_Papeleria,e_Combustible,
          E_AtencionPublico,E_Encomiendas,E_Luz,E_Agua,E_Telefono,E_Otros,E_Alfaro,T_Caja,T_Depositos,T_FaltaSobra,Estado,Compañia,Notas)
        values ('".$fecha2."','".$vartotal."','".$vartotalC."','".$totalrecibos."','".$e_descuentos."',
          '".$e_repuestos."','".$e_papeleria."','".$e_combustible."','".$e_atencion."','".$e_encomiendas."',
          '".$e_luz."','".$e_agua."','".$e_telefono."','".$e_otros."','".$e_alfaro."','".$totalcaja."','".$totaldepositos."','".$totaldif."','0','0','')";
    if(($res_cierre = odbc_exec($id,mb_convert_encoding($sql_cierre,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));      
  }


$self = "cierre_dia_resultado.php"; //Obtenemos la página en la que nos encontramos
//header("refresh:10; url=$self"); //Refrescamos cada 300 segundos
$self=$self."?Date1=".$fecha1."&Date2=".$fecha2."&tipo=".$tipo;
echo $self;
echo "<META HTTP-EQUIV='refresh' CONTENT='480; URL=".$self."'>"; 
//header("Refresh: 5; url=".$self);

?>
<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Cierre diario';
</script> 
<script src="js/bootstrap.min.js"></script>
</body>
</html>