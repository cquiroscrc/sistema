<?php 
date_default_timezone_set("America/Costa_Rica");
require_once('tcpdf_include.php');
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");


$fecha = date("d-m-Y H:i:s"); 

if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
      $factura = $_REQUEST['factura'];
}
$sql2 ="SELECT TOP 1 Proveedor FROM Pedidos WHERE Factura='".$factura."'";
$ejecutar2 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql2);
foreach ($ejecutar2 as $key => $arr) {
    $proveedor=$arr["Proveedor"];
}

$sql3 ="SELECT * FROM Proveedores WHERE codigo='".$proveedor."'";
$ejecutar3 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql3);
foreach ($ejecutar3 as $key => $arr) {
    $nombre=$arr["nombre"];
}


$sql="SELECT p.ID,p.Sugerido,p.Precio,p.Proveedor,p.Factura,p.Codigo,p.Cantidad,p.etiquetas,p.Fecha,i.DESCRIPCION,i.Ubicacion,p.caja,i.EXISTENCIA,
        substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1)
        WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
        WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
        Else p.codigo END As Filtro FROM Pedidos as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE Factura='".$factura."' ORDER BY Filtro";



class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        
        $this->SetFont('helvetica', 'I', 8);
        // Title
        $this->Cell(0, 15, 'Los Paticas -- Reporte Pedidos', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Los Paticas -- Reporte Pedidos -- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Maximo 540
$pdf-> startPageGroup();
//$pdf->setPrintHeader(false);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP,  PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE,15);
$pdf-> AddPage();

$pdf->SetFont('helvetica', 'B', 20);
$pdf->Write(0, 'Reporte de pedidos '.$factura.' - '.$nombre, '', 0, 'C', true, false, false, false, 0);
$pdf->SetFont('helvetica', 'B', 9);

$bloque2 = <<<EOD
<br>
<table cellspacing="0" cellpadding="1" border="1">

  <tr style="background-color:#FF0000;color:white;">
       <th style="width:105px">Código</th>
       <th style="width:210px">Descripción</th>
       <th style="width:45px">Ubi</th>
       <th style="width:70px">Exist</th>
       <th style="width:48px">Cant</th>
       <th style="width:50px">Imagen</th>
       <th style="width:40px">Etiqueta</th>
  </tr>

</table>


EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$i=0;
foreach ($ejecutar as $key => $arr) {
$codigos=$arr["Codigo"];
$sql_topran="SELECT * FROM topran WHERE Codigo='$codigos'";
$ejecutart = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_topran);
$topran="";
foreach ($ejecutart as $key2 => $arr1) {
    $topran=" *Topran ";
    $topran=$topran.$arr1["topran"];
    $topran=$topran." *";
}
$codigos2=$codigos.$topran;
$descripcion=$arr["DESCRIPCION"];
//$descripcion=$descripcion.$topran;
$costo=$arr["Precio"];
$cantidad=$arr["Cantidad"];
$ubicacion=$arr["Ubicacion"];
$etiquetas='';
$existencia=$arr["EXISTENCIA"];
$pdf->SetFont('helvetica', '', 10);

$path='../../../../subida_cat/';

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

            $existes = $_SERVER["DOCUMENT_ROOT"].'/subida_cat/'.$directorio.$foto.'.jpg';

            if (is_file($existes)) {
                $archivo=$filename;
            } else {
                $archivo=$path."inf.png";
            }


$bloque3 = <<<EOD

<table cellspacing="0" cellpadding="1" border="1">
<tbody>  
<tr>
    <td style="width:105px">$codigos2</td>
    <td style="width:210px">$descripcion</td>
    <td style="width:45px">$ubicacion</td>
    <td style="width:70px">$existencia</td>
    <td style="width:48px">$cantidad</td>
    <td style="width:50px"><img src="$archivo"  width="40" height="40"></td>
    <td style="width:40px"></td>
</tr>
</tbody>  
</table>


EOD;

$pdf-> writeHTML($bloque3,false,false,true,0,'');
$i=$i+1;

  if ($i==14) {
    $pdf-> AddPage();
    $i=0;
    $bloque2 = <<<EOD
    <br>
    <table cellspacing="0" cellpadding="1" border="1">
    
      <tr style="background-color:#FF0000;color:white;">
           <th style="width:105px">Código</th>
           <th style="width:210px">Descripción</th>
           <th style="width:45px">Ubi</th>
           <th style="width:70px">Exist</th>
           <th style="width:48px">Cant</th>
           <th style="width:50px">Imagen</th>
           <th style="width:40px">Etiqueta</th>
      </tr>  
    </table>

EOD;
$pdf-> writeHTML($bloque2,false,false,false,false,'');
  }
}
$pdf->Output(__DIR__ .'../../../../Pedido/pedido_'.$factura.'.pdf', 'F');
$pdf->Output('pedido_'.$factura.'.pdf', 'I');

