<?php
session_start();
    require("db.php");

    if((isset($_POST['codigo']))and(!empty($_POST['codigo']))){
        $codigo = $_POST['codigo'];
    }
    if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }
    if((isset($_POST['telefono']))and(!empty($_POST['telefono']))){
        $telefono = $_POST['telefono'];
    }
    if((isset($_POST['direccion']))and(!empty($_POST['direccion']))){
        $direccion = $_POST['direccion'];
    }
    if((isset($_POST['corto']))and(!empty($_POST['corto']))){
        $corto = $_POST['corto'];
    }
    if((isset($_POST['tipo']))and(!empty($_POST['tipo']))){
        $tipo = $_POST['tipo'];
    }
    

   




    $sql = "INSERT INTO proveedores
            (codigo,nombre,telefono,direccion,shortname,tipo)
            values
            ('".$codigo."','".$nombre."','".$telefono."','".$direccion."','".$corto."','".$tipo."')";

    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
       // echo 'Registro Guardado';
       header("location: proveedores_resultado.php");
    







?>