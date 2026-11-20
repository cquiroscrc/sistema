<?php
//session_start();
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


// ***** recibir datos de factura
if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $consecutivo = $_REQUEST['codigo'];      
    }

$sql="SELECT * FROM hfacturas WHERE nfac ='$consecutivo'"; 
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {    

        $factura= $respuesta[$i]['nfac'];
        //$fecha=strtotime($arr["fechafact"]);
        //$time = time();
        //$hora = date("H:i:s", $time);
        
        $fecha=$respuesta[$i]['fechafact'];
        $fecha=strtotime($fecha);
        $subtotal=$respuesta[$i]['subtotal'];
        $impuesto=$respuesta[$i]['impuesto'];
        $descuento=$respuesta[$i]['descuento'];
        $total=$respuesta[$i]['total'];
        $tipo=$respuesta[$i]['Tipo'];
        if ($tipo==0) {
          $tipo='CONTADO';

        }else{
          $tipo='CREDITO';
        }

        $estado=$respuesta[$i]['estado'];
        $cliente=$respuesta[$i]['proveedor'];
        $vendedor=$respuesta[$i]['vendedor'];
        $nombre=$respuesta[$i]['Nombre'];
        $adicional=$respuesta[$i]['Adicional'];
        $parcial=$subtotal-$descuento+$impuesto;
        $mensajeria=$total-$parcial;
        $referencia_consecutivo=$respuesta[$i]['consecutivo'];
        $sat=$respuesta[$i]['sat'];

      }

  $sqlvalores = "SELECT * FROM tbl_Utiles WHERE IDUtilidad=10";  /// Valores encabezado
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlvalores);
  
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) { 
        $nombreempresa=$respuesta[$i]['Descripcion'];
        $cedula=$respuesta[$i]['Valor'];
        
  }
  
  $sqlvendedor="SELECT * FROM Vendedores WHERE codigo = $vendedor";            /// Valores de cliente
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlvendedor);
  
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) { 
      $nombre_vendedor = $respuesta[$i]['nombre']; 
    }

  
  try {
      
      //$connector = new NetworkPrintConnector("172.16.12.86", 9100);   //Pruebas
      $connector = new NetworkPrintConnector("172.16.12.87", 9100);   //La de Caja
      
      
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
      $printer -> text($factura."\n"); /// Documento *******
      $printer -> feed();
      $printer -> selectPrintMode();
      $printer -> setEmphasis(true);
      //$fecha=strftime( "%d-%m-%Y", $fecha ); 
      
      $printer -> text("Fecha : ".date( "d-m-Y", $fecha )." Hora : ".date( "h:i:s", $fecha )."\n");  // Fecha *****
      $printer -> setEmphasis(false);
      $printer -> feed();
      $printer -> text("Nombre: ".strtoupper(trim($nombre))."\n"); /// Cliente *******
      $printer -> text("Adicional: ".strtoupper(trim($adicional))."\n");
      $printer -> text(strtoupper($tipo)."\n");
      $printer -> text("Cliente: ".$cliente."\n");
      $printer -> text("Vendedor: ".$vendedor." - ".$nombre_vendedor."\n"); // Vendedor
      $printer -> feed();
      $printer -> setEmphasis(true);
      $printer -> text("CANT // DESCRIPCION // PRECIO // TOTAL \n");
      $printer -> setEmphasis(false);
      $printer -> feed();
      // Ciclo Articulos
          $csubtotal=0;
          $cimpuesto=0;
          $ctotal=0;
          $sqlrelleno="SELECT h.Factura,h.codigo,h.vendedor,h.precio,h.cantidad,i.DESCRIPCION
            FROM hgeneral h, inventario i WHERE Factura='".$factura."' AND h.codigo = I.CODIGO";
            $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlrelleno);
  
                for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) { 
                
                 
                  $codigo=$respuesta[$i]['codigo'];
                  $descripcion=$respuesta[$i]['DESCRIPCION'];
                  $tama=strlen($descripcion);  /// Tamaño del texto
                  if ($tama>40) {
                    $descripcion = substr($descripcion,0,40);
                  }
                  $cantidad=$respuesta[$i]['cantidad'];
                  $precio=$respuesta[$i]['precio'];
                  $totalcod=$respuesta[$i]['cantidad']*$respuesta[$i]['precio'];
                  
                  $csubtotal=$csubtotal+$totalcod;
                  $printer -> text($cantidad." // ".$descripcion."\n");
                  $printer -> text("   ".$codigo."\n");
                  $printer -> text("                      ".number_format($precio,2)." // ".number_format($totalcod,2)."\n");
                  
                }

                // calculo de la factura

      $printer -> feed();
      //// TOTALES DE LA FACTURA

      $printer -> text("                SUBTOTAL   :    ".number_format($subtotal,2)."\n");  // SUBTOTAL****
      if ($descuento==0) {
        //No sale
      }else{
        $printer -> text("                DESCUENTO  :     ".number_format($descuento,2)."\n");  // Descuento****
      }
      $printer -> text("                IMPUESTO   :     ".number_format($impuesto,2)."\n");  // Impuesto****
      
      if ($mensajeria==0) {
        //No sale
      }else{
        $printer -> text("                MENSAJERIA:    (".number_format($mensajeria,2).")\n");  // Mensajeria****
      }
      $printer -> feed();
      $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
      $printer -> text("TOTAL : ".number_format($total,2)."\n");  // TOTAL *****
      $printer -> feed();   
      $printer -> selectPrintMode();
      // HACIENDA
      $printer -> feed();
      $printer -> text("Emitida conforme lo establecido en la\n");
      $printer -> text("resolución de Facturación Electrónica\n");
      $printer -> text("No. DGT-RES-0027-2024.  Version 4.4\n");
      $printer -> text("Barrio Carit / Tel: 2227-8252 / 2227-9892\n");
      $printer -> text("Fax: 2227-2110\n");
      $printer -> text("Gracias por su compra\n");
      $printer -> setEmphasis(true);
      $printer -> feed();
      $printer -> text("Visite : www.lospaticas.com\n");
      $printer -> setEmphasis(false);
      //$facturafinal='0010000103000064304';
      //$codigo_sat='50601101800310109035800100001030000641304199999999';
      
      $printer -> text("Factura Electronica #: ".$referencia_consecutivo."\n");
      $printer -> feed();
      $printer -> text($sat."\n");
// codigo QR
      $printer -> qrCode($sat, Printer::QR_ECLEVEL_L, 7);

      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      // Contado *******
      
        $printer -> text("REIMPRESION\n");
      
      $printer -> cut(); ///  TERMINA ******
      
      /* Close printer */
      $printer -> close();
      
     
      
  } catch (Exception $e) {
      echo "No se pudo imprimir: " . $e -> getMessage() . "\n";
      echo '<a href="consulta_factura.php"><img width="48"  height="48"  src= "img/back.png" >Volver</a>';
      $var_continua=false;
  }

echo 'Se Reimprimió correctamente';
?>

<SCRIPT LANGUAGE="javascript"> // Redirecciona al home      
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),1500);
    </SCRIPT> 