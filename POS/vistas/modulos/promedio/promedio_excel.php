<?php
session_start();
    
     require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
    $fecha = $_REQUEST['fecha'];
    $fecha = date("m-d-Y", strtotime($fecha));
}
$totalfinal=0;
$totalpiezas=0;
			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = 'Movimientos-'.$fecha.'.xls';

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
		<p style='font-weight:bold; border:1px solid #eee;'>Reporte de articulos sin movimiento</p>
		<br>
		<table border='1' cellpadding='2' cellspacing='0' width='100%'> 
		<?php 			
			echo utf8_decode("<tr> 
					<td style='font-weight:bold; border:1px solid #eee;'>ID</td> 
					<td style='font-weight:bold; border:1px solid #eee;'>Codigo</td> 
					<td style='font-weight:bold; border:1px solid #eee;'>Descripció</td> 
					<td style='font-weight:bold; border:1px solid #eee;'>Existencia</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Costo</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Ventas</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Fecha</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Ubicación</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Total</td>		
					</tr>");

            $sql = "SELECT CODIGO, DESCRIPCION, existencia,costo, venta, ubicacion, potencia,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
            Else CODIGO END As Filtro FROM inventario where not  EXISTS(select DISTINCT (codigo) from hgeneral where fecha > '$fecha' and hgeneral.codigo = inventario.codigo) AND existencia <> 0 order by Filtro";

    		$h=1;
    		$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    		foreach ($respuesta as $key => $arr) {

                $codigos=$arr["CODIGO"];
                $descripcion=$arr["DESCRIPCION"];
                $existencia=$arr["existencia"];
                $costo=$arr["costo"];
                $venta=$arr["venta"];
                $ubicacion=$arr["ubicacion"];
                $potencia=$arr['potencia'];
                $xpotencia=$potencia*100;
                $venta=$venta+$xpotencia;
                $totalcosto=$costo*$existencia;
                $totalfinal=$totalfinal+$totalcosto;
                $totalpiezas=$totalpiezas+$existencia;

                $sql5 = "SELECT codigo,max(fecha) as fecha FROM hgeneral where codigo like '".$codigos."' GROUP BY codigo";
                $ejecutar5 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql5);
                $ultima_fecha='01-01-1980';
                foreach ($ejecutar5 as $key => $arr5) {
                    $ultima_fecha = date("d-m-Y", strtotime($arr5['fecha']));
                }

			
			 	echo utf8_decode("<tr>
			 			<td>".$h."</td> 
			 			<td>".$codigos."</td> 
			 			<td>".$descripcion."</td>
			 			<td>".$existencia."</td>
			 			<td>".number_format($costo,2)."</td>
			 			<td>".number_format($venta,2)."</td>
			 			<td>".$ultima_fecha."</td>
			 			<td>".$ubicacion."</td>
			 			<td>".number_format($totalcosto,2)."</td>
			 		</tr>");
			 		$h++;
			}


			echo "</table>";
            echo "<br>";
            echo "Total Listado: ".number_format($totalfinal);
            echo "Total Piezas: ".number_format($totalpiezas);
            echo "Total Items: ".number_format($h);

?>