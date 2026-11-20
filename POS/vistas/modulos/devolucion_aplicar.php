<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

  require("../../modelos/conexion.php");
  require("../../controladores/utilitaria.controlador.php");
  require("../../modelos/utilitaria.modelo.php");
  
  $var_continua=true;
  $mensajeria=0;
  $descuento=0;
  $vendedor=0;
  // Consecutivo de prefactura
  if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
    $consecutivo = $_REQUEST['codigo'];      
  } 
  // jalar factura

      // carga Hfacturas
  $consulta="SELECT * FROM hfacturas WHERE nfac ='$consecutivo'";  
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {    // $val = $arr['key'];

    $documento= $respuesta[$i]['nfac'];
    $fecha=$respuesta[$i]['fechafact'];
    $subtotal=$respuesta[$i]['subtotal'];
    $impuesto=$respuesta[$i]['impuesto'];
    $descuento=$respuesta[$i]['descuento'];
    $total=$respuesta[$i]['total'];
    $tipo=$respuesta[$i]['Tipo'];
    $estado=$respuesta[$i]['estado'];
    $cliente=$respuesta[$i]['proveedor'];
    $vendedor=$respuesta[$i]['vendedor'];
    $nombre=$respuesta[$i]['Nombre'];
    $adicional=$respuesta[$i]['Adicional'];
    $parcial=$subtotal-$descuento+$impuesto;
    $mensajeria=$total-$parcial;
    $referencia_anular=$respuesta[$i]['consecutivo'];

  }
     
  $consulta="SELECT * FROM tmp_devol WHERE nfac ='$consecutivo'";  
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

  $parcialdev=0;

  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {    // $val = $arr['key'] 
 
    $subparcial=$respuesta[$i]['precio']*$respuesta[$i]['cantidad'];
    $parcialdev=$parcialdev+$subparcial;
    $vardesc=$respuesta[$i]['descuento'];
    $varimpuesto=$respuesta[$i]['impuesto'];
  }

  $subtotal=$parcialdev;
  $descuento=$parcialdev*$vardesc;
  $impuesto = ($subtotal-$descuento)*$varimpuesto;
  $total = $subtotal-$descuento+$impuesto;
  $parcial=$total;
  $mensajeria=0;

  $punto=strripos($_SESSION["ip"],".");
  $tama=strlen($_SESSION["ip"]);
  $quitar=($tama-$punto)*-1;
  $maquina=substr($_SESSION["ip"], $quitar+1);
    
  // Actualiza fecha del sistema
  $sql_fecha="SELECT * FROM fechas";       // Fecha del sistema      
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_fecha);
      
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {
  
    $fecha=strtotime($respuesta[$i]["Fecha"]);
    $time = time();
    $hora = date("H:i:s", $time);
  }

  // Verifica el consecutivo  
  $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura      
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
        
    $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura    
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
  
    for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {      
      $factura=$respuesta[$i]["numero"];
      $estado_factura=$respuesta[$i]['Estado'];
    }

  }
  
  //RESERVAR EL ESTADO
  $sql_estado = "UPDATE consecutivos set estado = 'O' WHERE TIPO ='06'"; 
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_estado);
          
      //****** Aqui comienza la Anulacion  
  $factura=str_pad($factura, 14, "0", STR_PAD_LEFT); 
  $factura='D-'.$factura; 
  $factura=trim($factura);
  $vendedor=str_pad($vendedor, 2, "0", STR_PAD_LEFT);
    
  //actualizar cxc ************
  if ($var_continua==true) {
    include("devolucion_cxc.php");  // Nos vamos a ejecutar anular_cxc
  }
  echo "<br>";  
  echo "salio de cxc";  
  echo "<br>"; 
  
  //actualizar Hfacturas ************
  if ($var_continua==true) {
    include("devolucion_hfacturas.php");  // Nos vamos a ejecutar anular_hfacturas
  }
  echo "<br>";  
  echo "salio de Hfacturas";  
  echo "<br>"; 

  //actualizar HGeneral ************
  if ($var_continua==true) {
    include("devolucion_hgeneral.php");  // Nos vamos a ejecutar anular_hgeneral
  }
  echo "<br>";  
  echo "salio de HGeneral";  
  echo "<br>"; 

  //actualizar Inventario ************ 
  if ($var_continua==true) {
    include("devolucion_inventario.php");  // Nos vamos a ejecutar anular_inventario
  }
  echo "<br>";  
  echo "salio de Inventario";  
  echo "<br>";

  //imprimir factura ***********
  if ($var_continua==true) {
    include("devolucion_imprime.php");// Nos vamos a ejecutar anular_imprime
  }
  echo "<br>";  
  echo "salio de imprimir";   
  echo "<br>";  
  
 


  //actualizar consecutivo  **************
  //RESERVAR EL ESTADO

  $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
  
  for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {
    $factura=$respuesta[$i]["numero"];         
  }
  
  $nueva_fac = $factura +1;
      
  $sql_estado = "UPDATE consecutivos set estado = 'L', numero=".$nueva_fac." WHERE TIPO ='06'";     
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_estado);
  
  $limpiar ="DELETE FROM tmp_devol WHERE nfac = '$consecutivo'";    
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($limpiar);

    ?>  
    <SCRIPT LANGUAGE="javascript"> // Redirecciona al home      
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),1500);
    </SCRIPT> 

</html>
