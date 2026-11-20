<?php
//session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

require __DIR__ . '/autoload.php';
use Escpos\Printer;
use Escpos\PrintConnectors\NetworkPrintConnector;
use Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Escpos\PrintBuffers\EscposPrintBuffer;
use Escpos\PrintBuffers\ImagePrintBuffer;
require_once("db.php");

// ***** recibir datos de factura
if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
      $nombre = $_REQUEST['nombre'];      
    }

if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
      $descripcion = $_REQUEST['descripcion'];      
    }

if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
      $monto = $_REQUEST['monto'];      
    }

$sqlvalores = "SELECT * FROM tbl_Utiles WHERE IDUtilidad=10";  /// Valores encabezado

  if(($resvalores = odbc_exec($id,$sqlvalores)) === false)
    die("Error en Select: ". odbc_errormsg($id));

  while($arrvalores = odbc_fetch_array($resvalores)){
        $nombreempresa=$arrvalores['Descripcion'];
        $cedula=$arrvalores['Valor'];
        
  }


  $sqlvalores = "SELECT * FROM Consecutivos WHERE tipo=04";  /// Valores encabezado

  if(($resvalores = odbc_exec($id,$sqlvalores)) === false)
    die("Error en Select: ". odbc_errormsg($id));

  while($arrvalores = odbc_fetch_array($resvalores)){
        $letra=$arrvalores['letra'];
        $numero=$arrvalores['numero'];
        
  }
  
  $sql_fecha="SELECT * FROM fechas";       // Fecha del sistema
      if(($res_fecha = odbc_exec($id,$sql_fecha)) === false)
          die("Error en Select: ". odbc_errormsg($id));
      
      while($arr_fecha = odbc_fetch_array($res_fecha)){
          $fecha=strtotime($arr_fecha["Fecha"]);
          $time = time();
          $hora = date("H:i:s", $time);
      }
  require "numeros_conversor.php";
      if ($monto)
      {
        $resultado = convertir($monto);
        $num = number_format($monto,2,'.',''); 
        $cents = substr($num,strlen($num)-2,strlen($num)-1); 
        $resultado2= convertir($cents);
        
      }     

for ($i=0; $i < 2; $i++) { 
    
    
  try {
      //$connector = new NetworkPrintConnector("192.168.1.86", 9100); // Hernan
      $connector = new NetworkPrintConnector("172.16.12.87", 9100);   //La de Caja
      //$connector = new NetworkPrintConnector("192.168.10.87", 9100);   //La de Caja
      
      $printer = new Printer($connector);
      $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
      $printer -> setEmphasis(true);
      $printer -> text("REPUESTOS LOS PATICAS\n");
      $printer -> setEmphasis(false);
      $printer -> selectPrintMode();
      $nombreempresa = trim($nombreempresa);
      $cedula=trim($cedula);
      $printer -> feed();
      $printer -> text($nombreempresa."    ".$cedula."\n");
      $printer -> feed();
      $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
      $printer -> text("*** RECIBO DINERO ***\n");
      $printer -> text($letra."/". $numero."\n"); /// Documento *******
      $printer -> feed();
      $printer -> selectPrintMode();
      $printer -> setEmphasis(true);
      //$fecha=strftime( "%d-%m-%Y", $fecha ); 
      
      $printer -> text("Fecha : ".strftime( "%d-%m-%Y", $fecha )." Hora : ".$hora."\n");  // Fecha *****
      $printer -> setEmphasis(false);
      $printer -> feed();
      $printer -> text("Nombre: ".strtoupper(trim($nombre))."\n"); /// Cliente *******
      $printer -> feed();
      $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
      $printer -> text("DESCRIPCION\n");
      $printer -> setEmphasis(true);
      $printer -> selectPrintMode();
      $printer -> feed();
      $printer -> text(strtoupper(trim($descripcion))."\n");
      $printer -> setEmphasis(false);
      $printer -> feed();
      // Ciclo Articulos
      $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
      $printer -> text("Monto\n");
      $printer -> selectPrintMode();
      $printer -> feed();
      $printer -> setEmphasis(true);
      $printer -> text($resultado." COLONES CON ".$resultado2." CENTIMOS. \n");
      $printer -> setEmphasis(false);
      $printer -> feed();
      $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
      $printer -> text("TOTAL : ".number_format($monto,2)."\n");  // TOTAL *****
      $printer -> feed();   
      $printer -> selectPrintMode();
      // HACIENDA
      $printer -> text("Recibo Conforme\n");
      $printer -> feed();
      $printer -> feed();
      $printer -> text("_____________________________________\n");
      $printer -> feed();
      $printer -> feed();
      $printer -> text("Firma:__________________________________\n");
      $printer -> feed();
      $printer -> feed();
      $printer -> text("Cedula:__________________________________\n");
      $printer -> feed();
      $printer -> feed();
      $printer -> setEmphasis(true);
      $printer -> feed();
      $printer -> text("Visite : www.lospaticas.com\n");
      $printer -> setEmphasis(false);
      
      $printer -> cut(); ///  TERMINA ******
      
      /* Close printer */
  
      $printer -> close();
      
     
      
  } catch (Exception $e) {
      echo "No se pudo imprimir: " . $e -> getMessage() . "\n";
      echo '<a href="consulta_factura.php"><img width="48"  height="48"  src= "img/back.png" >Volver</a>';
      $var_continua=false;
  }
}
//Actualizar consecutivo
$sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='04'";       // Consecutivo factura
      if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
          die("Error en Select: ". odbc_errormsg($id));
      
      while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
          $factura=$arr_consecutivo["numero"];
          
      }
      $nueva_fac = $factura +1;

 $sql_estado = "UPDATE consecutivos set numero=".$nueva_fac." WHERE TIPO ='04'"; 
      
        if(($resultado_estado = odbc_exec($id,$sql_estado)) === false)
             die("Error en Select: ". odbc_errormsg($id));


echo 'Se Reimprimió correctamente';
?>

<SCRIPT LANGUAGE="javascript"> // Redirecciona al home
       setTimeout("location.href = 'index.php';",1500);
</SCRIPT> 