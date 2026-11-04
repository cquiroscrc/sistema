<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");


$fecha = date("d-m-Y H:i:s");



$sql="SELECT i.CODIGO,i.DESCRIPCION,i.EXISTENCIA,
substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1)
WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
Else i.CODIGO END As Filtro FROM inventario i WHERE NOT EXISTS 
(SELECT c.Codigo FROM woocommerce c WHERE c.Codigo = i.CODIGO) ORDER BY Filtro";

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Los Paticas -- Reporte Woocommerce', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Reporte Woocommerce -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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
$pdf->Write(0, 'Reportes Woocommerce al '.$fecha, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

  <tr style="background-color:#FF0000;color:white;">
       <th style="width:115px">Código</th>
       <th style="width:350px">Descripción</th>
       <th style="width:45px">Cantidad</th>
       <th style="width:50px">Tamaño</th>
  </tr>

</table>


EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$i=0;
$color='black';
$cabyanterior='';
foreach ($ejecutar as $key => $arr) {
$codigos=$arr["CODIGO"];
$descripcion=$arr["DESCRIPCION"];
$cantidad=$arr["EXISTENCIA"];

    $path='../../../../subida_cat/';

    if (substr($codigos,0,1)==1) {
        $directorio='1/';
    }
    if (substr($codigos,0,1)==2) {
        $directorio='2/';
    }
    if (substr($codigos,0,1)==3) {
        $directorio='3/';
    }
    if (substr($codigos,0,1)==4) {
        $directorio='4/';
    }
    if (substr($codigos,0,1)==9) {
        $directorio='9/';
    }
    if (substr($codigos,0,1)==0) {
        $directorio='0/';
    }
    $codigos1=rtrim($codigos);
    if (substr($codigos,-2)=='EU') {
        $codigos1=substr($codigos,0,-2);
        $codigos1=rtrim($codigos1);

    }
    if (substr($codigos,-2)=='GR') {
        $codigos1=substr($codigos,0,-2);
        $codigos1=rtrim($codigos1);
    }
    if (substr($codigos,-2)=='TW') {
        $codigos1=substr($codigos,0,-2);
        $codigos1=rtrim($codigos1);

    }
    if (substr($codigos,-2)=='JP') {
        $codigos1=substr($codigos,0,-2);
        $codigos1=rtrim($codigos1);
    }
    if (substr($codigos,-2)=='ZL') {
        $codigos1=substr($codigos,0,-2);
        $codigos1=rtrim($codigos1);
    }

    $foto=substr ( $codigos1 , 1 );
    $foto=rtrim($foto);

    if (substr($foto,0,1)==' ') {
        //echo 'FOTOS';
        $foto=substr($foto, 2, strlen($foto));
    }

    $filename = $path.$directorio.$foto.".jpg";
    $archivo=$filename;

    $existes = $_SERVER["DOCUMENT_ROOT"].'/subida_cat/'.$directorio.$foto.'.jpg';

    if (is_file($existes)) {
        $archivo=$filename;
    } else {
        $archivo=$path."inf.png";
    }
    list($width, $height, $type, $attr) = getimagesize($archivo);
$pdf->SetFont('helvetica', '', 10);
    if ($width==500 && $height==500) {

    }else {

        $bloque3 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:115px">$codigos</td>
    <td style="width:350px">$descripcion</td>
    <td style="width:45px">$cantidad</td>
    <td style="width:50px">$width.X.$height</td>
   
</tr>
</tbody>  
</table>


EOD;

$pdf-> writeHTML($bloque3,false,false,true,0,'');
    }
$i=$i+1;


}
$pdf->Output('Reporte_woocommerce.pdf');

