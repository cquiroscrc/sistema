<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
require '../../extensiones/Escpos/autoload.php';
require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");
use Escpos\Printer;
use Escpos\PrintConnectors\NetworkPrintConnector;
use Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Escpos\PrintBuffers\EscposPrintBuffer;
use Escpos\PrintBuffers\ImagePrintBuffer;



if(isset($_REQUEST['motorizado']) && !empty($_REQUEST['motorizado'])){
  $motorizado=$_REQUEST['motorizado']; 
}


$sqlmensa = "SELECT * FROM mensajeros WHERE id='$motorizado'";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlmensa);
foreach ($ejecutar as $key => $arrmensa) {
      $nombremensa=$arrmensa['nombre'];
      $monto=$arrmensa['monto'];
      $mandado=$arrmensa['mandado'];
}


$sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='08'";  
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
foreach ($ejecutar as $key => $arr_consecutivo) {
      $consecutivo=$arr_consecutivo["numero"];
}

$sql="SELECT * FROM mandados where aplicado is null AND motorizado='$motorizado' ORDER BY fecha DESC";  
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

$mandados=0;
$unidades=0;
$subtotal=0;
foreach ($ejecutar as $key => $arr) {

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
    $printer -> feed();
    $printer -> setEmphasis(true);
    $printer -> text("CANT // DESCRIPCION \n");
    $printer -> setEmphasis(false);
    $printer -> feed();
    $printer -> text($mandados." // MANDADOS\n");  /// MANDADOS ******  
    $printer -> feed();
    //$fecha_sis1=strtotime($fecha1);
    //$fecha_sis2=strtotime($fecha2);
    //$printer -> text("DE: ".date("d-m-Y", $fecha_sis1)." // A: ".date("d-m-Y", $fecha_sis2)."\n");  /// MANDADOS ******   
    $printer -> feed();
    $printer -> text("CANTIDAD DE VIAJES: // ".$unidades." // \n");  /// MANDADOS ******   
    $printer -> feed();
    $printer -> text("                SUBTOTAL : ".number_format($subtotal*$monto,2)."\n");  // SUBTOTAL****
    $printer -> feed();
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
    $printer -> text("TOTAL : ".number_format($subtotal*$monto*1.1050,2)."\n");  // TOTAL *****
    $printer -> feed();   
    $printer -> selectPrintMode();
    // HACIENDA
    $printer -> feed();
    $printer -> feed();
    $printer -> text("Firma ____________________\n");
    $printer -> feed();
    $printer -> feed();
    $printer -> text("ORIGINAL\n");

    $printer -> cut(); ///  TERMINA ******
    
    /* Close printer */
    $printer -> close();
    $consecutivo++;
    
    $sql_consecutivo="UPDATE Consecutivos SET numero='$consecutivo' WHERE tipo='08'";  
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);

} catch (Exception $e) {
    echo "No se pudo imprimir: " . $e -> getMessage() . "\n";
    echo '<a href="reporte_mandados.php"><img width="48"  height="48"  src= "img/back.png" >Volver</a>';
}
