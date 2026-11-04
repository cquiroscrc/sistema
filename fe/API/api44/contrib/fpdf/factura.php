<?php


require('fpdf.php');
require ('tcpdf_barcodes_2d.php');
class FacturaPDF extends FPDF
{

    var $widths;
    var $aligns;

    function getData($sessionID, $Documento, $TipoDocumento){
        //Datos de Clientes y otros
        $sql="SELECT doc.DOCUMENTO, doc.fecha, doc.cedula, doc.nombre_cliente, doc.direccion, doc.celular, doc.correo, doc.a_nombre_de, doc.codigo_cliente, 
                dcl.articulo, dcl.desc_articulo, dcl.cantidad, dcl.precio, dcl.subtotal, dcl.descuento, dcl.total, 
                dcl.impuesto, doc.sat, doc.vendedor, doc.nombre, dcp.TOTALSERVGRAVADOS, dcp.TOTALSERVEXENTOS, dcp.TOTALCOMPROBANTE,
                dcp.TOTALMERCANCIASGRAVADAS, dcp.TOTALMERCANCIASEXENTAS, dcp.TOTALGRAVADO, 
                dcp.TOTALEXENTO, dcp.TOTALVENTA, dcp.TOTALDESCUENTOS, TOTAL_SERVICIOS_EXONERADOS,TOTAL_MERCANCIAS_EXONERADAS,TOTAL_EXONERADO,
                dcp.TOTALVENTANETA, dcp.TOTALIMPUESTO, dcp.QRCode,doc.Adicional,
                (SELECT  NOMBRE  from inventario.dbo.COMPAÑIAS WHERE CONFIGURADA='SI' ) as NOMBRE_COMPANIA,
                (SELECT  EMAIL_DOC_ELECTRONICO from inventario.dbo.COMPAÑIAS  WHERE CONFIGURADA='SI') as EMAIL_DOC_ELECTRONICO,
                (SELECT  DIRECCION_LINEA_3 from inventario.dbo.COMPAÑIAS WHERE CONFIGURADA='SI' ) as DIRECCION_LINEA_3,
                (SELECT  DIRECCION_LINEA_1 from inventario.dbo.COMPAÑIAS  WHERE CONFIGURADA='SI') as DIRECCION_LINEA_1,
                (SELECT  DIRECCION_LINEA_2 from inventario.dbo.COMPAÑIAS  WHERE CONFIGURADA='SI') as DIRECCION_LINEA_2,
                (SELECT  NIT from inventario.dbo.COMPAÑIAS WHERE CONFIGURADA='SI' ) as NIT,
                (SELECT TELEFONO_1 from inventario.dbo.COMPAÑIAS WHERE CONFIGURADA='SI' )as TELEFONO_1,
                (SELECT TELEFONO_2 from inventario.dbo.COMPAÑIAS WHERE CONFIGURADA='SI' )as TELEFONO_2
             FROM   (inventario.dbo.DOCUMENTOS doc 
             LEFT OUTER JOIN inventario.dbo.DOC_ELECTRONICO_PROCESADO dcp
             ON (doc.DOCUMENTO=dcp.NUMERO_DOC)
             AND (doc.TIPO_DOC=dcp.TIPO_DOC)) 
             INNER JOIN inventario.dbo.DOCUMENTOS_LINEA dcl ON (doc.DOCUMENTO=dcl.documento)
                AND (doc.TIPO_DOC=dcl.TIPO_DOC)
             WHERE  doc.DOCUMENTO='$Documento'         
             ORDER BY doc.DOCUMENTO";

        $result=sql_exec($sql,2,false,true);
        return $result;
    }
    function CrearDocumento($sessionID, $Documento, $TipoDocumento)
    {

        $filename = "$Documento.pdf";

        $result = $this->getData($sessionID, $Documento, $TipoDocumento);

        $this->AddPage(); //Primera pagina, las demas automaticas
        $this->ImprimirFooter(  $result);
        $this->ImprimirEncabezados($sessionID, $Documento, $TipoDocumento,$result);
        $this->ImprimirLineas($sessionID, $Documento, $TipoDocumento, $result);

        //$this->Output("F", $filename);
        return $this->Output("S");
        //return $filename;
    }

function ImprimirLineas($sessionID, $Documento, $TipoDocumento,$result){

    $this->SetWidths(array(30,50,18,20,20,21,21,23));
    $this->SetAligns(array('L','L','R','R','R','R','R','R'));
    foreach ($result as $linea){
        $this->Row(
            array(
                $linea["articulo"],
                $linea["desc_articulo"],
                $linea["cantidad"],
                number_format($linea["precio"],2),
                number_format($linea["subtotal"],2),
                number_format($linea["impuesto"],2),
                number_format($linea["descuento"],2),
                number_format($linea["total"],2)),
            $sessionID, $Documento, $TipoDocumento,$result);
        $this->SetX( 3);

    }
}
function  PrintTotals($text,$value,$x,$y,$align,$multi=false){
        $this->SetXY($x,$y);
        if($multi){
            $this->MultiCell(50,10,$text);
            $this->SetXY($x+30,$y); $this->MultiCell(50,10,number_format($value,2));
        }
        else{
            $this->Cell(50,10,$text ,"",2,$align,false);
            $this->SetXY($x+30,$y); $this->Cell(50,10,number_format($value,2),"",2,$align,false);
        }

}
function ImprimirFooter(  $result){


    // set the barcode content and type
    $barcodeobj = new TCPDF2DBarcode($result[0]["sat"], 'QRCODE,H');
    // output the barcode as PNG image
    $image=$barcodeobj->getBarcodePNG(4, 4, array(0,0,0));
    $img_base64_encoded = "data:image/png;base64,$image";
    $imageContent = file_get_contents($img_base64_encoded);
    $filename =$result[0]["sat"]. '.png';
    file_put_contents ($filename, $imageContent);

    $this->Line(2, $this->GetPageHeight()-90, $this->GetPageWidth()-3, $this->GetPageHeight()-90);
    $this->Line(48, $this->GetPageHeight()-90, 48, $this->GetPageHeight()-15);
    $this->Image($filename,5,$this->GetPageHeight()-88,40,40);
    unlink($filename);
    //MemImage($img_base64_encoded, 5,$this->GetPageHeight()-88,40,40);
    $this->Line(2, $this->GetPageHeight()-15, $this->GetPageWidth()-3, $this->GetPageHeight()-15);

    $lsep=4.9;
    $linit=90;
    $x=60;
    $this->SetFont("Arial", "", 9);

    $this->PrintTotals("Total Mercancias Gravadas :", $result[0]["TOTALMERCANCIASGRAVADAS"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Total Mercancias Exentas :", $result[0]["TOTALMERCANCIASEXENTAS"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Total Mercancias Exoneradas :", $result[0]["TOTAL_MERCANCIAS_EXONERADAS"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;

    $this->PrintTotals("Total Servicios Gravados :", $result[0]["TOTALSERVGRAVADOS"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Total Servicios Exentos :", $result[0]["TOTALSERVEXENTOS"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Total Servicios Exonerados :", $result[0]["TOTAL_SERVICIOS_EXONERADOS"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;

    $this->PrintTotals("Total  Gravado :", $result[0]["TOTALGRAVADO"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Total  Exento :", $result[0]["TOTALEXENTO"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Total  Exonerado :", $result[0]["TOTAL_EXONERADO"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;

    $this->PrintTotals("Total Descuentos:", $result[0]["TOTALDESCUENTOS"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Total Venta Neta:", $result[0]["TOTALVENTANETA"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Total Impuestos:", $result[0]["TOTALIMPUESTO"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;
    $this->PrintTotals("Monto Impuesto Tarifa 13% :", $result[0]["TOTALIMPUESTO"] , $x,$this->GetPageHeight()-$linit, "R");$linit=$linit-$lsep;

    $this->Line($this->GetPageWidth()-65, $this->GetPageHeight()-65, $this->GetPageWidth()-3, $this->GetPageHeight()-65);//Horizontal
    $this->Line($this->GetPageWidth()-65, $this->GetPageHeight()-90, $this->GetPageWidth()-65, $this->GetPageHeight()-9);//Vertical
    $this->PrintTotals("Subtotal :", $result[0]["TOTALVENTANETA"] , $this->GetPageWidth()-94,$this->GetPageHeight()-63, "R");
    $this->PrintTotals("Descuento :", $result[0]["TOTALDESCUENTOS"] , $this->GetPageWidth()-94,$this->GetPageHeight()-57, "R");
    $this->PrintTotals("IVA :", $result[0]["TOTALIMPUESTO"] , $this->GetPageWidth()-94,$this->GetPageHeight()-51, "R");

    $this->SetFontSize(11);
    $this->SetFont("Arial",'B');
    $this->Line($this->GetPageWidth()-65, $this->GetPageHeight()-41, $this->GetPageWidth()-3, $this->GetPageHeight()-41);
    $this->SetXY($this->GetPageWidth()-65,$this->GetPageHeight()-40);
    $this->Cell(50,10," Total Comprobante :");
    
    $this->SetFontSize(14);
    $this->SetFont("Arial",'B');
    $this->SetXY($this->GetPageWidth()-60,$this->GetPageHeight()-31);
    $this->Cell(50,10, number_format($result[0]["TOTALCOMPROBANTE"],2),0,0,"R");

    $this->Line(2, $this->GetPageHeight()-9, $this->GetPageWidth()-3, $this->GetPageHeight()-9);

    $this->SetFontSize(9);
    $this->SetFont("Arial",'');
    $this->SetXY(3,-15);
    $this->Text(3,-15,  $result[0]["sat"]);


}
function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetXY($this->GetPageWidth()-64,-17);
        $this->SetFont('Arial','',9);
        $this->Cell(0,10,'Ver XML 4.4                          Pagina '.$this->PageNo(),0,0,'L');

        $this->SetXY(48,-13);
        $this->SetFont('Arial','B',10);
        $this->Cell(50,2,params_get("clave"),0,0,'C');

        $this->SetXY(43,-6);
        $this->SetFont('Arial','B',10);
        $this->Cell($this->GetPageWidth()/2,1,' Autorizada mediante resolucion No MH-DGT-RES-0027-2024',0,0,'C');
    }
function  ImprimirEncabezados($sessionID, $Documento, $TipoDocumento,$result){
        $pw=$this->GetPageWidth()-5;
        $ph=$this->GetPageHeight()-4;

        // logo : 184 de ancho et 120 de altp
        $this->Image(__DIR__ . '\LogoPaticas-small.png', 3, 4, 50, 37);
        $this->SetXY(110, 5);
        $this->SetFont("Arial", "B", 9);

        $this->RoundedRect(2, 2, $this->GetPageWidth()-5, $this->GetPageHeight()-5, 0, 'D');
        //Rectangulo para imagen
        $this->Line(2, 42, $pw+2, 42); //Horizontal, todo el ancho de ;a [agoma
        $this->Line(55, 2, 55, 42); //Vertical lado derecho imagen

        $this->Line($pw/1.5, 2, $pw/1.5 ,36);//Vertical division entre EMPRESA y FACTURA

        // Nombre empresa
        $this->SetXY( 80,5);
        $this->SetFontSize(14);
        $this->Cell(30,5,$result[0]["NOMBRE_COMPANIA"],'',0,'C');

        //Fecha en negrita y 11
        $this->SetFontSize(11);
        $this->SetXY( $pw/1.5,2);
        $date = date('d/m/Y H:i:s');
        $this->Cell(70,8,$date,'TB',0,'C');

        // Cedula en 9
        $this->SetFontSize(9);
        $this->SetXY( 55,14);
        $this->Cell(81.5,4,$result[0]["NIT"],'B',0,'C');

        //Numero de Factura en rojo
        $this->SetXY( $pw/1.5,10);
        $this->SetTextColor(255,0,0);
        $this->SetFontSize(14);
        $this->Cell(70,9,$Documento,'B',0,'C');

        //Direcciones: Tres lineas
        $this->SetTextColor(0,0,0);
        $this->SetFontSize(9);
        $this->SetXY( 55,20);
        $this->Cell(81.5,4,$result[0]["DIRECCION_LINEA_1"],'',0,'L');
        $this->SetXY( 55,26);
        $this->Cell(81.5,4,$result[0]["DIRECCION_LINEA_2"],'',0,'L');
        $this->SetXY( 55,32);
        $this->Cell(81.5,4,$result[0]["DIRECCION_LINEA_3"],'',0,'L');

        //Telefonos de la compania, 2
        $this->SetXY( $pw/1.5,19);
        $this->SetFont('');
        $this->SetTextColor(0,0,0);
        $this->SetFontSize(10);
        $text="Telefonos: " . $result[0]["TELEFONO_1"] . "   " .$result[0]["TELEFONO_2"];
        $this->Cell(70,8,$text,'B',0,'L');
        //Correo de la compania
        $this->SetXY( $pw/1.5,27);
        $text="Correo: \n" . $result[0]["EMAIL_DOC_ELECTRONICO"];
        $this->MultiCell(70,5,$text,'', 'L',false);

        // Venededor
        $this->SetXY( 55,36);
        $text="Vendedor:". $result[0]["vendedor"]  . "   ". $result[0]["nombre"];
        $this->Cell($pw-53,6,$text,'T', 0,'L');

        //Cliente
        $this->SetXY( 2,42);
        $text="Cliente: ". $result[0]["codigo_cliente"]  . "   ". $result[0]["nombre_cliente"];
        $this->Cell($pw-93,8,$text,'R', 0,'L');

        $this->SetXY( 2,49);
        $text= rtrim($result[0]["a_nombre_de"]) . " - ". $result[0]["Adicional"]; //.'('.$result[0]["Adicional"]+')'
        $this->Cell($pw-93,8,$text,'BR', 0,'L');

        //Segunda Columna de Datos
        $xPosInit=114;
        //Telefono y Correo
        $this->SetXY( $xPosInit,42);
        $text="Telefono:    ". $result[0]["celular"]  ;
        $this->Cell($pw-93,8,$text,'', 0,'L');

        //Correo
        $this->SetXY( $xPosInit,49);
        $text="Correo:  ". $result[0]["correo"]  ;
        $this->Cell(93,8,$text,'B', 0,'L');

        //Condicion Pago
        $this->SetXY( $xPosInit,56);
        $text="Condicion Pago:  Contado" ;
        $this->Cell(93,8,$text,'LB', 0,'L');

        //Medio Pago
        $this->SetXY( $xPosInit,64);
        $text="Medio Pago:  Efectivo" ;
        $this->Cell(93,8,$text,'LB', 0,'L');

        //Primera fila de datos
        //Cedula
        $xPosInit=2;
        $this->SetXY( $xPosInit,56);
        $text="Cedula:  ". $result[0]["cedula"]  ;
        $this->Cell($pw-93,8,$text,'B', 0,'L');     //x=2 hasta la mita del pdf.

        //Direccion
        $this->SetXY( $xPosInit,64);
        $text="Direccion:   ". $result[0]["direccion"]  ;
        $this->MultiCell($pw-93,8,$text,'RB', 'L',false); //x=2 hasta la mita del pdf.

        //Encabezados de columnas de los codigos de las lineas
        $this->SetXY( 3,$this->GetY()+1);
        $this->Cell(30,6,'Codigo','LRBT', 0,'C');

        $this->SetXY( $this->GetX(),$this->GetY());
        $this->Cell(50,6,'Descripcion','RBT', 0,'C');


        $this->SetXY( $this->GetX(),$this->GetY());
        $this->Cell(18,6,'Cantidad','RBT', 0,'C');

        $this->SetXY( $this->GetX(),$this->GetY());
        $this->Cell(20,6,'Precio','RBT', 0,'C');

        $this->SetXY( $this->GetX(),$this->GetY());
        $this->Cell(20,6,'Subtotal','RBT', 0,'C');

        $this->SetXY( $this->GetX(),$this->GetY());
        $this->Cell(21,6,'IVA','RBT', 0,'C');

        $this->SetXY( $this->GetX(),$this->GetY());
        $this->Cell(21,6,'Descuento','RBT', 0,'C');

        $this->SetXY( $this->GetX(),$this->GetY());
        $this->Cell(23,6,'Total','RBT', 0,'C');

        $this->SetFontSize(9);
        $this->SetXY( $this->GetX()-$pw+2,$this->GetY()+7);
    }

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function Row($data,$sessionID, $Documento, $TipoDocumento,$result)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h,$sessionID, $Documento, $TipoDocumento,$result);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            if($i==0) {
                $this->Line($x-1,$y,$x+$w,$y);
                $this->Line($x-1,$y+$h,$x+$w,$y+$h);
            }
            else{
                if($i==count($data)){
                    $this->Line($x-1,$y,$x+$w,$y);
                    $this->Line($x-1,$y+$h,$x+$w,$y+$h);
                }
                else{
                    $this->Rect($x,$y,$w,$h);
                }
            }
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h,$sessionID, $Documento, $TipoDocumento,$result)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger-90){
            $this->AddPage($this->CurOrientation);
            //echo "\nNew Page x=".$this->GetX() . ',y='.$this->GetY()."";
            $this->ImprimirFooter(  $result);
            $this->ImprimirEncabezados($sessionID, $Documento, $TipoDocumento,$result);
           // $this->ImprimirFooter($sessionID, $Documento, $TipoDocumento, $result);
        }

    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
    // private functions
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

    function Rotate($angle, $x=-1, $y=-1)
    {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }

}



