<?php
  session_start();
  include("conectarDB.php");

  if((isset($_GET['salir']))and($_GET['salir']=='si')){
    header("location: ../index.php");
    die;
  }

  if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
    $usuario = $_POST['nombre'];
    echo $usuario;
  }
  
  if((isset($_POST['contrasena']))and(!empty($_POST['contrasena']))){
    $clave = $_POST['contrasena'];
    echo $clave;
  }
  
  if(((isset($usuario))and(!empty($usuario)))and((isset($clave))and(!empty($clave)))){
    $sql = "SELECT * from usuarios
    where usuario='".$usuario."' and password='".$clave."'";
    echo $sql;
    $acceso=0;
    //busca el valor de usuario y password
    $resultado = mysql_query($sql);
    if (!$resultado) {
      $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";    
      die($mensaje);
    }
    
    $cantidad = mysql_affected_rows();
    if($cantidad>0){
      while ($fila = mysql_fetch_assoc($resultado)) {
        $_SESSION['profile']['nombre'] = $fila['nombre'];
      }
    
      mysql_free_result($resultado);
      header("location: anular_factura.php");

    }else{
      mysql_free_result($resultado);
      header("location: login.php?error=1");
    }
  
  }else{
      mysql_free_result($resultado);
      header("location: login.php?error=1");
  }
?>
