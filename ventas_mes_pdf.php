<?php
session_start();
require('fpdf.php');
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
global $tipo;
$tipo='vw';  /// si se va ha hacer Daihatsu cambiar esto
     include "menu.php";


if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
$fecha2 = $_REQUEST['Date2'];

}
     
?>
<!DOCTYPE html>
<html lang="en">
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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Reporte de pedido</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->

    <link href="css/signin.css" rel="stylesheet">
    
    
    <script src="js/ie-emulation-modes-warning.js"></script>

  </head>

<?php
/*
$ruta='reportes/catalogo.pdf';
if (file_exists($ruta)) {
      unlink($ruta);
}
*/
class PDF extends FPDF
{
// Cargar los datos

    function LoadData($tipo,$fecha1,$fecha2)
    {
      include("db.php");
        $sql = "SELECT h.codigo,i.DESCRIPCION,SUM(h.cantidad) as veces FROM hgeneral h INNER JOIN inventario i on h.codigo = i.codigo
        WHERE h.movimiento = 'VENTA' AND 
        h.fecha BETWEEN '".$fecha1." 00:00:00.000' AND '".$fecha2." 23:59:59.999' GROUP BY h.codigo,i.CODIGO,i.DESCRIPCION ORDER BY veces DESC";

    
    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  
      while($arr = odbc_fetch_array($resultado)){

            $codigos=$arr["codigo"];
            $descripcion=mb_convert_encoding($arr["DESCRIPCION"],"ISO-8859-1", "UTF-8");
            $cantidad=$arr["veces"];
            
            $path='subida_cat/';

              if (substr($codigos,0,1)==1) {
                $directorio='1/';
              }
              if (substr($codigos,0,1)==2) {
                $directorio='2/';
              }
              if (substr($codigos,0,1)==3) {
                $directorio='3/';
              }
              if (substr($codigos,0,1)==9) {
                $directorio='9/';
              }
              if (substr($codigos,0,1)==0) {
                $directorio='0/';
              }
              $codigos1=rtrim($codigos);
              if (substr($codigos,-2)=='EU') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
                 
              } 
              if (substr($codigos,-2)=='GR') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
              }
              if (substr($codigos,-2)=='TW') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
                 
              }
              if (substr($codigos,-2)=='JP') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
              }
              if (substr($codigos,-2)=='ZL') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
              }

              $foto=substr ( $codigos1 , 1 );
              $foto=rtrim($foto);

              if (substr($foto,0,1)==' ') {
                //echo 'FOTOS';
                $foto=substr($foto, 2, strlen($foto));
              }
              
              $filename = $path.$directorio.$foto.".jpg";
              $archivo=$filename;

              if (file_exists($filename)) {
                  $archivo=$filename;
              } else {
                  $archivo=$path."inf.jpg";
              }
           
            $datos[]= array($codigos ,substr($descripcion,0,60) ,$cantidad,$archivo);
           
          }
                  
          
        $data= $datos;
        //devuelve valor   
        return $data;
    }


function Header()
{   
    $this->SetTextColor(128);
    $this->Cell(20,40,'                                           '.date("d/m/Y"));
    // Logo
    $this->Image('img/logo.png',10,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    $this->SetTextColor(0);
    // Movernos a la derecha
    $this->Cell(50);
    // Título
    global $tipo, $mov;
    
    
    $this->Cell(0,15,'Reporte de Ventas Articulos');
    
    // Salto de línea
    $this->Ln(23); //titulo de tabla
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('Arial','',10);
    $this->SetFont('','B');

    $header = array('Codigo', 'Descripcion','Veces','Imagen');
    $w = array(35, 130, 12,25);
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Restauración de colores y fuentes
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
}

// Pie de página
function Footer()
{
    // Posición a 1,5 cm del final
    $this->SetY(-15);
    // Arial itálica 8
    $this->SetFont('Arial','I',8);
    // Color del texto en gris
    $this->SetTextColor(128);
    // Número de página
    $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
}

// Tabla coloreada
function FancyTable($header, $data)
{
    // Colores, ancho de línea y fuente en negrita
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Cabecera

    $w = array(35, 130, 12,25);

    // Restauración de colores y fuentes
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Datos
    $fill =false;
   
    foreach($data as $row)
    {   
        
        //$this->Cell($w[0],17,$row[0],0,0,'C',$fill);
        $this->Cell($w[0],15,$row[0],1,0,'C',$fill);
        $this->Cell($w[1],15,$row[1],1,0,'C',$fill);
        $this->Cell($w[2],15,number_format($row[2],2),1,0,'C',$fill);
        $this->Cell( 25, 15, $this->Image($row[3], $this->GetX()+7, $this->GetY()+1, 12), 1, 0, 'C', false );
        //$this->Image($row[5],null,null,20);
    
        $this->Ln(15);

    }
    // Línea de cierre
    //$this->Cell(array_sum($w),0,'','T');
}
}
$pdf = new PDF('P','mm','letter');
// Títulos de las columnas
$header = array('Codigo', 'Descripcion','Veces','Imagen');
// Carga de datos
$data = $pdf->LoadData($tipo,$fecha1,$fecha2);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output($name='reportes/veces.pdf');
//$pdf->Output('pedidos.pdf','D');
//$pdf->Output();
//sleep(350);
echo '<br>';
echo '<div id="myProgress">
  <div id="myBar">
  <div id="label">10%</div></div>
</div>';

echo '<br>';
echo '<div id="botones" style="display:none;">';
echo '<a href="ventas_mes_articulos.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a target="_blank"  href="reportes/veces.pdf"><img width="48"  height="48" src= "img/pdf.png">Abrir Reporte</a> ';

echo '</div>';
?>
<script>
function move() {
  
}
</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Veces PDF';
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