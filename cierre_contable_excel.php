<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
        include "menu.php";
?>
<!DOCTYPE html>
<html>
<style>
#myProgress {
  position: relative;
  width: 100%;
  height: 30px;
  background-color: #ddd;
}

#myBar {
  position: absolute;
  width: 1%;
  height: 100%;
  background-color: #FF0000;
}
#label {
  text-align: center;
  line-height: 30px;
  color: white;
}
</style>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de Pedido</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      
      <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/signin.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <?php
      
    

error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set("America/Costa_Rica");

/** Include PHPExcel */
require_once ("Classes/PHPExcel.php");
include("db.php");




if(isset($_REQUEST['fecha1']) && !empty($_REQUEST['fecha1'])){
          $fecha1 = $_REQUEST['fecha1'];          
}

if(isset($_REQUEST['fecha2']) && !empty($_REQUEST['fecha2'])){
          $fecha2 = $_REQUEST['fecha2'];          
}


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Hernan Hernandez")
							 ->setLastModifiedBy("Hernan Hernandez")
							 ->setTitle("Reporte Contable")
							 ->setSubject("Reporte Contable")
							 ->setDescription("Reporte Contable")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Reportes");


// Add some data, we will use printing features
//$sql_pedido="SELECT * FROM hacer_pedido WHERE pedido='$pedido'";
/*
$sql_pedido="SELECT codigo,cantidad,pedido,
substring(codigo,1,1) as marca,substring(codigo,6,3 ) as centro,substring(codigo,10,15) as producto,substring(codigo, 2, 3) as carro,
Case substring(codigo, 1, 1)
WHEN '1' then CONCAT((SUBSTRING(codigo,1,1)),(SUBSTRING(codigo,5,4 )),(SUBSTRING(codigo,9,4 )),' ',(SUBSTRING(codigo,1,4 )),' ',(ltrim(rtrim(SUBSTRING(codigo,13,12 )))))
ELSE codigo END as FILTRO
FROM hacer_pedido WHERE pedido='".$pedido."' ORDER BY FILTRO";
*/
 $sql="SELECT * FROM cierrediario WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'";;  


//echo $sql_pedido;

if(($r_pedido = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
          

$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte contable entre '.$fecha1.' y '.$fecha2);
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('L3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('M3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('N3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('O3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Fecha');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Ventas Contado');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Ventas Credito');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Recibos');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Descuentos');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Repuestos');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Papeleria');
$objPHPExcel->getActiveSheet()->setCellValue('H3', 'Combustible');
$objPHPExcel->getActiveSheet()->setCellValue('I3', 'Atencion');
$objPHPExcel->getActiveSheet()->setCellValue('J3', 'Encomiendas');
$objPHPExcel->getActiveSheet()->setCellValue('K3', 'Otros');
$objPHPExcel->getActiveSheet()->setCellValue('L3', 'Alfaro');
$objPHPExcel->getActiveSheet()->setCellValue('M3', 'Caja');
$objPHPExcel->getActiveSheet()->setCellValue('N3', 'Depositos');
$objPHPExcel->getActiveSheet()->setCellValue('O3', 'Diferencias');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);

$i=4; 
$h=1;

$tcontado=0;
$tcredito=0;
$trecibos=0;
$tdescuentos=0;
$trepuestos=0;
$tpapeleria=0;
$tcombustible=0;
$tatencion=0;
$tencomiendas=0;
$tluz=0;
$tagua=0;
$ttelefono=0;
$totros=0;
$talfaro=0;
$ttcaja=0;
$ttdepositos=0;
$ttdiferencia=0;
      

    
while($row = odbc_fetch_array($r_pedido)){
      $fecha=$row['Fecha'];
      $icontado=$row['I_VentasEfectivo'];
      $icredito=$row['I_VentasCredito'];
      $irecibos=$row['I_RecibosDinero'];
      $edescuento=$row['E_Descuentos'];
      $erepuestos=$row['E_Repuestos'];
      $epapeleria=$row['E_Papeleria'];
      $ecombustible=$row['E_Combustible'];
      $eatencion=$row['E_AtencionPublico'];
      $eencomiendas=$row['E_Encomiendas'];
      $eluz=$row['E_Luz'];
      $eagua=$row['E_Agua'];
      $etelefono=$row['E_Telefono'];
      $eotros=$row['E_Otros'];
      $ealfaro=$row['E_Alfaro'];
      $tcaja=$row['T_Caja'];
      $tdepositos=$row['T_Depositos'];
      $tdiferencia=$row['T_FaltaSobra'];

      $tcontado=$tcontado+$icontado;
      $tcredito=$tcredito+$icredito;
      $trecibos=$trecibos+$irecibos;
      $tdescuentos=$tdescuentos+$edescuento;
      $trepuestos=$trepuestos+$erepuestos;
      $tpapeleria=$tpapeleria+$epapeleria;
      $tcombustible=$tcombustible+$ecombustible;
      $tatencion=$tatencion+$eatencion;
      $tencomiendas=$tencomiendas+$eencomiendas;
      $tluz=$tluz+$eluz;
      $tagua=$tagua+$eagua;
      $ttelefono=$ttelefono+$etelefono;
      $totros=$totros+$eotros;
      $talfaro=$talfaro+$ealfaro;
      $ttcaja=$ttcaja+$tcaja;
      $ttdepositos=$ttdepositos+$tdepositos;
      $ttdiferencia=$ttdiferencia+$tdiferencia;

      
      //echo $p_codigo;
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $fecha);
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $icontado);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $icredito);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $irecibos);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $edescuento);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $erepuestos);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $epapeleria);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $ecombustible);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $eatencion);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $eencomiendas);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $eotros);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $ealfaro);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $tcaja);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $tdepositos);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $tdiferencia);
			$i++;$h++;
	  

}

$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, 'Totales');
$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $tcontado);
$objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $tcredito);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $trecibos);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $tdescuentos);
$objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $trepuestos);
$objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $tpapeleria);
$objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $tcombustible);
$objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $tatencion);
$objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $tencomiendas);
$objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $totros);
$objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $talfaro);
$objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $ttcaja);
$objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $ttdepositos);
$objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $ttdiferencia);
$i++;$h++;
    
$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$i=$i-1;
$objPHPExcel->getActiveSheet()->getStyle('A3:O'.$i)->applyFromArray($styleArray);
unset($styleArray);

// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&G&C&HReporte Contable entre'.$fecha1.' y '.$fecha2);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

// Set page orientation and size
//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Contable');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Save Excel 2007 file

$callStartTime = microtime(true);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objWriter->save('reportes/contable.xls');
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
echo '<div id="myProgress">
  <div id="myBar">
  <div id="label">10%</div></div>
</div>';

echo '<br>';
echo '<div id="botones" style="display:none;">';
echo '<a href="cierre_contable.php?Date2='.$fecha2.'&Date1='.$fecha1.'"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a href="reportes/contable.xls"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
echo '</div>';

?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // S_Stock';
    var elem = document.getElementById("myBar");
    var width = 1;
    var id = setInterval(frame, 150);
    function frame() {
      if (width >= 100) {
        clearInterval(id);
        document.getElementById('myProgress').style.display = 'none';
        document.getElementById('botones').style.display = 'block';
      } else {
        width++;
        elem.style.width = width + '%';
        document.getElementById("label").innerHTML = width * 1  + '%';
      }
    }
</script> 
  
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Cierre contable';
</script> 
  <script src="js/bootstrap.min.js"></script> 
</body>
</html>