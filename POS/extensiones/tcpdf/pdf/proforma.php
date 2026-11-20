<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['prefactura']) && !empty($_REQUEST['prefactura'])){
      $prefactura = $_REQUEST['prefactura'];
}

if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
      $nombre = $_REQUEST['nombre'];
      $nombre = strtoupper($nombre);
}

// aqui se borra y ya carga
//$prefactura="hhernandezv-6";

$tabla1="Consecutivos";
$item = "tipo";
$valor = "03";
$sql="SELECT * FROM Consecutivos WHERE tipo='$valor'";
//$ventas = ControladorUtilitaria::ctrMostrarUtilitaria($tabla1,$item, $valor);
$ventas = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
foreach ($ventas as $key => $value) {
    $proformacod = $value["numero"];
    $NUMoc=$proformacod;
    $proformacod ='PR-'.$proformacod;
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
							PROFORMA
						<br>
							<b>$proformacod </b>
					</div>
				</td>
			</tr>

	</table>


EOF;
$pdf-> writeHTML($bloque1,false,false,false,false,'');
$fechaActual = date('d-m-Y H:i:s');

$valor = "SELECT h.Factura, h.vendedor, h.codcliente, v.nombre ,c.correo, c.nombre as nomcliente FROM hpregeneral h, Vendedores v, clientes c WHERE h.codcliente= c.codigo AND h.vendedor = v.codigo AND Factura = '$prefactura'";
/// cambiar el numero de prefactura
            
$encabezado = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);

foreach ($encabezado as $key => $nombres) {
	$codigo_cliente=$nombres["codcliente"];
	$nombre_cliente=$nombres["nomcliente"];
	$codigo_vendedor=$nombres["vendedor"];
	$nombre_vendedor=$nombres["nombre"];
	$correo_cliente=$nombres["correo"];

}

$bloque2 = <<<EOF
<hr>
<table>
		<tr>
			<td style="width:250px">
				<div style="background-color:white;">
                    <div><b>Cliente:</b></div>
                    <div> $codigo_cliente - $nombre </div>
                    <div style="color:blue;"> $correo_cliente</div>
                </div>
			</td>

			<td style="width:40px">
			</td>
			
			<td style="width:250px">
				<div ="background-color:white;" >
					<div> <b>Vendedor:</b></div>
					<div> $codigo_vendedor - $nombre_vendedor</div>
					<div>Fecha: $fechaActual</div>

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
	        <th style="text-align:center;" width="110"><b> CODIGO</b></th>
	        <th style="text-align:center;" width="235"><b> DESCRIPCION</b></th>
	        <th style="text-align:center;" width="60"><b> PRECIO</b></th>
	        <th style="text-align:center;" width="42"><b> CANT</b></th>
	        <th style="text-align:center;" width="73"><b> TOTAL</b></th>
	    </tr>

</table>

EOF;
$pdf-> writeHTML($bloque3,false,false,false,false,'');

//sql
$valor = "SELECT h.Factura, h.codigo, h.cantidad, h.precio,i.DESCRIPCION FROM hpregeneral h, inventario i WHERE h.codigo= i.CODIGO AND Factura = '$prefactura'  ORDER BY  h.Id ASC";
/// cambiar el numero de prefactura
            
$carga = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);
$conteo=0;
$prosubtotal=0;
foreach ($carga as $key => $item) {
$conteo=$conteo+1;
$totalpieza= $item["precio"]*$item["cantidad"];
$prosubtotal=$prosubtotal+$totalpieza;
$valorpieza= '&cent;'. number_format($item["precio"],2);
$totalpieza= '&cent;'. number_format($totalpieza,2);
$remplazo=rand(10,99);
$codigo_remplazo=str_replace(" ",$remplazo,$item['codigo']);


$bloque4=<<<EOF

<table style="font-size:8.5px;" border="0" cellspacing="0" cellpadding="2" >
	
	    <tr>
	        <th width="20"> $conteo</th>
	        <th width="110"> $codigo_remplazo</th>
	        <th width="235"> $item[DESCRIPCION]</th>
	        <th style="text-align:right;" width="60">$valorpieza</th>
	        <th style="text-align:right;" width="42"> $item[cantidad]</th>
	        <th style="text-align:right;" width="73"> $totalpieza</th>
	    </tr>


</table>

EOF;
$pdf-> writeHTML($bloque4,false,false,false,false,'');
}
$proimpuesto= $prosubtotal*0.13;
$prototal = $prosubtotal*1.13;
$prosubtotal='&cent;'.number_format($prosubtotal,2);
$prototal='&cent;'.number_format($prototal,2);
$proimpuesto='&cent;'.number_format($proimpuesto,2);
$bloque5 = <<<EOF
<br>
<hr>
<table style="font-size:12px; text-align:right">

	<tr>
		<td style="width:375px; text-align:center">
			**** Esta proforma tiene una validez de 8 dias desde su creación ****
		</td>		
		<td style="width:80px;">
			 <b>SUBTOTAL:</b> 
		</td>

		<td style="width:90px;">
			$prosubtotal
		</td>

	</tr>

	<tr>
		<td style="width:375px; text-align:center">
			**** Esta proforma no tiene contemplado algun tipo de envio ****
		</td>
		<td style="width:80px;">
			 <b>IMPUESTO:</b> 
		</td>

		<td style="width:90px;">
			$proimpuesto
		</td>

	</tr>

	<tr>
		<td style="width:375px;">
		</td>
		<td style="width:175px;">
			 ----------------------------------------
		</td>

	</tr>

	<tr>
		<td style="width:375px;">
		</td>
		<td style="width:80px;">
			 <b>TOTAL:</b> 
		</td>

		<td style="width:90px;">
			$prototal
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

$pdf->writeHTML($bloque5, false, false, false, false, '');

$pdf->Output(__DIR__ .'../../../../Proformas/proforma_'.$proformacod.'.pdf', 'F');
$pdf->Output('proforma_'.$proformacod.'.pdf', 'I');

$NUMoc=$NUMoc+1;
$valor1 = "UPDATE Consecutivos SET numero = $NUMoc WHERE tipo= '03'";
$actualiza = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor1);

 ?>