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
    if((isset($_POST['cedula']))and(!empty($_POST['cedula']))){
        $cedula = $_POST['cedula'];
    }
    if((isset($_POST['comentario']))and(!empty($_POST['comentario']))){
        $comentario = $_POST['comentario'];
    }
    

   




    $sql = "INSERT INTO clientes
            (codigo,nombre,telefono,direccion,cedula,comentario)
            values
            ('".$codigo."','".$nombre."','".$telefono."','".$direccion."','".$cedula."','".$comentario."')";

    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
       // echo 'Registro Guardado';
       header("location: clientes_resultado.php");
    







?>