<?php 

date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['orden']) && !empty($_REQUEST['orden'])){
      $orden = $_REQUEST['orden'];
}
if(isset($_REQUEST['empresa']) && !empty($_REQUEST['empresa'])){
      $empresa = $_REQUEST['empresa'];
      $empresa=strtoupper($empresa);
}

$tabla1="Consecutivos";
$item = "tipo";
$valor = "09";

$ventas = ControladorUtilitaria::ctrMostrarUtilitaria($tabla1,$item, $valor);

foreach ($ventas as $key => $value) {

}

$proformacod = $value["numero"];
$NUMoc = $value["numero"];




$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Maximo 540
$pdf-> startPageGroup();
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
							ORDEN DE COMPRA
						<br>
							<b>$proformacod </b>
					</div>
				</td>
			</tr>

	</table>


EOF;
$pdf-> writeHTML($bloque1,false,false,false,false,'');
$fechaActual = date('d-m-Y H:i:s');



$bloque2 = <<<EOF
<hr>
<table>
		<tr>
			<td style="width:250px">
				<div style="background-color:white;">
                    <div><b>Proveedor:</b></div>
                    <div> $empresa </div>
                    
                </div>
			</td>

			<td style="width:40px">
			
			</td>
			
			<td style="width:250px">
			<div style="background-color:white;">
                    <div><b>Fecha:</b></div>
                    <div> $fechaActual </div>
                    
                </div>
				
			</td>

		</tr>

</table



EOF;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$bloque3=<<<EOF

<table border="1" cellspacing="0" cellpadding="2">
	    <tr>
	        <th style="text-align:center;" width="20"><b> #</b></th>
	        <th style="text-align:center;" width="110"><b> CANTIDAD</b></th>
	        <th style="text-align:center;" width="410"><b> DESCRIPCION</b></th>
	        
	    </tr>

</table>

EOF;
$pdf-> writeHTML($bloque3,false,false,false,false,'');


$conteo=0;
$prosubtotal=0;
$valor = "SELECT * FROM tmp_OC WHERE consecutivo= '$orden' ORDER BY Id ASC";
/// cambiar el numero de prefactura
         
$carga = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);
$conteo=0;
$prosubtotal=0;
foreach ($carga as $key => $item) {
	$conteo=$conteo+1;
	$cantidadOC= $item["cantidad"];
	$descripcionOC= strtoupper($item["descripcion"]);

$bloque4=<<<EOF

<table style="font-size:12spx;" border="0" cellspacing="0" cellpadding="2" >
	
	    <tr>
	        <th style="text-align:center;" width="20"> $conteo</th>
	        <th style="text-align:center;" width="110"> $cantidadOC</th>
	        <th style="text-align:center;" width="410"> $descripcionOC</th>
	        
	    </tr>


</table>

EOF;
$pdf-> writeHTML($bloque4,false,false,false,false,'');
}
$bloque5 = <<<EOF
<br>
<hr>
<table>
<tr>
	<td>
	</td>

</tr>
<tr>
	<td>
	</td>

</tr>
<tr>
	<td>
	</td>

</tr>
<tr>
	<td>
	</td>

</tr>

</table>
EOF;
$pdf-> writeHTML($bloque5,false,false,false,false,'');

$bloque6 = <<<EOF
<br>
<table style="font-size:12px; text-align:right">

	<tr>
		<td style="width:180px; text-align:center">
		_________________________
		</td>		       
		<td style="width:180px; text-align:center">
		_______________________
		</td>
		<td style="width:180px; text-align:center">
		_________________________
		</td>

	</tr>

	<tr>
		<td style="width:180px; text-align:center">
			Nombre
		</td>
		<td style="width:180px; text-align:center">
			Cédula
		</td>
		<td style="width:180px; text-align:center">
			Firma
		</td>

	</tr>
	<tr>
		<td>
		</td>

	</tr>

	

	<tr>
		
		<td style="width:540px; text-align:center">
			<br>
			Muchas gracias por preferencia<br>
			<span style="color:blue;"><b>info@lospaticas.com / www.lospaticas.com / Facebook/LosPaticas</b></span>
		</td>

	</tr>

</table>



EOF;

$pdf->writeHTML($bloque6, false, false, false, false, '');


$NUMoc=$NUMoc+1;
$valor1 = "UPDATE Consecutivos SET numero = $NUMoc WHERE tipo= '09'";

$actualiza = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor1);

$valor = "DELETE FROM tmp_OC WHERE consecutivo= '$orden'";

$elimina = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);

$pdf->Output(__DIR__ .'../../../../Ordenes/orden_'.$proformacod.'.pdf', 'F');
$pdf->Output('orden_'.$proformacod.'.pdf', 'I');
 ?>