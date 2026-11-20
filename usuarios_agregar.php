<?php
session_start();
    include("db.php");


     if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }
    if((isset($_POST['usuario']))and(!empty($_POST['usuario']))){
        $usuario = $_POST['usuario'];
    }
    if((isset($_POST['password']))and(!empty($_POST['password']))){
        $password = $_POST['password'];
    }
    if((isset($_POST['acceso']))and(!empty($_POST['acceso']))){
        $acceso = $_POST['acceso'];
    }
    

   




    $sql = "INSERT INTO usuarios
            (usuario,password,nombre,acceso)
            values
            ('".$usuario."','".$password."','".$nombre."','".$acceso."')";

    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: usuarios_resultado.php");
    







?>