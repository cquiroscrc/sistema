<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");



if(isset($_REQUEST['fini']) && !empty($_REQUEST['fini'])){
      $fecha = $_REQUEST['fini'];
}
if(isset($_REQUEST['ffin']) && !empty($_REQUEST['ffin'])){
    $fechafin = $_REQUEST['ffin'];
}



$sql="SELECT a.Codigo,i.Descripcion, a.Factura,a.fecha,a.cantidad,a.movimiento,a.precio,a.tipo,t.IDMovimiento,t.Estado,t.Comentario FROM 
hgeneral a, ajutras t, inventario i WHERE a.fecha BETWEEN '".$fecha." 00:00:00.000' AND '".$fechafin." 23:59:59.000' AND a.Factura =  t.IDMovimiento 
AND a.Codigo = i.CODIGO AND t.Comentario like '%MAL VENDIDO%' AND a.tipo= '1' order by a.fecha";

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Los Paticas -- Ajustes x Mal Vendido ', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Ajustes x Mal Vendido -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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

$pdf->SetFont('helvetica', 'B', 15);
$pdf->Write(0, 'Ajustes x Mal Vendido Entrada entre '.$fecha.' y '.$fechafin, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:115px">Código</th>
       <th style="width:150px">Descripción</th>
       <th style="width:45px">Cantidad</th>
       <th style="width:55px">Movimiento</th>
       <th style="width:48px">Ajuste #</th>
       <th style="width:48px">Fecha</th>
       <th style="width:55px">Costo</th>
       <th style="width:55px">Total</th>
	</tr>

</table>


EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$i=0;
$totalfinal=0;
$totalfinalE=0;
foreach ($ejecutar as $key => $arr) {
$codigos=$arr["Codigo"];
$descripcion=$arr["Descripcion"];
$cantidad=$arr["cantidad"];
$movimiento=$arr["tipo"];
    if ($movimiento==2) {
        $movimiento='Salida';
    }else{
        $movimiento='Entrada';
    }
$ajuste=$arr["Factura"];
$fechaajuste=$arr["fecha"];
$precio=$arr["precio"];
$total=$precio*$cantidad;
$totalfinalE=$totalfinalE+$total;
$precio=number_format($precio,2);
$total=number_format($total,2);

$pdf->SetFont('helvetica', '', 10);
$bloque3 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:115px">$codigos</td>
    <td style="width:150px">$descripcion</td>
    <td style="width:45px">$cantidad</td>
    <td style="width:55px">$movimiento</td>
    <td style="width:48px">$ajuste</td>
    <td style="width:48px">$fecha</td>
    <td style="width:55px">$precio</td>
    <td style="width:55px">$total</td>
</tr>
</tbody>  
</table>


EOD;

$pdf-> writeHTML($bloque3,false,false,true,0,'');
$i=$i+1;



}
//********Salida

$sql="SELECT a.Codigo,i.Descripcion, a.Factura,a.fecha,a.cantidad,a.movimiento,a.precio,a.tipo,t.IDMovimiento,t.Estado,t.Comentario FROM 
hgeneral a, ajutras t, inventario i WHERE a.fecha BETWEEN '".$fecha." 00:00:00.000' AND '".$fechafin." 23:59:59.000' AND a.Factura =  t.IDMovimiento 
AND a.Codigo = i.CODIGO AND t.Comentario like '%MAL VENDIDO%' AND a.tipo= '2' order by a.fecha";

$pdf-> AddPage();

$pdf->SetFont('helvetica', 'B', 15);
$pdf->Write(0, 'Ajustes x Mal Vendido Salida entre '.$fecha.' y '.$fechafin, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque4 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:115px">Código</th>
       <th style="width:150px">Descripción</th>
       <th style="width:45px">Cantidad</th>
       <th style="width:55px">Movimiento</th>
       <th style="width:48px">Ajuste #</th>
       <th style="width:48px">Fecha</th>
       <th style="width:55px">Costo</th>
       <th style="width:55px">Total</th>
	</tr>

</table>


EOD;
$pdf-> writeHTML($bloque4,false,false,false,false,'');

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$i=0;
$totalfinalS=0;
foreach ($ejecutar as $key => $arr) {
    $codigos=$arr["Codigo"];
    $descripcion=$arr["Descripcion"];
    $cantidad=$arr["cantidad"];
    $movimiento=$arr["tipo"];
    if ($movimiento==2) {
        $movimiento='Salida';
    }else{
        $movimiento='Entrada';
    }
    $ajuste=$arr["Factura"];
    $fechaajuste=$arr["fecha"];
    $precio=$arr["precio"];
    $total=$precio*$cantidad;
    $totalfinalS=$totalfinalS+$total;
    $precio=number_format($precio,2);
    $total=number_format($total,2);

    $pdf->SetFont('helvetica', '', 10);
    $bloque5 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:115px">$codigos</td>
    <td style="width:150px">$descripcion</td>
    <td style="width:45px">$cantidad</td>
    <td style="width:55px">$movimiento</td>
    <td style="width:48px">$ajuste</td>
    <td style="width:48px">$fecha</td>
    <td style="width:55px">$precio</td>
    <td style="width:55px">$total</td>
</tr>
</tbody>  
</table>


EOD;

    $pdf-> writeHTML($bloque5,false,false,true,0,'');
    $i=$i+1;



}
$pdf->SetFont('helvetica', 'B', 15);
$totalfinal=$totalfinalE-$totalfinalS;
$totalfinalE=number_format($totalfinalE,2);
$totalfinalS=number_format($totalfinalS,2);
$totalfinal=number_format($totalfinal,2);


$pdf->Write(0, 'Total Entradas: '.$totalfinalE, '', 0, 'L', true, false, false, false, 0);
$pdf->Write(0, 'Total Salidas: '.$totalfinalS, '', 0, 'L', true, false, false, false, 0);
$pdf->Write(0, 'Diferencia: '.$totalfinal, '', 0, 'L', true, false, false, false, 0);

$pdf->Output('vendido.pdf');


 ?>