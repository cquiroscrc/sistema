<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");


    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura =$_REQUEST['Factura']; 
    }
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
    }
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
      $cantidad = $_REQUEST['cantidad'];
    }
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];
    }
    if(isset($_REQUEST['costo']) && !empty($_REQUEST['costo'])){
      $costo = $_REQUEST['costo'];  
    }
    if(isset($_REQUEST['costoa']) && !empty($_REQUEST['costoa'])){
      $costoa = $_REQUEST['costoa'];
    }
    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
      $precio = $_REQUEST['precio']; 
    }
    if(isset($_REQUEST['subtotal']) && !empty($_REQUEST['subtotal'])){
      $subtotal = $_REQUEST['subtotal'];
    }
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
      $descuento = $_REQUEST['descuento']; 
    }

    $porcentaje=$descuento / $subtotal;
    $sugerido=$costo-($costo*$porcentaje);
    $costo=$sugerido;

    $fecha=  date('Y-m-d 00:00:00');
    
    
    //var_dump($codigo);
    $codigo=strtoupper($codigo);  
    //// *************** Incluye la consulta ******************
    $recarga="no";

    $sqlesta="SELECT * FROM mcompras WHERE Factura = '$Factura' AND Codigo = '$codigo'";
    $esta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlesta);
    

    
    foreach ($esta as $key => $value) {
          if ($cantidad==$value["Cantidad"]) {
            $recarga="si";
              
          }else{
              // Actualiza cantidad
              $sqlact_cant="UPDATE mcompras SET Cantidad=$cantidad WHERE Factura='$Factura' AND Codigo='$codigo'";
              $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlact_cant);
              $recarga="si";
          }
           
    }

    
   
    
    if ($recarga=="no") {
        
        $sql="INSERT INTO mcompras (Factura,Codigo,Proveedor,Precio,Cantidad,PrecioAnterior,PrecioSugerido,Fecha) values ('".$Factura."','".$codigo."','".$proveedor."','".$precio."','".$cantidad."','".$costoa."','".$costo."','".$fecha."')";  
         
          
         $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        echo "exito";
      }  
      
 

    //// *************** Recibe codigo ******************