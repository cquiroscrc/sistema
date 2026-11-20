<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['planilla']) && !empty($_REQUEST['planilla'])){
    $planilla = $_REQUEST['planilla'];
}
if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
    $fecha = $_REQUEST['fecha'];
    $mostrar = date("d/m/Y", strtotime($fecha));
}



class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Reporte de Planilla', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Reporte Planilla -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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
$pdf-> AddPage('L');

$pdf->SetFont('helvetica', 'B', 20);
$pdf->Write(0, 'Reporte de Planilla '.$planilla.' del '.$mostrar, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:20px">ID</th>
       <th style="width:90px">Nombre</th>
       <th style="width:65px">Salario</th>
       <th style="width:60px">Reajuste</th>
       <th style="width:55px">Extras</th>
       <th style="width:60px">Comision</th>
       <th style="width:60px">Mandados</th>
       <th style="width:35px">O Ingr</th>
       <th style="width:60px">CCSS</th>
       <th style="width:60px">Retenciones</th>
       <th style="width:35px">Vales</th>
       <th style="width:42px">Compras</th>
       <th style="width:35px">O Egr</th>
       <th style="width:70px">Total</th>
	</tr>

</table>
   
EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$sql_planilla = "SELECT p.id_planilla,p.fecha,p.id_empleado,p.base,p.reajuste,p.extras,p.comision,p.mandados,p.otros_ingresos,p.ccss,p.retencion,p.vales,p.compras,p.otros_egresos,p.total, e.nombre, e.apellido
 FROM rh_planilla p, rh_empleados e WHERE p.id_empleado=e.id_empleado AND p.id_planilla ='".$planilla."' AND p.fecha = '".$fecha."' ORDER BY p.id_empleado";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_planilla);

$tbase=0;$treajuste=0;$textras=0;$tcomision=0;$tmandados=0;$toingresos=0;
$tccss=0;$tretencion=0;$tvales=0;$tcompras=0;$toegresos=0;$ttotal=0;

foreach ($ejecutar as $key => $arr) {
    $id=$arr["id_empleado"];
    $nombre=$arr["nombre"]." ".$arr["apellido"];
    $base=$arr["base"];
    $tbase=$tbase+$base;
    $reajuste=$arr["reajuste"];
    $treajuste=$treajuste+$reajuste;
    $extras=$arr["extras"];
    $textras=$textras+$extras;
    $comision=$arr["comision"];
    $tcomision=$tcomision+$comision;
    $mandados=$arr["mandados"];
    $tmandados=$tmandados+$mandados;
    $oingresos=$arr["otros_ingresos"];
    $toingresos=$toingresos+$oingresos;
    $ccss=$arr["ccss"];
    $tccss=$tccss+$ccss;
    $retencion=$arr["retencion"];
    $tretencion=$tretencion+$retencion;
    $vales=$arr["vales"];
    $tvales=$tvales+$vales;
    $compras=$arr["compras"];
    $tcompras=$tcompras+$compras;
    $oegresos=$arr["otros_egresos"];
    $toegresos=$toegresos+$oegresos;
    $total=$arr["total"];
    $ttotal=$ttotal+$total;

    $base=number_format($base,2);
    $reajuste=number_format($reajuste,2);
    $extras=number_format($extras,2);
    $comision=number_format($comision,2);
    $mandados=number_format($mandados,2);
    $oingresos=number_format($oingresos,2);
    $ccss=number_format($ccss,2);
    $retencion=number_format($retencion,2);
    $vales=number_format($vales,2);
    $compras=number_format($compras,2);
    $oegresos=number_format($oegresos,2);
    $total=number_format($total,2);

    $pdf->SetFont('helvetica', '', 10);

$bloque3 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tr>
    <td style="width:20px;text-align:right;">$id</td>
    <td style="width:90px">$nombre</td>
    <td style="width:65px;text-align:right;">$base</td>
    <td style="width:60px;text-align:right;">$reajuste</td>
    <td style="width:55px;text-align:right;">$extras</td>
    <td style="width:60px;text-align:right;">$comision</td>
    <td style="width:60px;text-align:right;">$mandados</td>
    <td style="width:35px;text-align:right;">$oingresos</td>
    <td style="width:60px;text-align:right;">$ccss</td>
    <td style="width:60px;text-align:right;">$retencion</td>
    <td style="width:35px;text-align:right;">$vales</td>
    <td style="width:42px;text-align:right;">$compras</td>
    <td style="width:35px;text-align:right;">$oegresos</td>
    <td style="width:70px;text-align:right;">$total</td>
</tr>
</table>
EOD;
$pdf-> writeHTML($bloque3,false,false,true,0,'');
}

$tbase=number_format($tbase,2);
$treajuste=number_format($treajuste,2);
$textras=number_format($textras,2);
$tcomision=number_format($tcomision,2);
$tmandados=number_format($tmandados,2);
$toingresos=number_format($toingresos,2);
$tccss=number_format($tccss,2);
$tretencion=number_format($tretencion,2);
$tvales=number_format($tvales,2);
$tcompras=number_format($tcompras,2);
$toegresos=number_format($toegresos,2);
$ttotal=number_format($ttotal,2);

$bloque4 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tbody> 

<tr style="background-color:#FF0000;color:white;">
    <th style="width:20px"></th>
    <th style="width:90px"><b>Totales</b></th>
    <th style="width:65px;text-align:right;"><b>$tbase</b></th>
    <th style="width:60px;text-align:right;"><b>$treajuste</b></th>
    <th style="width:55px;text-align:right;"><b>$textras</b></th>
    <th style="width:60px;text-align:right;"><b>$tcomision</b></th>
    <th style="width:60px;text-align:right;"><b>$tmandados</b></th>
    <th style="width:35px;text-align:right;"><b>$toingresos</b></th>
    <th style="width:60px;text-align:right;"><b>$tccss</b></th>
    <th style="width:60px;text-align:right;"><b>$tretencion</b></th>
    <th style="width:35px;text-align:right;"><b>$tvales</b></th>
    <th style="width:42px;text-align:right;"><b>$tcompras</b></th>
    <th style="width:35px;text-align:right;"><b>$toegresos</b></th>
    <th style="width:70px;text-align:right;"><b>$ttotal</b></th>
</tr>
</tbody>  
</table>

EOD;
$pdf-> writeHTML($bloque4,false,false,true,0,'');

$pdf->AddPage('P');
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Write(0, 'Reporte de Planilla '.$planilla.' del '.$mostrar, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque5 = <<<EOD

<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:25px">ID</th>
       <th style="width:150px">Nombre</th>
       <th style="width:100px">Cuenta</th>
       <th style="width:75px">Salario</th>
       <th style="width:75px">Reajustes</th>
       <th style="width:100px">Total</th>
	</tr>

</table>

EOD;
$pdf-> writeHTML($bloque5,false,false,true,0,'');
$total_salario=0;
$total_reajustes=0;
$total_extras=0;
$total_vales=0;
$total_compras=0;
$total_comision=0;
$total_mandados=0;
$total_egresos=0;
$total_ingresos=0;
$total_ccss=0;
$total_total=0;
$total_sal_ccss=0;
$total_sal_reajustes=0;

$sql_planilla = "SELECT p.id_planilla,p.fecha,p.id_empleado,p.base,p.reajuste,p.extras,p.comision,p.mandados,p.otros_ingresos,p.ccss,p.retencion,p.vales,p.compras,p.otros_egresos,p.total, e.nombre, e.apellido, e.cuenta
 FROM rh_planilla p, rh_empleados e WHERE p.id_empleado=e.id_empleado AND p.id_planilla ='".$planilla."' AND p.fecha = '".$fecha."' ORDER BY p.id_empleado";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_planilla);

foreach ($ejecutar as $key => $row) {
    $id=$row["id_empleado"];
    $nombre=$row["nombre"]." ".$row["apellido"];
    $base= $row['base'];
    $cuenta = $row['cuenta'];
    $reajuste= $row['reajuste'];
    $retencion=$row['retencion'];
    $extras=$row['extras'];
    $vales=$row['vales'];
    $compras=$row['compras'];
    $otros_egresos=$row['otros_egresos'];
    $comision=$row['comision'];
    $mandados=$row['mandados'];
    $ccss=$row['ccss'];
    $otros_ingresos=$row['otros_ingresos'];
    $total=$row['total'];

    $total_salario=$total_salario+$base;
    $total_reajustes=$total_reajustes+$reajuste;
    $total_extras=$total_extras+$extras;
    $total_vales=$total_vales+$vales;
    $total_compras=$total_compras+$compras;
    $total_comision=$total_comision+$comision;
    $total_mandados=$total_mandados+$mandados;
    $total_ccss=$total_ccss+$ccss;
    $total_ingresos=$total_ingresos+$otros_ingresos;
    $total_egresos=$total_egresos+$otros_egresos;
    $total_total=$total_total+$total;

    $empleado_reajustes=$reajuste+$otros_ingresos-$vales-$compras-$otros_egresos;
    $empleado_ccss=$base+$mandados+$comision+$extras-$ccss-$retencion;
    if ($empleado_reajustes<0) {

        $empleado_ccss=$empleado_ccss+$empleado_reajustes;
        $empleado_reajustes=0;
    }

    $total_sal_ccss=$total_sal_ccss+$empleado_ccss;
    $total_sal_reajustes=$total_sal_reajustes+$empleado_reajustes;

    $empleado_reajustes=number_format($empleado_reajustes,2);
    $empleado_ccss=number_format($empleado_ccss,2);
    $total=number_format($total,2);

    $bloque6 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tr>
    <td style="width:25px;text-align:right;">$id</td>
    <td style="width:150px">$nombre</td>
    <td style="width:100px;text-align:right;">$cuenta</td>
    <td style="width:75px;text-align:right;">$empleado_ccss</td>
    <td style="width:75px;text-align:right;">$empleado_reajustes</td>
    <td style="width:100px;text-align:right;">$total</td>
    
</tr>
</table>
EOD;
    $pdf-> writeHTML($bloque6,false,false,true,0,'');


}
$total_fin=$total_sal_ccss+$total_sal_reajustes;
$total_fin=number_format($total_fin,2);
$total_sal_reajustes=number_format($total_sal_reajustes,2);
$total_sal_ccss=number_format($total_sal_ccss,2);

$bloque7 = <<<EOD

<br>
<table cellspacing="0" cellpadding="1" border="1">

	<tr style="background-color:#FF0000;color:white;">
       <th style="width:25px"></th>
       <th style="width:150px">Totales</th>
       <th style="width:100px"></th>
       <th style="width:75px;text-align:right;">$total_sal_ccss</th>
       <th style="width:75px;text-align:right;">$total_sal_reajustes</th>
       <th style="width:100px;text-align:right;">$total_fin</th>
	</tr>

</table>

EOD;
$pdf-> writeHTML($bloque7,false,false,true,0,'');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Write(0, '2,184,300.00 -> 744,750.00 -> 55,030.63  ->>> 3,866,017.70  -- Flor 384,119.00', '', 0, 'L', true, false, false, false, 0);


$pdf->Output('Reporte_planilla.pdf');


 ?>