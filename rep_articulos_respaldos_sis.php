<?php
session_start();
include "menu.php";
require('fpdf.php');


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



//$fecha1=$fecha2;


class PDF extends FPDF
{
    // Cargar los datos

    function LoadData()
    {
        // Leer las líneas del fichero
        require("db.php");
        //global $codigo, $nombre;
        
        
            $sql="SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            Else CODIGO END As Filtro FROM inventario ORDER BY Filtro";    

        

        

        
        if(($res = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

        
        $data = array(); 
        while($arr = odbc_fetch_array($res)){        
            $data[] = $arr;    
            

        }
        

        return $data;
    }

    function Header()
    {   
        $this->SetTextColor(128);
        // Logo
        $this->Image('img/logo.png',10,8,33);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        $this->SetTextColor(0);
        // Movernos a la derecha
        $this->Cell(40);
        // Título
        global $codigo, $nombre, $tipos;
        $this->Cell(0,15,'Reporte de Inventario ');
        
        // Salto de línea
        $this->Ln(23); //titulo de tabla
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',10);
        $this->SetFont('','B');
        
        $header = array('Codigo', 'Descripcion','Cantidad', 'Venta', 'Ubicacion');
        
        $w = array(40,85,22,30,25,);
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
    function FancyTable($data)
    {
        // Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Cabecera

       $w = array(40,85,22,30,25);

        // Restauración de colores y fuentes
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Datos
        $fill = false;

        $i=0;
        

        foreach($data as $row)
        {
            $this->Cell($w[0],6,$data[$i]['CODIGO'],'LR',0,'L',$fill);
            $this->Cell($w[1],6,substr($data[$i]['DESCRIPCION'],0,35),'LR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($data[$i]['EXISTENCIA'],2),'LR',0,'L',$fill);
            $this->Cell($w[3],6,number_format($data[$i]['VENTA'],2),'LR',0,'L',$fill);
            $this->Cell($w[4],6,$data[$i]['ubicacion'],'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;

            
            $i++;
        }
        
    }

}


$pdf = new PDF('P','mm','letter');
// Carga de datos
$data = $pdf->LoadData();
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($data);
$pdf->Output($name='reportes/inventario_respaldo.pdf');
echo '<br>';
echo '<a class="agregarUsuario" href="reportes/inventario_respaldo.pdf"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
?>

<script src="js/bootstrap.min.js"></script>