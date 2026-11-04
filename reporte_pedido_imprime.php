<?php
session_start();
require('fpdf.php');
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
global $tipo, $mov;
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
      
}
if(isset($_REQUEST['mov']) && !empty($_REQUEST['mov'])){
  $mov = $_REQUEST['mov'];
  
} 

if(isset($_REQUEST['formato']) && !empty($_REQUEST['formato'])){
  $formato = $_REQUEST['formato'];
  
} 
     include "menu.php";
     require('db.php');
     require_once ("Classes/PHPExcel.php");

$sql_pro="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad =5" ;  
  if(($res = odbc_exec($id,$sql_pro)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  while($arr = odbc_fetch_array($res)){
  $fecha_sis=$arr['Valor'];
  $fecha_sis=(int)$fecha_sis;

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
if ($formato==1) {
  
class PDF extends FPDF
{
// Cargar los datos

    function LoadData($tipo,$mov)
    {
      require('db.php');
       if ($tipo=='vw' ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '1%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
        }
        elseif ($tipo=='dai' ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '2%' 
          order by codigo";    
        }
        elseif ($tipo=='acc') {
          $sql="SELECT * FROM inventario WHERE codigo like '9%' 
          order by codigo";    
        }
        elseif ($tipo=='vol') {
          $sql="SELECT * FROM inventario WHERE codigo like '0%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
        }
        elseif ($tipo=='us') {
          $sql="SELECT * FROM inventario WHERE codigo like '3%' 
          order by codigo";    
        }

        if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

        while($arr = odbc_fetch_array($resultado)){
       
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $existencia=$arr["EXISTENCIA"];
          $costo=$arr["COSTO"];
          $venta=$arr["VENTA"];
          $costodol=$arr["costodol"];
          $proveedor=$arr["proveedor"];

          $sql2="SELECT codigo, shortname FROM Proveedores WHERE codigo='$proveedor'";
          
            if(($prov = odbc_exec($id,$sql2)) === false)
                die("Error en Select: ". odbc_errormsg($id));
            
            $nombre_prove="Local";
            
            while($arr2 = odbc_fetch_array($prov)){
                $nombre_prove=$arr2["shortname"];
            }
            /// aqui val el valor de estudio, cantidad de meses tambien en moviemientos.php   
            $sql3="SELECT (SUM(cantidad)/6) as promedio from hgeneral where codigo = '$codigos' 
                AND movimiento not in('Ajuste') AND 
                substring(factura,1,1)='F' AND 
                FECHA >= Dateadd(mm,-6,Getdate())AND  
                FECHA <= GETDATE()";

            if(($prom = odbc_exec($id,$sql3)) === false)
                die("Error en Select: ". odbc_errormsg($id));
        
            while($arr3 = odbc_fetch_array($prom)){
                $promedio=$arr3["promedio"];
                if ($promedio=="") {
                  $promedio=0; 
                }
            }

        $sql4="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
          if(($pedi = odbc_exec($id,$sql4)) === false)
            die("Error en Select: ". odbc_errormsg($id));  
         //$datos[]= array();
        while($arr4 = odbc_fetch_array($pedi)){
            $pedidos=$arr4["pedido"];
            if ($pedidos=="") {
                  $pedidos=0; 
            }
        }

        $sql_pro="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad =5" ;  
          if(($res = odbc_exec($id,$sql_pro)) === false)
            die("Error en Select: ". odbc_errormsg($id));
          while($arr = odbc_fetch_array($res)){
          $fecha_sis=$arr['Valor'];
          $fecha_sis=(int)$fecha_sis;

        }
        

         
        $suge = ($promedio*$fecha_sis)- $existencia-$pedidos;
        //$datos[]= array($codigos , $descripcion , $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*6 , $pedidos , $suge);
        

        if ($suge>0) {
            $pedir="";
            if ($mov==1) {  // Movimiento
              if ($promedio>0.99) {
                $datos[]= array($codigos ,substr($descripcion,0,20) ,$pedir, $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*$fecha_sis , $pedidos , $suge);
              }else{
             
              }

            }elseif ($mov==2) { //Movimiento medio
              if (($promedio<0.99)&&($promedio>0.49)) {
              $datos[]= array($codigos ,substr($descripcion,0,20) ,$pedir, $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*$fecha_sis , $pedidos , $suge);
              }else{
                //break;
              }

            }elseif ($mov==3) {  // sin movimiento
              if ($promedio<=0.49) {
              $datos[]= array($codigos ,substr($descripcion,0,20) ,$pedir, $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*$fecha_sis , $pedidos , $suge);
              }else{
                //break;
              }       

            }elseif ($mov==4) { 
              $datos[]= array($codigos ,substr($descripcion,0,20) ,$pedir, $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*$fecha_sis , $pedidos , $suge);
            }     
        }
      }



          $data= $datos;
        //devuelve valor   
        return $data;
    }


function Header()
{   
    
    $this->SetTextColor(128);
    $this->Cell(20,40,date("d/m/Y"));
    // Logo
    $this->Image('img/logo.png',10,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    $this->SetTextColor(0);
    // Movernos a la derecha
    $this->Cell(50);
    // Título
    global $tipo, $mov, $fecha_sis;
    
    if ($tipo=="vw") {
      $tipodesc="Volkswagen";
    }elseif ($tipo=="dai") {
      $tipodesc="Daihatsu";
    }elseif ($tipo=="acc") {
      $tipodesc="Accesorios";
    }elseif ($tipo=="vol") {
      $tipodesc="Volvo";
    }elseif ($tipo=="us") {
      $tipodesc="Usados";
    }
    if ($mov=="1") {
      $movdesc="Con Movimiento";
    }elseif ($mov=="2") {
      $movdesc="Medio movimiento";
    }elseif ($mov=="3") {
      $movdesc="Sin Movimiento";
    }elseif ($mov=="4") {
      $movdesc="Todos";
    }


    $this->Cell(0,15,'Reporte para pedidos '.$tipodesc.' '.$movdesc);
    
    // Salto de línea
    $this->Ln(23); //titulo de tabla
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('Arial','',10);
    $this->SetFont('','B');

    $header = array('Codigo', 'Descripcion','Pedir', 'Exist', 'Costo','Venta','Dolar','Proveedor','Prom      ','      X '.$fecha_sis.'','Transito','Sugerido');
    $w = array(45, 50, 15, 15, 25, 25, 15, 20, 12, 12, 15, 17);
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

    $w = array(45, 50, 15, 15, 25, 25, 15, 20, 12, 12, 15, 17);

    // Restauración de colores y fuentes
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Datos
    $fill = false;
    foreach($data as $row)
    {
        $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
        $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
        $this->Cell($w[2],6,$row[2],'LR',0,'L',$fill);
        $this->Cell($w[3],6,number_format($row[3],2),'LR',0,'L',$fill);
        $this->Cell($w[4],6,number_format($row[4],2),'LR',0,'L',$fill);
        $this->Cell($w[5],6,number_format($row[5],2),'LR',0,'L',$fill);
        $this->Cell($w[6],6,number_format($row[6],2),'LR',0,'L',$fill);
        $this->Cell($w[7],6,$row[7],'LR',0,'L',$fill);
        $this->Cell($w[8],6,number_format($row[8],2),'LR',0,'L',$fill);
        $this->Cell($w[9],6,number_format($row[9],2),'LR',0,'L',$fill);
        $this->Cell($w[10],6,number_format($row[10],2),'LR',0,'L',$fill);
        $this->Cell($w[11],6,number_format($row[11],2),'LR',0,'L',$fill);
        $this->Ln();
        $fill = !$fill;
    }
    // Línea de cierre
    $this->Cell(array_sum($w),0,'','T');
}
}

$pdf = new PDF('L','mm','letter');
// Títulos de las columnas
$header = array('Codigo', 'Descripcion', 'Existencia', 'Costo','Venta','Dolar','Proveedor','Prom Mens','Prom x 6','Transito','Sugerido');
// Carga de datos
$data = $pdf->LoadData($tipo,$mov);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output($name='reportes/pedidos.pdf');
//$pdf->Output('pedidos.pdf','D');
//$pdf->Output();
//sleep(350);
}

////************************** Excel ******************************

if ($formato==2) {
  
  if ($tipo=="vw") {
    $tipodesc="Volkswagen";
  }elseif ($tipo=="dai") {
    $tipodesc="Daihatsu";
  }elseif ($tipo=="acc") {
    $tipodesc="Accesorios";
  }elseif ($tipo=="vol") {
    $tipodesc="Volvo";
  }elseif ($tipo=="us") {
    $tipodesc="Usados";
  }
  if ($mov=="1") {
    $movdesc="Con Movimiento";
  }elseif ($mov=="2") {
    $movdesc="Medio movimiento";
  }elseif ($mov=="3") {
    $movdesc="Sin Movimiento";
  }elseif ($mov=="4") {
    $movdesc="Todos";
  }

$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Hernan Hernandez")
               ->setLastModifiedBy("Hernan Hernandez")
               ->setTitle("Reporte para Pedido")
               ->setSubject("Reporte para Pedido")
               ->setDescription("Reporte para hacer pedido")
               ->setKeywords("office 2007 openxml php")
               ->setCategory("Reportes");


    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte para Pedido  '.$tipodesc.' '.$movdesc);
    $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Codigo');
    $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Descripcion');
    $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Tecnica');
    $objPHPExcel->getActiveSheet()->setCellValue('D3', 'Pedir');
    $objPHPExcel->getActiveSheet()->setCellValue('E3', 'Existencia');
    $objPHPExcel->getActiveSheet()->setCellValue('F3', 'Costo');
    $objPHPExcel->getActiveSheet()->setCellValue('G3', 'Venta');
    $objPHPExcel->getActiveSheet()->setCellValue('H3', 'Dolar');
    $objPHPExcel->getActiveSheet()->setCellValue('I3', 'Proveedor');
    $objPHPExcel->getActiveSheet()->setCellValue('J3', 'Prom');
    $objPHPExcel->getActiveSheet()->setCellValue('K3', 'X '.$fecha_sis);
    $objPHPExcel->getActiveSheet()->setCellValue('L3', 'Transito');
    $objPHPExcel->getActiveSheet()->setCellValue('M3', 'Sugerido');
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $i=4;

        if ($tipo=='vw' ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '1%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
        }
        elseif ($tipo=='dai' ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '2%' 
          order by codigo";    
        }
        elseif ($tipo=='acc') {
          $sql="SELECT * FROM inventario WHERE codigo like '9%' 
          order by codigo";    
        }
        elseif ($tipo=='vol') {
          $sql="SELECT * FROM inventario WHERE codigo like '0%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
        }
        elseif ($tipo=='us') {
          $sql="SELECT * FROM inventario WHERE codigo like '3%' 
          order by codigo";    
        }

        if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

        while($arr = odbc_fetch_array($resultado)){
       
            $codigos=$arr["CODIGO"];
            $descripcion=$arr["DESCRIPCION"];
            $tecnica=$arr["DESC_ALT"];
            $existencia=$arr["EXISTENCIA"];
            $costo=$arr["COSTO"];
            $venta=$arr["VENTA"];
            $costodol=$arr["costodol"];
            $proveedor=$arr["proveedor"];

            $sql2="SELECT codigo, shortname FROM Proveedores WHERE codigo='$proveedor'";
          
            if(($prov = odbc_exec($id,$sql2)) === false)
                die("Error en Select: ". odbc_errormsg($id));
            
            $nombre_prove="Local";
            
            while($arr2 = odbc_fetch_array($prov)){
                $nombre_prove=$arr2["shortname"];
            }
            /// aqui val el valor de estudio, cantidad de meses tambien en moviemientos.php   
            $sql3="SELECT (SUM(cantidad)/6) as promedio from hgeneral where codigo = '$codigos' 
                AND movimiento not in('Ajuste') AND 
                substring(factura,1,1)='F' AND 
                FECHA >= Dateadd(mm,-6,Getdate())AND  
                FECHA <= GETDATE()";

            if(($prom = odbc_exec($id,$sql3)) === false)
                die("Error en Select: ". odbc_errormsg($id));
        
            while($arr3 = odbc_fetch_array($prom)){
                $promedio=$arr3["promedio"];
                if ($promedio=="") {
                  $promedio=0; 
                }
            }

            $sql4="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
              if(($pedi = odbc_exec($id,$sql4)) === false)
                die("Error en Select: ". odbc_errormsg($id));  
             //$datos[]= array();
            while($arr4 = odbc_fetch_array($pedi)){
                $pedidos=$arr4["pedido"];
                if ($pedidos=="") {
                      $pedidos=0; 
                }
            }

            $sql_pro="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad =5" ;  
              if(($res = odbc_exec($id,$sql_pro)) === false)
                die("Error en Select: ". odbc_errormsg($id));
              while($arr = odbc_fetch_array($res)){
              $fecha_sis=$arr['Valor'];

            }
            

         
            $suge = ($promedio*$fecha_sis)- $existencia-$pedidos;
            //$datos[]= array($codigos , $descripcion , $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*6 , $pedidos , $suge);
            

            if ($suge>0) {
                $pedir="";
                if ($mov==1) {  // Movimiento
                  if ($promedio>0.99) {
                    //$datos[]= array($codigos ,substr($descripcion,0,20) ,$pedir, $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*$fecha_sis , $pedidos , $suge);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $codigos);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $tecnica);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $pedir);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $existencia);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $costo);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $venta +100);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $costodol);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $nombre_prove);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $promedio);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $promedio*$fecha_sis);
                    $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $pedidos);
                    $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $suge);
                    $i++;


                  }else{
                  // nada
                  }

                }elseif ($mov==2) { //Movimiento medio
                  if (($promedio<0.99)&&($promedio>0.49)) {
                  //$datos[]= array($codigos ,substr($descripcion,0,20) ,$pedir, $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*$fecha_sis , $pedidos , $suge);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $codigos);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $tecnica);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $pedir);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $existencia);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $costo);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $venta +100);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $costodol);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $nombre_prove);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $promedio);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $promedio*$fecha_sis);
                    $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $pedidos);
                    $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $suge);
                    $i++;


                  }else{
                    //nada
                  }

                }elseif ($mov==3) {  // sin movimiento
                  if ($promedio<=0.49) {
                  //$datos[]= array($codigos ,substr($descripcion,0,20) ,$pedir, $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*$fecha_sis , $pedidos , $suge);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $codigos);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $tecnica);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $pedir);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $existencia);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $costo);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $venta +100);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $costodol);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $nombre_prove);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $promedio);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $promedio*$fecha_sis);
                    $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $pedidos);
                    $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $suge);
                    $i++;

                  }else{
                    //nada
                  }       

                }elseif ($mov==4) { 
                  //$datos[]= array($codigos ,substr($descripcion,0,20) ,$pedir, $existencia , $costo , $venta+ 100 , $costodol , $nombre_prove , $promedio , $promedio*$fecha_sis , $pedidos , $suge);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $codigos);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $tecnica);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $pedir);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $existencia);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $costo);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $venta +100);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $costodol);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $nombre_prove);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $promedio);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $promedio*$fecha_sis);
                    $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $pedidos);
                    $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $suge);
                    $i++;

                }     
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
      $objPHPExcel->getActiveSheet()->getStyle('A3:M'.$i)->applyFromArray($styleArray);
      unset($styleArray);

      // Set header and footer. When no different headers for odd/even are used, odd header is assumed.
      $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&G&C&HReporte para Pedido  '.$tipodesc.' '.$movdesc);
      $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

      // Set page orientation and size
      $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
      //$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('Pedido');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      // Save Excel 2007 file

      $callStartTime = microtime(true);

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
      $objWriter->save('reportes/pedido_estudio.xls');
      $callEndTime = microtime(true);
      $callTime = $callEndTime - $callStartTime;


}


//*************** Excel **********************
echo '<br>';
echo '<a class="agregarUsuario" href="reporte_pedido.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
if ($formato==1) {
  echo '<a class="agregarUsuario" target="_blank" href="reportes/pedidos.pdf"><img width="48"  height="48" src= "img/pdf.png">Abrir Reporte PDF</a> ';
  
}
if ($formato==2) {
  echo '<a class="agregarUsuario" target="_blank" href="reportes/pedido_estudio.xls"><img width="48"  height="48" src= "img/excel.png">Abrir Reporte Excel</a> ';  
}

?>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Rep Pedido Resultado';
</script>
<script src="js/bootstrap.min.js"></script>