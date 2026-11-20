<?php
session_start();
    require("db.php");

    
    if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }
    if((isset($_POST['telefono']))and(!empty($_POST['telefono']))){
        $telefono = $_POST['telefono'];
    }
    if((isset($_POST['correo']))and(!empty($_POST['correo']))){
        $correo = $_POST['correo'];
    }
    

    $sql = "INSERT INTO tbl_TEL_Numeros
            (Numero,Descripcion,Correo)
            values
            ('".$telefono."','".ucwords($nombre)."','".$correo."')";

    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
       // echo 'Registro Guardado';
       header("location: factura_telefonos.php");

?>