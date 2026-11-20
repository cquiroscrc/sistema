<?php
session_start();
    include("db.php");

     if((isset($_POST['id']))and(!empty($_POST['id']))){
        $id_usuario = $_POST['id'];
    }
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
    

   




    $sql = "UPDATE acc_usuarios SET usuario='$usuario',password='$password',nombre='$nombre',acceso='$acceso' 
    WHERE id='$id_usuario'";


    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: usuarios_resultado.php");
    






?>