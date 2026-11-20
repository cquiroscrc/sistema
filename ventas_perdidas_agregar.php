<?php
session_start();
    require("db.php");

    if((isset($_POST['codigo']))and(!empty($_POST['codigo']))){
        $codigo = $_POST['codigo'];
    }
    if((isset($_POST['descripcion']))and(!empty($_POST['descripcion']))){
        $descripcion = $_POST['descripcion'];
    }
    
    $sql = "INSERT INTO tmpTras
            (codigo,descripcion)
            values
            ('".$codigo."','".$descripcion."')";

    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
       // echo 'Registro Guardado';
       header("location: ventas_perdidas_resultado.php");
    







?>