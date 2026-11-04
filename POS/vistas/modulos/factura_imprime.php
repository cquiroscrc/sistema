<?php
//session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

require '../../extensiones/Escpos/autoload.php';
use Escpos\Printer;
use Escpos\PrintConnectors\NetworkPrintConnector;
use Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Escpos\PrintBuffers\EscposPrintBuffer;
use Escpos\PrintBuffers\ImagePrintBuffer;



$entra=2;
if ($tiponum==1) {
  //entra 3 veces
  $tiponom='Credito';
  $entra=3;
}else{
  $tiponom='Contado';
}



for ($entrax = 1; $entrax <= $entra; $entrax++) { /// Ciclo factura

  $sqlvalores = "SELECT * FROM tbl_Utiles WHERE IDUtilidad=10";  /// Valores encabezado
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlvalores);
  
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {   

        $nombreempresa=$respuesta[$i]['Descripcion'];
        $cedula=$respuesta[$i]['Valor'];
        
  }
  
  $sqlvendedor="SELECT * FROM Vendedores WHERE codigo = $vendedor";            /// Valores de Vendedor
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlvendedor);
  
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {
    $nombre_vendedor = $respuesta[$i]['nombre']; 
  }

  $sqlcliente="SELECT * FROM clientes WHERE codigo = $cliente";            /// Valores de cliente
  $respuestacliente = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcliente);
  
  for ( $i = sizeof($respuestacliente) - 1; $i >= 0; $i--) {
    $correo = $respuestacliente[$i]['correo']; 
  }

  
  try {
      // Conecta a la direccion de la impresora -- La direccion debe ser local, pero sirve desde afuera
      //$connector = new NetworkPrintConnector("172.16.12.86", 9100); // Hernan
      $connector = new NetworkPrintConnector("172.16.12.87", 9100);   //La de Caja
      
      //Comienza a imprimir
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
      $printer -> text($factura."\n");
      $printer -> feed();
      $printer -> selectPrintMode();
      $printer -> setEmphasis(true);
      $printer -> text("Fecha : ".strftime( "%d-%m-%Y", $fecha )." Hora : ".$hora."\n");
      $printer -> setEmphasis(false);
      $printer -> feed();
      $printer -> text("Nombre: ".strtoupper(trim($nombre))."\n"); 
      $printer -> text("Adicional: ".strtoupper(trim($adicional))."\n");
      if ($medio_pago=='01'){
        $printer -> text("Medio Pago: Efectivo\n"); // Efectivo
      }
      if ($medio_pago=='02'){
        $printer -> text("Medio Pago: Tarjeta\n"); // Correo
      }
      if ($medio_pago=='04'){
        $printer -> text("Medio Pago: Deposito Bancario\n"); // Correo
      }
      if ($medio_pago=='06'){
        $printer -> text("Medio Pago: Sinpe MOBIL\n"); // Correo
      }
      
      if ($exoneracion==true) {
        $printer -> text("Exoneracion: ".strtoupper(trim($numerodocumento))."\n");
        $printer -> text("Exoneracion: ".strtoupper(trim($documentodescripcion))."\n");                                                          
      }
      $printer -> text(strtoupper($tiponom)."\n");
      $printer -> text("Cliente: ".$cliente."\n");
      $printer -> text("Correo: ".$correo."\n"); // Correo
      $printer -> text("Vendedor: ".$vendedor." - ".$nombre_vendedor."\n");
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
      
      if ($exoneracion==true) {
      $printer -> text("                EXONERACION:     ".number_format($montoexoneracion,2)."\n");  // Impuesto****                                  
      }
      
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
      $printer -> setEmphasis(true);
      $printer -> text("No se aceptan devoluciones o reclamos\n");
      $printer -> text("SIN LA FACTURA ni despues de 30 días.\n");
      $printer -> setEmphasis(false);
      $printer -> text("Las piezas electricas no tienen devolución\n");
      $printer -> text("Barrio Carit / Tel: 2227-8252 / 2227-9892\n");
      $printer -> text("Fax: 2227-2110\n");
      $printer -> text("Gracias por su compra\n");
      $printer -> setEmphasis(true);
      $printer -> feed();
      $printer -> text("Visite : www.lospaticas.com\n");
      $printer -> setEmphasis(false);
      $printer -> text("Factura Electrónica: ".$facturafinal."\n");
      $printer -> feed();
      $printer -> text($codigo_sat."\n");
      // codigo QR
      $printer -> qrCode($codigo_sat, Printer::QR_ECLEVEL_L, 7);

      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      $printer -> feed();
      // Contado imprime 2 veces*******
      if (($tiponom=='Contado') and($entrax==1)) {
        $printer -> text("ORIGINAL\n");
      }
      if (($tiponom=='Contado') and($entrax==2)) {
        $printer -> text("COPIA\n");
      }
      // Credito imprime 3 veces
      if (($tiponom=='Credito') and($entrax==1)) {
        $printer -> text("ORIGINAL\n");
      }
      if (($tiponom=='Credito') and($entrax==2)) {
        $printer -> text("COPIA\n");
      }
      if (($tiponom=='Credito') and($entrax==3)) {
        $printer -> text("CREDITO\n");
      }
      // Frecuente imprime 2 veces******
      if (($tiponom=='frecuente') and($entrax==1)) {
        $printer -> text("ORIGINAL\n");
      }
      if (($tiponom=='frecuente') and($entrax==2)) {
        $printer -> text("COPIA\n");
      }

      $printer -> cut(); ///  TERMINA ******
      
      /* Close printer */
      $printer -> close();
      
      $var_continua=true;
      
  } catch (Exception $e) {
      echo "No se pudo imprimir: " . $e -> getMessage() . "\n";
      $var_continua=false;
  }
} // End For