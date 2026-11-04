<?php
session_start();
require('fpdf.php');
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
global $factura, $proveedor,$nombre_prove;
  

if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
  $factura = $_REQUEST['fact'];
} 

if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
  $proveedor = $_REQUEST['proveedor'];
} 

     include "menu.php";
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

    <title>Reporte de pedido</title>

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

    function LoadData($factura,$proveedor)
    {
      require('db.php');
          
          

        $sql="SELECT p.ID,p.Sugerido,p.Precio,p.Proveedor,p.Factura,p.Codigo,p.Cantidad,p.etiquetas,p.Fecha,i.DESCRIPCION,i.Ubicacion,p.caja,i.EXISTENCIA,
        substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1)
        WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
        WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
        Else p.codigo END As Filtro FROM Pedidos as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE Factura='".$factura."' ORDER BY Filtro";


        if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

        while($arr = odbc_fetch_array($resultado)){
       
          $codigos=$arr["Codigo"];
          $descripcion=$arr["DESCRIPCION"];
          $costo=$arr["Precio"];
          $cantidad=$arr["Cantidad"];
          $ubicacion=$arr["Ubicacion"];
          $etiquetas='';
          $existencia=$arr["EXISTENCIA"];

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
      
          $datos[]= array($codigos ,substr($descripcion,0,30) ,$ubicacion, $costo , $cantidad,$archivo,$etiquetas,$existencia);
        
        }



          $data= $datos;
        //devuelve valor   
        return $data;
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
     global $factura, $nombre_prove, $proveedor;
     require('db.php');
      $sql2="SELECT codigo, nombre FROM Proveedores WHERE codigo='$proveedor'";
                
        if(($prov = odbc_exec($id,$sql2)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
        while($arr2 = odbc_fetch_array($prov)){
            $nombre_prove=$arr2["nombre"];
        }

    $this->Cell(0,0,$nombre_prove.' * FACTURA '.$factura,0,0,'C',false);
    // Salto de línea
    $this->Ln(4); //titulo de tabla
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('Arial','',10);
    $this->SetFont('','B');

    $header = array('Codigo', 'Descripcion','Ubi', 'Exist','Cant','Imagen','Etiquetas');
    $w = array(38, 75, 13, 17,13,25, 20);
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

    $w = array(38, 75, 13, 17,13,25, 20);

    // Restauración de colores y fuentes
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Datos
    $fill = false;
    foreach($data as $row)
    {
       
        $this->Cell($w[0],15,$row[0],1,0,'C',$fill);
        $this->Cell($w[1],15,$row[1],1,0,'C',$fill);
        $this->Cell($w[2],15,$row[2],1,0,'C',$fill);
        $this->Cell($w[3],15,number_format($row[7],2),1,0,'C',$fill);
        $this->Cell($w[4],15,number_format($row[4],2),1,0,'C',$fill);
        $this->Cell( 25, 15, $this->Image($row[5], $this->GetX()+7, $this->GetY()+1, 12), 1, 0, 'C', false );
        $this->Cell($w[6],15,$row[6],1,0,'C',$fill);
        $this->Ln();
        //$fill = !$fill;
    }
    // Línea de cierre
    $this->Cell(array_sum($w),0,'','T');
}
}

$pdf = new PDF('P','mm','letter');
// Títulos de las columnas
$header = array('Codigo', 'Descripcion','Ubi', 'Costo','Cant','Imagen','Etiquetas');
// Carga de datos
$data = $pdf->LoadData($factura,$proveedor);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output($name='reportes/Rep_pedidos.pdf');
//$pdf->Output('pedidos.pdf','D');
//$pdf->Output();
//sleep(350);
echo '<br>';
echo '<a class="agregarUsuario" href="pedido_mostrar.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a class="agregarUsuario" target="_blank" href="reportes/Rep_pedidos.pdf"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
?>

<script src="js/bootstrap.min.js"></script>
