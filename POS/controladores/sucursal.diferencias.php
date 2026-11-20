<?php
session_start();
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");



			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = 'diferencias.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");
		?>
		<p style='font-weight:bold; border:1px solid #eee;'>Reporte de Diferencias de articulos en Sucursal</p>
		<br>
		<table border='1' cellpadding='2' cellspacing='0' width='100%'> 
		<?php 			
			echo utf8_decode("<tr> 
					<td style='font-weight:bold; border:1px solid #eee;'>Código</td> 
					<td style='font-weight:bold; border:1px solid #eee;'>Descripcion</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Cantidad</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Bodega</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Ubicación 1</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Ubicación 2</td>		
					</tr>");
		
			 $sql = "SELECT i.CODIGO, i.DESCRIPCION, i.EXISTENCIA, i.ubicacion as ubicacion,e.ubicacion as ubica2, e.cantidad, e.ubicacion as sucubi,substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1) WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 ))) WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 ))) Else i.CODIGO END As Filtro FROM inventario i, tbl_exi_suc e WHERE i.CODIGO = e.codigo AND e.cantidad >= i.EXISTENCIA order by Filtro";
    
    		$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    		foreach ($respuesta as $key => $value) {
    	
			 	echo utf8_decode("<tr>
			 			<td>".$value["CODIGO"]."</td> 
			 			<td>".$value["DESCRIPCION"]."</td>
			 			<td>".number_format($value["EXISTENCIA"],2)."</td>
			 			<td>".number_format($value["cantidad"],2)."</td>
			 			<td>".$value["ubicacion"]."</td>
			 			<td>".$value["ubica2"]."</td>
			 		</tr>");

			}


			echo "</table>";



