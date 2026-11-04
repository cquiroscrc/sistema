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
    $punto=strripos($_SESSION["ip"],".");
    $tama=strlen($_SESSION["ip"]);
    $quitar=($tama-$punto)*-1;
    $maquina=substr($_SESSION["ip"], $quitar+1);
    // Consecutivo de prefactura
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 
    // jalar factura


   
      // carga Hfacturas
      $sql="SELECT * FROM hfacturas WHERE nfac ='$consecutivo'";  
      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
      
      foreach ($respuesta as $key => $arr) {
      
        $documento= $arr['nfac'];
        $fecha=$arr['fechafact'];
        $subtotal=$arr['subtotal'];
        $impuesto=$arr['impuesto'];
        $descuento=$arr['descuento'];
        $total=$arr['total'];
        $tipo=$arr['Tipo'];
        $estado=$arr['estado'];
        $cliente=$arr['proveedor'];
        $vendedor=$arr['vendedor'];
        $nombre=$arr['Nombre'];
        $adicional=$arr['Adicional'];
        $parcial=$subtotal-$descuento+$impuesto;
        $mensajeria=$total-$parcial;
        $referencia_anular=$arr['consecutivo'];

      }
     
    
    // Actualiza fecha del sistema
    $sql_fecha="SELECT * FROM fechas";       // Fecha del sistema
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_fecha);
  
      foreach ($respuesta as $key => $arr) {
          $fecha=strtotime($arr["Fecha"]);
          $time = time();
          $hora = date("H:i:s", $time);
      }

    // Verifica el consecutivo  
    $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
  
      foreach ($respuesta as $key => $arr) {
          $factura=$arr["numero"];
          $consecutivo_fac=$factura;
          $estado_factura=$arr['Estado'];
          $casa_sucursal =$arr['CASA_SUCURSAL'];
          $terminal=$arr['TERMINAL'];
          $cod_tipo=$arr['COD_TIPO'];
      }
      /// REVISA HASTA QUE ESTE SUELTO EL ESTADO
      while($estado_factura<>"L"){ 
        
        $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura
          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
  
          foreach ($respuesta as $key => $arr) {
            $factura=$arr["numero"];
            $estado_factura=$arr['Estado'];
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
    include("anular_cxc.php");  // Nos vamos a ejecutar anular_cxc
  }
echo "<br>";  
echo "salio de cxc";  
echo "<br>"; 
//actualizar Hfacturas ************
  if ($var_continua==true) {
    include("anular_hfacturas.php");  // Nos vamos a ejecutar anular_hfacturas
  }
echo "<br>";  
echo "salio de Hfacturas";  
echo "<br>"; 

//actualizar HGeneral ************
  if ($var_continua==true) {
    include("anular_hgeneral.php");  // Nos vamos a ejecutar anular_hgeneral
  }
echo "<br>";  
echo "salio de HGeneral";  
echo "<br>"; 

//actualizar Inventario ************ 
  if ($var_continua==true) {
    include("anular_inventario.php");  // Nos vamos a ejecutar anular_inventario
  }
echo "<br>";  
echo "salio de Inventario";  
echo "<br>";

  //imprimir factura ***********
  if ($var_continua==true) {
    include("anular_imprime.php");// Nos vamos a ejecutar anular_imprime
  }
 echo "<br>";  
 echo "salio de imprimir";   
 echo "<br>";  
  
 //Cambia estado ANULADA la FACTURA

$sql_anular = "UPDATE hfacturas set estado = 'ANULADA' WHERE Nfac ='$documento'"; 
      
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_anular);
  


  //actualizar consecutivo  **************
//RESERVAR EL ESTADO
      $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura
      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
  
          foreach ($respuesta as $key => $arr) {
          $factura=$arr["numero"];
          
      }
      $nueva_fac = $factura +1;
      
      $sql_estado = "UPDATE consecutivos set estado = 'L', numero=".$nueva_fac." WHERE TIPO ='06'"; 
      
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_estado);
      //echo $sql_estado; 
      //echo "<br>";

    ?>  
    <SCRIPT LANGUAGE="javascript"> // Redirecciona al home
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),1500);
    </SCRIPT> 


<script src="js/bootstrap.min.js"></script>
</html>
