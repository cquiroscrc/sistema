
<?php
require('fpdf.php');
require("db.php");


if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
    $fecha2 = $_REQUEST['Date2'];

}


$fecha1=$fecha2;
$varsubtotal=0;
$varimpuesto=0;
$vardescuento=0;
$vartotal=0;
$varsubtotalC=0;
$varimpuestoC=0;
$vardescuentoC=0;
$vartotalC=0;
$varmensajeria=0;
$tempmensa=0;
$tempmensaC=0;
$totalrecibos=0;
$totalegresos=0;
$totalingresos=0;
$totaldepositos=0;
$totaldif=0;
$nc=0;
$rep=0;
$pap=0;
$com=0;
$aten=0;
$enco=0;
$luz=0;
$agua=0;
$tel=0;
$otro=0;
$alf=0;



class PDF extends FPDF
{


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
        $this->Cell(0,15,'Reporte de Cierre de Caja de '.$fecha1);
        
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


}


$pdf = new PDF('P','mm','letter');
// Carga de datos
//$data = $pdf->LoadData($fecha1,$fecha2);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
//$pdf->FancyTable($data);
// Meter datos

$sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  order by nfac";  
if(($res = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

while($arr = odbc_fetch_array($res)){        
    if ($arr["Tipo"]=='0'){
      $varsubtotal = $varsubtotal + $arr['subtotal'];
      $varimpuesto = $varimpuesto + $arr['impuesto'];
      $vardescuento = $vardescuento + $arr['descuento'];
      $vartotal= $vartotal + $arr['total'];
      $temptotal=$varsubtotal+$varimpuesto-$vardescuento;
      $tempmensa=$vartotal-$temptotal;
      
    
    }else{
      $varsubtotalC = $varsubtotalC + $arr['subtotal'];
      $varimpuestoC = $varimpuestoC + $arr['impuesto'];
      $vardescuentoC = $vardescuentoC + $arr['descuento'];
      $vartotalC= $vartotalC + $arr['total'];
      $temptotalC=$varsubtotalC+$varimpuestoC-$vardescuentoC;
      $tempmensaC=$vartotalC-$temptotalC;
    }
    $varmensajeria=$tempmensa+$tempmensaC;
}
// Ventas
$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);
$pdf->Ln(25);
$pdf->SetFillColor(255,0,0);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(15,0,0);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Arial','B',15);
$pdf->SetFont('','B');
$pdf->Cell(0,10,'Ventas',1,1,'C',true);

$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);

$pdf->Cell(0,15,'Ventas de Contado');
$pdf->Cell(-130);
$pdf->Cell(0,15,'Ventas de Credito');
$pdf->Cell(-65);
$pdf->Cell(0,15,'Total de Ventas');
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0);
$pdf->Cell(-193);
$pdf->Cell(0,25,'Subtotal '.number_format($varsubtotal,2));
$pdf->Cell(-130);
$pdf->Cell(0,25,'Subtotal '.number_format($varsubtotalC,2));
$pdf->Cell(-65);
$pdf->Cell(0,25,'Subtotal '.number_format($varsubtotalC+$varsubtotal,2));
$pdf->Cell(-193);
$pdf->Cell(0,35,'Impuesto '.number_format($varimpuesto,2));
$pdf->Cell(-130);
$pdf->Cell(0,35,'Impuesto '.number_format($varimpuestoC,2));
$pdf->Cell(-65);
$pdf->Cell(0,35,'Impuesto '.number_format($varimpuestoC+$varimpuesto,2));
$pdf->Cell(-193);
$pdf->Cell(0,45,'Descuento '.number_format($vardescuento,2));
$pdf->Cell(-130);
$pdf->Cell(0,45,'Descuento '.number_format($vardescuentoC,2));
$pdf->Cell(-65);
$pdf->Cell(0,45,'Descuento '.number_format($vardescuentoC+$vardescuento,2));
$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);
$pdf->Cell(-193);
$pdf->Cell(0,55,'Total '.number_format($vartotal,2));
$pdf->Cell(-130);
$pdf->Cell(0,55,'Total '.number_format($vartotalC,2));
$pdf->Cell(-65);
$pdf->Cell(0,55,'Total '.number_format($vartotalC+$vartotal,2));


$sql_recibos="SELECT * FROM cxc WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and NFAC like'R-%' order by NFac";  

if(($res_recibos = odbc_exec($id,$sql_recibos)) === false)
    die("Error en Select: ". odbc_errormsg($id));

while($arr_recibos = odbc_fetch_array($res_recibos)){
    $totalrecibos=$totalrecibos+$arr_recibos["Total"];
}
$totalrecibos=$totalrecibos*-1;
$pdf->SetFont('Arial','B',15);
$pdf->Cell(-193);
$pdf->Cell(0,68,'Total Recibos :');
$pdf->Cell(-130);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,68,number_format($totalrecibos,2));
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(0);
$pdf->Cell(-65);
$pdf->Cell(0,68,'Total Mensajeria :'.number_format($varmensajeria,2));

//Egresos
$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);
$pdf->Ln(45);
$pdf->SetFillColor(255,0,0);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(15,0,0);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Arial','B',15);
$pdf->SetFont('','B');
$pdf->Cell(0,10,'Reporte Egresos',1,1,'C',true);

$sql_egresos="SELECT e.Fecha, e.Documento, e.Tipo, e.Descripcion, e.Monto, t.Descripcion as egreso
FROM tbl_Egresos e, tbl_TiposEgresos t
WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'
AND e.Tipo = t.Tipo order by e.Tipo,e.Documento";

