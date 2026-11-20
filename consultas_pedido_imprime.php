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


if(isset($_REQUEST['pedidos']) && !empty($_REQUEST['pedidos'])){
          $pedido = $_REQUEST['pedidos'];          
}


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Hernan Hernandez")
							 ->setLastModifiedBy("Hernan Hernandez")
							 ->setTitle("Reporte Pedido")
							 ->setSubject("Reporte Pedido")
							 ->setDescription("Reporte para hacer pedido")
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
 $sql_pedido="SELECT codigo,cantidad,pedido,
        substring(codigo,1,1),substring(codigo,6,3 ),substring(codigo,10,15),Case substring(codigo, 1, 1)
        WHEN '0' then substring(codigo,1,1)+substring(codigo,5,4 )+substring(codigo,9,4 )+' '+substring(codigo,1,4 )+' '+ltrim(rtrim(substring(codigo,13,12 )))
        WHEN '1' then substring(codigo,1,1)+substring(codigo,5,4 )+substring(codigo,9,4 )+' '+substring(codigo,1,4 )+' '+ltrim(rtrim(substring(codigo,13,12 )))
        Else codigo END As Filtro FROM hacer_pedido WHERE pedido='".$pedido."' ORDER BY Filtro";


//echo $sql_pedido;

if(($r_pedido = odbc_exec($id,$sql_pedido)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
          

$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte Pedido # '.$pedido);
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'ID');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Codigo Tai');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Codigo');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Tecnico');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Descripcion');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Cantidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);

$i=4; 
$h=1;


    
while($row = odbc_fetch_array($r_pedido)){
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
      if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($resultado)){
        $descripcion=$arr["DESCRIPCION"];
        $tecnico='';
        $tecnica=$arr['DESC_ALT'];
        $tecnico_inicio=strpos($tecnica,'(');
        $tecnico_fin=  strpos($tecnica,')');
        $tecnico=substr($tecnica,$tecnico_inicio,$tecnico_fin);
        if ($tecnico==null) {
          $tecnico='';
        }

      }
      //echo $p_codigo;
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $h);
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $p_codigo);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $p_min_pedido);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $tecnico);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $descripcion);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $p_cantidad);
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
$objPHPExcel->getActiveSheet()->getStyle('A3:F'.$i)->applyFromArray($styleArray);
unset($styleArray);

// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&G&C&HReporte de Pedido '.$pedido);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

// Set page orientation and size
//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Pedido');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Save Excel 2007 file

$callStartTime = microtime(true);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objWriter->save('reportes/estudio_pedido.xls');
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
echo '<div id="myProgress">
  <div id="myBar">
  <div id="label">10%</div></div>
</div>';

echo '<br>';
echo '<div id="botones" style="display:none;">';
echo '<a href="consultas_pedidos.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a href="reportes/estudio_pedido.xls"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
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
    document.title = 'Sistema / Pedidos';
</script> 
  <script src="js/bootstrap.min.js"></script> 
</body>
</html>