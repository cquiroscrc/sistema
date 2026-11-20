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
							 ->setTitle("Reporte Clientes")
							 ->setSubject("Reporte Clientes")
							 ->setDescription("Reporte de clientes")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Reportes");


// Add some data, we will use printing features
//$sql_pedido="SELECT * FROM hacer_pedido WHERE pedido='$pedido'";

$sql = "SELECT * from clientes where codigo < '9999'";
if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));


$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte Clientes Frecuentes');
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
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'ID');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Nombre');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Cedula');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Telefono');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Celular');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Direccion');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Correo');
$objPHPExcel->getActiveSheet()->setCellValue('H3', 'Nacimiento');
$objPHPExcel->getActiveSheet()->setCellValue('I3', 'Afiliacion');
$objPHPExcel->getActiveSheet()->setCellValue('J3', 'Comentario');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
$i=4; 
$h=1;
while($arr = odbc_fetch_array($resultado)){
       

     
  
  
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $arr['codigo']);
  	$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $arr['nombre']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $arr['cedula']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $arr['telefono']);
  	$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $arr['celular']);
  	$objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $arr['direccion']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $arr['correo']);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $arr['nacimiento']);
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $arr['afiliacion']);
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $arr['comentario']);
    

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
$objPHPExcel->getActiveSheet()->getStyle('A3:K'.$i)->applyFromArray($styleArray);
unset($styleArray);

// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&G&C&HReporte de Cliente Frecuentes');
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

// Set page orientation and size
//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('S_Stock');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Save Excel 2007 file

$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objWriter->save('reportes/cliente_frecuente.xlsx');
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo '<div id="myProgress">
  <div id="myBar">
  <div id="label">10%</div></div>
</div>';

echo '<br>';
echo '<div id="botones" style="display:none;">';
echo '<a href="clientes_resultado_excel.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a href="reportes/cliente_frecuente.xlsx"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
echo '</div>';
?>
<script>
function move() {
  
}
</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // C_Frecuente';
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