<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

  require("../../modelos/conexion.php");
  require("../../controladores/utilitaria.controlador.php");
  require("../../modelos/utilitaria.modelo.php");
  
  $var_continua=true;
  $si_entro=false;
  // Consecutivo de prefactura
  if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
    $prefactura = $_REQUEST['codigo'];      
  }
  echo $prefactura;
  // jalar factura
if ($var_continua==true) {
  # code...

      // carga Hfacturas
  $consulta="SELECT * FROM hprefacturas WHERE nfac ='$prefactura'";  
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);
$cliente='0';

foreach ($respuesta as $key => $valor) {
    $subtotal=$valor['subtotal'];
    $impuesto=$valor['impuesto'];
    $descuento=$valor['descuento'];
    $total=$valor['total'];
    $cliente=$valor['proveedor'];
    $nombre=$valor['Nombre'];
    $adicional=$valor['Adicional'];
    $vendedor=$valor['vendedor'];
    $exoneracion=$valor['Exoneracion'];
    $numerodocumento=$valor['numerodocumento'];
    $identificacion=$valor['identificacion'];
    $fechaemision=$valor['fechaemision'];
    $fechavencimiento=$valor['fechavencimiento'];
    $documentocodigo=$valor['documentocodigo'];
    $documentodescripcion=$valor['documentodescripcion'];
    $nombreinstitucion=$valor['nombreinstitucion'];
    $porcentaje=$valor['porcentaje'];
    $montoexoneracion=$valor['montoexoneracion'];
    $medio_pago=$valor['medio_pago'];
    $si_entro=true;
    echo 'Entró a prefactura';
    echo "<br>";  
 } 




  if ($si_entro==false) {
    echo 'No entró';
    echo '<br>';
    echo $consulta;
    var_dump($respuesta);
    exit;
  }

  $nombre= ltrim($nombre);
  $punto=strripos($_SESSION["ip"],".");
  $tama=strlen($_SESSION["ip"]);
  $quitar=($tama-$punto)*-1;
  $maquina=substr($_SESSION["ip"], $quitar+1);
  $tipo=1;
  $estado='APLICADA';
  $mensajeria=0;
  $parcial=$total;

    
  // Actualiza fecha del sistema
  $sql_fecha="SELECT * FROM fechas";       // Fecha del sistema      
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_fecha);
      
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {
  
    $fecha=strtotime($respuesta[$i]["Fecha"]);
    $time = time();
    $hora = date("H:i:s", $time);
  }

  // Verifica el consecutivo  
  $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura      
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
      
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) { 

    $factura=$respuesta[$i]["numero"];
    $consecutivo_fac=$factura;
    $estado_factura=$respuesta[$i]['Estado'];
    $casa_sucursal =$respuesta[$i]['CASA_SUCURSAL'];
    $terminal=$respuesta[$i]['TERMINAL'];
    $cod_tipo=$respuesta[$i]['COD_TIPO'];
  }

  /// REVISA HASTA QUE ESTE SUELTO EL ESTADO
  while($estado_factura<>"L"){ 
        
    $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura    
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
  
    for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {      
      $factura=$respuesta[$i]["numero"];
      $estado_factura=$respuesta[$i]['Estado'];
    }

  }
  
  //RESERVAR EL ESTADO
  $sql_estado = "UPDATE consecutivos set estado = 'O' WHERE TIPO ='01'"; 
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_estado);
          
      //****** Aqui comienza la Anulacion  
  $factura=str_pad($factura, 14, "0", STR_PAD_LEFT); 
  $factura='F-'.$factura; 
  $factura=trim($factura);
  $vendedor=str_pad($vendedor, 2, "0", STR_PAD_LEFT);


  if ($exoneracion==true) {
  // Insertar en exoneraciones

      $inserta = "INSERT INTO EXONERACIONES (Factura,NumeroDocumento,Identificacion,FechaVencimiento,FechaEmision,TipoDocumentoCodigo,Descripcion,NombreInstitucion,Cliente,Subtotal,Descuento, Impuesto,Total,MontoExoneracion,PorcentajeExoneracion) Values ('$factura','$numerodocumento','$identificacion','$fechavencimiento','$fechaemision',
      '$documentocodigo','$documentodescripcion','$nombreinstitucion','$cliente','$subtotal','$descuento','$impuesto','$total','$montoexoneracion','$porcentaje')";
        //echo $inserta;
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);

}

    
  //actualizar cxc ************
  if ($var_continua==true) {
    include("factura_cxc.php");  // Nos vamos a ejecutar anular_cxc
  }
  echo "<br>";  
  echo "salio de cxc";  
  echo "<br>"; 
  
  //actualizar Hfacturas ************
  if ($var_continua==true) {
    include("factura_hfacturas.php");  // Nos vamos a ejecutar anular_hfacturas
  }
  echo "<br>";  
  echo "salio de Hfacturas";  
  echo "<br>"; 

  //actualizar HGeneral ************
  if ($var_continua==true) {
    include("factura_hgeneral.php");  // Nos vamos a ejecutar anular_hgeneral
  }
  echo "<br>";  
  echo "salio de HGeneral";  
  echo "<br>"; 

  //actualizar Inventario ************ 
  if ($var_continua==true) {
    include("factura_inventario.php");  // Nos vamos a ejecutar anular_inventario
  }
  echo "<br>";  
  echo "salio de Inventario";  
  echo "<br>";

  //imprimir factura ***********
  if ($var_continua==true) {
    include("factura_imprime.php");// Nos vamos a ejecutar anular_imprime
  }
  echo "<br>";  
  echo "salio de imprimir";   
  echo "<br>";  
  
  
  //actualizar consecutivo  **************
  //RESERVAR EL ESTADO

  $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
  
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {
    $factura=$respuesta[$i]["numero"];         
  }

  if ($var_continua==true) {
   
    $nueva_fac = $factura +1;
        
    $sql_estado = "UPDATE consecutivos set estado = 'L', numero=".$nueva_fac." WHERE TIPO ='01'";     
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_estado);
    
    $limpiar ="DELETE FROM hprefacturas WHERE nfac = '$prefactura'";    
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($limpiar);

    $limpiar ="DELETE FROM hpregeneral WHERE Factura = '$prefactura'";    
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($limpiar);
  }
}

echo '<br>';
echo '<input name="button" type="button" onclick="factura" value="Cerrar esta ventana" /> 
';
    ?>  
    <SCRIPT LANGUAGE="javascript"> // Redirecciona al home      
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),3000);
    </SCRIPT> 

</html>
