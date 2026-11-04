
<?php
require('fpdf.php');



if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
    $fecha2 = $_REQUEST['Date2'];

}


//$fecha1=$fecha2;


class PDF extends FPDF
{
    // Cargar los datos

    function LoadData($fecha1,$fecha2)
    {
        // Leer las líneas del fichero
        require_once("db.php");
        
        $sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  order by nfac";  
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
        global $fecha1, $fecha2;
        $this->Cell(0,15,'Reporte de Facturas de '.$fecha1.' a '.$fecha2);
        
        // Salto de línea
        $this->Ln(23); //titulo de tabla
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',10);
        $this->SetFont('','B');

        $header = array('Estado', 'Factura','Fecha', 'Subtotal', 'Impuesto','Descuento','Total','Cliente');
        $w = array(25,35,22,25,25,25,28,17);
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

       $w = array(25,35,22,25,25,25,28,17);

        // Restauración de colores y fuentes
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Datos
        $fill = false;

        $i=0;
        $varsubtotal=0; $varimpuesto=0; $vardescuento=0; $vartotal=0;
        $varsubtotalC=0; $varimpuestoC=0; $vardescuentoC=0; $vartotalC=0;
        $varmensajeria=0; $tempmensa=0; $tempmensaC=0; $totalrecibos=0;
        $totalegresos=0; $totalingresos=0; $totaldepositos=0; $totaldif=0;

        foreach($data as $row)
        {
            $this->Cell($w[0],6,$data[$i]['estado'],'LR',0,'L',$fill);
            $this->Cell($w[1],6,$data[$i]['nfac'],'LR',0,'L',$fill);
            $fecha=strtotime($data[$i]['fechafact']);
            $ffac=date('Y-m-d',$fecha);
            $this->Cell($w[2],6,$ffac,'LR',0,'L',$fill);
            $this->Cell($w[3],6,number_format($data[$i]['subtotal'],2),'LR',0,'L',$fill);
            $this->Cell($w[4],6,number_format($data[$i]['impuesto'],2),'LR',0,'L',$fill);
            $this->Cell($w[5],6,number_format($data[$i]['descuento'],2),'LR',0,'L',$fill);
            $this->Cell($w[6],6,number_format($data[$i]['total'],2),'LR',0,'L',$fill);
            $this->Cell($w[7],6,$data[$i]['proveedor'],'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;

            if ($data[$i]["Tipo"]=='0'){
              $varsubtotal = $varsubtotal + $data[$i]['subtotal'];
              $varimpuesto = $varimpuesto + $data[$i]['impuesto'];
              $vardescuento = $vardescuento + $data[$i]['descuento'];
              $vartotal= $vartotal + $data[$i]['total'];
              $temptotal=$varsubtotal+$varimpuesto-$vardescuento;
              $tempmensa=$vartotal-$temptotal;
              
            
            }else{
              $varsubtotalC = $varsubtotalC + $data[$i]['subtotal'];
              $varimpuestoC = $varimpuestoC + $data[$i]['impuesto'];
              $vardescuentoC = $vardescuentoC + $data[$i]['descuento'];
              $vartotalC= $vartotalC + $data[$i]['total'];
              $temptotalC=$varsubtotalC+$varimpuestoC-$vardescuentoC;
              $tempmensaC=$vartotalC-$temptotalC;
            }
            
            $varmensajeria=$tempmensa+$tempmensaC;
            $i++;
        }
        // Línea de cierre
        $this->Cell(array_sum($w),0,'','T');
        $this->SetFont('Arial','B',15);
        $this->SetTextColor(0);
        $this->Cell(-200);
        $this->Cell(0,10,'Ventas de Contado');
        $this->Cell(-130);
        $this->Cell(0,10,'Ventas de Credito');
        $this->Cell(-65);
        $this->Cell(0,10,'Total de Ventas');
        $this->SetFont('Arial','',12);
        $this->SetTextColor(0);
        $this->Cell(-193);
        $this->Cell(0,20,'Subtotal '.number_format($varsubtotal,2));
        $this->Cell(-130);
        $this->Cell(0,20,'Subtotal '.number_format($varsubtotalC,2));
        $this->Cell(-65);
        $this->Cell(0,20,'Subtotal '.number_format($varsubtotalC+$varsubtotal,2));
        $this->Cell(-193);
        $this->Cell(0,30,'Impuesto '.number_format($varimpuesto,2));
        $this->Cell(-130);
        $this->Cell(0,30,'Impuesto '.number_format($varimpuestoC,2));
        $this->Cell(-65);
        $this->Cell(0,30,'Impuesto '.number_format($varimpuestoC+$varimpuesto,2));
        $this->Cell(-193);
        $this->Cell(0,40,'Descuento '.number_format($vardescuento,2));
        $this->Cell(-130);
        $this->Cell(0,40,'Descuento '.number_format($vardescuentoC,2));
        $this->Cell(-65);
        $this->Cell(0,40,'Descuento '.number_format($vardescuentoC+$vardescuento,2));
        $this->SetFont('Arial','B',15);
        $this->SetTextColor(0);
        $this->Cell(-193);
        $this->Cell(0,50,'Total '.number_format($vartotal,2));
        $this->Cell(-130);
        $this->Cell(0,50,'Total '.number_format($vartotalC,2));
        $this->Cell(-65);
        $this->Cell(0,50,'Total '.number_format($vartotalC+$vartotal,2));
        $this->SetFont('Arial','U',12);
        $this->SetTextColor(0);
        $this->Cell(-65);
        $this->Cell(0,65,'Total Mensajeria '.number_format($varmensajeria,2));
        

    }

}


$pdf = new PDF('P','mm','letter');
// Carga de datos
$data = $pdf->LoadData($fecha1,$fecha2);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($data);
$pdf->Output();

?>