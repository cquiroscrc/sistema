<?php
session_start();
    require("db.php");

    if((isset($_POST['id']))and(!empty($_POST['id']))){
        $id_codigo = $_POST['id'];
    }
     if((isset($_POST['codigo']))and(!empty($_POST['codigo']))){
        $codigo = $_POST['codigo'];
    }
     if((isset($_POST['descripcion']))and(!empty($_POST['descripcion']))){
        $descripcion = $_POST['descripcion'];
    }
    if((isset($_POST['proveedor']))and(!empty($_POST['proveedor']))){
        $proveedor = $_POST['proveedor'];
    }
    
    $sql = "UPDATE tmpTras SET codigo='$codigo',descripcion='$descripcion',ubicacion='$proveedor' WHERE codigo='$id_codigo'";


   echo $sql; //exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: ventas_perdidas_resultado.php");
    
?>