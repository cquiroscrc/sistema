<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }

if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
  $codigo = $_REQUEST['codigo'];
  
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

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

/** Include PHPExcel */
require_once ("Classes/PHPExcel.php");
require_once("db.php");


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Hernan Hernandez")
							 ->setLastModifiedBy("Hernan Hernandez")
							 ->setTitle("Reporte Pedido")
							 ->setSubject("Reporte Pedido")
							 ->setDescription("Reporte para bajo pedido")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Reportes");


// Add some data, we will use printing features
//$sql_pedido="SELECT * FROM hacer_pedido WHERE pedido='$pedido'";

$sql5="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad=5";
if(($prom2 = odbc_exec($id,$sql5)) === false)
    die("Error en Select: ". odbc_errormsg($id));
while($arr5 = odbc_fetch_array($prom2)){
    $tiempo=$arr5["Valor"];
}

if (substr($codigo,0,1)==1 ) { // vw
    $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
    order by substring(Codigo,1,1),substring(Codigo,5,4), 
    substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
}
elseif (substr($codigo,0,1)==0 ) { // vw
    $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
    order by substring(Codigo,1,1),substring(Codigo,5,4), 
    substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
}
elseif (substr($codigo,0,1)==2) {
     $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
    order by codigo";    
}
elseif (substr($codigo,0,1)==3) {
     $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
    order by codigo";    
}
elseif (substr($codigo,0,1)==9) {
     $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
    order by codigo";    
}elseif (substr($codigo,0,1)==8) {
      $fam=substr($codigo,1,1);
      $des=substr($codigo,3,strlen($codigo));
     $sql="SELECT * FROM inventario WHERE codigo like '$fam%' 
     AND descripcion like '%$des%' order by descripcion";    
}

if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));


$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte Pedido Sobre Stock');
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
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Codigo');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Descripcion');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Existencia');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Costo');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Venta');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Dolar');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Proveedor');
$objPHPExcel->getActiveSheet()->setCellValue('H3', 'Prom Mens');
$objPHPExcel->getActiveSheet()->setCellValue('I3', 'Prom total');
$objPHPExcel->getActiveSheet()->setCellValue('J3', 'Transito');
$objPHPExcel->getActiveSheet()->setCellValue('K3', 'Sugerido');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);

$i=4; 
$h=1;
while($arr = odbc_fetch_array($resultado)){
       

     
  $codigos=$arr["CODIGO"];
  $descripcion=$arr["DESCRIPCION"];
  $desc_tecnica=$arr["DESC_ALT"];
  $existencia=$arr["EXISTENCIA"];
  $costo=$arr["COSTO"];
  $venta=$arr["VENTA"];
  $costodol=$arr["costodol"];
  $proveedor=$arr["proveedor"];
  $sql4="SELECT codigo, shortname FROM Proveedores WHERE codigo='$proveedor'";

  if(($prov = odbc_exec($id,$sql4)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    $nombre_prove="Local";
    while($arr4 = odbc_fetch_array($prov)){
      $nombre_prove=$arr4["shortname"];
    }

  $codigobusq =$codigos;

 /// aqui val el valor de estudio, cantidad de meses tambien en moviemientos.php
  $sql2="SELECT (SUM(cantidad)/6) as promedio from hgeneral where codigo = '$codigobusq' 
      AND movimiento not in('Ajuste') AND 
      substring(factura,1,1)='F' AND 
      FECHA >= Dateadd(mm,-6,Getdate())AND  
      FECHA <= GETDATE()";

  if(($prom = odbc_exec($id,$sql2)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr2 = odbc_fetch_array($prom)){
      $promedio=$arr2["promedio"];
  
    }
 
  $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigobusq'";
  if(($pedi = odbc_exec($id,$sql3)) === false)
      die("Error en Select: ". odbc_errormsg($id));  

    while($arr3 = odbc_fetch_array($pedi)){
      $pedidos=$arr3["pedido"];
    }
    
    $promedio2=$promedio*2;
    $total=$existencia+$pedidos;

  $suge = ($promedio*$tiempo)- $existencia-$pedidos;  
  if ($promedio2>$total) {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $codigos);
  	$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $descripcion);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $existencia);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $costo);
  	$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $venta);
  	$objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $costodol);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $nombre_prove);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, number_format($promedio,2));
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, number_format($promedio*$tiempo,2));
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, number_format($pedidos,2));
    $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, number_format($suge,2));

  	$i++;$h++;
  	  
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
$objPHPExcel->getActiveSheet()->getStyle('A3:K'.$i)->applyFromArray($styleArray);
unset($styleArray);

// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&G&C&HReporte de Sobre Stock');
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
$objWriter->save('reportes/bajo_stock.xlsx');
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo '<div id="myProgress">
  <div id="myBar">
  <div id="label">10%</div></div>
</div>';

echo '<br>';
echo '<div id="botones" style="display:none;">';
echo '<a href="pedir_stock_consulta.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a href="reportes/bajo_stock.xlsx"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
echo '</div>';
?>
<script>
function move() {
  
}
</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // B_Stock';
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