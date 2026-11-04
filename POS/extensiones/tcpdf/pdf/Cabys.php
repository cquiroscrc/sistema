<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");


$fecha = date("d-m-Y H:i:s"); 

if(isset($_REQUEST['grupo']) && !empty($_REQUEST['grupo'])){
      $grupo = $_REQUEST['grupo'];
}

$sql="SELECT i.CODIGO,i.DESCRIPCION,i.EXISTENCIA,i.CODIGO_CABYS,c.Descripcion,
substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1)
WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
Else i.CODIGO END As Filtro FROM inventario i, DE_CABYS c  WHERE i.CODIGO_CABYS = c.Codigo ORDER BY i.CODIGO_CABYS,Filtro";

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Los Paticas -- Reporte Cabys', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Reporte Cabys -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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
$pdf->Write(0, 'Reportes Cabys al '.$fecha, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

  <tr style="background-color:#FF0000;color:white;">
       <th style="width:115px">Código</th>
       <th style="width:170px">Descripción</th>
       <th style="width:45px">Cantidad</th>
       <th style="width:75px">Cabys</th>
       <th style="width:150px">Descripción Cabys</th>
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
$cabys=$arr["CODIGO_CABYS"];
$descripcioncabys=$arr["Descripcion"];
if ($cabys!=$cabyanterior){
    $color='red';
    $cabyanterior=$cabys;
}else{
    $color='black';
}
$pdf->SetFont('helvetica', '', 10);

$bloque3 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:115px">$codigos</td>
    <td style="width:170px">$descripcion</td>
    <td style="width:45px">$cantidad</td>
    <td style="width:75px;color:$color">$cabys</td>
    <td style="width:150px">$descripcioncabys</td>
</tr>
</tbody>  
</table>


EOD;

$pdf-> writeHTML($bloque3,false,false,true,0,'');
$i=$i+1;


}
$pdf->Output('Reporte_Cabys.pdf');

