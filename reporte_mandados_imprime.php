<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
include "menu.php";
require __DIR__ . '/autoload.php';
use Escpos\Printer;
use Escpos\PrintConnectors\NetworkPrintConnector;
use Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Escpos\PrintBuffers\EscposPrintBuffer;
use Escpos\PrintBuffers\ImagePrintBuffer;
require_once("db.php");

if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
  $fecha2 = $_REQUEST['Date2'];
}
if(isset($_REQUEST['motorizado']) && !empty($_REQUEST['motorizado'])){
  $motorizado=$_REQUEST['motorizado']; 
}


$sqlmensa = "SELECT * FROM mensajeros WHERE id='$motorizado'";

   if(($resmensa = odbc_exec($id,$sqlmensa)) === false)
  die("Error en Select: ". odbc_errormsg($id));

while($arrmensa = odbc_fetch_array($resmensa)){
      $nombremensa=$arrmensa['nombre'];
      $monto=$arrmensa['monto'];
      $mandado=$arrmensa['mandado'];
}


$sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='08'";  
    if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
        $consecutivo=$arr_consecutivo["numero"];
    }

$sql="SELECT * FROM mandados WHERE fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and motorizado ='$motorizado'
      order by motorizado,fecha ASC";  

          if(($res = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

$mandados=0;
$unidades=0;
$subtotal=0;
while($arr = odbc_fetch_array($res)){
      $cantidad=$arr["cantidad"];
      $mandados=$mandados+$arr['cantidad'];
      $motorizado=$arr["motorizado"];
      $subtotal=$subtotal+($arr['cantidad']-$mandado);
      if ($cantidad==0) {
        //no suma
      }else{
        $unidades++;      
      }
}


try {
    //$connector = new NetworkPrintConnector("192.168.1.86", 9100); // Hernan
    $connector = new NetworkPrintConnector("172.16.12.87", 9100);   //La de Caja
    
    
    $printer = new Printer($connector);
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
    $printer -> setEmphasis(true);
    $printer -> text("REPUESTOS LOS PATICAS\n");
    $printer -> setEmphasis(false);
    $printer -> selectPrintMode();
    $printer -> text("Tai Costa S.A Cedula 3-101-090358-37\n");
    $printer -> feed();
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
    $printer -> text("M-000000".$consecutivo."\n"); /// Documento *******
    $printer -> text("PAGO DE MANDADOS\n"); /// Documento *******
    $printer -> feed();
    $printer -> selectPrintMode();
    $printer -> setEmphasis(true);
    $fecha=strftime( "%d-%m-%Y %H:%M:%S", time() );
    $printer -> text($fecha."\n");  // Fecha *****
    $printer -> setEmphasis(false);
    $printer -> feed();
    $printer -> text("Mensajero: ".$nombremensa."\n"); /// Mensajero *******
    $printer -> text("CONTADO\n");
    $printer -> text("Vendedor: 15 - Hernan Hernandez\n");
    $printer -> feed();
    $printer -> setEmphasis(true);
    $printer -> text("CANT // DESCRIPCION \n");
    $printer -> setEmphasis(false);
    $printer -> feed();
    $printer -> text($mandados." // MANDADOS\n");  /// MANDADOS ******  
    $printer -> feed();
    $fecha_sis1=strtotime($fecha1);
    $fecha_sis2=strtotime($fecha2);
    $printer -> text("DE: ".date("d-m-Y", $fecha_sis1)." // A: ".date("d-m-Y", $fecha_sis2)."\n");  /// MANDADOS ******   
    $printer -> feed();
    $printer -> text("CANTIDAD DE VIAJES: // ".$unidades." // \n");  /// MANDADOS ******   
    $printer -> feed();
    $printer -> text("                SUBTOTAL : ".number_format($subtotal*$monto,2)."\n");  // SUBTOTAL****
    $printer -> feed();
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
    $printer -> text("TOTAL : ".number_format($subtotal*$monto,2)."\n");  // TOTAL *****
    $printer -> feed();   
    $printer -> selectPrintMode();
    // HACIENDA
    $printer -> text("Autorizado para impresion por sistema\n");
    $printer -> text("de computo mediante reolucion #11-97\n");
    $printer -> feed();
    $printer -> feed();
    $printer -> feed();
    $printer -> feed();
    $printer -> feed();
    $printer -> text("FIRMA________________________________________\n");
    $printer -> feed();
    $printer -> feed();
    $printer -> text("ORIGINAL\n");

    $printer -> cut(); ///  TERMINA ******
    
    /* Close printer */
    $printer -> close();
    $consecutivo++;
    
    $sql_consecutivo="UPDATE Consecutivos SET numero='$consecutivo' WHERE tipo='08'";  
    if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    echo '<a href="reporte_mandados.php"><img width="48"  height="48"  src= "img/back.png" >Volver</a>';
} catch (Exception $e) {
    echo "No se pudo imprimir: " . $e -> getMessage() . "\n";
    echo '<a href="reporte_mandados.php"><img width="48"  height="48"  src= "img/back.png" >Volver</a>';
}
