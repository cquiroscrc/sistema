<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
  $fecha = $_REQUEST['fecha'];
  
} 
$fecha1=$fecha;
$fecha2=$fecha1;
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
$nc=0;
$rep=0;
$pap=0;
$com=0;
$aten=0;
$enco=0;
$luz=0;
$agua=0;
$tel=0;
$otro=0;
$alf=0;


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Maximo 540
$pdf-> startPageGroup();
$pdf-> AddPage();

$bloque1 = <<<EOF
	<table>
			<tr>
				<td style="width:150px"><img src="images/logo-blanco-bloque.png"></td>
				<td style="width:240px">
					<div style="font-size:12px; text-align:center;">
						<br>
							<b>Reporte de Cierre diario del día $fecha</b>						
					</div>
				</td>			
				<td>					
				</td>
			</tr>
	</table>

EOF;
$pdf-> writeHTML($bloque1,false,false,false,false,'');

$sql="SELECT * FROM cxc WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and NFAC like'R-%' order by NFac";  
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
foreach ($ejecutar as $key => $arr) {
    $totalrecibos=$totalrecibos+$arr["Total"];
}
$totalrecibos_i=$totalrecibos;
$totalrecibos=number_format($totalrecibos,2);

$sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  order by nfac";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

foreach ($ejecutar as $key => $arr) {

	if ($arr["Tipo"]=='0'){
      $varsubtotal = $varsubtotal + $arr['subtotal'];
      $varimpuesto = $varimpuesto + $arr['impuesto'];
      $vardescuento = $vardescuento + $arr['descuento'];
      $vartotal= $vartotal + $arr['total'];
      $temptotal=$varsubtotal+$varimpuesto-$vardescuento;
      
      
    
    }else{
      $varsubtotalC = $varsubtotalC + $arr['subtotal'];
      $varimpuestoC = $varimpuestoC + $arr['impuesto'];
      $vardescuentoC = $vardescuentoC + $arr['descuento'];
      $vartotalC= $vartotalC + $arr['total'];
      $temptotalC=$varsubtotalC+$varimpuestoC-$vardescuentoC;
      
    }
    


}

$varsubtotalt=number_format($varsubtotal+$varsubtotalC,2);
$varimpuestot=number_format($varimpuesto+$varimpuestoC,2);
$vardescuentot=number_format($vardescuento+$vardescuentoC,2);
$vartotalt=number_format($vartotal+$vartotalC,2);


$varsubtotal=number_format($varsubtotal,2);
$varimpuesto=number_format($varimpuesto,2);
$vardescuento=number_format($vardescuento,2);
$vartotal_i=$vartotal;
$vartotal=number_format($vartotal,2);
$varsubtotalC=number_format($varsubtotalC,2);
$varimpuestoC=number_format($varimpuestoC,2);
$vardescuentoC=number_format($vardescuentoC,2);
$vartotalC=number_format($vartotalC,2);


$bloque2 = <<<EOF
<table>
<tr>
	<td style="background-color:#FF0000;color:white;text-align:center;"><b>Ventas</b></td>
</tr>
</table>

EOF;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$bloque3 = <<<EOF
<table>
<tr>
<td><h3>Ventas de Contado</h3></td>
<td><h3>Ventas de Credito</h3></td>
<td><h3>Total de Ventas</h3></td>
</tr><tr>
<td><h4>Subtotal: $varsubtotal</h4></td>
<td><h4>Subtotal: $varsubtotalC</h4></td>
<td><h4>Subtotal: $varsubtotalt</h4></td>
</tr><tr>
<td><h4>Impuesto: $varimpuesto</h4></td>
<td><h4>Impuesto: $varimpuestoC</h4></td>
<td><h4>Impuesto: $varimpuestot</h4></td>
</tr><tr>
<td><h4>Descuento: $vardescuento</h4></td>
<td><h4>Descuento: $vardescuentoC</h4></td>
<td><h4>Descuento: $vardescuentot</h4></td>
</tr><tr>
<td><h3><b>Total: $vartotal</b></h3></td>
<td><h3><b>Total: $vartotalC</b></h3></td>
<td><h3><b>Total: $vartotalt</b></h3></td>
</tr>

</table>
<h3>Total Recibos: $totalrecibos </h3>

EOF;
$pdf-> writeHTML($bloque3,false,false,false,false,'');

$bloque4 = <<<EOF
<table>
<tr>
	<td style="background-color:#FF0000;color:white;text-align:center;"><b>Reporte de Egresos</b></td>
</tr>
</table>

EOF;
$pdf-> writeHTML($bloque4,false,false,false,false,'');

$sql_egresos="SELECT e.Fecha, e.Documento, e.Tipo, e.Descripcion, e.Monto, t.Descripcion as egreso
FROM tbl_Egresos e, tbl_TiposEgresos t
WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'
AND e.Tipo = t.Tipo order by e.Tipo,e.Documento";

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_egresos);

