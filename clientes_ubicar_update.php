<?php
session_start();
    require("db.php");

    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];
    }
    if(isset($_REQUEST['provincia']) && !empty($_REQUEST['provincia'])){
          $provincia = $_REQUEST['provincia'];
        }    
    if(isset($_REQUEST['canton']) && !empty($_REQUEST['canton'])){
          $canton = $_REQUEST['canton'];
        }
    if(isset($_REQUEST['distrito']) && !empty($_REQUEST['distrito'])){
          $distrito = $_REQUEST['distrito'];
        } 
    if(isset($_REQUEST['barrio']) && !empty($_REQUEST['barrio'])){
          $barrio = $_REQUEST['barrio'];
        } 
    if(isset($_REQUEST['otro']) && !empty($_REQUEST['otro'])){
          $otro = $_REQUEST['otro'];
    }     

 
    $sql = "UPDATE clientes SET provincia='$provincia',canton='$canton',distrito='$distrito',barrio='$barrio',otras_senas='$otro' WHERE codigo=$cliente";


   //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: clientes_ubicar.php");
    
?>