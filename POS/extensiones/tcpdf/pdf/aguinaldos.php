<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['year']) && !empty($_REQUEST['year'])){
      $year = $_REQUEST['year'];
}

if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
}

if(isset($_REQUEST['empleado']) && !empty($_REQUEST['empleado'])){
    $empleados = $_REQUEST['empleado'];
}

$ano=$year-1;
$fecha1=$ano.'-12-01';
$fecha2=$year.'-11-30';
$t_base=0;
$t_reajustes=0;

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Maximo 540
$pdf-> startPageGroup();

//******************TODOS*****************
if ($empleados=='todos') {
    $sql_empleados="SELECT r.id_planilla, r.fecha, r.id_empleado, r.base, r.ccss, r.extras,r.reajuste, r.total, r.mandados,r.otros_ingresos, e.nombre, e.apellido, r.comision, e.estado
      FROM rh_planilla r, rh_empleados e WHERE Fecha BETWEEN '$fecha1' AND
      '$fecha2' AND e.id_empleado = r.id_empleado AND e.estado = 'a' order by r.id_empleado, r.fecha";

    $pdf-> AddPage();

    $bloque1 = <<<EOF
	<br>
	<table>
			<tr>
				<td style="width:150px"><img src="images/logo-blanco-bloque.png"></td>
				<td style="background-color:white; width:140px">
					<div style="font-size:12px; text-align:center; line-height:15px">
						<br>
							<b>TAI COSTA S.A</b>
						<br>
							3-101-090358
					</div>
				</td>

				<td style="background-color:white; width:150px">
					<div style="font-size:9px; text-align:center; line-height:15px">
						<br>
							Teléfono: (506)2227-8252
						<br>
							<span style="color:blue;">info@lospaticas.com</span>
					</div>
				</td>
				<td style="background-color:white; width:100px">
					<div style="font-size:12px; text-align:center; line-height:15px; color:red">
						<br>
							AÑO
						<br>
							<b>$year </b>
					</div>
				</td>
			</tr>

	</table>
    <hr>

EOF;
    $pdf-> writeHTML($bloque1,false,false,false,false,'');

    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Write(0, 'Reporte Aguinaldo '.$year, '', 0, 'C', true, false, false, false, 0);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Write(0, '', '', 0, 'C', true, false, false, false, 0);

    $bloque2=<<<EOF

<table border="1" cellspacing="0" cellpadding="2">
	    <tr style="background-color:#1b9eee;color:white;">
	        <th style="text-align:center;" width="135"><b>Empleado</b></th>
	        <th style="text-align:center;" width="15"><b>Id</b></th>
	        <th style="text-align:center;" width="60"><b>Base</b></th>
	        <th style="text-align:center;" width="55"><b>Comision</b></th>
	        <th style="text-align:center;" width="65"><b>T.base</b></th>
	        <th style="text-align:center;" width="55"><b>Reajuste</b></th>
	        <th style="text-align:center;" width="45"><b>Extras</b></th>
	        <th style="text-align:center;" width="65"><b>T.Reajustes</b></th>
	        <th style="text-align:center;" width="65"><b>Total</b></th>
	    </tr>

</table>

EOF;
    $pdf-> writeHTML($bloque2,false,false,false,false,'');

    $ano=$year-1;
    $fecha1=$ano.'-12-01';
    $fecha2=$year.'-11-30';
    $newdate=strtotime("$fecha2");
    $newdate = date ( 'Y-m-d' , $newdate );
    $fecha2=$newdate;
    $cod_empleado='';

    $sql="SELECT r.id_planilla, r.fecha, r.id_empleado, r.base, r.ccss, r.extras,r.reajuste, r.total, r.mandados,r.otros_ingresos, e.nombre, e.apellido, r.comision, e.estado
      FROM rh_planilla r, rh_empleados e WHERE Fecha BETWEEN '$fecha1' AND
      '$fecha2' AND e.id_empleado = r.id_empleado  AND e.estado = 'a' order by r.id_empleado, r.fecha";

    $empleado=0;
    $nombre='';
    $total=0;
    $base_agui=0;
    $comision_agui=0;
    $reajuste_agui=0;
    $extras_agui=0;
    $mandados=0;
    $mandados_agui=0;
    $base=0;
    $comision=0;
    $reajuste=0;
    $extras=0;
    $mandados=0;
    $oingresos=0;


    $carga = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

    foreach ($carga as $key => $row) {

        if ($empleado=='') {
            $nombre = $row["nombre"] . ' ' . $row["apellido"];
            $empleado = $row["id_empleado"];
        }
        if ($empleado!=$row["id_empleado"]) {
            $base_agui=$base_agui/12;
            $comision_agui=$comision_agui/12;
            $reajuste_agui=$reajuste_agui/12;
            $extras_agui=$extras_agui/12;
            $mandados_agui=$mandados_agui/12;
            $totalbase=$base_agui+$mandados_agui+$comision_agui;
            $totalreajustes=$reajuste_agui+$extras_agui;
            $total=$totalbase+$totalreajustes;
            $t_base=$t_base+$totalbase;
            $t_reajustes=$t_reajustes+$totalreajustes;
            $base_aguidec=number_format($base_agui,2);
            $comision_aguidec=number_format($comision_agui,2);
            $totalbasedec=number_format($totalbase,2);
            $reajuste_aguidec=number_format($reajuste_agui,2);
            $extras_aguidec=number_format($extras_agui,2);
            $totalreajustesdec=number_format($totalreajustes,2);
            $totaldec=number_format($total,2);
            $base_agui=0;
            $comision_agui=0;
            $reajuste_agui=0;
            $extras_agui=0;
            $mandados_agui=0;
            $totalbase=0;
            $totalreajustes=0;
            $total=0;
$bloque4=<<<EOF

<table border="1" cellspacing="0" cellpadding="2">
	    <tr style="background-color:white;color:black;">
	        <th style="text-align:left;" width="135"><b>$nombre</b></th>
	        <th style="text-align:center;" width="15"><b>$empleado</b></th>
	        <th style="text-align:right;" width="60"><b>$base_aguidec</b></th>
	        <th style="text-align:right;" width="55"><b>$comision_aguidec</b></th>
	        <th style="text-align:right;" width="65"><b>$totalbasedec</b></th>
	        <th style="text-align:right;" width="55"><b>$reajuste_aguidec</b></th>
	        <th style="text-align:right;" width="45"><b>$extras_aguidec</b></th>
	        <th style="text-align:right;" width="65"><b>$totalreajustesdec</b></th>
	        <th style="text-align:right;" width="65"><b>$totaldec</b></th>
	    </tr>

</table>
EOF;
$pdf-> writeHTML($bloque4,false,false,false,false,'');

        }

        $nombre = $row["nombre"] . ' ' . $row["apellido"];
        $empleado = $row["id_empleado"];
        $base= $row["base"];
        $base_agui=$base_agui+$base;
        $comision=$row["comision"];
        $comision_agui=$comision_agui+$comision;
        $reajuste=$row["reajuste"];
        $oingresos=$row["otros_ingresos"];
        $reajuste_agui=$reajuste_agui+$reajuste+$oingresos;
        $extras=$row["extras"];
        $extras_agui=$extras_agui+$extras;
        $mandados=$row["mandados"];
        $mandados_agui=$mandados_agui+$mandados;

    }
    $base_agui=$base_agui/12;
    $comision_agui=$comision_agui/12;
    $reajuste_agui=$reajuste_agui/12;
    $extras_agui=$extras_agui/12;
    $mandados_agui=$mandados_agui/12;

    $totalbase=$base_agui+$mandados_agui+$comision_agui;
    $totalreajustes=$reajuste_agui+$extras_agui;
    $total=$totalbase+$totalreajustes;

    $t_base=$t_base+$totalbase;
    $t_reajustes=$t_reajustes+$totalreajustes;

    $base_aguidec=number_format($base_agui,2);
    $comision_aguidec=number_format($comision_agui,2);
    $totalbasedec=number_format($totalbase,2);
    $reajuste_aguidec=number_format($reajuste_agui,2);
    $extras_aguidec=number_format($extras_agui,2);
    $totalreajustesdec=number_format($totalreajustes,2);
    $totaldec=number_format($total,2);
    $base_agui=0;
    $comision_agui=0;
    $reajuste_agui=0;
    $extras_agui=0;
    $mandados_agui=0;
    $totalbase=0;
    $totalreajustes=0;
    $total=0;

$bloque3 = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
 <tbody> 
    <tr>  
        <th style="text-align:left;" width="135"><b>$nombre</b></th>
        <th style="text-align:center;" width="15"><b>$empleado</b></th>
        <th style="text-align:right;" width="60"><b>$base_aguidec</b></th>
        <th style="text-align:right;" width="55"><b>$comision_aguidec</b></th>
        <th style="text-align:right;" width="65"><b>$totalbasedec</b></th>
        <th style="text-align:right;" width="55"><b>$reajuste_aguidec</b></th>
        <th style="text-align:right;" width="45"><b>$extras_aguidec</b></th>
        <th style="text-align:right;" width="65"><b>$totalreajustesdec</b></th>
        <th style="text-align:right;" width="65"><b>$totaldec</b></th> 
    </tr>
</tbody>
</table>
EOD;
$pdf-> writeHTML($bloque3,false,false,false,false,'');

$t_total=$t_base+$t_reajustes;
$t_base=number_format($t_base,2);
$t_reajustes=number_format($t_reajustes,2);
$t_total=number_format($t_total,2);

$bloque5 = <<<EOD
<table border="1" cellspacing="0" cellpadding="2">
	    <tr style="background-color:#1b9eee;color:white;">
	        <th style="text-align:center;" width="135"><b>Total</b></th>
	        <th style="text-align:center;" width="15"><b></b></th>
	        <th style="text-align:center;" width="60"><b></b></th>
	        <th style="text-align:center;" width="55"><b></b></th>
	        <th style="text-align:center;" width="65"><b>$t_base</b></th>
	        <th style="text-align:center;" width="55"><b></b></th>
	        <th style="text-align:center;" width="45"><b></b></th>
	        <th style="text-align:center;" width="65"><b>$t_reajustes</b></th>
	        <th style="text-align:center;" width="65"><b>$t_total</b></th>
	    </tr>

</table>
EOD;
$pdf-> writeHTML($bloque5,false,false,false,false,'');

//*****************Hasta aqui el encabezado, ahora vienen individuales **********
    $sql_todos= "SELECT DISTINCT id_empleado FROM rh_planilla WHERE Fecha BETWEEN '$fecha1' AND
    '$fecha2'  order by id_empleado";

    $carga = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_todos);
    foreach ($carga as $key => $arr) {
        $empl=$arr['id_empleado'];
        $pdf-> AddPage();

$bloque6 = <<<EOF
<br>
<br>
<table>
        <tr>
            <td style="width:150px"><img src="images/logo-blanco-bloque.png"></td>
            <td style="background-color:white; width:140px">
                <div style="font-size:12px; text-align:center; line-height:15px">
                    <br>
                        <b>TAI COSTA S.A</b>
                    <br>
                        3-101-090358
                </div>
            </td>

            <td style="background-color:white; width:150px">
                <div style="font-size:9px; text-align:center; line-height:15px">
                    <br>
                        Teléfono: (506)2227-8252
                    <br>
                        <span style="color:blue;">info@lospaticas.com</span>
                </div>
            </td>
            <td style="background-color:white; width:100px">
                <div style="font-size:12px; text-align:center; line-height:15px; color:red">
                    <br>
                        AÑO
                    <br>
                        <b>$year </b>
                </div>
            </td>
        </tr>

</table>
<hr>

EOF;
$pdf-> writeHTML($bloque6,false,false,false,false,'');

$pdf->SetFont('helvetica', 'B', 20);
$pdf->Write(0, 'Reporte Aguinaldo '.$year, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Write(0, '', '', 0, 'C', true, false, false, false, 0);

$bloque7=<<<EOF

<table border="1" cellspacing="0" cellpadding="2">
	    <tr style="background-color:#1b9eee;color:white;">
	        <th style="text-align:center;" width="30"><b>Id Pl</b></th>
	        <th style="text-align:center;" width="40"><b>Fecha</b></th>
	        <th style="text-align:center;" width="105"><b>Empleado</b></th>
	        <th style="text-align:center;" width="15"><b>Id</b></th>
	        <th style="text-align:center;" width="60"><b>Base</b></th>
	        <th style="text-align:center;" width="50"><b>Comision</b></th>
	        <th style="text-align:center;" width="60"><b>T.base</b></th>
	        <th style="text-align:center;" width="50"><b>Reajuste</b></th>
	        <th style="text-align:center;" width="45"><b>Extras</b></th>
	        <th style="text-align:center;" width="56"><b>T.Reajustes</b></th>
	        <th style="text-align:center;" width="60"><b>Total</b></th>
	    </tr>

</table>

EOF;
$pdf-> writeHTML($bloque7,false,false,false,false,'');

        $ano=$year-1;
        $fecha1=$ano.'-12-01';
        $fecha2=$year.'-11-30';
        $newdate=strtotime("$fecha2");
        $newdate = date ( 'Y-m-d' , $newdate );
        $fecha2=$newdate;

        $sql="SELECT r.id_planilla, r.fecha, r.id_empleado, r.base, r.ccss, r.extras,r.reajuste, r.total, r.mandados,r.otros_ingresos, e.nombre, e.apellido, r.comision
      FROM rh_planilla r, rh_empleados e WHERE Fecha BETWEEN '$fecha1' AND
      '$fecha2' AND e.id_empleado = r.id_empleado AND r.id_empleado= '$empl'  order by r.id_empleado, r.fecha";
        $nombre='';
        $treajuste_parcial=0;
        $treajuste=0;
        $ttotal=0;
        $ttotal_parcial=0;
        $total=0;
        $rep_total=0;
        $rep_reajustes=0;
        $carga1 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

        foreach ($carga1 as $key => $row) {
            $planilla = $row["id_planilla"];
            $fecha = date("d-m-y", strtotime($row["fecha"]));
            $nombre = $row["nombre"] . ' ' . $row["apellido"];
            $empleado = $row["id_empleado"];
            $treajuste_parcial = 0;
            $ttotal_parcial = 0;
            $base= $row["base"];
            $comision=$row["comision"];
            $reajuste=$row["reajuste"]+ $row["otros_ingresos"];
            $extras=$row["extras"];
            $tbase = ($row["base"] + $row["comision"]) / 12;
            $ttotal = ($row["reajuste"] + $row["extras"] + $row["mandados"]+$row["otros_ingresos"]) / 12;
            $ttotal_parcial = $ttotal_parcial + $tbase;
            $treajuste_parcial = $treajuste_parcial + $ttotal;
            $total = $total + $tbase;
            $treajuste = floatval($treajuste) + floatval($ttotal);
            $totaltotal=$tbase+$ttotal;

            $base=number_format($base,2);
            $comision=number_format($comision,2);
            $tbase=number_format($tbase,2);
            $reajuste=number_format($reajuste,2);
            $extras=number_format($extras,2);
            $ttotal=number_format($ttotal,2);
            $totaltotal=number_format($totaltotal,2);
            $nombrecorto=substr($nombre, 0, 20);

$bloque8 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
 <tbody> 
    <tr>
      <td style="text-align:center;width:30px">$planilla</td> 
      <td style="text-align:center;width:40px">$fecha</td>
      <td style="width:105px">$nombrecorto</td>
      <td style="text-align:center;width:15px">$empleado</td>
      <td style="text-align:right;width:60px">$base</td>
      <td style="text-align:right;width:50px">$comision</td>
      <td style="text-align:right;color:blue;width:60px">$tbase</td>   
      <td style="text-align:right;width:50px">$reajuste</td>
      <td style="text-align:right;width:45px">$extras</td>
      <td style="text-align:right;color:red;width:56px">$ttotal</td>
      <td style="text-align:right;color:green; width:60px">$totaltotal</td> 
    </tr>
</tbody>
</table>
EOD;

$pdf-> writeHTML($bloque8,false,false,false,false,'');
        }
        $rep_total=$rep_total+$total;
        $rep_reajustes=floatval($rep_reajustes)+floatval($treajuste);
        $supertotal=floatval($treajuste)+floatval($total);
        $total=number_format($total,2);
        $treajuste=number_format($treajuste,2);
        $supertotal=number_format($supertotal,2);



$bloque9=<<<EOF

<table border="1" cellspacing="0" cellpadding="2">
	    <tr style="background-color:#1b9eee;color:white;">
	        <th style="text-align:center;" width="30"><b></b></th>
	        <th style="text-align:center;" width="40"><b></b></th>
	        <th style="text-align:center;" width="105"><b>Totales:</b></th>
	        <th style="text-align:center;" width="15"><b></b></th>
	        <th style="text-align:center;" width="60"><b></b></th>
	        <th style="text-align:center;" width="50"><b></b></th>
	        <th style="text-align:right;" width="60"><b>$total</b></th>
	        <th style="text-align:center;" width="50"><b></b></th>
	        <th style="text-align:center;" width="45"><b></b></th>
	        <th style="text-align:right;" width="56"><b>$treajuste</b></th>
	        <th style="text-align:right;" width="60"><b>$supertotal</b></th>
	    </tr>

</table>
EOF;
        $pdf-> writeHTML($bloque9,false,false,false,false,'');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, '', '', 0, 'C', true, false, false, false, 0);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Write(0, 'Nombre: '.$nombre, '', 0, 'L', true, false, false, false, 0);
        $pdf->SetFont('helvetica', 'B', 35);
        $pdf->Write(0, '', '', 0, 'C', true, false, false, false, 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, 'Firma: ____________________________________________', '', 0, 'L', true, false, false, false, 0);

    }
