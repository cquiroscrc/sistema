<?php
session_start();
    
     require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");



			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = 'actualiza.xls';

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
		
		<table border='1' cellpadding='2' cellspacing='0' width='100%'> 
		<?php 			
			echo utf8_decode("<tr> 
					<td style='font-weight:bold;'>SKU</td> 
					<td style='font-weight:bold; '>Tipo</td> 
					<td style='font-weight:bold; '>Nombre</td>
					<td style='font-weight:bold; '>Publicado</td>
					<td style='font-weight:bold; '>catalog_visibility</td>
					<td style='font-weight:bold; '>description</td>
					<td style='font-weight:bold; '>Clase de impuesto</td>
					<td style='font-weight:bold; '>stock_status</td>
					<td style='font-weight:bold; '>Precio normal</td>
					<td style='font-weight:bold; '>category_ids</td>

					</tr>");
				

			$sql="SELECT i.CODIGO,i.DESCRIPCION,i.EXISTENCIA,i.VENTA,i.potencia, w.Codigo as cod2, w.Precio, substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1) WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))Else i.CODIGO END As Filtro FROM inventario i, woocommerce w WHERE i.CODIGO = w.Codigo ORDER BY Filtro";

			 
    		$h=0;
    		$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    		foreach ($respuesta as $key => $row) {

    			  $sku=$row['CODIGO'];
    			  $tipo="simple";
			      $descripcion=$row['DESCRIPCION'];
			      $descripcion= strtolower($descripcion);
          		  $descripcion = ucwords($descripcion); 
          		  $nombre=$descripcion;
			      $visibilidad="visible";
			      $publicado="1";
			      $impuesto="taxable";
			      $stock=$row['EXISTENCIA'];
			      if ($stock==0) {
			      	$inventario=0;
			      }else{
			      	$inventario=1;
			      }
			      $potencia=$row["potencia"];
			      $potencia=$potencia*100;
			      $precio=$row["VENTA"]+$potencia;
			      $precio_woo=$row["Precio"];
			      $t_cat=substr($sku,0,1);
			      if ($t_cat=='1') {
			      	$cat='Volkswagen';
			      }elseif ($t_cat=='2') {
			      	$cat='Daihatsu';
			      }elseif ($t_cat=='3') {
			      	$cat='Accesorios Empi';
			      }elseif ($t_cat=='4') {
			      	$cat='Repuestos';
			      }elseif ($t_cat=='9') {
			      	$cat='Accesorios';
			      }

                if ($inventario==0 || $precio!=$precio_woo) {
                    echo utf8_decode("<tr>
			 			<td>".$sku."</td> 
			 			<td>".$tipo."</td> 
			 			<td>".$nombre."</td>
			 			<td>".$publicado."</td>
			 			<td>".$visibilidad."</td>
			 			<td>".$descripcion."</td>
			 			<td>".$impuesto."</td>
			 			<td>".$inventario."</td>
			 			<td>".$precio."</td>
			 			<td>".$cat."</td>
			 			
			 		</tr>");

                }

			 	
			}


			echo "</table>";



