<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    include "menu.php";
    require('fpdf.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de consulta Pedido</title>
      <script src="js/jquery.min.js"></script>
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <br>
    
    <center><h1>Reporte Stock minimo</h1></center>
    
     
    <?php

  

    
    
    if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
      $cod = $_REQUEST['datos'];
      
    } 
    if(isset($_REQUEST['minimo']) && !empty($_REQUEST['minimo'])){
      $stock_minimo = $_REQUEST['minimo'];
      
    } 
    if(isset($_REQUEST['maximo']) && !empty($_REQUEST['maximo'])){
      $stock_maximo = $_REQUEST['maximo'];
      
    } 
    if(isset($_REQUEST['mensual']) && !empty($_REQUEST['mensual'])){
      $meses = $_REQUEST['mensual'];
      
    }
    if ($cod=='1') {
      $codigo='1';
    }elseif ($cod=='2') {
      $codigo='2';
    }elseif ($cod=='4') {
      $codigo='0';
    }elseif ($cod=='9') {
      $codigo='9';
    }elseif ($cod=='3') {
      $codigo='3';
    }elseif ($cod='5') {
      $codigo='';
    }
   
    class PDF extends FPDF
{
    // Cargar los datos

    function LoadData($codigo,$stock_minimo,$stock_maximo,$meses)
    {
        
        require("db.php");
          // Meses de estudio
          //$sql5="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad=5";
          //if(($prom2 = odbc_exec($id,$sql5)) === false)
          //    die("Error en Select: ". odbc_errormsg($id));
          //while($arr5 = odbc_fetch_array($prom2)){
          //    $tiempo=$arr5["Valor"];
          //}

          $suge=0;
          $reglon=0;
        
        $sql="SELECT i.CODIGO,i.DESCRIPCION,i.EXISTENCIA,i.costodol, p.shortname,
          substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1)
          WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
          WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
          Else i.CODIGO END As Filtro FROM inventario as  i LEFT join Proveedores as p on p.codigo=i.proveedor WHERE i.CODIGO like '".$codigo."%' ORDER BY Filtro";

        if(($resultado = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

        $data = array();
        $i=0;

        while($arr = odbc_fetch_array($resultado)){
       

        //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $existencia=$arr["EXISTENCIA"];
          $costodol=$arr["costodol"];
          $proveedor=$arr["shortname"];
          if ($proveedor=='') {
            $proveedor='Local';
          }
          $minimo=0;

          //  ***** Promedio de Ventas ****
          $sql2="SELECT (SUM(cantidad)/12) as promedio from hgeneral where codigo = '$codigos' 
              AND movimiento not in('Ajuste') AND 
              substring(factura,1,1)='F' AND 
              FECHA >= Dateadd(yy,-1,Getdate())AND  
              FECHA <= GETDATE()";
         
          if(($prom = odbc_exec($id,$sql2)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
            while($arr2 = odbc_fetch_array($prom)){
              $promedio=$arr2["promedio"];
          
            }

          $minimo = $promedio*$stock_minimo;
          ///**** Busqueda en pedidos ***
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
          if(($pedi = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=$arr3["pedido"];
            }
            
            $suge = ($promedio*$stock_maximo)- $existencia-$pedidos;
            if ($suge>0) {
              if ($promedio>$meses) {
                if ($existencia < $minimo ) {
                  $data[$i]['CODIGO']=$codigos;
                  $data[$i]['DESCRIPCION']= substr($descripcion,0,25);
                  $data[$i]['EXISTENCIA']=$existencia;
                  $data[$i]['COSTODOL']=$costodol;
                  $data[$i]['PROVEEDOR']=$proveedor;
                  $data[$i]['PROMEDIO']=$promedio;
                  $data[$i]['MINIMO']=$promedio*$stock_minimo; //// VALOR MINIMO = PROMEDIO * CANT DE MESES ****
                  $data[$i]['NECESIDAD']=$promedio*$stock_maximo;
                  $data[$i]['PEDIDOS']=$pedidos;
                  $data[$i]['SUGERIDO']=$suge;
                  
                  $i++;         
                }  
              }
            }
         
                
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
        global $codigo, $fecha_sis,$data,$stock_minimo,$stock_maximo,$meses;
         if ($codigo=='1') {
          $nombre='Volkswagen';
        }elseif ($codigo=='2') {
          $nombre='Daihatsu';
        }elseif ($codigo=='0') {
          $nombre='Volvo';
        }elseif ($codigo=='9') {
          $nombre='Accesorios';
        }elseif ($codigo=='3') {
          $nombre='Usados';
        }elseif ($codigo='') {
          $nombre='Todos';
        }
          $resultado = count($data);
        $this->Cell(0,15,'Reporte de Stock Minimo de '.$nombre. ' '.date("d-m-Y", $fecha_sis).' -- '.$resultado.' registros');
        $this->Ln(1);
        $this->Cell(40,30,'                            Minimo de meses '.$stock_minimo. ' --  Maximo de meses '.$stock_maximo.'  --  Promedio x mes '.$meses );
        
        // Salto de línea
        $this->Ln(22); //titulo de tabla
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',10);
        $this->SetFont('','B');
       
        $header = array('Codigo', 'Descripcion','Existencia', 'Dolar', 'Proveedor','Prom Men','Minimo','Prom Tot','Transito','Sugerido','Pedido');
        
        $w = array(40,65,20,12,20,18,18,18,15,18,18);
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

       $w = array(40,65,20,12,20,18,18,18,15,18,1);

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
            $this->Cell($w[1],6,$data[$i]['DESCRIPCION'],'LR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($data[$i]['EXISTENCIA'],2),'LR',0,'L',$fill);
            $this->Cell($w[3],6,number_format($data[$i]['COSTODOL'],2),'LR',0,'L',$fill);
            $this->Cell($w[4],6,$data[$i]['PROVEEDOR'],'LR',0,'L',$fill);
            $this->Cell($w[5],6,number_format($data[$i]['PROMEDIO'],2),'LR',0,'L',$fill);
            $this->Cell($w[6],6,number_format($data[$i]['MINIMO'],2),'LR',0,'L',$fill);
            $this->Cell($w[7],6,number_format($data[$i]['NECESIDAD'],2),'LR',0,'L',$fill);
            $this->Cell($w[8],6,number_format($data[$i]['PEDIDOS'],2),'LR',0,'L',$fill);
            $this->Cell($w[9],6,number_format($data[$i]['SUGERIDO'],2),'LR',0,'L',$fill);
            $this->Cell($w[9],6,'','LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
            $i++;
        }
        
    }

}

$pdf = new PDF('L','mm','letter');
// Carga de datos
$data = $pdf->LoadData($codigo,$stock_minimo,$stock_maximo,$meses);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($data);
$pdf->Output($name='reportes/stock.pdf');
echo '<br>';
echo '<a href="minimos_rep_articulos.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a href="reportes/stock.pdf"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
?>

<script src="js/bootstrap.min.js"></script>

  </body>
</html>