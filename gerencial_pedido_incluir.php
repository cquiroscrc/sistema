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
    
    if(isset($_REQUEST['s_pedido']) && !empty($_REQUEST['s_pedido'])){
          $s_pedido = $_REQUEST['s_pedido']; 
    }
    
    if(isset($_REQUEST['sugerido']) && !empty($_REQUEST['sugerido'])){
          $cantidad = $_REQUEST['sugerido'];      
    }
    if(isset($_REQUEST['existe']) && !empty($_REQUEST['existe'])){
          $existe = $_REQUEST['existe'];      
    }



  if ($existe=='si') {
    $sql = "UPDATE hacer_pedido SET cantidad='$cantidad' WHERE codigo='$codigo' AND pedido='$s_pedido'";
  }else{
    $sql = "INSERT INTO hacer_pedido
            (codigo,cantidad,pedido)
            values
            ('".$codigo."','".$cantidad."','".$s_pedido."')";
    }
    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

   
       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      header("location: gerencial_pedido_resultado.php?codigo=".$cod.'&s_pedido='.$s_pedido.'#'.$marcador);
    
?>
