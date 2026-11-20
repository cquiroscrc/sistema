<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");




 $sql="SELECT * FROM tmpCxP order by proveedor,nfac,fechaFact";  

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Los Paticas -- Facturas a pagar', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Facturas a pagar -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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
$pdf->Write(0, 'Facturas a pagar', '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:115px">Fecha</th>
       <th style="width:75px">Factura</th>
       <th style="width:55px">Proveedor</th>
       <th style="width:70px">Total</th>
       <th style="width:70px">Cheque</th>
       <th style="width:100px">Firma</th>
	</tr>

</table>


EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$saldo_parcial=0;
$saldo=0;
$proveedor='';
foreach ($ejecutar as $key => $arr) {
$saldo_parcial2=number_format($saldo_parcial,2);
$saldo2=number_format($saldo,2);

      if ($proveedor=='') {
        $proveedor=$arr['proveedor'];
      }
      $sql_pro="SELECT nombre FROM Proveedores WHERE codigo='$proveedor'";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pro);
        foreach ($ejecutar as $key => $value) {
          $nombre=$value['nombre'];
        }

if ($proveedor!=$arr['proveedor']) {
// Parcial Adentro
$bloque4 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:115px">Total Proveedor</th> 
  <th style="width:75px"></th>
  <th style="width:55px">$proveedor</th>
  <th style="width:70px"><b>$saldo_parcial2</b></th>
  <th style="width:170px">$nombre</th>
  
</tr>

</table>
EOD;
$saldo_parcial=0;
$proveedor=$arr['proveedor'];   
$pdf->SetFont('helvetica', 'B', 10);
$pdf-> writeHTML($bloque4,false,false,true,0,'');
}
      

      $fecha=$arr["fechaFact"];
      $factura= $arr["nfac"];
      $proveedor=$arr['proveedor'];
      $total=$arr["total"];
      $saldo_parcial=$saldo_parcial+$total;
      $saldo=$saldo+$total;
      $total=number_format($arr["total"],2);     
      $cheque=$arr['chkAsociado'];
// Facturas
$bloque3 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:115px">$fecha</td>
    <td style="width:75px">$factura</td>
    <td style="width:55px">$proveedor</td>
    <td style="width:70px">$total</td>
    <td style="width:70px">$cheque</td>
    
</tr>
</tbody>  
</table>
EOD;
$pdf->SetFont('helvetica', 'B', 9);
$pdf-> writeHTML($bloque3,false,false,true,0,''); 
}
// Parcial Afuera
$saldo_parcial2=number_format($saldo_parcial,2);
$saldo2=number_format($saldo,2);
$sql_pro="SELECT nombre FROM Proveedores WHERE codigo='$proveedor'";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pro);
        foreach ($ejecutar as $key => $value) {
          $nombre=$value['nombre'];
        }
$bloque5 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:115px">Total Proveedor</th> 
  <th style="width:75px"></th>
  <th style="width:55px">$proveedor</th>
  <th style="width:70px"><b>$saldo_parcial2</b></th>
  <th style="width:170px">$nombre</th>
  
</tr>

</table>
EOD;
$pdf->SetFont('helvetica', 'B', 10);
$pdf-> writeHTML($bloque5,false,false,true,0,'');
// Totales
$bloque6 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
  
<tr style="background-color:#FF0000;color:white;">
  <th style="width:115px">Total</th> 
  <th style="width:75px"></th>
  <th style="width:55px"></th>
  <th style="width:70px"><b>$saldo2</b></th>
  <th style="width:70px"></th>
  <th style="width:100px"></th>
</tr>

</table>
EOD;
$pdf-> writeHTML($bloque6,false,false,true,0,'');

$pdf->Output('pagos.pdf');


 ?>