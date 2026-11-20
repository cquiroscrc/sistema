<?php
session_start();
    require("db.php");

     if((isset($_POST['id']))and(!empty($_POST['id']))){
        $id_telefono = $_POST['id'];
    }
     if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }
    if((isset($_POST['telefono']))and(!empty($_POST['telefono']))){
        $telefono = $_POST['telefono'];
    }
    if((isset($_POST['correo']))and(!empty($_POST['correo']))){
        $correo = $_POST['correo'];
    }

  
    $sql = "UPDATE tbl_TEL_Numeros SET Descripcion='$nombre',Numero='$telefono',correo='$correo' 
    WHERE id=$id_telefono";


   //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: telefono_resultado.php");
    
?>