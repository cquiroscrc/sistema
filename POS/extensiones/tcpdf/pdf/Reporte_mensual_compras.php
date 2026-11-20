<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['fini']) && !empty($_REQUEST['fini'])){
    $fini = $_REQUEST['fini'];
    $mostrarini = date("d/m/Y", strtotime($fini));
}
if(isset($_REQUEST['ffin']) && !empty($_REQUEST['ffin'])){
    $ffin = $_REQUEST['ffin'];
    $mostrarfin = date("d/m/Y", strtotime($ffin));
}

$sql="SELECT c.Factura,c.proveedor,c.fecha,c.tipo,c.subtotal,c.impuesto,c.descuento,c.total, p.nombre, p.tipo FROM 
                    compras c, Proveedores p WHERE fecha BETWEEN '$fini 00:00:00.000' AND '$ffin 23:59:59.000'  
                    AND c.proveedor =  p.codigo AND p.tipo='L' order by proveedor,fecha";

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Los Paticas -- Reporte de Compras', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Reporte de compras -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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
$pdf->Write(0, 'Reporte de compras Locales del '.$fini.' al '.$ffin, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:70px">Fecha</th>
       <th style="width:60px">Factura</th>
       <th style="width:60px">Subtotal</th>
       <th style="width:60px">Impuesto</th>
       <th style="width:50px">Descuento</th>
       <th style="width:75px">Total</th>
       <th style="width:40px">Tipo</th>
       <th style="width:40px">Proveedor</th>
       <th style="width:95px">Nombre</th>
	</tr>

</table>


EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$parcial_subtotal=0;
$parcial_impuesto=0;
$parcial_descuento=0;
$parcial_total=0;
$varproveeedor='';
$varnombre='';
$varsubtotal =0;
$varimpuesto =0;
$vardescuento =0;
$vartotal =0;

foreach ($ejecutar as $key => $arr) {

    if ($varproveeedor=='') {
        $varproveeedor=$arr['proveedor'];
        $varnombre=$arr['nombre'];
    }

    if ($varproveeedor!=$arr['proveedor']) {
// Parcial Adentro
$parcial_subtotal=number_format($parcial_subtotal,2);
$parcial_impuesto=number_format($parcial_impuesto,2);
$parcial_descuento=number_format($parcial_descuento,2);
$parcial_total=number_format($parcial_total,2);
$bloque4 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:130px">Total Proveedor</th> 
  <th style="width:60px"><b>$parcial_subtotal</b></th>
  <th style="width:60px"><b>$parcial_impuesto</b></th>
  <th style="width:50px"><b>$parcial_descuento</b></th>
  <th style="width:75px"><b>$parcial_total</b></th>
  <th style="width:40px">$varproveeedor</th>
  <th style="width:135px">$varnombre</th> 
</tr>
</table>
EOD;
$varproveeedor=$arr['proveedor'];
$varnombre=$arr['nombre'];
$parcial_subtotal=0;
$parcial_impuesto=0;
$parcial_descuento=0;
$parcial_total=0;
$pdf->SetFont('helvetica', 'B', 10);
$pdf-> writeHTML($bloque4,false,false,true,0,'');
}
    $fecha_sis=$arr['fecha'];
    $fecha_sis = date("d/m/Y", strtotime($fecha_sis));
    $parcial_subtotal=$parcial_subtotal+ $arr['subtotal'];
    $varsubtotal = $varsubtotal + $arr["subtotal"];
    $parcial_impuesto=$parcial_impuesto+$arr['impuesto'];
    $varimpuesto = $varimpuesto + $arr["impuesto"];
    $parcial_descuento=$parcial_descuento+$arr['descuento'];
    $vardescuento = $vardescuento + $arr["descuento"];
    $parcial_total=$parcial_total+$arr['total'];
    $vartotal= $vartotal + $arr["total"];

// Facturas
    $arrfactura=$arr["Factura"];
    $arrsubtotal= number_format($arr["subtotal"],2);
    $arrimpuesto= number_format($arr["impuesto"],2);
    $arrdescuento= number_format($arr["descuento"],2);
    $arrtotal= number_format($arr["total"],2);
    $arrtipo=$arr["tipo"];
    $arrproveedor=$arr["proveedor"];
    $arrnombre=$arr["nombre"];
$bloque3 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:70px">$fecha_sis</td>
    <td style="width:60px">$arrfactura</td>
    <td style="width:60px">$arrsubtotal</td>
    <td style="width:60px">$arrimpuesto</td>
    <td style="width:50px">$arrdescuento</td>
    <td style="width:75px">$arrtotal</td>
    <td style="width:40px">$arrtipo</td>
    <td style="width:40px">$arrproveedor</td>
    <td style="width:95px">$arrnombre</td>
</tr>
</tbody>  
</table>
EOD;
$pdf->SetFont('helvetica', 'B', 9);
$pdf-> writeHTML($bloque3,false,false,true,0,''); 
}
// Parcial Afuera
$parcial_subtotal=number_format($parcial_subtotal,2);
$parcial_impuesto=number_format($parcial_impuesto,2);
$parcial_descuento=number_format($parcial_descuento,2);
$parcial_total=number_format($parcial_total,2);

$bloque5 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:130px">Total Proveedor</th> 
  <th style="width:60px"><b>$parcial_subtotal</b></th>
  <th style="width:60px"><b>$parcial_impuesto</b></th>
  <th style="width:50px"><b>$parcial_descuento</b></th>
  <th style="width:75px"><b>$parcial_total</b></th>
  <th style="width:40px">$varproveeedor</th>
  <th style="width:135px">$varnombre</th>  
</tr>

</table>
EOD;
$pdf->SetFont('helvetica', 'B', 10);
$pdf-> writeHTML($bloque5,false,false,true,0,'');
// Totales

$fechas=$fini.' - '.$ffin;
$varsubtotal=number_format($varsubtotal,2);
$varimpuesto=number_format($varimpuesto,2);
$vardescuento =number_format($vardescuento,2);
$vartotal=number_format($vartotal,2);
$bloque6 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:130px">Total Reporte</th> 
  <th style="width:60px">$varsubtotal</th>
  <th style="width:60px">$varimpuesto</th>
  <th style="width:50px">$vardescuento</th>
  <th style="width:75px">$vartotal</th>
  <th style="width:175px">$fechas</th>
</tr>

</table>
EOD;
$pdf-> writeHTML($bloque6,false,false,true,0,'');
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Write(0, 'Reporte de compras Exteriores del '.$fini.' al '.$ffin, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$sql="SELECT c.Factura,c.proveedor,c.fecha,c.tipo,c.subtotal,c.impuesto,c.descuento,c.total, p.nombre, p.tipo FROM 
                    compras c, Proveedores p WHERE fecha BETWEEN '$fini 00:00:00.000' AND '$ffin 23:59:59.000'  
                    AND c.proveedor =  p.codigo AND p.tipo='E' order by proveedor,fecha";

$bloque7 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:70px">Fecha</th>
       <th style="width:60px">Factura</th>
       <th style="width:60px">Subtotal</th>
       <th style="width:60px">Impuesto</th>
       <th style="width:50px">Descuento</th>
       <th style="width:75px">Total</th>
       <th style="width:40px">Tipo</th>
       <th style="width:40px">Proveedor</th>
       <th style="width:95px">Nombre</th>
	</tr>

</table>

EOD;
$pdf-> writeHTML($bloque7,false,false,true,0,'');

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$parcial_subtotal=0;
$parcial_impuesto=0;
$parcial_descuento=0;
$parcial_total=0;
$varproveeedor='';
$varnombre='';
$varsubtotal =0;
$varimpuesto =0;
$vardescuento =0;
$vartotal =0;

foreach ($ejecutar as $key => $arr) {

    if ($varproveeedor=='') {
        $varproveeedor=$arr['proveedor'];
        $varnombre=$arr['nombre'];
    }

    if ($varproveeedor!=$arr['proveedor']) {
// Parcial Adentro
        $parcial_subtotal=number_format($parcial_subtotal,2);
        $parcial_impuesto=number_format($parcial_impuesto,2);
        $parcial_descuento=number_format($parcial_descuento,2);
        $parcial_total=number_format($parcial_total,2);

        $bloque8 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:130px">Total Proveedor</th> 
  <th style="width:60px"><b>$parcial_subtotal</b></th>
  <th style="width:60px"><b>$parcial_impuesto</b></th>
  <th style="width:50px"><b>$parcial_descuento</b></th>
  <th style="width:75px"><b>$parcial_total</b></th>
  <th style="width:40px">$varproveeedor</th>
  <th style="width:135px">$varnombre</th> 
</tr>
</table>
EOD;
        $varproveeedor=$arr['proveedor'];
        $varnombre=$arr['nombre'];
        $parcial_subtotal=0;
        $parcial_impuesto=0;
        $parcial_descuento=0;
        $parcial_total=0;
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf-> writeHTML($bloque8,false,false,true,0,'');
    }
    $fecha_sis=$arr['fecha'];
    $fecha_sis = date("d/m/Y", strtotime($fecha_sis));
    $parcial_subtotal=$parcial_subtotal+ $arr['subtotal'];
    $varsubtotal = $varsubtotal + $arr["subtotal"];
    $parcial_impuesto=$parcial_impuesto+$arr['impuesto'];
    $varimpuesto = $varimpuesto + $arr["impuesto"];
    $parcial_descuento=$parcial_descuento+$arr['descuento'];
    $vardescuento = $vardescuento + $arr["descuento"];
    $parcial_total=$parcial_total+$arr['total'];
    $vartotal= $vartotal + $arr["total"];

// Facturas
    $arrfactura=$arr["Factura"];
    $arrsubtotal= number_format($arr["subtotal"],2);
    $arrimpuesto= number_format($arr["impuesto"],2);
    $arrdescuento= number_format($arr["descuento"],2);
    $arrtotal= number_format($arr["total"],2);
    $arrtipo=$arr["tipo"];
    $arrproveedor=$arr["proveedor"];
    $arrnombre=$arr["nombre"];
    $bloque9 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:70px">$fecha_sis</td>
    <td style="width:60px">$arrfactura</td>
    <td style="width:60px">$arrsubtotal</td>
    <td style="width:60px">$arrimpuesto</td>
    <td style="width:50px">$arrdescuento</td>
    <td style="width:75px">$arrtotal</td>
    <td style="width:40px">$arrtipo</td>
    <td style="width:40px">$arrproveedor</td>
    <td style="width:95px">$arrnombre</td>
</tr>
</tbody>  
</table>
EOD;
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf-> writeHTML($bloque9,false,false,true,0,'');
}
// Parcial Afuera
$parcial_subtotal=number_format($parcial_subtotal,2);
$parcial_impuesto=number_format($parcial_impuesto,2);
$parcial_descuento=number_format($parcial_descuento,2);
$parcial_total=number_format($parcial_total,2);

$bloque10 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:130px">Total Proveedor</th> 
  <th style="width:60px"><b>$parcial_subtotal</b></th>
  <th style="width:60px"><b>$parcial_impuesto</b></th>
  <th style="width:50px"><b>$parcial_descuento</b></th>
  <th style="width:75px"><b>$parcial_total</b></th>
  <th style="width:40px">$varproveeedor</th>
  <th style="width:135px">$varnombre</th>  
</tr>

</table>
EOD;
$pdf->SetFont('helvetica', 'B', 10);
$pdf-> writeHTML($bloque10,false,false,true,0,'');
// Totales

$fechas=$fini.' - '.$ffin;
$varsubtotal=number_format($varsubtotal,2);
$varimpuesto=number_format($varimpuesto,2);
$vardescuento =number_format($vardescuento,2);
$vartotal=number_format($vartotal,2);
$bloque11 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:130px">Total Reporte</th> 
  <th style="width:60px">$varsubtotal</th>
  <th style="width:60px">$varimpuesto</th>
  <th style="width:50px">$vardescuento</th>
  <th style="width:75px">$vartotal</th>
  <th style="width:175px">$fechas</th>
</tr>

</table>
EOD;
$pdf-> writeHTML($bloque11,false,false,true,0,'');

$pdf->Output('Reporte_mensual_compras.pdf');
 ?>