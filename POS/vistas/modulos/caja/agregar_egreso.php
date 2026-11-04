<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");
    $descripcion1='';
    $empresa='';
    $hora='';
    $lugar='';
    $cliente='';
    $monto=0;

    if(isset($_REQUEST['egresos']) && !empty($_REQUEST['egresos'])){
          $egresos = $_REQUEST['egresos'];      
    }
    if ($_REQUEST['egresos']==0) {
      $egresos = $_REQUEST['egresos'];
    } 
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto'];      
    } 
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion1 = $_REQUEST['descripcion'];
          
    }
    if(isset($_REQUEST['empresa']) && !empty($_REQUEST['empresa'])){
          $empresa = $_REQUEST['empresa'];      
          $empresa= $empresa.' / ';
    }

    if(isset($_REQUEST['hora']) && !empty($_REQUEST['hora'])){
          $hora = $_REQUEST['hora'];      
          $hora= $hora.' / ';
    }

    if(isset($_REQUEST['lugar']) && !empty($_REQUEST['lugar'])){
          $lugar = $_REQUEST['lugar'];      
          $lugar= $lugar.' / ';
    }
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2']; 
    }
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
          $cliente = $_REQUEST['cliente']; 
    }
    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $documento = $_REQUEST['documento']; 
    }
    $subtotal=0;
    $iva=0;
    if(isset($_REQUEST['subtotal']) && !empty($_REQUEST['subtotal'])){
        $subtotal = $_REQUEST['subtotal'];
    }
    if(isset($_REQUEST['iva']) && !empty($_REQUEST['iva'])){
        $iva = $_REQUEST['iva'];
    }

 
 $descripcion=$descripcion1.$empresa .$hora.$lugar.$cliente;
 $descripcion=strtoupper($descripcion);
    
  $sql = "INSERT INTO tbl_egresos
            (Tipo,Documento,Fecha,Monto,Descripcion,Compañia,Subtotal,IVA)
            values
            ('".$egresos."','".$documento."','".$Date2."','".$monto."','".$descripcion."',0,'".$subtotal."','".$iva."')";

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    echo "exito";  
            