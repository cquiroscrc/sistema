<?php
session_start();
    require("db.php");

    if((isset($_POST['factura']))and(!empty($_POST['factura']))){
        $factura = $_POST['factura'];
    }
    if((isset($_POST['estado']))and(!empty($_POST['estado']))){
        $estado = $_POST['estado'];
    }
    if((isset($_POST['recibos']))and(!empty($_POST['recibos']))){
        $recibos = $_POST['recibos'];
    }
    if((isset($_POST['devoluciones']))and(!empty($_POST['devoluciones']))){
        $devoluciones = $_POST['devoluciones'];
    }
    if((isset($_POST['ajustes']))and(!empty($_POST['ajustes']))){
        $ajustes = $_POST['ajustes'];
    }
    if((isset($_POST['proformas']))and(!empty($_POST['proformas']))){
        $proformas = $_POST['proformas'];
    }
    if((isset($_POST['clientes']))and(!empty($_POST['clientes']))){
        $clientes = $_POST['clientes'];
    }
    if((isset($_POST['ordenes']))and(!empty($_POST['ordenes']))){
        $ordenes = $_POST['ordenes'];
    }

    //Factura
    $sql= "UPDATE Consecutivos SET numero='$factura' WHERE tipo='01'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    //Estado
    $sql= "UPDATE Consecutivos SET Estado='$estado' WHERE tipo='01'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Recibos
    $sql= "UPDATE Consecutivos SET numero='$recibos' WHERE tipo='04'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Ajustes
    $sql= "UPDATE Consecutivos SET numero='$ajustes' WHERE tipo='05'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Devoluciones
    $sql= "UPDATE Consecutivos SET numero='$devoluciones' WHERE tipo='06'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Proformas
    $sql= "UPDATE Consecutivos SET numero='$proformas' WHERE tipo='03'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Clientes
    $sql= "UPDATE Consecutivos SET numero='$clientes' WHERE tipo='07'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Ordenes
    $sql= "UPDATE Consecutivos SET numero='$ordenes' WHERE tipo='09'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));


// echo 'Registro Guardado';
       header("location: ../../utilidades");

?>