<?php
session_start();
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");



			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = 'ventas_perdidas.xls';

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
		<p style='font-weight:bold; border:1px solid #eee;'>Reporte Ventas Perdidas</p>
		<br>
		<table border='1' cellpadding='2' cellspacing='0' width='100%'>
		<?php 			
			echo utf8_decode("<tr> 
					<th style='font-weight:bold;'>Código</th> 
					<th style='font-weight:bold;'>Descripcion</th>
					<th style='font-weight:bold;'>Adicional</th>
					</tr>");

                $sql="SELECT s.codigo,s.descripcion,s.ubicacion,
                substring(s.codigo,1,1),substring(s.codigo,6,3 ),substring(s.codigo,10,15),Case substring(s.codigo, 1, 1)
                WHEN '0' then substring(s.codigo,1,1)+substring(s.codigo,5,4 )+substring(s.codigo,9,4 )+' '+substring(s.codigo,1,4 )+' '+ltrim(rtrim(substring(s.codigo,13,12 )))
                WHEN '1' then substring(s.codigo,1,1)+substring(s.codigo,5,4 )+substring(s.codigo,9,4 )+' '+substring(s.codigo,1,4 )+' '+ltrim(rtrim(substring(s.codigo,13,12 )))
                Else s.codigo END As Filtro FROM tmpTras s ORDER BY Filtro";

    		$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    		foreach ($respuesta as $key => $value) {
    	
			
			 	echo utf8_decode("<tr>
			 			<td>".$value["codigo"]."</td> 
			 			<td>".$value["descripcion"]."</td>
			 			<td>".$value["ubicacion"]."</td>
			 		</tr>");

			}


			echo "</table>";

        ?>