//**************FIN TODOS*****************
}else {


    $pdf->AddPage();

    $bloque1 = <<<EOF
	<br>
	<table>
			<tr>
				<td style="width:150px"><img src="images/logo-blanco-bloque.png"></td>
				<td style="background-color:white; width:140px">
					<div style="font-size:12px; text-align:center; line-height:15px">
						<br>
							<b>TAI COSTA S.A</b>
						<br>
							3-101-090358
					</div>
				</td>

				<td style="background-color:white; width:150px">
					<div style="font-size:9px; text-align:center; line-height:15px">
						<br>
							Teléfono: (506)2227-8252
						<br>
							<span style="color:blue;">info@lospaticas.com</span>
					</div>
				</td>
				<td style="background-color:white; width:100px">
					<div style="font-size:12px; text-align:center; line-height:15px; color:red">
						<br>
							AÑO
						<br>
							<b>$year </b>
					</div>
				</td>
			</tr>

	</table>
    <hr>

EOF;
    $pdf->writeHTML($bloque1, false, false, false, false, '');

    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Write(0, 'Reporte Aguinaldo ' . $year, '', 0, 'C', true, false, false, false, 0);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Write(0, '', '', 0, 'C', true, false, false, false, 0);

    $bloque2 = <<<EOF

<table border="1" cellspacing="0" cellpadding="2">
	    <tr style="background-color:#1b9eee;color:white;">
	        <th style="text-align:center;" width="30"><b>Id Pl</b></th>
	        <th style="text-align:center;" width="40"><b>Fecha</b></th>
	        <th style="text-align:center;" width="105"><b>Empleado</b></th>
	        <th style="text-align:center;" width="15"><b>Id</b></th>
	        <th style="text-align:center;" width="60"><b>Base</b></th>
	        <th style="text-align:center;" width="50"><b>Comision</b></th>
	        <th style="text-align:center;" width="60"><b>T.base</b></th>
	        <th style="text-align:center;" width="50"><b>Reajuste</b></th>
	        <th style="text-align:center;" width="45"><b>Extras</b></th>
	        <th style="text-align:center;" width="56"><b>T.Reajustes</b></th>
	        <th style="text-align:center;" width="60"><b>Total1</b></th>
	    </tr>

</table>

EOF;
    $pdf->writeHTML($bloque2, false, false, false, false, '');

    $ano = $year - 1;
    $fecha1 = $ano . '-12-01';
    $fecha2 = $year . '-11-30';
    $newdate = strtotime("$fecha2");
    $newdate = date('Y-m-d', $newdate);
    $fecha2 = $newdate;
//sql

    $sql = "SELECT r.id_planilla, r.fecha, r.id_empleado, r.base, r.ccss, r.extras,r.reajuste, r.total, r.mandados,r.otros_ingresos, e.nombre, e.apellido, r.comision
      FROM rh_planilla r, rh_empleados e WHERE Fecha BETWEEN '$fecha1' AND
      '$fecha2' AND e.id_empleado = r.id_empleado AND r.id_empleado= '$empleados' order by r.id_empleado, r.fecha";

    $empleado = 0;
    $nombre = '';
    $treajuste_parcial = 0;
    $treajuste = 0;
    $ttotal = 0;
    $ttotal_parcial = 0;
    $total = 0;
    $rep_total = 0;
    $rep_reajustes = 0;

    $carga = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

    foreach ($carga as $key => $row) {
        $planilla = $row["id_planilla"];
        $fecha = date("d-m-y", strtotime($row["fecha"]));
        $nombre = $row["nombre"] . ' ' . $row["apellido"];
        $empleado = $row["id_empleado"];
        $treajuste_parcial = 0;
        $ttotal_parcial = 0;
        $base = $row["base"];
        $comision = $row["comision"];
        $reajuste = $row["reajuste"] + $row["otros_ingresos"];
        $extras = $row["extras"];
        $tbase = ($row["base"] + $row["comision"]) / 12;
        $ttotal = ($row["reajuste"] + $row["extras"] + $row["mandados"] + $row["otros_ingresos"]) / 12;
        $ttotal_parcial = $ttotal_parcial + $tbase;
        $treajuste_parcial = $treajuste_parcial + $ttotal;
        $total = $total + $tbase;
        $treajuste = floatval($treajuste) + floatval($ttotal);
        $totaltotal = $tbase + $ttotal;


        $base = number_format($base, 2);
        $comision = number_format($comision, 2);
        $tbase = number_format($tbase, 2);
        $reajuste = number_format($reajuste, 2);
        $extras = number_format($extras, 2);
        $ttotal = number_format($ttotal, 2);
        $totaltotal = number_format($totaltotal, 2);
        $nombrecorto = substr($nombre, 0, 20);
        $bloque3 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
 <tbody> 
    <tr>
      <td style="text-align:center;width:30px">$planilla</td> 
      <td style="text-align:center;width:40px">$fecha</td>
      <td style="width:105px">$nombrecorto</td>
      <td style="text-align:center;width:15px">$empleado</td>
      <td style="text-align:right;width:60px">$base</td>
      <td style="text-align:right;width:50px">$comision</td>
      <td style="text-align:right;color:blue;width:60px">$tbase</td>   
      <td style="text-align:right;width:50px">$reajuste</td>
      <td style="text-align:right;width:45px">$extras</td>
      <td style="text-align:right;color:red;width:56px">$ttotal</td>
      <td style="text-align:right;color:green; width:60px">$totaltotal</td> 
    </tr>
</tbody>
</table>
EOD;

        $pdf->writeHTML($bloque3, false, false, false, false, '');
    }
    $rep_total = $rep_total + $total;
    $rep_reajustes = floatval($rep_reajustes) + floatval($treajuste);
    $supertotal = floatval($treajuste) + floatval($total);
    $total = number_format($total, 2);
    $treajuste = number_format($treajuste, 2);
    $supertotal = number_format($supertotal, 2);


    $bloque4 = <<<EOF

<table border="1" cellspacing="0" cellpadding="2">
	    <tr style="background-color:#1b9eee;color:white;">
	        <th style="text-align:center;" width="30"><b></b></th>
	        <th style="text-align:center;" width="40"><b></b></th>
	        <th style="text-align:center;" width="105"><b>Totales:</b></th>
	        <th style="text-align:center;" width="15"><b></b></th>
	        <th style="text-align:center;" width="60"><b></b></th>
	        <th style="text-align:center;" width="50"><b></b></th>
	        <th style="text-align:right;" width="60"><b>$total</b></th>
	        <th style="text-align:center;" width="50"><b></b></th>
	        <th style="text-align:center;" width="45"><b></b></th>
	        <th style="text-align:right;" width="56"><b>$treajuste</b></th>
	        <th style="text-align:right;" width="60"><b>$supertotal</b></th>
	    </tr>

</table>
EOF;
    $pdf->writeHTML($bloque4, false, false, false, false, '');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Write(0, '', '', 0, 'C', true, false, false, false, 0);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Write(0, 'Nombre: ' . $nombre, '', 0, 'L', true, false, false, false, 0);
    $pdf->SetFont('helvetica', 'B', 35);
    $pdf->Write(0, '', '', 0, 'C', true, false, false, false, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Write(0, 'Firma: ____________________________________________', '', 0, 'L', true, false, false, false, 0);
}
$pdf->Output('Reporte_aguinaldo.pdf');
?>