if(($res_egresos = odbc_exec($id,$sql_egresos)) === false)
    die("Error en Select: ". odbc_errormsg($id));

while($arr_egresos = odbc_fetch_array($res_egresos)){
    
    if ($arr_egresos["Tipo"]==0) {
        $nc=$nc+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==1) {
        $rep=$rep+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==2) {
        $pap=$pap+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==3) {
        $com=$com+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==4) {
        $aten=$aten+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos["Tipo"]==5) {
        $enco=$enco+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==6) {
        $luz=$luz+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==7) {
        $agua=$agua+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==8) {
        $tel=$tel+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==9) {
        $otro=$otro+$arr_egresos['Monto'];
    }
    elseif ($arr_egresos['Tipo']==10) {
        $alf=$alf+$arr_egresos['Monto'];
    }

    $totalegresos=$totalegresos+$arr_egresos["Monto"];
}
 
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0);
$pdf->Cell(0,15,'NC x Descuentos : '.number_format($nc,2));
$pdf->Cell(-130);
$pdf->Cell(0,15,'Repuesto :  '.number_format($rep,2));
$pdf->Cell(-65);
$pdf->Cell(0,15,'Papeleria : '.number_format($pap,2));
//segunda linea
$pdf->Cell(-196);     
$pdf->Cell(0,25,'Combustible : '.number_format($com,2));
$pdf->Cell(-130);
$pdf->Cell(0,25,'Atencion :  '.number_format($aten,2));
$pdf->Cell(-65);
$pdf->Cell(0,25,'Encomiendas : '.number_format($enco,2));
// tercera linea        
$pdf->Cell(-196);     
$pdf->Cell(0,35,'Luz : '.number_format($luz,2));
$pdf->Cell(-130);
$pdf->Cell(0,35,'Agua :  '.number_format($agua,2));
$pdf->Cell(-65);
$pdf->Cell(0,35,'Telefono : '.number_format($tel,2));
$pdf->Cell(-196);     
$pdf->Cell(0,45,'Otros Egresos : '.number_format($otro,2));
$pdf->Cell(-130);
$pdf->Cell(0,45,'Egresos Alfaro :  '.number_format($alf,2));
$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);
$pdf->Cell(-196);
$pdf->Cell(0,60,'Total de Egresos : ');
$pdf->Cell(-80);
$pdf->Cell(0,60,number_format($totalegresos,2));

//Depositos
$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);
$pdf->Ln(38);
$pdf->SetFillColor(255,0,0);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(15,0,0);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Arial','B',15);
$pdf->SetFont('','B');
$pdf->Cell(0,10,'Depositos',1,1,'C',true);

$sql_depositos="SELECT B.Fecha, B.Cuenta, B.Documento, B.Descripcion, B.Credito, C.Banco FROM tbl_Bancos B, tbl_Cuentas C 
WHERE B.Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND B.Descripcion like 'Caja del %' and B.Cuenta = C.Cuenta order by B.Cuenta, B.Documento";  

if(($res_depositos = odbc_exec($id,$sql_depositos)) === false)
    die("Error en Select: ". odbc_errormsg($id));


$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);

$pdf->Cell(0,15,'Cuenta');
$pdf->Cell(-135);
$pdf->Cell(0,15,'Banco');
$pdf->Cell(-80);
$pdf->Cell(0,15,'Documento');
$pdf->Cell(-35);
$pdf->Cell(0,15,'Credito');
$pdf->SetFont('Arial','',12);
$reglon=25;
while($arr_depositos = odbc_fetch_array($res_depositos)){
    $pdf->Cell(-196);
    $pdf->Cell(0,$reglon,$arr_depositos['Cuenta']);
    $pdf->Cell(-135);
    $pdf->Cell(0,$reglon,$arr_depositos['Banco']);
    $pdf->Cell(-80);
    $pdf->Cell(0,$reglon,$arr_depositos['Documento']);
    $pdf->Cell(-35);
    $pdf->Cell(0,$reglon,number_format($arr_depositos['Credito'],2));
    $reglon=$reglon+10;
    $totaldepositos=$totaldepositos+$arr_depositos["Credito"];
}
$pdf->SetFont('Arial','B',15);
$pdf->Cell(-196);
$pdf->Cell(0,$reglon,'Total Depositos');
$pdf->Cell(-80);
$pdf->Cell(0,$reglon,number_format($totaldepositos,2));

//Totales
$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);

$pdf->Ln(75);
$pdf->SetFillColor(255,0,0);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(15,0,0);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Arial','B',15);
$pdf->SetFont('','B');
$pdf->Cell(0,10,'Totales',1,1,'C',true);

$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);
$pdf->Cell(0,15,'Total de Ingresos');
$pdf->Cell(-135);
$pdf->Cell(0,15,'Total de Egresos');
$pdf->Cell(-80);
$pdf->Cell(0,15,'Total Depositos');
$pdf->Cell(-35);
$totalingresos=$vartotal+$totalrecibos;
$totaldif=$totaldepositos-$totalingresos+$totalegresos;
if ($totaldif<0) {    
    //negativo
    $pdf->Cell(0,15,'Faltante');    
  }else{
    $pdf->Cell(0,15,'Sobrante');    
  }
$pdf->SetFont('Arial','',12);
$pdf->Cell(-196);
$pdf->Cell(0,25,number_format($totalingresos,2));
$pdf->Cell(-135);
$pdf->Cell(0,25,number_format($totalegresos,2));
$pdf->Cell(-80);
$pdf->Cell(0,25,number_format($totaldepositos,2));
$pdf->Cell(-35);
$pdf->Cell(0,25,number_format($totaldif,2));

//cierra pdf
$pdf->Output();
?>