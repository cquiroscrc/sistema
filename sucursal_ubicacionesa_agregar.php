<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    
    
    if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
          $ubicacion = strtoupper($_REQUEST['ubicacion']);      
    }
   
    
  $sql = "UPDATE tbl_exi_suc SET ubicacion = '$ubicacion' WHERE CODIGO ='$codigo'";
    
    //echo $sql; exit;
    //if(($prom = odbc_exec($id,$sql)) === false)
      //        die("Error en Select: ". odbc_errormsg($id));
    $prom = odbc_exec($id,$sql);
    

       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      header("location: sucursal_consultas.php");
    
?>
