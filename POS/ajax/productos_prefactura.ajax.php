<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");


          
   
$codcliente=0;
$vendedor='000';

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura =$_REQUEST['Factura']; 
    }

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
      
    }
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
      $cantidad = $_REQUEST['cantidad'];
      
      
    }
    if(isset($_REQUEST['codcliente']) && !empty($_REQUEST['codcliente'])){
      $codcliente = $_REQUEST['codcliente'];
      
    }
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
      $vendedor = $_REQUEST['vendedor'];
      
    }
    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
      $precio = $_REQUEST['precio'];
      
    }


    $punto=strripos($_SESSION["ip"],".");
    $tama=strlen($_SESSION["ip"]);
    $quitar=($tama-$punto)*-1;
    $maquina=substr($_SESSION["ip"], $quitar+1);
    $fecha=  date('Y-m-d 00:00:00');
    
    
    //var_dump($codigo);
    $codigo=strtoupper($codigo);  
    //// *************** Incluye la consulta ******************
    $recarga="no";

    $sqlesta="SELECT * FROM hpregeneral WHERE Factura = '$Factura' AND codigo = '$codigo'";
    $esta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlesta);
    
   
    
    foreach ($esta as $key => $value) {
          if ($cantidad==$value["cantidad"]) {
            $recarga="si";
              
          }else{
              // Actualiza cantidad
              $sqlact_cant="UPDATE hpregeneral SET cantidad=$cantidad WHERE Factura='$Factura' AND codigo='$codigo'";
              $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlact_cant);
              $recarga="si";

              $sqlactualiza="UPDATE hpregeneral SET codcliente=$codcliente, vendedor='$vendedor',fecha ='$fecha', maquina='$maquina' WHERE Factura='$Factura'";

              $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);
          }
           
    }

    
   
    
    if ($recarga=="no") {
        
        $sqlactualiza="UPDATE hpregeneral SET codcliente=$codcliente, vendedor='$vendedor',fecha ='$fecha', maquina='$maquina' WHERE Factura='$Factura'";

        $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlactualiza);
        

        $sql="INSERT INTO hpregeneral (Factura, fecha, codigo, cantidad, movimiento, tipo, codcliente, maquina, vendedor, precio, estado) VALUES ('$Factura', $fecha, '$codigo', $cantidad, 'Prefactura', '1', $codcliente,$maquina ,$vendedor,$precio ,'Pendiente')";    

        
          //// *************** Ejecuto la consulta ******************
      

        $datos = array("Factura"=>$Factura,
               "fecha"=>$fecha,
               "codigo"=>$codigo,
               "cantidad"=>$cantidad,
               "movimiento"=>"Prefactura",
               "tipo"=>1,
               "codcliente"=>$codcliente,
               "maquina"=>$maquina,
               "vendedor"=>$vendedor,
               "precio"=>$precio,
               "estado"=>"Pendiente");
    
      
             
        $insertcodigo= ControladorUtilitaria::ctrIngresarUtilitariaPrefactura($datos);

        
        
      }  
      
 

    //// *************** Recibe codigo ******************