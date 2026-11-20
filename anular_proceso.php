<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");
  include "menu.php";
    $var_continua=true;
    $mensajeria=0;
    $descuento=0;
    $vendedor=0;
    // Consecutivo de prefactura
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 
    // jalar factura


   
      // carga Hfacturas
      $sql="SELECT * FROM hfacturas WHERE nfac ='$consecutivo'";  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($res)){
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
      if(($res_fecha = odbc_exec($id,$sql_fecha)) === false)
          die("Error en Select: ". odbc_errormsg($id));
      
      while($arr_fecha = odbc_fetch_array($res_fecha)){
          $fecha=strtotime($arr_fecha["Fecha"]);
          $time = time();
          $hora = date("H:i:s", $time);
      }

    // Verifica el consecutivo  
    $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura
      if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
          die("Error en Select: ". odbc_errormsg($id));
      
      while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
          $factura=$arr_consecutivo["numero"];
          $consecutivo_fac=$factura;
          $estado_factura=$arr_consecutivo['Estado'];
          $casa_sucursal =$arr_consecutivo['CASA_SUCURSAL'];
          $terminal=$arr_consecutivo['TERMINAL'];
          $cod_tipo=$arr_consecutivo['COD_TIPO'];
      }
      /// REVISA HASTA QUE ESTE SUELTO EL ESTADO
      while($estado_factura<>"L"){ 
        
        $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura
          if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
            die("Error en Select: ". odbc_errormsg($id));
      
          while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
            $factura=$arr_consecutivo["numero"];
            $estado_factura=$arr_consecutivo['Estado'];
          }

      }
      //RESERVAR EL ESTADO
      $sql_estado = "UPDATE consecutivos set estado = 'O' WHERE TIPO ='06'"; 
      
        if(($resultado_estado = odbc_exec($id,$sql_estado)) === false)
             die("Error en Select: ". odbc_errormsg($id));
          
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
      
        if(($resultado_anular = odbc_exec($id,$sql_anular)) === false)
             die("Error en Select: ". odbc_errormsg($id));
  


  //actualizar consecutivo  **************
//RESERVAR EL ESTADO
      $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='06'";       // Consecutivo factura
      if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
          die("Error en Select: ". odbc_errormsg($id));
      
      while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
          $factura=$arr_consecutivo["numero"];
          
      }
      $nueva_fac = $factura +1;
      
      $sql_estado = "UPDATE consecutivos set estado = 'L', numero=".$nueva_fac." WHERE TIPO ='06'"; 
      
        if(($resultado_estado = odbc_exec($id,$sql_estado)) === false)
             die("Error en Select: ". odbc_errormsg($id));
      //echo $sql_estado; 
      //echo "<br>";

    ?>  
    <SCRIPT LANGUAGE="javascript"> // Redirecciona al home
      
      setTimeout("location.href = 'anular_factura.php';",1500);
    </SCRIPT> 

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Anulacio FE';
</script> 
<script src="js/bootstrap.min.js"></script>
</html>
