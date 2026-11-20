<?php
session_start();
require('fpdf.php');
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
global $codigo, $familia;
  

 $cod="XX";
    $familia=2;
    
    //// *************** Incluye recibir ******************
      include "consulta_recibe.php";
      //// *************** Incluye recibir ******************
    
    if (substr($cod,0,5)=='1___ ') {
      $codigo=$cod;
    }
    else{
      $cant=stripos($cod,"_",0);
      if ($cant<>0) {
        $codigo= substr($cod,0,$cant);  
      }else{
        $codigo=$cod;
      }
    }
    $codigo= trim($codigo);
    $codigobusq = '';
    
    if ($familia==1) {
      $codigo='1___ '.$codigo;

    }

    // include "menu.php";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Reporte de Ubicacion</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->

    <link href="css/signin.css" rel="stylesheet">
    
    
    <script src="js/ie-emulation-modes-warning.js"></script>

  </head>

<?php


class PDF extends FPDF
{
// Cargar los datos

    function LoadData($codigo,$familia)
    {
      require('db.php');
          
          

        $suge=0;
        if ($codigo != ""){
          $reglon=1;
          
          //// *************** Incluye la consulta ******************
          include "consulta_resultado_ubica.php";
          //// *************** Incluye la consulta ******************
          
          if(($resultado = odbc_exec($id,$sql)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arr = odbc_fetch_array($resultado)){
       
           $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $existencia=$arr["EXISTENCIA"];
          $ubicacion=$arr["ubicacion"];
          $venta=$arr["VENTA"];
          $potencia = $arr['potencia'];
          $xpotencia = $potencia * 100;
          $venta = $venta + $xpotencia;

          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
          if(($pedi = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=$arr3["pedido"];
            }

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
              if (substr($codigos,0,1)==4) {
                $directorio='4/';
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
      
          $datos[]= array($codigos ,substr($descripcion,0,30) ,$existencia,$venta,$pedidos,$ubicacion,$archivo);
        
        }



          $data= $datos;
        //devuelve valor   
        return $data;
    }
  }

function Header()
{   
    //$this->SetTextColor(128);
    //$this->Cell(20,40,date("d/m/Y"));
    // Logo
    //$this->Image('img/logo.png',10,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    $this->SetTextColor(0);
    // Movernos a la derecha
    //$this->Cell(50);
    // Título
     global $codigo, $familia;
     require('db.php');
      

    $this->Cell(0,0,'Consulta '.$codigo,0,0,'C',false);
    // Salto de línea
    $this->Ln(4); //titulo de tabla
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('Arial','',10);
    $this->SetFont('','B');

    $header = array('Codigo', 'Descripcion','Existe', 'Venta','Trans','Ubi','Imagen');
    $w = array(38, 75, 13, 17,13,13, 25);
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

    $w = array(38, 75, 13, 17,13,13, 25);

    // Restauración de colores y fuentes
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Datos
    $fill = false;
    foreach($data as $row)
    {
      
        $this->Cell($w[0],15,$row[0],1,0,'C',false);
        $this->Cell($w[1],15,$row[1],1,0,'C',false);
        $this->Cell($w[2],15,number_format($row[2],2),1,0,'L',false);       
        $this->Cell($w[3],15,number_format($row[3],2),1,0,'L',false);
        $this->Cell($w[4],15,$row[4],1,0,'C',false);
        $this->Cell($w[5],15,$row[5],1,0,'C',false);
        $this->Cell( 25, 15, $this->Image($row[6], $this->GetX()+7, $this->GetY()+1, 12), 1, 0, 'C', false );
        $this->Ln();
        //$fill = !$fill;
    }
    // Línea de cierre
    $this->Cell(array_sum($w),0,'','T');
}

}

$pdf = new PDF('P','mm','letter');
// Títulos de las columnas
$header = array('Codigo', 'Descripcion','Existe', 'Venta','Trans','Ubi','Imagen');
// Carga de datos
$data = $pdf->LoadData($codigo,$familia);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output($name='reportes/consulta.pdf');
//$pdf->Output('pedidos.pdf','D');
//$pdf->Output();
//sleep(350);
echo '<br>';
//echo '<a class="agregarUsuario" href="consulta_simple.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';
echo '<a class="agregarUsuario" target="_blank" href="reportes/consulta.pdf"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a>';

?>

<script type="text/javascript" language="JavaScript">
      document.title = 'Sistema / Consulta simple PDF';
    </script>
<script src="js/bootstrap.min.js"></script>