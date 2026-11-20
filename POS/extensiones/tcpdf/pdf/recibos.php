<?php 

date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");
$cliente=0;
if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
    $cliente = $_REQUEST['cliente'];
}
if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
      $nombre = $_REQUEST['nombre'];
      $nombre=strtoupper($nombre);
}
if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
      $descripcion = $_REQUEST['descripcion'];
      $descripcion=strtoupper($descripcion);
}
if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
    $monto = $_REQUEST['monto'];
}
if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
    $fecha = $_REQUEST['fecha'];
}

$tabla1="Consecutivos";
$item = "tipo";
$valor = "04";

$ventas = ControladorUtilitaria::ctrMostrarUtilitaria($tabla1,$item, $valor);

foreach ($ventas as $key => $value) {
    $num_recibo = $value["numero"];
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Maximo 540
$pdf-> startPageGroup();
$pdf-> AddPage();

$bloque1 = <<<EOF
	<br>
	<table>
			<tr>
				<td style="width:150px"><img src="images/logo-blanco-bloque.png"></td>
				<td style=" width:140px">
					<div style="font-size:12px; text-align:center; line-height:15px">
						<br>
							<b>TAI COSTA S.A</b>
						<br>
							3-101-090358
					</div>
				</td>

				<td style="width:150px">
					<div style="font-size:9px; text-align:center; line-height:15px">
						<br>
							Teléfono: (506)2227-8252
						<br>
							<span style="color:blue;">info@lospaticas.com</span>
					</div>
				</td>
				<td style="width:100px">
					<div style="font-size:12px; text-align:center; line-height:15px; color:red">
						<br>
							RECIBO DE DINERO
						<br>
							<b>$num_recibo </b>
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
				<div style="font-size:10px; background-color:white;line-height:15px">
                    <div><b>Nombre Cliente:</b></div>
                    <div> $cliente - $nombre </div> 
                </div>
			</td>
			<td style="width:40px">
			</td>
			<td style="width:250px">
			<div style="font-size:10px; background-color:white;line-height:15px">
                    <div><b>Fecha:</b></div>
                    <div> $fechaActual </div>     
                </div>
			</td>
		</tr>
</table
EOF;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$bloque3=<<<EOF
<table>
		<tr>
			<td style="width:250px">
				<div style="background-color:white;">
                    <div><b>Descripción:</b></div>
                    <div> $descripcion </div> 
                </div>
			</td>

			<td style="width:40px">
			
			</td>
			
			<td style="width:250px">
			<div style="background-color:white;">
                    <div><b>Monto números:</b></div>
                    <div> $monto </div>      
                </div>
			</td>
		</tr>
</table

EOF;
$pdf-> writeHTML($bloque3,false,false,false,false,'');

$bloque5 = <<<EOF

<table>

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
		<td style="color:blue">ORIGINAL
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
<br>
<hr>
<br>
EOF;

$bloque7 = <<<EOF
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
		<td style="color:red">COPIA
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
<br>
<hr>
<br>
EOF;
$pdf->writeHTML($bloque6, false, false, false, false, '');

$pdf->writeHTML($bloque1, false, false, false, false, '');
$pdf->writeHTML($bloque2, false, false, false, false, '');
$pdf->writeHTML($bloque3, false, false, false, false, '');
$pdf->writeHTML($bloque5, false, false, false, false, '');
$pdf->writeHTML($bloque7, false, false, false, false, '');

$time = time();
$fch=explode("/",$fecha);$tfecha=$fch[1]."/".$fch[2]."/".$fch[0];
$newDate = date("Y/m/d", strtotime($fecha));

$fecha_hora= strftime(  $newDate ) ." ". date("H:i:s", $time).".000";

$NUMoc=$num_recibo+1;
$sql = "INSERT INTO cxc
            (IDCliente,NFac,IDCompañia,Fecha,DiasCredito,FechaLimite,Subtotal,Impuesto,Descuento,Total,Saldo,Estado)
            values
            ('".$cliente."','R-".$num_recibo."','0','".$fecha_hora."','0','".$fecha."','".$monto."','0','0','".$monto."','0','Cancelada')";

$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

$valor1 = "UPDATE Consecutivos SET numero = $NUMoc WHERE tipo= '04'";

$actualiza = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor1);

//echo $sql;

$pdf->Output(__DIR__ .'../../../../Recibos/R_'.$num_recibo.'.pdf', 'F');
$pdf->Output('orden_'.$num_recibo.'.pdf', 'I');
 ?>