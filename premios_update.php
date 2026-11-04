<?php
session_start();
    require("db.php");

     if((isset($_POST['id']))and(!empty($_POST['id']))){
        $id_premio = $_POST['id'];
    }
     if((isset($_POST['premio']))and(!empty($_POST['premio']))){
        $premio = $_POST['premio'];
    }


    $sql = "UPDATE tbl_premios SET Premio='$premio' WHERE ID=$id_premio";


   //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: premios_resultado.php");
    
?>