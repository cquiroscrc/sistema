<?php
session_start();
    require("db.php");

    if((isset($_POST['mes']))and(!empty($_POST['mes']))){
        $mes = $_POST['mes'];
    }
    if((isset($_POST['ano']))and(!empty($_POST['ano']))){
        $ano = $_POST['ano'];
    }
    if((isset($_POST['meta']))and(!empty($_POST['meta']))){
        $meta = $_POST['meta'];
    }
    
    $sql = "INSERT INTO tbl_metas
            (mes,ano,meta)
            values
            ('".$mes."','".$ano."','".$meta."')";

    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
       // echo 'Registro Guardado';
       header("location: index.php");
    







?>