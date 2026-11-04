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
      <title>Resultado de Clientes</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
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

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

/** Include PHPExcel */
require_once ("Classes/PHPExcel.php");
require_once("db.php");


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Hernan Hernandez")
							 ->setLastModifiedBy("Hernan Hernandez")
							 ->setTitle("Reporte Reserva")
							 ->setSubject("Reporte Reserva")
							 ->setDescription("Reporte de clientes")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Reportes");


// Add some data, we will use printing features
//$sql_pedido="SELECT * FROM hacer_pedido WHERE pedido='$pedido'";

 $sql = "SELECT CODIGO, DESCRIPCION, EXISTENCIA, ubicacion,
    substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
    WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
    WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
    Else CODIGO END As Filtro FROM inventario WHERE reserva=1 order by Filtro
    ";
if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));


$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte Reservas');
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Codigo');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Descripcion');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Existencia');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Ubicacion');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(65);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$i=4; 
$h=1;
while($arr = odbc_fetch_array($resultado)){
       

     
  
  
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $arr['CODIGO']);
  	$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $arr['DESCRIPCION']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, number_format($arr['EXISTENCIA'],2));
  	$objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $arr['ubicacion']);
  	

  	$i++;$h++;
  	  
  
}

$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$i=$i-1;
$objPHPExcel->getActiveSheet()->getStyle('A3:D'.$i)->applyFromArray($styleArray);
unset($styleArray);

// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&G&C&HReporte de Productos en Reserva');
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

// Set page orientation and size
//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Productos en Reserva');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Save Excel 2007 file

$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objWriter->save('reportes/reserva.xlsx');
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo '<div id="myProgress">
  <div id="myBar">
  <div id="label">10%</div></div>
</div>';

echo '<br>';
echo '<div id="botones" style="display:none;">';
echo '<a href="reserva_consultas.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a href="reportes/reserva.xlsx"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
echo '</div>';
?>
<script>
function move() {
  
}
</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Reserva';
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
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>