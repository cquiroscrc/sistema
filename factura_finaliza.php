<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");
    $var_continua=true;
    $mensajeria=0;
    $descuento=0;
    $vendedor=0;
    // Consecutivo de prefactura
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 
    // Numero de cliente y tipo
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];  
      if ($cliente =='contado' ) {
        $cliente=0;     
      }
      
      $sqlcliente="SELECT * FROM clientes WHERE codigo = ".$cliente."";
            
        if(($r_cliente = odbc_exec($id,$sqlcliente)) === false)
            die("Error en Select: ". odbc_errormsg($id));

          while($res_cliente = odbc_fetch_array($r_cliente)){
            $nombre = $res_cliente['nombre']; 
          }
          if ($cliente > 10000) {
            // Credito
            $tipo='credito';
          }
          //// *************** falta hacer lo de los clientes nuevos que no son frecuentes
          if ($cliente < 10000) {
            // frecuente
            $tipo='frecuente';
          }
          if ($cliente == 0) {
            // contado
            $tipo='contado';
          } 
         

    } 
    // Nombre por si es cliente de contado o Descuento aparte  
    if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
      $nombre = $_REQUEST['nombre'];      
    } 
    //Mensajeria
    if(isset($_REQUEST['mensajeria']) && !empty($_REQUEST['mensajeria'])){
      $mensajeria = $_REQUEST['mensajeria'];  
    }
    //Descuento
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
      $descuento = $_REQUEST['descuento'];  
    }
    //Vendedor
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
      $vendedor = $_REQUEST['vendedor'];  
    }
    // Verifica que el vendedor este asignado
    if($vendedor==0) {
       $message = "Digite el vendedor";
        echo "<script type='text/javascript'>alert('$message');</script>";
        ?>
        <SCRIPT LANGUAGE="javascript"> // Redirecciona al home
            <?php 
              $direccion="factura_nombre.php?consecutivo=".$consecutivo."&cliente=".$cliente."&descuento=".$descuento."&mensajeria=".$mensajeria."&vendedor=".$vendedor;
            ?>
            location.href = "<?php echo $direccion ?> "; 
            
         </SCRIPT> 

        <?php  
        exit;
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
    $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura
      if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
          die("Error en Select: ". odbc_errormsg($id));
      
      while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
          $factura=$arr_consecutivo["numero"];
          $estado_factura=$arr_consecutivo['Estado'];
      }
      /// REVISA HASTA QUE ESTE SUELTO EL ESTADO
      while($estado_factura<>"L"){ 
        
        $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura
          if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
            die("Error en Select: ". odbc_errormsg($id));
      
          while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
            $factura=$arr_consecutivo["numero"];
            $estado_factura=$arr_consecutivo['Estado'];
          }

      }
      //RESERVAR EL ESTADO
      $sql_estado = "UPDATE consecutivos set estado = 'O' WHERE TIPO ='01'"; 
      
        if(($resultado_estado = odbc_exec($id,$sql_estado)) === false)
             die("Error en Select: ". odbc_errormsg($id));
          
      //****** Aqui comienza la facturación  
  $factura=str_pad($factura, 14, "0", STR_PAD_LEFT); 
  $factura='F-'.$factura; 
  $factura=trim($factura);
  $vendedor=str_pad($vendedor, 2, "0", STR_PAD_LEFT);
  //echo $factura;
  //echo $vendedor;
  //exit;      
  //actualizar cxc ************
  if ($var_continua==true) {
    include("factura_cxc.php");  
  }
echo "<br>";  
echo "salio de cxc";  
echo "<br>"; 
//actualizar Hfacturas ************
  if ($var_continua==true) {
    include("factura_hfacturas.php");  
  }
echo "<br>";  
echo "salio de Hfacturas";  
echo "<br>"; 

//actualizar HGeneral ************
  if ($var_continua==true) {
    include("factura_hgeneral.php");  
  }
echo "<br>";  
echo "salio de HGeneral";  
echo "<br>"; 

//actualizar Inventario ************
  if ($var_continua==true) {
    include("factura_inventario.php");  
  }
echo "<br>";  
echo "salio de Inventario";  
echo "<br>";

  //imprimir factura ***********
  if ($var_continua==true) {
    include("factura_imprime.php");
  }
 echo "<br>";  
 echo "salio de imprimir";   
 echo "<br>";  
  
 //Eliminar temporal
  if ($var_continua==true) {
    include("factura_temporal.php");
  }
  echo "<br>";  
  echo "salio de temporal";   
 


  //actualizar consecutivo  **************
//RESERVAR EL ESTADO
      $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura
      if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
          die("Error en Select: ". odbc_errormsg($id));
      
      while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
          $factura=$arr_consecutivo["numero"];
          
      }
      $nueva_fac = $factura +1;
      
      $sql_estado = "UPDATE consecutivos set estado = 'L', numero=".$nueva_fac." WHERE TIPO ='01'"; 
      
        if(($resultado_estado = odbc_exec($id,$sql_estado)) === false)
             die("Error en Select: ". odbc_errormsg($id));
      //echo $sql_estado; 
      //echo "<br>";

    ?>  
    <SCRIPT LANGUAGE="javascript"> // Redirecciona al home
      
      setTimeout("location.href = 'factura_iniciar.php';",1500);
    </SCRIPT> 

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Facturando';
</script> 
<script src="js/bootstrap.min.js"></script>
</html>
