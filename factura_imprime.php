<?php
//session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
include "menu.php";
require __DIR__ . '/autoload.php';
use Escpos\Printer;
use Escpos\PrintConnectors\NetworkPrintConnector;
use Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Escpos\PrintBuffers\EscposPrintBuffer;
use Escpos\PrintBuffers\ImagePrintBuffer;
require_once("db.php");



$entra=2;
if ($tipo=='credito') {
  //entra 3 veces
  $entra=3;
}

for ($entrax = 1; $entrax <= $entra; $entrax++) { /// Ciclo factura

  $sqlvalores = "SELECT * FROM tbl_Utiles WHERE IDUtilidad=10";  /// Valores encabezado

  if(($resvalores = odbc_exec($id,$sqlvalores)) === false)
    die("Error en Select: ". odbc_errormsg($id));

  while($arrvalores = odbc_fetch_array($resvalores)){
        $nombreempresa=$arrvalores['Descripcion'];
        $cedula=$arrvalores['Valor'];
        
  }
  if ($cliente =='contado' ) {
          $cliente=0;     
        }
  $sqlvendedor="SELECT * FROM Vendedores WHERE codigo = $vendedor";            /// Valores de cliente
  if(($r_vendedor = odbc_exec($id,$sqlvendedor)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    while($res_vendedor = odbc_fetch_array($r_vendedor)){
      $nombre_vendedor = $res_vendedor['nombre']; 
    }

  $sqlvendedor="SELECT * FROM clientes WHERE codigo = $cliente";            /// Valores de cliente
  if(($r_cliente = odbc_exec($id,$sqlcliente)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    while($res_cliente = odbc_fetch_array($r_cliente)){
      
      if ($cliente==0) {
        if ($nombre<>'CLIENTE DE CONTADO') {
          
        }else{
          $nombre = $res_cliente['nombre'];     
        }
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
      $printer -> text("Fecha : ".strftime( "%d-%m-%Y", $fecha )." Hora : ".$hora."\n");  // Fecha *****
      $printer -> setEmphasis(false);
      $printer -> feed();
      $printer -> text("Nombre: ".strtoupper($nombre)."\n"); /// Cliente *******
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
          $sqlrelleno="SELECT m.ID,m.Factura,m.Codigo,m.Vendedor,m.Precio,m.Cantidad,m.Fecha,i.DESCRIPCION
            FROM tmp_factura m, inventario i WHERE Factura='".$consecutivo."' AND m.Codigo = I.CODIGO  ORDER BY ID";

                if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

                while($relleno = odbc_fetch_array($resultadorelleno)){
                 
                $codigo=$relleno['Codigo'];
                $descripcion=$relleno['DESCRIPCION'];
                $tama=strlen($descripcion);  /// Tamaño del texto
                if ($tama>40) {
                  $descripcion = substr($descripcion,0,40);
                }
                $cantidad=$relleno['Cantidad'];
                $precio=$relleno['Precio'];
                $totalcod=$relleno['Cantidad']*$relleno['Precio'];
                
                $csubtotal=$csubtotal+$totalcod;
                $printer -> text($cantidad." // ".$descripcion."\n");
                $printer -> text("   ".$codigo."\n");
                $printer -> text("                      ".number_format($precio,2)." // ".number_format($totalcod,2)."\n");
                  
                }


                // calculo de la factura
                
                $cimpuesto=($csubtotal-$descuento)*0.13;
                $ctotal=$csubtotal-$descuento+$cimpuesto+$mensajeria;

      $printer -> feed();
      //// TOTALES DE LA FACTURA

      $printer -> text("                SUBTOTAL   :    ".number_format($csubtotal,2)."\n");  // SUBTOTAL****
      if ($descuento==0) {
        //No sale
      }else{
        $printer -> text("                DESCUENTO  :     ".number_format($descuento,2)."\n");  // Descuento****
      }
      $printer -> text("                IMPUESTO   :     ".number_format($cimpuesto,2)."\n");  // Impuesto****
      
      if ($mensajeria==0) {
        //No sale
      }else{
        $printer -> text("                MENSAJERIA:    (".number_format($mensajeria,2).")\n");  // Mensajeria****
      }
      $printer -> feed();
      $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED | Printer::MODE_DOUBLE_WIDTH);
      $printer -> text("TOTAL : ".number_format($ctotal,2)."\n");  // TOTAL *****
      $printer -> feed();   
      $printer -> selectPrintMode();
      // HACIENDA
      $printer -> text("Autorizado para impresion por sistema\n");
      $printer -> text("de computo mediante resolucion #11-97\n");
      $printer -> setEmphasis(true);
      $printer -> text("NO SE ACEPTAN RECLAMOS O DEVOLUCIONES\n");
      $printer -> text("PASADOS LOS 30 DIAS O SIN LA FACTURA\n");
      $printer -> setEmphasis(false);
      $printer -> text("Las piezas electricas no tienen devolución\n");
      $printer -> feed();
      $printer -> text("Barrio Carit / Tel: 2227-8252 / 2227-9892\n");
      $printer -> text("Fax: 2227-2110\n");
      $printer -> text("Gracias por su compra\n");
      $printer -> setEmphasis(true);
      $printer -> feed();
      $printer -> text("Visite : www.lospaticas.com\n");
      $printer -> setEmphasis(false);
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      // Contado *******
      if (($tipo=='contado') and($entrax==1)) {
        $printer -> text("ORIGINAL\n");
      }
      if (($tipo=='contado') and($entrax==2)) {
        $printer -> text("COPIA\n");
      }
      // Credito
      if (($tipo=='credito') and($entrax==1)) {
        $printer -> text("ORIGINAL\n");
      }
      if (($tipo=='credito') and($entrax==2)) {
        $printer -> text("COPIA\n");
      }
      if (($tipo=='credito') and($entrax==3)) {
        $printer -> text("CREDITO\n");
      }
      // Frecuente ******
      if (($tipo=='frecuente') and($entrax==1)) {
        $printer -> text("ORIGINAL\n");
      }
      if (($tipo=='frecuente') and($entrax==2)) {
        $printer -> text("COPIA\n");
      }

      $printer -> cut(); ///  TERMINA ******
      
      /* Close printer */
      $printer -> close();
      
      $var_continua=true;
      //echo '<a href="factura_iniciar.php"><img width="48"  height="48"  src= "img/back.png" >Volver</a>';
  } catch (Exception $e) {
      echo "No se pudo imprimir: " . $e -> getMessage() . "\n";
      echo '<a href="factura_iniciar.php"><img width="48"  height="48"  src= "img/back.png" >Volver</a>';
      $var_continua=false;
  }
} // End For