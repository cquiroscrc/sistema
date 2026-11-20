<?php
session_start();
include "menu.php";
require('fpdf.php');

if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
  $datos = $_REQUEST['datos'];
  
} 
if(isset($_REQUEST['tipos']) && !empty($_REQUEST['tipos'])){
  $tipos = $_REQUEST['tipos'];
  
} 
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

$codigo='';
$nombre='';

if ($datos=='VW') {
    $codigo='1';
    $nombre='Volkswagen';
}elseif ($datos=='DH') {
    $codigo='2';
    $nombre='Daihatsu';
}elseif ($datos=='EM') {
    $codigo='3';
    $nombre='Empi';
}elseif ($datos=='TY') {
    $codigo='4';
    $nombre='Toyota';
}elseif ($datos=='NS') {
    $codigo='4';
    $nombre='Nissan';
}elseif ($datos=='HY') {
    $codigo='4';
    $nombre='Hyundai';
}elseif ($datos=='AC') {
    $codigo='9';
    $nombre='Accesorios';
}elseif ($datos=='TT') {
    $datos="%";
    $codigo='';
    $nombre='Todas las marcas';    
}


//$fecha1=$fecha2;


class PDF extends FPDF
{
    // Cargar los datos

    function LoadData($codigo,$nombre,$tipos,$datos)
    {
        // Leer las líneas del fichero
        require("db.php");
        //global $codigo, $nombre;
        
        if ($tipos=='ventas') {
            $sql="SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            Else CODIGO END As Filtro FROM inventario WHERE CODIGO like '".$codigo."%' and MARCA like '".$datos."' ORDER BY Filtro";    

        }elseif ($tipos=='costos') {
            $sql="SELECT CODIGO,DESCRIPCION,EXISTENCIA,COSTO,ubicacion,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            Else CODIGO END As Filtro FROM inventario WHERE CODIGO like '".$codigo."%' and MARCA like '".$datos."' ORDER BY Filtro";
        }elseif ($tipos=='ubicaciones') {
            $sql="SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            Else CODIGO END As Filtro FROM inventario WHERE CODIGO like '".$codigo."%'  and MARCA like '".$datos."' ORDER BY ubicacion,Filtro";
        }elseif ($tipos=='negativos') {
            $sql="SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            Else CODIGO END As Filtro FROM inventario WHERE CODIGO like '".$codigo."%' and MARCA like '".$datos."' AND EXISTENCIA < 0 ORDER BY Filtro";
        }

        

        //echo $sql;
        if(($res = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

        
        $data = array(); 
        while($arr = odbc_fetch_array($res)){  
            //CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,
            $codigos=$arr["CODIGO"];
            $descripcion=$arr["DESCRIPCION"];
            $existencia= $arr["EXISTENCIA"];
            if ($tipos=='ventas') {
                $venta=$arr["VENTA"];}
            elseif ($tipos=='costos') {
                 $venta=$arr["COSTO"]; 
            }else{
              $venta=0;
            }

            $ubica=$arr["ubicacion"];
            


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

            $data[] = array($codigos,substr($descripcion, 0,30),$existencia,$venta,$ubica,$archivo) ;  
            

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
        $this->Cell(0,15,'Reporte de Inventario '.$nombre.' '.$tipos);
        
        // Salto de línea
        $this->Ln(23); //titulo de tabla
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',10);
        $this->SetFont('','B');
        if ($tipos=='costos') {
            $header = array('Codigo', 'Descripcion','Cantidad', 'Costo', 'Ubicacion','Imagen');
        }
        else{
            $header = array('Codigo', 'Descripcion','Cantidad', 'Venta', 'Ubicacion','Imagen');
        }
        $w = array(38,75,17,23,18,25);
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
    function FancyTable($data,$tipos)
    {
        // Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Cabecera

       $w = array(38,75,17,23,18,25);

        // Restauración de colores y fuentes
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Datos
        $fill = false;

        $i=0;
        

        foreach($data as $row)
        {
            $this->Cell($w[0],15,$row[0],1,0,'C',$fill);
            $this->Cell($w[1],15,$row[1],1,0,'C',$fill);
            $this->Cell($w[2],15,number_format($row[2],2),1,0,'C',$fill);
            if ($tipos=='costos') {
                $this->Cell($w[3],15,number_format($row[3],2),1,0,'C',$fill);
            }
            else{
                $this->Cell($w[3],15,number_format($row[3],2),1,0,'C',$fill);
            }   
            
            $this->Cell($w[4],15,$row[4],1,0,'C',$fill);
            $this->Cell( 25, 15, $this->Image($row[5], $this->GetX()+7, $this->GetY()+1, 12), 1, 0, 'C', false );
            $this->Ln();
            $fill = !$fill;

            
            $i++;
        }
        
    }

}


$pdf = new PDF('P','mm','letter');
// Carga de datos
$data = $pdf->LoadData($codigo,$nombre,$tipos,$datos);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($data,$tipos);
$pdf->Output($name='reportes/inventario.pdf');
echo '<br>';
echo '<a class="agregarUsuario" href="rep_articulos.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a class="agregarUsuario" target="_blank" href="reportes/inventario.pdf"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
?>

<script src="js/bootstrap.min.js"></script>