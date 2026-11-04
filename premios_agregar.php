<?php
session_start();
    require("db.php");

    $sql = "SELECT MAX( ID ) as campo  FROM tbl_premios";

        if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
        while($arr = odbc_fetch_array($resultado)){
            $campo_id=$arr["campo"];
        }

    $id_premio=$campo_id + 1;    

   

    if((isset($_POST['premio']))and(!empty($_POST['premio']))){
        $premio = $_POST['premio'];    
    }

    
    $sql = "INSERT INTO tbl_premios
            (ID,Premio)
            values
            ('".$id_premio."','".$premio."')";

    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
       // echo 'Registro Guardado';
       header("location: premios_resultado.php");
    







?>