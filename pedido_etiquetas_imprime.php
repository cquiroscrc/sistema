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

date_default_timezone_set('Europe/London');

/** Include PHPExcel */
require_once ("Classes/PHPExcel.php");
require_once("db.php");
include("conectarDB.php");

if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
          $factura = $_REQUEST['fact'];          
}

if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];          
}

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Hernan Hernandez")
							 ->setLastModifiedBy("Hernan Hernandez")
							 ->setTitle("Etiquetas")
							 ->setSubject("Etiquetas")
							 ->setDescription("Reporte para hacer Etiquetas")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Reportes");


// Add some data, we will use printing features

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Codigo');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Descripcion');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Detalle');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Cajas');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);

$i=2; 


//$sql_pedido="SELECT * FROM Pedidos WHERE Factura='$factura'";
$sql_pedido="SELECT p.ID,p.Sugerido,p.Precio,p.Proveedor,p.Factura,p.Codigo,p.Cantidad,p.Etiquetas,p.Fecha,i.Ubicacion,p.Caja,
    substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1)
    WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
    WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
    Else p.codigo END As Filtro FROM Pedidos as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE Factura='".$factura."' ORDER BY Filtro";


    if(($resultado_pedido = odbc_exec($id,$sql_pedido)) === false)
      die("Error en Select: ". odbc_errormsg($id));

  while($row =odbc_fetch_array($resultado_pedido)){
      $p_factura=$row['Factura'];
      $pedido=$p_factura;
      $p_codigo=$row['Codigo'];
      $p_cantidad=$row['Cantidad'];
      $p_proveedor=$row['Proveedor'];
      $p_fecha=$row['Fecha'];
      $p_caja=$row['Caja'];
      $fecha=strtotime($p_fecha);
      $fecha=date("dmy",$fecha);
      $etiquetas=$row['Etiquetas'];


      $sql="SELECT * FROM inventario WHERE codigo like '$p_codigo%'";    

      if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($resultado)){
        $descripcion=mb_convert_encoding($arr["DESCRIPCION"],  "ISO-8859-1", "UTF-8");
        $ubicacion=$arr['ubicacion'];
      }
      $detalle=$proveedor."-".$factura."-".$fecha."-".number_format($p_cantidad,0)."-".$ubicacion;
      for ($h=0; $h < $etiquetas; $h++) { 
      	$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $p_codigo." ");
  			$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $descripcion);
  			$objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $detalle);
  			$objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $p_caja);
  			$i++;
	    } 

}

$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$i=$i-1;
//$objPHPExcel->getActiveSheet()->getStyle('A3:D'.$i)->applyFromArray($styleArray);
//unset($styleArray);

// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&G&C&HReporte de Pedido '.$pedido);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

// Set page orientation and size
//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('etiquetas');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Save Excel 2007 file

$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objWriter->save('reportes/etiquetas-'.$pedido.'.xlsx');
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
echo '<br>';
echo '<a class= "agregarUsuario" href="pedido_mostrar.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
echo '<a class="agregarUsuario" href="reportes/etiquetas-'.$pedido.'.xlsx"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';

?>

  </body>
</html>