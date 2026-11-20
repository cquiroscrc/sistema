<?php
session_start();
    require("db.php");

     if((isset($_POST['id']))and(!empty($_POST['id']))){
        $id_cliente = $_POST['id'];
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
    if((isset($_POST['celular']))and(!empty($_POST['celular']))){
        $celular = $_POST['celular'];
    }
    if((isset($_POST['correo']))and(!empty($_POST['correo']))){
        $correo = $_POST['correo'];
    }
    if((isset($_POST['nacimiento']))and(!empty($_POST['nacimiento']))){
        $nacimiento = $_POST['nacimiento'];
    }
    if((isset($_POST['comentario']))and(!empty($_POST['comentario']))){
        $comentario = $_POST['comentario'];
    }

   




    $sql = "UPDATE clientes SET nombre='$nombre',telefono='$telefono',direccion='$direccion',cedula='$cedula',comentario='$comentario',celular='$celular',
    correo='$correo',nacimiento='$nacimiento' WHERE codigo=$id_cliente";


   //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: clientes_resultado.php");
    
?>