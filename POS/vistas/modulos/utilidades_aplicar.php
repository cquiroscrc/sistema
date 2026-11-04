<?php
session_start();
    require("db.php");

    if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }
    if((isset($_POST['cedula']))and(!empty($_POST['cedula']))){
        $cedula = $_POST['cedula'];
    }
    if((isset($_POST['iva']))and(!empty($_POST['iva']))){
        $iva = $_POST['iva'];
    }
    if((isset($_POST['ccss']))and(!empty($_POST['ccss']))){
        $ccss = $_POST['ccss'];
    }
    if((isset($_POST['agregado']))and(!empty($_POST['agregado']))){
        $agregado = $_POST['agregado'];
    }
    if((isset($_POST['planilla']))and(!empty($_POST['planilla']))){
        $planilla = $_POST['planilla'];
    }
    if((isset($_POST['meses']))and(!empty($_POST['meses']))){
        $meses = $_POST['meses'];
    }
    if((isset($_POST['factor']))and(!empty($_POST['factor']))){
        $factor = $_POST['factor'];
    }
    //Nombre
    $sql= "UPDATE tbl_Utiles SET Valor ='$nombre' WHERE IDUtilidad ='3'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    //Nombre
    $sql= "UPDATE tbl_Utiles SET Descripcion='$nombre' WHERE IDUtilidad='10'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
    //Cedula
    $sql= "UPDATE tbl_Utiles SET Valor='$cedula' WHERE IDUtilidad='10'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //IVA
    $sql= "UPDATE tbl_Utiles SET Valor='$iva' WHERE IDUtilidad='4'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //CCSS
    $sql= "UPDATE tbl_Utiles SET Valor='$ccss' WHERE IDUtilidad='12'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Agregado Precio
    $sql= "UPDATE tbl_Utiles SET Valor='$agregado' WHERE IDUtilidad='13'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Consecutivo Planilla
    $sql= "UPDATE tbl_Utiles SET Valor='$planilla' WHERE IDUtilidad='8'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Meses Analizar
    $sql= "UPDATE tbl_Utiles SET Valor='$meses' WHERE IDUtilidad='1'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Factor Precio
    $sql= "UPDATE tbl_Utiles SET Valor='$factor' WHERE IDUtilidad='13'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));


// echo 'Registro Guardado';
       header("location: ../../utilidades");

?>