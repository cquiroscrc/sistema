<?php
session_start();
    
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");
$cuenta='';
$guion='';
if(isset($_REQUEST['fini']) && !empty($_REQUEST['fini'])){
          $fini = $_REQUEST['fini'];
}
if(isset($_REQUEST['ffin']) && !empty($_REQUEST['ffin'])){
    $ffin = $_REQUEST['ffin'];
}
if(isset($_REQUEST['tipor']) && !empty($_REQUEST['tipor'])){
    $tipor = $_REQUEST['tipor'];
}
if(isset($_REQUEST['cuenta']) && !empty($_REQUEST['cuenta'])){
    $cuenta = $_REQUEST['cuenta'];
    $guion="-";
}

			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = 'REPORTE-'.$tipor.$guion.$cuenta.'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

            if ($tipor=="VENTAS") {
                $sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fini 00:00:00.000' AND '$ffin 23:59:59.000'  order by nfac";
                echo "<p style='font-weight:bold; border:1px solid #eee;'>Reporte de Ventas del ".$fini." al ".$ffin."</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold;'>Estado</td> 
					<td style='font-weight:bold; '>Factura</td> 
					<td style='font-weight:bold; '>Fecha</td> 
					<td style='font-weight:bold; '>Subtotal</td>
					<td style='font-weight:bold; '>Impuesto</td>
					<td style='font-weight:bold; '>Descuento</td>
					<td style='font-weight:bold; '>Total</td>
					<td style='font-weight:bold; '>Cliente</td>
					</tr>");


                $varsubtotal=0; $varimpuesto=0; $vardescuento=0; $vartotal=0;
                $varsubtotalC=0; $varimpuestoC=0; $vardescuentoC=0; $vartotalC=0;
                $varmensajeria=0; $tempmensa=0; $tempmensaC=0; $totalrecibos=0;
                $totalegresos=0; $totalingresos=0; $totaldepositos=0; $totaldif=0;

                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $row) {

                    $estado=$row['estado'];
                    $nfac=$row['nfac'];
                    $fechafac=$row['fechafact'];
                    $subtotal=$row['subtotal'];
                    $impuesto=$row['impuesto'];
                    $descuento =$row['descuento'];
                    $total =$row['total'];
                    $proveedor =$row['proveedor'];
                    $tipo =$row['Tipo'];
                    if ($tipo=='0') {
                        $varsubtotal = $varsubtotal + $subtotal;
                        $varimpuesto = $varimpuesto + $impuesto;
                        $vardescuento = $vardescuento + $descuento;
                        $vartotal= $vartotal + $total;
                        $temptotal=$varsubtotal+$varimpuesto-$vardescuento;
                        $tempmensa=$vartotal-$temptotal;
                    }else{
                        $varsubtotalC = $varsubtotalC + $subtotal;
                        $varimpuestoC = $varimpuestoC + $impuesto;
                        $vardescuentoC = $vardescuentoC + $descuento;
                        $vartotalC= $vartotalC + $total;
                        $temptotalC=$varsubtotalC+$varimpuestoC-$vardescuentoC;
                        $tempmensaC=$vartotalC-$temptotalC;
                    }
                    echo utf8_decode("<tr>
			 			<td>".$estado."</td> 
			 			<td>".$nfac."</td> 
			 			<td>".$fechafac."</td>
			 			<td>".number_format($subtotal,2)."</td>
			 			<td>".number_format($impuesto,2)."</td>
			 			<td>".number_format($descuento,2)."</td>
			 			<td>".number_format($total,2)."</td>
			 			<td>".$proveedor."</td>
			 		</tr>");
                }


                echo "</table>";
                echo "<p></p>";
                echo "<table>";
                echo "<tr>";
                echo "<td style='font-weight:bold; '>Total Ventas Contado</td></tr>";
                echo "<tr><td style='font-weight:bold; '>Subtotal:</td>";
                echo "<td style='font-weight:bold;'>".number_format($varsubtotal,2)."</td></tr>";
                echo "<tr><td style='font-weight:bold; '>Descuento:</td>";
                echo "<td style='font-weight:bold;'>".number_format($vardescuento,2)."</td></tr>";
                echo "<tr><td style='font-weight:bold; '>Impuesto:</td>";
                echo "<td style='font-weight:bold;'>".number_format($varimpuesto,2)."</td></tr>";
                echo "<tr><td style='font-weight:bold; '>Total:</td>";
                echo "<td style='font-weight:bold;'>".number_format($vartotal,2)."</td></tr>";
                echo "<tr></tr>";
                echo "<td style='font-weight:bold;'>Total Ventas Credito</td></tr>";
                echo "<tr><td style='font-weight:bold;'>Subtotal:</td>";
                echo "<td style='font-weight: bold;'>".number_format($varsubtotalC,2)."</td></tr>";
                echo "<tr><td style='font-weight:bold;'>Descuento:</td>";
                echo "<td style='font-weight: bold'>".number_format($vardescuentoC,2)."</td></tr>";
                echo "<tr><td style='font-weight:bold;'>Impuesto:</td>";
                echo "<td style='font-weight: bold'>".number_format($varimpuestoC,2)."</td></tr>";
                echo "<tr><td style='font-weight:bold; '>Total:</td>";
                echo "<td style='font-weight:bold; '>".number_format($vartotalC,2)."</td></tr>";

            }
            if ($tipor=="COMPRAS") {
                $sql="SELECT c.Factura,c.proveedor,c.fecha,c.tipo,c.subtotal,c.impuesto,c.descuento,c.total, p.nombre FROM 
                    compras c, Proveedores p WHERE fecha BETWEEN '$fini 00:00:00.000' AND '$ffin 23:59:59.000'  
                    AND c.proveedor =  p.codigo order by proveedor,fecha";

                echo "<p style='font-weight:bold; border:1px solid #eee;'>Reporte de Compras del ".$fini." al ".$ffin."</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold;'>Fecha</td> 
					<td style='font-weight:bold;'>Factura</td> 
					<td style='font-weight:bold;'>Subtotal</td> 
					<td style='font-weight:bold;'>Impuesto</td>
					<td style='font-weight:bold;'>Descuento</td>
					<td style='font-weight:bold;'>Total</td>
					<td style='font-weight:bold;'>Tipo</td>
					<td style='font-weight:bold;'>Proveedor</td>
					<td style='font-weight:bold;'>Nombre</td>
					</tr>");
                $parcial_subtotal=0;
                $parcial_impuesto=0;
                $parcial_descuento=0;
                $parcial_total=0;
                $varproveeedor='';
                $varnombre='';
                $varsubtotal =0;
                $varimpuesto =0;
                $vardescuento =0;
                $vartotal =0;

                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    if ($varproveeedor=='') {
                        $varproveeedor=$arr['proveedor'];
                        $varnombre=$arr['nombre'];
                    }
                    if ($varproveeedor!=$arr['proveedor']) {
                        echo '<th><b>Totales</b></th><th></th><th><b>'.number_format($parcial_subtotal,2).'</th>
                        <th><b>'.number_format($parcial_impuesto,2).'</th><th><b>'.number_format($parcial_descuento,2).'</th>
                        <th><b>'.number_format($parcial_total,2).'</th><th></th><th><b>'.$varproveeedor.'</th><th><b>'.$varnombre.'</th></tr>';
                        $varproveeedor=$arr['proveedor'];
                        $varnombre=$arr['nombre'];
                        $parcial_subtotal=0;
                        $parcial_impuesto=0;
                        $parcial_descuento=0;
                        $parcial_total=0;
                    }
                    $fecha_sis=strtotime($arr['fecha']);
                    echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
                    echo "<td>".$arr["Factura"]."</td>";
                    echo "<td>".number_format($arr["subtotal"],2)."</td>";
                    $parcial_subtotal=$parcial_subtotal+ $arr['subtotal'];
                    $varsubtotal = $varsubtotal + $arr["subtotal"];
                    echo "<td>".number_format($arr["impuesto"],2)."</td>";
                    $parcial_impuesto=$parcial_impuesto+$arr['impuesto'];
                    $varimpuesto = $varimpuesto + $arr["impuesto"];
                    echo "<td>".number_format($arr["descuento"],2)."</td>";
                    $parcial_descuento=$parcial_descuento+$arr['descuento'];
                    $vardescuento = $vardescuento + $arr["descuento"];
                    echo "<td>".number_format($arr["total"],2)."</td>";
                    $parcial_total=$parcial_total+$arr['total'];
                    $vartotal= $vartotal + $arr["total"];
                    echo "<td>".$arr["tipo"]."</td>";
                    echo "<td>".$arr["proveedor"]."</td>";
                    echo "<td>".$arr["nombre"]."</td>";
                    echo "</tr> ";
                }
                echo '<th><b>Totales</th><th></th><th><b>'.number_format($parcial_subtotal,2).'</th>
                <th><b>'.number_format($parcial_impuesto,2).'</th><th><b>'.number_format($parcial_descuento,2).'</th>
                <th><b>'.number_format($parcial_total,2).'</th><th></th><th><b>'.$varproveeedor.'</th><th><b>'.$varnombre.'</th></tr>';
                $fecha11=strtotime($fini);
                $fecha22=strtotime($ffin);
                echo "<td><b> Fecha:</td><td><b>".date("d-m-Y", $fecha11)." --- ".date("d-m-Y", $fecha22)."</td>
                <td><b>Subtotal: ".number_format($varsubtotal,2)."</td><td><b>Impuesto: ".number_format($varimpuesto,2)."</td>
                <td><b>Descuento: ".number_format($vardescuento,2)."</td><td><b>Total: ".number_format($vartotal,2)."</td><td>
                </td><td></td><td></td></tr>" ;
                echo '</table>';
            }
            if ($tipor=="CONTABLE") {
                $sql="SELECT * FROM cierrediario WHERE Fecha BETWEEN '$fini 00:00:00.000' AND '$ffin 23:59:59.000'";

                echo "<p style='font-weight:bold; border:1px solid #eee;'>Reporte Contable del ".$fini." al ".$ffin."</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Fecha</td> 
					<td style='font-weight:bold; '>Ventas Contado</td> 
					<td style='font-weight:bold; '>Ventas Crédito</td> 
					<td style='font-weight:bold; '>Recibos</td>
					<td style='font-weight:bold; '>Adelantos</td>
					<td style='font-weight:bold; '>Repuestos</td>
					<td style='font-weight:bold; '>Papelería</td>
					<td style='font-weight:bold; '>Empaque</td>
					<td style='font-weight:bold; '>Parqueo</td>
					<td style='font-weight:bold; '>Combustibles</td>
					<td style='font-weight:bold; '>Atención</td>
					<td style='font-weight:bold; '>Encomiendas</td>
					<td style='font-weight:bold; '>Otros</td>
					<td style='font-weight:bold; '>Tarjetas</td>
					<td style='font-weight:bold; '>Caja</td>
					<td style='font-weight:bold; '>Depósitos</td>
					<td style='font-weight:bold; '>Diferencia</td>
					</tr>");
                $varsubtotal =0;
                $varimpuesto =0;
                $vardescuento =0;
                $vartotal =0;
                $contado=0;
                $credito=0;
                $recibos=0;
                $descuentos=0;
                $gasolina=0;
                $empaque=0;
                $repuestos=0;
                $papeleria=0;
                $combustible=0;
                $atencion=0;
                $encomiendas=0;
                $otros=0;
                $alfaro=0;
                $adelanto=0;
                $caja=0;
                $depositos=0;
                $diferencia=0;

                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    $fecha_sis=strtotime($arr['Fecha']);
                    echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
                    echo "<td>".number_format($arr["I_VentasEfectivo"],2)."</td>";
                    $contado=$contado+$arr["I_VentasEfectivo"];
                    echo "<td>".number_format($arr["I_VentasCredito"],2)."</td>";
                    $credito=$credito+$arr["I_VentasCredito"];
                    echo "<td>".number_format($arr["I_RecibosDinero"],2)."</td>";
                    $recibos=$recibos+$arr["I_RecibosDinero"];
                    echo "<td>".number_format($arr["E_Descuentos"],2)."</td>";
                    $descuentos=$descuentos+$arr["E_Descuentos"];
                    echo "<td>".number_format($arr["E_Repuestos"],2)."</td>";
                    $repuestos=$repuestos+$arr["E_Repuestos"];
                    echo "<td>".number_format($arr["E_Papeleria"],2)."</td>";
                    $papeleria=$papeleria+$arr["E_Papeleria"];
                    echo "<td>".number_format($arr["E_Luz"],2)."</td>";
                    $empaque=$empaque+$arr["E_Luz"];
                    echo "<td>".number_format($arr["E_Combustible"],2)."</td>";
                    $combustible=$combustible+$arr["E_Combustible"];
                    echo "<td>".number_format($arr["E_Agua"],2)."</td>";
                    $gasolina=$gasolina+$arr["E_Agua"];
                    echo "<td>".number_format($arr["E_AtencionPublico"],2)."</td>";
                    $atencion=$atencion+$arr["E_AtencionPublico"];
                    echo "<td>".number_format($arr["E_Encomiendas"],2)."</td>";
                    $encomiendas=$encomiendas+$arr["E_Encomiendas"];
                    echo "<td>".number_format($arr["E_Otros"],2)."</td>";
                    $otros=$otros+$arr["E_Otros"];
                    echo "<td>".number_format($arr["E_Alfaro"],2)."</td>";
                    $alfaro=$alfaro+$arr["E_Alfaro"];
                    echo "<td>".number_format($arr["T_Caja"],2)."</td>";
                    $caja=$caja+$arr["T_Caja"];
                    echo "<td>".number_format($arr["T_Depositos"],2)."</td>";
                    $depositos=$depositos+$arr["T_Depositos"];
                    if ($arr["T_FaltaSobra"]>0) {
                        echo "<td style='color:green'><b>".number_format($arr["T_FaltaSobra"],2)."</td>";
                    }else{
                        echo "<td style='color:red'><b>".number_format($arr["T_FaltaSobra"],2)."</td>";
                    }


                    $diferencia=$diferencia+$arr['T_FaltaSobra'];
                    echo "</tr> ";
                }
                echo '<th><b>Totales</th><th><b>'.number_format($contado,2).'</th><th><b>'.number_format($credito,2).'</th>
                <th><b>'.number_format($recibos,2).'</th><th><b>'.number_format($descuentos,2).'</th><th><b>'.number_format($repuestos,2).'</th>
                <th><b>'.number_format($papeleria,2).'</th><th><b>'.number_format($empaque,2).'</th><th><b>'.number_format($combustible,2).'</th><th><b>'.number_format($gasolina,2).'</th><th><b>'.number_format($atencion,2).'</th>
                <th><b>'.number_format($encomiendas,2).'</th><th><b>'.number_format($otros,2).'</th><th><b>'.number_format($alfaro,2).'</th>
                <th><b>'.number_format($caja,2).'</th><th><b>'.number_format($depositos,2).'</th><th><b>'.number_format($diferencia,2).'</th></tr>';
                echo '</table>';

            }
            if ($tipor=="FINANCIERO") {
                $sql = "SELECT TOP 12 * from tbl_Financiero ORDER BY Fecha DESC";

                //***** Ingresos / Egresos ****

                echo "<p style='font-weight:bold; border:1px solid #eee;'>Reporte Financiero</p>
                <br>
                <p style='font-weight:bold; border:1px solid #eee;'>Ingresos / Egresos</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Fecha</td> 
					<td style='font-weight:bold; '>Ventas</td> 
					<td style='font-weight:bold; '>Descuento</td> 
					<td style='font-weight:bold; '>Total Ventas</td>
					<td style='font-weight:bold; '>C. Locales</td>
					<td style='font-weight:bold; '>C. Exteriores</td>
					<td style='font-weight:bold; '>T.Compras</td>
					<td style='font-weight:bold; '>Gastos</td>
					<td style='font-weight:bold; '>G. Fijos</td>
					<td style='font-weight:bold; '>Total</td>
					<td style='font-weight:bold; '>Diferencia</td>
					</tr>");
                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    $fecha = strtotime($arr['Fecha']);
                    $ventas = $arr['I_Ventas'];
                    $descuento = $arr['I_Descuentos'];
                    $totalventas = $arr['I_TotalVentas'];
                    $diferencia = $arr['Diferencia'];
                    $local = $arr['E_ComprasL'];
                    $exterior = $arr['E_ComprasE'];
                    $tcompras=$arr["E_TotCompras"];
                    $gastos = $arr['Gastos'];
                    $gastosfijos = $arr['GastosFijos'];
                    $tegresos = $arr['TotalEgresos'];

                    echo '<tr>';
                    echo '<td>'.date("d-m-Y", $fecha).'</td><td>'.number_format($ventas,2).'</td>
                    <td>'.number_format($descuento,2).'</td><td>'.number_format($totalventas,2).'</td>
                    <td>'.number_format($local,2).'</td><td>'.number_format($exterior,2).'</td>
                    <td>'.number_format($tcompras,2).'</td><td>'.number_format($gastos,2).'</td>
                    <td>'.number_format($gastosfijos,2).'</td><td>'.number_format($tegresos,2).'</td>
                    <td>'.number_format($diferencia,2).'</td>';
                }
                echo "</table>";
                echo "<p></p>";
            //***** Inventario ****
                echo "<p style='font-weight:bold; border:1px solid #eee;'>Inventario</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";
                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Fecha</td> 
					<td style='font-weight:bold; '>Volkswagen</td> 
					<td style='font-weight:bold; '>C. VW</td> 
					<td style='font-weight:bold; '>Volvo</td>
					<td style='font-weight:bold; '>C. Vol</td>
					<td style='font-weight:bold; '>Daihatsu</td>
					<td style='font-weight:bold; '>C. Dai</td>
					<td style='font-weight:bold; '>Accesorios</td>
					<td style='font-weight:bold; '>C. Acces</td>
					</tr>");
                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    $fecha = strtotime($arr['Fecha']);
                    $tcostoVW = $arr['T_CostoVW'];
                    $texisVW = $arr['T_ExisVW'];
                    $tcostoVV = $arr['T_CostoVV'];
                    $texisVV = $arr['T_ExisVV'];
                    $tcostoDH = $arr['T_CostoDH'];
                    $texisDH = $arr['T_ExisDH'];
                    $tcostoAC = $arr['T_CostoAC'];
                    $texisAC = $arr['T_ExisAC'];
                    echo '<tr>';
                    echo '<td>'.date("d-m-Y", $fecha).'</td><td>'.number_format($tcostoVW,2).'</td><td>'.number_format($texisVW,2).'</td>
                    <td>'.number_format($tcostoVV,2).'</td><td>'.number_format($texisVV,2).'</td><td>'.number_format($tcostoDH,2).'</td>
                    <td>'.number_format($texisDH,2).'</td><td>'.number_format($tcostoAC,2).'</td><td>'.number_format($texisAC,2).'</td></tr>';
                }
                echo '</table>';
                echo "<p></p>";
                //**** Ventas ****
                echo "<p style='font-weight:bold; border:1px solid #eee;'>Inventario de Ventas 1</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";
                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Fecha</td> 
					<td style='font-weight:bold; '>C. VW Local</td> 
					<td style='font-weight:bold; '>M. VW Local</td> 
					<td style='font-weight:bold; '>C. VW Ext</td>
					<td style='font-weight:bold; '>M. VW Ext</td>
					<td style='font-weight:bold; '>C. Vol Local</td> 
					<td style='font-weight:bold; '>M. Vol Local</td> 
					<td style='font-weight:bold; '>C. Vol Ext</td>
					<td style='font-weight:bold; '>M. Vol Ext</td>
					</tr>");
                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    $fecha = strtotime($arr['Fecha']);
                    $CanVenLVW = $arr['T_VenCanLVW'];
                    $CanVenEVW = $arr['T_VenCanEVW'];
                    $MVenLVW = $arr['T_VenMonLVW'];
                    $MVenEVW = $arr['T_VenMonEVW'];
                    $CanVenLVV = $arr['T_VenCanLVV'];
                    $CanVenEVV = $arr['T_VenCanEVV'];
                    $MVenLVV = $arr['T_VenMonLVV'];
                    $MVenEVV = $arr['T_VenMonEVV'];

                    echo '<tr>';
                    echo '<td>'.date("d-m-Y", $fecha).'</td><td>'.number_format($CanVenLVW,2).'</td><td>'.number_format($MVenLVW,2).'</td>
                    <td>'.number_format($CanVenEVW,2).'</td><td>'.number_format($MVenEVW,2).'</td><td>'.number_format($CanVenLVV,2).'</td>
                    <td>'.number_format($MVenLVV,2).'</td><td>'.number_format($CanVenEVV,2).'</td><td>'.number_format($MVenEVV,2).'</td>';
                }
                echo '</table>';
                echo "<p></p>";
                echo "<p style='font-weight:bold; border:1px solid #eee;'>Inventario de Ventas 2</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";
                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Fecha</td> 
					<td style='font-weight:bold; '>C. DH Local</td> 
					<td style='font-weight:bold; '>M. DH Local</td> 
					<td style='font-weight:bold; '>C. DH Ext</td>
					<td style='font-weight:bold; '>M. DH Ext</td>
					<td style='font-weight:bold; '>C. ACC Local</td> 
					<td style='font-weight:bold; '>M. ACC Local</td> 
					<td style='font-weight:bold; '>C. ACC Ext</td>
					<td style='font-weight:bold; '>M. ACC Ext</td>
					</tr>");
                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    $fecha = strtotime($arr['Fecha']);
                    $CanVenLDH = $arr['T_VenCanLDH'];
                    $CanVenEDH = $arr['T_VenCanEDH'];
                    $MVenLDH = $arr['T_VenMonLDH'];
                    $MVenEDH = $arr['T_VenMonEDH'];
                    $CanVenLAC = $arr['T_VenCanLAC'];
                    $CanVenEAC = $arr['T_VenCanEAC'];
                    $MVenLAC = $arr['T_VenMonLAC'];
                    $MVenEAC = $arr['T_VenMonEAC'];

                    echo '<tr>';
                    echo '<td>'.date("d-m-Y", $fecha).'<td>'.number_format($CanVenLDH,2).'</td><td>'.number_format($MVenLDH,2).'</td>
                    <td>'.number_format($CanVenEDH,2).'</td><td>'.number_format($MVenEDH,2).'</td><td>'.number_format($CanVenLAC,2).'</td>
                    <td>'.number_format($MVenLAC,2).'</td><td>'.number_format($CanVenEAC,2).'</td><td>'.number_format($MVenEAC,2).'</td>';
                }
                echo '</table>';
                echo "<p></p>";
                //***** Gastos ****
                echo "<p style='font-weight:bold; border:1px solid #eee;'>Gastos</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";
                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Fecha</td> 
					<td style='font-weight:bold; '>Salarios</td> 
					<td style='font-weight:bold; '>Alquiler/td> 
					<td style='font-weight:bold; '>C.C.S.S</td>
					<td style='font-weight:bold; '>Tributación</td>
					<td style='font-weight:bold; '>Agua</td>
					<td style='font-weight:bold; '>Luz</td>
					<td style='font-weight:bold; '>Telefono</td>
					<td style='font-weight:bold; '>Reserva</td>
					<td style='font-weight:bold; '>C. Financiero</td>
					<td style='font-weight:bold; '>Total</td>
					</tr>");
                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    $fecha = strtotime($arr['Fecha']);
                    $salario = $arr['GF_Salarios'];
                    $alquiler = $arr['GF_Alquiler'];
                    $ccss = $arr['GF_CCSS'];
                    $tributacion = $arr['GF_Tributacion'];
                    $agua = $arr['GF_Agua'];
                    $luz = $arr['GF_Luz'];
                    $telefono = $arr['GF_Telefono'];

                    $financiero= $arr['GF_CostoFinancieroP'];
                    $subtotal=$salario+$alquiler+$ccss+$tributacion+$agua+$luz+$telefono;

                    $c_financiero=($subtotal*$financiero)/100;

                    $total = ($subtotal+$c_financiero)/12;
                    $g_total=$subtotal+$c_financiero+$total;

                    echo '<tr>';
                    echo '<td>'.date("d-m-Y", $fecha).'<td>'.number_format($salario,2).'</td><td>'.number_format($alquiler,2).'</td>
                    <td>'.number_format($ccss,2).'</td><td>'.number_format($tributacion,2).'</td><td>'.number_format($agua,2).'</td>
                    <td>'.number_format($luz,2).'</td><td>'.number_format($telefono,2).'</td><td>'.number_format($total,2).'</td>
                    <td>'.number_format($c_financiero,2).'</td><td>'.number_format($g_total,2).'</td>';
                }
                echo '</table>';
                echo "<p></p>";

            }
            if ($tipor=="BANCOS") {
                
                $sql="SELECT b.Fecha,b.Cuenta,b.Documento,b.Descripcion,b.Credito,b.Debito, c.Banco FROM 
                tbl_Bancos b, tbl_Cuentas c WHERE b.Cuenta ='$cuenta' AND b.Fecha BETWEEN '$fini 00:00:00.000' AND 
                '$ffin 23:59:59.000' AND b.Cuenta= c.Cuenta ORDER BY b.Fecha,b.Descripcion";

                echo "<p style='font-weight:bold; border:1px solid #eee;'>Reporte de Bancos del ".$fini." al ".$ffin."</p>
                <br>
                <p style='font-weight:bold; border:1px solid #eee;'>Cuenta ".$cuenta."</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold;'>Fecha</td> 
					<td style='font-weight:bold;'>Cuenta</td> 
					<td style='font-weight:bold;'>Nombre</td> 
					<td style='font-weight:bold;'>Documento</td>
					<td style='font-weight:bold;'>Descripción</td>
					<td style='font-weight:bold;'>Credito</td>
					<td style='font-weight:bold;'>Debito</td>
					
					</tr>");
                $credito=0;
                $debito=0;
                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    $fecha_sis=strtotime($arr['Fecha']);
                    echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
                    echo "<td>".$arr['Cuenta']."</td>";
                    echo "<td>".$arr['Banco']."</td>";
                    echo "<td>".$arr['Documento']."</td>";
                    echo utf8_decode("<td>".$arr['Descripcion']."</td>");
                    if ($arr['Credito']>0) {
                        echo "<td style='color:blue'>".number_format($arr['Credito'],2)."</td>";
                    }else{
                        echo "<td>".number_format($arr['Credito'],2)."</td>";
                    }
                    if ($arr['Debito']>0) {
                        echo "<td style='color:red'>".number_format($arr['Debito'],2)."</td>";
                    }else{
                        echo "<td>".number_format($arr['Debito'],2)."</td>";
                    }
                    $credito=$credito+$arr['Credito'];
                    $vdocumento=$arr['Documento'];
                    $vcuenta=$arr['Cuenta'];
                    $debito=$debito+$arr['Debito'];
                    echo "</tr> ";
                }
                echo "<th></th> ";
                echo "<th></th>";
                echo "<th></th>";
                echo "<th></th>";
                echo "<th></th>";
                echo "<th>".number_format($credito,2)."</th>";
                echo "<th>".number_format($debito,2)."</th>";
                echo "</tr> ";
                echo '</table>';
            }
            if ($tipor=="EGRESOS") {
                $sql="SELECT e.Tipo, e.Documento, e.Fecha, e.Monto, e.Subtotal, e.IVA, e.Descripcion, t.Descripcion as descripciontipo FROM
                tbl_Egresos e, tbl_TiposEgresos t WHERE e.Fecha BETWEEN '$fini 00:00:00.000' AND '$ffin 23:59:59.000' AND 
                e.Tipo= t.Tipo ORDER BY e.Tipo";

                echo "<p style='font-weight:bold; border:1px solid #eee;'>Reporte de egresos del ".$fini." al ".$ffin."</p>
                <br>
                <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Fecha</td> 
					<td style='font-weight:bold; '>Documento</td> 
					<td style='font-weight:bold; '>Monto</td>
					<td style='font-weight:bold; '>Subtotal</td> 
					<td style='font-weight:bold; '>IVA</td>
					<td style='font-weight:bold; '>Descripción</td>
					<td style='font-weight:bold; '>Tipo</td>
					<td style='font-weight:bold; '>Egreso</td>
					</tr>");

                $vartipo='';
                $varnombre='';
                $monto=0;
                $submonto=0;
                $montoparcial=0;
                $montototal=0;
                $subtotalparcial=0;
                $subtotaltotal=0;
                $ivamonto=0;
                $ivaparcial=0;
                $ivatotal=0;
                $excento=0;

                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    if ($vartipo=='') {
                        $varnombre=$arr['descripciontipo'];
                        $vartipo=$arr['Tipo'];
                    }

                    if ($vartipo!=$arr['Tipo']) {
                        echo "<th></th> ";
                        echo "<th></th>";
                        echo "<th>".number_format($montoparcial,2)."</th>";
                        echo "<th>".number_format($subtotalparcial,2)."</th>";
                        echo "<th>".number_format($ivaparcial,2)."</th>";
                        echo "<th></th>";
                        echo "<th>".$vartipo."</th>";
                        echo "<th>".$varnombre."</th>";
                        echo "</tr> ";
                        $montototal=$montototal+$montoparcial;
                        $subtotaltotal=$subtotaltotal+$subtotalparcial;
                        $ivatotal=$ivatotal+$ivaparcial;
                        $montoparcial=0;
                        $subtotalparcial=0;
                        $ivaparcial=0;
                        $varnombre=$arr['descripciontipo'];
                        $vartipo=$arr['Tipo'];
                    }

                    $fecha_sis=strtotime($arr['Fecha']);
                    echo "<td>".date("d-m-Y", $fecha_sis)."</td> ";
                    echo "<td>".$arr['Documento']."</td>";
                    echo "<td>".number_format($arr['Monto'],2)."</td>";
                    echo "<td>".number_format($arr['Subtotal'],2)."</td>";
                    echo "<td>".number_format($arr['IVA'],2)."</td>";
                    $monto=$monto+$arr['Monto'];
                    $submonto=$submonto+$arr['Subtotal'];
                    $ivamonto=$ivamonto+$arr['IVA'];
                    $montoparcial=$montoparcial+$arr['Monto'];
                    $subtotalparcial=$subtotalparcial+$arr['Subtotal'];
                    $ivaparcial=$ivaparcial+$arr['IVA'];
                    if ($arr['IVA']<>0){
                        $excento=$excento+$arr['Monto'];
                    }

                    echo "<td>".$arr['Descripcion']."</td>";
                    echo "<td>".$arr['Tipo']."</td>";
                    echo "<td>".$arr['descripciontipo']."</td>";
                    echo "</tr> ";
                }
                echo "<th></th> ";
                echo "<th></th>";
                echo "<th>".number_format($montoparcial,2)."</th>";
                echo "<th>".number_format($subtotalparcial,2)."</th>";
                echo "<th>".number_format($ivaparcial,2)."</th>";
                echo "<th></th>";
                echo "<th>".$vartipo."</th>";
                echo "<th>".$varnombre."</th>";
                echo "</tr> ";
                $montototal=$montototal+$montoparcial;
                $subtotaltotal=$subtotaltotal+$subtotalparcial;
                $ivatotal=$ivatotal+$ivaparcial;
                $montoparcial=0;
                $subtotalparcial=0;
                $ivaparcial=0;

                echo '</table>';
                echo '<p></p>';
                echo '<table>';
                echo "<th style='font-weight:bold; border:1px solid #eee;'>Totales</th>";
                echo "<tr><th style='font-weight:bold; border:1px solid #eee;'>Total:</th><td style=' border:1px solid #eee;'>".number_format($excento,2)."</td></tr>";
                echo "<tr><th style='font-weight:bold; border:1px solid #eee;'>Subtotal:</th><td style=' border:1px solid #eee;'>".number_format($subtotaltotal,2)."</td></tr>";
                echo "<tr><th style='font-weight:bold; border:1px solid #eee;'>IVA</th><td style=' border:1px solid #eee;'>".number_format($ivatotal,2)."</td></tr>";
                $formula=$montototal-$excento;
                echo "<tr><th style='font-weight:bold; border:1px solid #eee;'>Excento</th><td style=' border:1px solid #eee;'>".number_format($formula,2)."</td></tr>";
                echo "<tr><th style='font-weight:bold; border:1px solid #eee;'>Total Egresos</th><td style=' border:1px solid #eee;'>".number_format($montototal,2)."</td></tr>";
                echo '</table>';
            }
            if ($tipor=="D151") {
                $sql="SELECT TOP 20 proveedor,sum(subtotal) as Subtotal,sum(descuento) as Descuento from hfacturas where (fechafact BETWEEN
                '".$fini." 00:00:00.000'AND '".$ffin." 23:59:59.000') AND proveedor <> 0 GROUP BY proveedor ORDER BY Subtotal desc" ;

                echo "<p style='font-weight:bold; border:1px solid #eee;'>Reporte ventas del ".$fini." al ".$ffin."</p>
                <br>
                <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Cliente</td> 
					<td style='font-weight:bold; '>Nombre</td> 
					<td style='font-weight:bold; '>Cedula</td> 
					<td style='font-weight:bold; '>Subtotal</td>
					<td style='font-weight:bold; '>Descuento</td>
					<td style='font-weight:bold; '>Total</td>
					</tr>");

                $TSubtotal=0;
                $TDescuentos=0;
                $TTotal=0;
                $total_porcentaje=0;

                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    /// Totales para sacar cuentas
                    $subtotal=$arr["Subtotal"];
                    $descuento=$arr["Descuento"];
                    $proveedor=$arr["proveedor"];
                    echo "<td>".$proveedor."</td> ";
                    // Cargar nombre
                    $sql_cl="SELECT * FROM clientes WHERE codigo='".$proveedor."'" ;

                    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cl);
                    foreach ($respuesta as $key => $arr_cl) {
                        $nombre=$arr_cl["nombre"];
                        $cedula=$arr_cl["cedula"];
                    }
                    //echo "<td>".$arr["nombre"]."</td>";
                    echo "<td>".$nombre."</td>";
                    echo "<td>".$cedula."</td>";
                    // volver
                    echo "<td>".number_format($subtotal,2)."</td>";
                    $TSubtotal = $TSubtotal + $subtotal;
                    echo "<td>".number_format($descuento,2)."</td>";
                    $TDescuentos = $TDescuentos + $descuento;
                    $total = $subtotal - $descuento;
                    echo "<td>".number_format($total,2)."</td>";
                    echo "</tr> ";
                }
                echo '</table>';
                echo "<p></p>";

                $sql="SELECT proveedor,sum(subtotal) as Subtotal,sum(descuento) as Descuento from compras where (fecha BETWEEN
                '".$fini." 00:00:00.000'AND '".$ffin." 23:59:59.000') AND proveedor <> 0 GROUP BY proveedor ORDER BY Subtotal desc" ;

                echo "<p style='font-weight:bold; border:1px solid #eee;'>Reporte Compras del ".$fini." al ".$ffin."</p>
                <br>
                <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold; '>Proveedor</td> 
					<td style='font-weight:bold; '>Nombre</td> 
					<td style='font-weight:bold; '>Cedula</td> 
					<td style='font-weight:bold; '>Subtotal</td>
					<td style='font-weight:bold; '>Descuento</td>
					<td style='font-weight:bold; '>Total</td>
					</tr>");

                $TSubtotal=0;
                $TDescuentos=0;
                $TTotal=0;
                $total_porcentaje=0;

                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    /// Totales para sacar cuentas
                    $subtotal=$arr["Subtotal"];
                    $descuento=$arr["Descuento"];
                    $proveedor=$arr["proveedor"];
                    echo "<td>".$proveedor."</td> ";
                    // Cargar nombre
                    $sql_cl="SELECT * FROM Proveedores WHERE codigo='".$proveedor."'" ;

                    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cl);
                    foreach ($respuesta as $key => $arr_cl) {
                        $nombre=$arr_cl["nombre"];
                        $cedula=$arr_cl["comentarios"];
                    }
                    if ($proveedor=='1') {
                        $nombre="CONTADO";
                        $cedula="SIN CEDULA";
                    }
                    //echo "<td>".$arr["nombre"]."</td>";
                    echo "<td>".$nombre."</td>";
                    echo "<td>".$cedula."</td>";
                    // volver
                    echo "<td>".number_format($subtotal,2)."</td>";
                    $TSubtotal = $TSubtotal + $subtotal;
                    echo "<td>".number_format($descuento,2)."</td>";
                    $TDescuentos = $TDescuentos + $descuento;
                    $total = $subtotal - $descuento;
                    echo "<td>".number_format($total,2)."</td>";
                    echo "</tr> ";
                }
                echo '</table>';
                echo "<p></p>";
            }
            if ($tipor=="Cabys") {
                $sql="SELECT i.CODIGO,i.DESCRIPCION,i.EXISTENCIA,i.CODIGO_CABYS,c.Descripcion,
                substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1)
                WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
                WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
                Else i.CODIGO END As Filtro FROM inventario i, DE_CABYS c  WHERE i.CODIGO_CABYS = c.Codigo ORDER BY i.CODIGO_CABYS,Filtro";

                echo "<p style='font-weight:bold; border:1px solid #eee;'>Los Paticas -- Reporte Cabys</p>
                <br>
		        <table border='1' cellpadding='2' cellspacing='0' width='100%'>";

                echo utf8_decode("<tr> 
					<td style='font-weight:bold;'>Código</td> 
					<td style='font-weight:bold; '>Descripción</td> 
					<td style='font-weight:bold; '>Cantidad</td> 
					<td style='font-weight:bold; '>Cabys</td>
					<td style='font-weight:bold; '>Descripción Cabys</td>
					</tr>");

                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $row) {

                    $codigo=$row['CODIGO'];
                    $descripcion=$row['DESCRIPCION'];
                    $existencia=$row['EXISTENCIA'];
                    $cabys=$row['CODIGO_CABYS'];
                    $descripcioncabys=$row['Descripcion'];
                    $espacio='*';
                    echo utf8_decode("<tr>
			 			<td>".$codigo."</td> 
			 			<td>".$descripcion."</td> 
			 			<td>".number_format($existencia,2)."</td>
			 			<td>".$cabys.$espacio."</td>
			 			<td>".$descripcioncabys."</td>
			 			
			 		</tr>");
                }


            }
?>



