<?php
session_start();
    
     require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['pedidos']) && !empty($_REQUEST['pedidos'])){
          $pedido = $_REQUEST['pedidos'];          
}

			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = 'PEDIDO-'.$pedido.'.xls';

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
		<p style='font-weight:bold; border:1px solid #eee;'>Reporte de articulos para pedido</p>
		<br>
		<table border='1' cellpadding='2' cellspacing='0' width='100%'> 
		<?php 			
			echo utf8_decode("<tr> 
					<td style='font-weight:bold; border:1px solid #eee;'>ID</td> 
					<td style='font-weight:bold; border:1px solid #eee;'>Codigo Tai</td> 
					<td style='font-weight:bold; border:1px solid #eee;'>Codigo</td> 
					<td style='font-weight:bold; border:1px solid #eee;'>Tecnica</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Descripcion</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Cantidad</td>
                    <td style='font-weight:bold; border:1px solid #eee;'>Costo</td>					
					</tr>");
		
			 $sql="SELECT codigo,cantidad,pedido,substring(codigo,1,1),substring(codigo,6,3 ),substring(codigo,10,15),Case substring(codigo, 1, 1)
        WHEN '0' then substring(codigo,1,1)+substring(codigo,5,4 )+substring(codigo,9,4 )+' '+substring(codigo,1,4 )+' '+ltrim(rtrim(substring(codigo,13,12 )))
        WHEN '1' then substring(codigo,1,1)+substring(codigo,5,4 )+substring(codigo,9,4 )+' '+substring(codigo,1,4 )+' '+ltrim(rtrim(substring(codigo,13,12 )))
        Else codigo END As Filtro FROM hacer_pedido WHERE pedido='".$pedido."' ORDER BY Filtro";

    		$h=0;
    		$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    		foreach ($respuesta as $key => $row) {

    			  $p_pedido=$row['pedido'];
			      $p_codigo=$row['codigo'];
			      $p_cantidad=$row['cantidad'];
			      $largo=strlen($row['codigo']);
			      $inicio=substr($row['codigo'],0,1);
			      $final =substr($row['codigo'], -2,2);
			      if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW' ||$final=='ZL'){
			        $p_min_pedido=substr($row['codigo'],1,$largo-3);  
			      }else{
			        $p_min_pedido=substr($row['codigo'],1);  
			      }
			      

			      $sql="SELECT * FROM inventario WHERE codigo like '$p_codigo%'";    
			      $descripcion='CODIGO NUEVO';
			      $tecnico='';
			      $consulta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    			  foreach ($consulta as $key => $arr) {
				        $descripcion=$arr["DESCRIPCION"];
				        $costodol=$arr['costodol'];
				        $tecnico='';
				        $tecnica=$arr['DESC_ALT'];
				        $tecnico_inicio=strpos($tecnica,'(');
				        $tecnico_fin=  strpos($tecnica,')');
				        $tecnico=substr($tecnica,$tecnico_inicio,$tecnico_fin);
				        if ($tecnico==null) {
				          $tecnico='';
				        }

				    }
    	
			
			 	echo utf8_decode("<tr>
			 			<td>".$h."</td> 
			 			<td>".$p_codigo."</td> 
			 			<td>".$p_min_pedido."</td>
			 			<td>".$tecnico."</td>
			 			<td>".$descripcion."</td>
			 			<td>".number_format($p_cantidad,2)."</td>
			 			<td>".number_format($costodol,2)."</td>
			 		</tr>");
			 		$h++;
			}


			echo "</table>";



