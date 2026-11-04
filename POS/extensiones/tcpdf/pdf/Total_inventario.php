<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");



//Total Inventario suma
$sql_ti="select sum(costo*existencia) as total from inventario where ubicacion NOT LIKE '9%'" ;

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_ti);
foreach ($ejecutar as $key => $arr) {
    $ti=$arr["total"];
}
//Total conteo items de piezas
$sql_tti="select count(codigo) as total from inventario" ;

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tti);
foreach ($ejecutar as $key => $arr) {
    $tti=$arr["total"];
}
// Total Conteo existencia de piezas
$sql_tic="select sum(existencia) as total from inventario" ;

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tic);
foreach ($ejecutar as $key => $arr) {
    $tic=$arr["total"];
}
// Total inventario Importado
$sql_tii="select sum(costo*existencia) as total from inventario where 
    (ubicacion NOT LIKE '9%' )AND (codigo like '%GR' or codigo like '%EU' or codigo like '%JP' or codigo like '%TW' or codigo like '%ZL')" ;

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tii);
foreach ($ejecutar as $key => $arr_tii) {
    $tii=$arr_tii["total"];
    $til=$ti-$tii;
}

// Total inventario pegas
$sql_tp="select sum(costo*existencia) as total from inventario where 
    ubicacion LIKE '9%' OR ubicacion ='CASITA'" ;

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tp);
foreach ($ejecutar as $key => $arr_tp) {
    $tp=$arr_tp["total"];
}

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Reporte de Inventario', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Reporte Inventario -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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
$pdf->Write(0, 'Reporte de Inventario', '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);
$ti=number_format($ti,2);
$tii=number_format($tii,2);
$til=number_format($til,2);
$tp=number_format($tp,2);
$tic=number_format($tic,2);
$tti=number_format($tti,2);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:70px">Marca</th>
       <th style="width:90px">Total</th>
       <th style="width:80px">Importado</th>
       <th style="width:75px">Local</th>
       <th style="width:75px">Pegas</th>
       <th style="width:65px">Existencias</th>
       <th style="width:75px">Items</th>
	</tr>

</table>
   
EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$bloque3 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
       <td style="width:70px">Total</td>
       <td style="width:90px">$ti</td>
       <td style="width:80px">$tii</td>
       <td style="width:75px;text-align:right;">$til</td>
       <td style="width:75px;text-align:right;">$tp</td>
       <td style="width:65px;text-align:right;">$tic</td>
       <td style="width:75px;text-align:right;">$tti</td>
	</tr>
</tbody>
</table>
EOD;
$pdf-> writeHTML($bloque3,false,false,true,0,'');

$sql_marcas = "SELECT * FROM Marcas";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_marcas);

foreach ($ejecutar as $key => $arr_marcas) {
    $marca=$arr_marcas['codigo'];
    $descripcion=$arr_marcas['descripcion'];
    $numero=$arr_marcas['numero'];


    $sql_m="select sum(costo*existencia) as total from inventario where MARCA ='".$marca."' and ubicacion NOT LIKE '9%'" ;
    $total=0;

    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_m);
    foreach ($ejecutar as $key => $arr_m) {
        $total=$arr_m["total"];
    }

    $sql_ti="select sum(costo*existencia) as total from inventario where (ubicacion NOT LIKE '9%' ) AND MARCA = '".$marca."' AND (codigo like '%GR' 
                or codigo like '%EU' or codigo like '%JP' or codigo like '%TW' or codigo like '%ZL')" ;
    $total_i=0;
    $total_l=0;

    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_ti);
    foreach ($ejecutar as $key => $arr_ti) {
        $total_i=$arr_ti["total"];
        $total_l=$total-$total_i;
    }

    $sql_e="select sum(existencia) as total from inventario where MARCA = '".$marca."'" ;
    $existencia=0;

    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_e);
    foreach ($ejecutar as $key => $arr_e) {
        $existencia=$arr_e["total"];
    }

    $sql_c="select count(codigo) as total from inventario where MARCA = '".$marca."'" ;
    $cantidad=0;

    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_c);
    foreach ($ejecutar as $key => $arr_c) {
        $cantidad=$arr_c["total"];
    }

    $sql_pegas="select sum(costo*existencia) as total from inventario where 
    MARCA = '".$marca."' and ubicacion LIKE '9%' or MARCA='".$marca."' and ubicacion ='CASITA'" ;
    $pegas=0;

    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pegas);
    foreach ($ejecutar as $key => $arr_pegas) {
        $pegas=$arr_pegas["total"];
    }


    $pdf->SetFont('helvetica', '', 10);

    $total=number_format($total,2);
    $total_i=number_format($total_i,2);
    $total_l=number_format($total_l,2);
    $pegas=number_format($pegas,2);
    $existencia=number_format($existencia,2);
    $cantidad=number_format($cantidad,2);


$bloque4 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tbody> 

<tr>
    <td style="width:70px">$descripcion</td>
    <td style="width:90px">$total</td>
    <td style="width:80px">$total_i</td>
    <td style="width:75px;text-align:right;">$total_l</td>
    <td style="width:75px;text-align:right;">$pegas</td>
    <td style="width:65px;text-align:right;">$existencia</td>
    <td style="width:75px;text-align:right;">$cantidad</td>
</tr>
</tbody>  
</table>


EOD;

$pdf-> writeHTML($bloque4,false,false,true,0,'');

}
$pdf->Output('Reporte_Inventario.pdf');


 ?>