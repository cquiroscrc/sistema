<?php
session_start();
    require("db.php");

    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];
    }
    
    if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
          $nombre = $_REQUEST['nombre'];
          $nombre = strtoupper($nombre);
    }     

 
    $sql = "UPDATE clientes SET nombre='$nombre' WHERE codigo=$cliente";


   //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: clientes_nombre.php");
    
?>