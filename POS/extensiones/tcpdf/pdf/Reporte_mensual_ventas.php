<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");



if(isset($_REQUEST['fini']) && !empty($_REQUEST['fini'])){
      $fini = $_REQUEST['fini'];
}
if(isset($_REQUEST['ffin']) && !empty($_REQUEST['ffin'])){
      $ffin = $_REQUEST['ffin'];
}
$sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fini 00:00:00.000' AND '$ffin 23:59:59.000'  order by nfac";
$i=0;
$varsubtotal=0; $varimpuesto=0; $vardescuento=0; $vartotal=0;
$varsubtotalC=0; $varimpuestoC=0; $vardescuentoC=0; $vartotalC=0;
$varmensajeria=0; $tempmensa=0; $tempmensaC=0; $totalrecibos=0;
$totalegresos=0; $totalingresos=0; $totaldepositos=0; $totaldif=0;


class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Reporte de Facturas', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Reporte de Facturas -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Maximo 540
$pdf-> startPageGroup();
//$pdf->setPrintHeader(false);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP,  PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE,15);
$pdf-> AddPage();

$pdf->SetFont('helvetica', 'B', 20);
$pdf->Write(0, 'Reporte de Facturas del '.$fini.' al '.$ffin, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:70px">Estado</th>
       <th style="width:100px">Factura</th>
       <th style="width:60px">Fecha</th>
       <th style="width:65px">Subtotal</th>
       <th style="width:65px">Impuesto</th>
       <th style="width:65px">Descuento</th>
       <th style="width:75px">Total</th>
       <th style="width:50px">Cliente</th>
	</tr>

</table>


EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$i=0;
foreach ($ejecutar as $key => $arr) {
$estado=$arr["estado"];
$nfac=$arr["nfac"];
$fecha=strtotime($arr['fechafact']);
$fechafact=date('Y-m-d',$fecha);
$subtotal=number_format($arr["subtotal"],2);
$impuesto=number_format($arr["impuesto"],2);
$descuento=number_format($arr["descuento"],2);
$total=number_format($arr["total"],2);
$proveedor=$arr["proveedor"];

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
  $tempmensaC=$vartotalC-$temptotalC;
}

$i++;


$pdf->SetFont('helvetica', '', 10);


$bloque3 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:70px">$estado</td>
    <td style="width:100px">$nfac</td>
    <td style="width:60px">$fechafact</td>
    <td style="width:65px;text-align:right;">$subtotal</td>
    <td style="width:65px;text-align:right;">$impuesto</td>
    <td style="width:65px;text-align:right;">$descuento</td>
    <td style="width:75px;text-align:right;">$total</td>
    <td style="width:50px;text-align:right;">$proveedor</td>
</tr>
</tbody>  
</table>


EOD;

$pdf-> writeHTML($bloque3,false,false,true,0,'');

}
$varsubtotalt=number_format($varsubtotal+$varsubtotalC,2);
$varimpuestot=number_format($varimpuesto+$varimpuestoC,2);
$vardescuentot=number_format($vardescuento+$vardescuentoC,2);
$vartotalt=number_format($vartotal+$vartotalC,2);


$varsubtotal=number_format($varsubtotal,2);
$varimpuesto=number_format($varimpuesto,2);
$vardescuento=number_format($vardescuento,2);
$vartotal=number_format($vartotal,2);
$varsubtotalC=number_format($varsubtotalC,2);
$varimpuestoC=number_format($varimpuestoC,2);
$vardescuentoC=number_format($vardescuentoC,2);
$vartotalC=number_format($vartotalC,2);

$bloque4 = <<<EOD
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





EOD;
$pdf-> writeHTML($bloque4,false,false,true,0,'');

$pdf->Output('Reporte_mensual_Facturas.pdf');


 ?>