foreach ($ejecutar as $key => $arr_egresos) {
    
    if ($arr_egresos["Tipo"]==0) {
        $nc=$nc+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==1) {
        $rep=$rep+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==2) {
        $pap=$pap+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==3) {
        $com=$com+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==4) {
        $aten=$aten+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==5) {
        $enco=$enco+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==6) {
        $luz=$luz+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==7) {
        $agua=$agua+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==8) {
        $tel=$tel+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==9) {
        $otro=$otro+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==10) {
        $alf=$alf+$arr_egresos['Monto'];
    }

    $totalegresos=$totalegresos+$arr_egresos["Monto"];
}
$totalegresos_i=$totalegresos;
$totalegresos=number_format($totalegresos,2);
$nc=number_format($nc,2);
$rep=number_format($rep,2);
$pap=number_format($pap,2);
$com=number_format($com,2);
$aten=number_format($aten,2);
$enco=number_format($enco,2);
$luz=number_format($luz,2);
$agua=number_format($agua,2);
$tel=number_format($tel,2);
$otro=number_format($otro,2);
$alf=number_format($alf,2);

$bloque5 = <<<EOF
<table>
<tr>
<td><h4>Adelanto Compras: $nc</h4></td>
<td><h4>Repuestos: $rep</h4></td>
<td><h4>Papelería: $pap</h4></td>
</tr><tr>
<td><h4>Parqueos: $com</h4></td>
<td><h4>Atención: $aten</h4></td>
<td><h4>Encomiendas: $enco</h4></td>
</tr><tr>
<td><h4>Material Empaque: $luz</h4></td>
<td><h4>Combustible: $agua</h4></td>
<td><h4>Teléfono: $tel</h4></td>
</tr><tr>
<td><h4>Otros Egresos: $otro</h4></td>
<td><h4>Cierres de Tarjetas: $alf</h4></td>
<td><h4></h4></td>
</tr>
</table>
<h3>Total Egresos: $totalegresos </h3>

EOF;
$pdf-> writeHTML($bloque5,false,false,false,false,'');

$bloque6 = <<<EOF
<table>
<tr>
	<td style="background-color:#FF0000;color:white;text-align:center;"><b>Depositos</b></td>
</tr>
</table>

EOF;
$pdf-> writeHTML($bloque6,false,false,false,false,'');

$bloque7 = <<<EOF
<table>
<tr>
<td><h3>Cuenta</h3></td>
<td><h3>Banco</h3></td>
<td><h3>Documento</h3></td>
<td><h3>Credito</h3></td>
</tr>
</table>

EOF;
$pdf-> writeHTML($bloque7,false,false,false,false,'');

$sql_depositos="SELECT B.Fecha, B.Cuenta, B.Documento, B.Descripcion, B.Credito, C.Banco FROM tbl_Bancos B, tbl_Cuentas C 
WHERE B.Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND B.Descripcion like 'Caja del %' and B.Cuenta = C.Cuenta order by B.Cuenta, B.Documento"; 
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_depositos);

foreach ($ejecutar as $key => $arr_depositos) {
$cuenta = $arr_depositos['Cuenta'];
$banco=$arr_depositos['Banco'];
$documento=$arr_depositos['Documento'];
$credito=$arr_depositos['Credito'];
$totaldepositos=$totaldepositos+$arr_depositos["Credito"];
$credito=number_format($credito,2);
$bloque8 = <<<EOF
<table>
<tr>
<td><h4>$cuenta</h4></td>
<td><h4>$banco</h4></td>
<td><h4>$documento</h4></td>
<td><h4>$credito</h4></td>
</tr>
</table>

EOF;
$pdf-> writeHTML($bloque8,false,false,false,false,'');

}
$totaldepositos_i=$totaldepositos;
$totaldepositos=number_format($totaldepositos,2);
$bloque9 = <<<EOF

<h3><b>Total Depositos: $totaldepositos</b></h3>

<table>
<tr>
	<td style="background-color:#FF0000;color:white;text-align:center;"><b>Totales</b></td>
</tr>
</table>

EOF;
$pdf-> writeHTML($bloque9,false,false,false,false,'');

$totalingresos=$vartotal_i+$totalrecibos_i;
$totaldif=$totaldepositos_i-$totalingresos+$totalegresos_i;

$totalingresos=number_format($totalingresos,2);
$totaldif=number_format($totaldif,2);

$bloque10 = <<<EOF
<table>
<tr>
<td><h3>Total Ingresos</h3></td>
<td><h3>Total Egresos</h3></td>
<td><h3>Total Depositos</h3></td>
<td><h3>Diferencia</h3></td>
</tr>
<tr>
<td><h4>$totalingresos</h4></td>
<td><h4>$totalegresos</h4></td>
<td><h4>$totaldepositos</h4></td>
<td><h4>$totaldif</h4></td>
</tr>
</table>

EOF;
$pdf-> writeHTML($bloque10,false,false,false,false,'');

$pdf->Output('Reporte_Cierre.pdf');


 ?>