<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    
    
    if(isset($_REQUEST['cod']) && !empty($_REQUEST['cod'])){
          $cod = $_REQUEST['cod'];      
    }
    if(isset($_REQUEST['marcador']) && !empty($_REQUEST['marcador'])){
          $marcador = $_REQUEST['marcador'];      
    }
    
  $sql = "UPDATE inventario SET reserva = 1 WHERE CODIGO ='$codigo'";
    
    //echo $sql; exit;
    //if(($prom = odbc_exec($id,$sql)) === false)
      //        die("Error en Select: ". odbc_errormsg($id));
    $prom = odbc_exec($id,$sql);
    

       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      header("location: reserva_resultado.php?codigo=".$cod.'#'.$marcador."");
    
?>
