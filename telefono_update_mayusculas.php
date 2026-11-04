<?php
session_start();
    require("db.php");

    $sql_listado="SELECT * FROM tbl_TEL_Numeros";
    if(($resultado = odbc_exec($id,$sql_listado)) === false)
            die("Error en Select: ". odbc_errormsg($id));
     while($arr = odbc_fetch_array($resultado)){
         $nombre=ucwords($arr['Descripcion']);
         $id_telefono =$arr['id'];       

            $sql = "UPDATE tbl_TEL_Numeros SET Descripcion='$nombre' WHERE id=$id_telefono";

             if(($resultado2 = odbc_exec($id,$sql)) === false)
                die("Error en Select: ". odbc_errormsg($id));


     }    

  
    
       // echo 'Registro Guardado';
       header("location: telefono_resultado.php");
    
?>