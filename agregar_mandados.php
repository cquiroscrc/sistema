<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("db.php");

    if((isset($_POST['mensajero']))and(!empty($_POST['mensajero']))){
        $mensajero = $_POST['mensajero'];
    }
    if((isset($_POST['vendedor']))and(!empty($_POST['vendedor']))){
        $vendedor = $_POST['vendedor'];
    }
    if((isset($_POST['factura']))and(!empty($_POST['factura']))){
        $factura = $_POST['factura'];
    }
    if((isset($_POST['monto']))and(!empty($_POST['monto']))){
        $monto = $_POST['monto'];
    }
    if((isset($_POST['lugar']))and(!empty($_POST['lugar']))){
        $lugar = $_POST['lugar'];
        $lugar=strtoupper($lugar);
    }
    if((isset($_POST['cantidad']))and(!empty($_POST['cantidad']))){
        $cantidad = $_POST['cantidad'];
    }
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
        $fecha = $_REQUEST['Date2'];
    }       
  
    
    $time = date ("H:i:s");
    //echo $fecha;
    //$fecha = $fecha . ' ' . $time;
    $fecha .=$time;
    $newdate = strtotime ( $fecha);
    $newdate = date ( 'Y-m-d H:i:s.m' , $newdate );
    //echo $newdate;
    //echo $fecha;

    $sql = "INSERT INTO mandados
            (envia,factura,monto,fecha,lugar,cantidad,motorizado)
            values
            ('".$vendedor."','".$factura."',".$monto.",'".$newdate."','".$lugar."',".$cantidad.",'".$mensajero."')";

//echo $sql; exit;        
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: index.php");
    







?>