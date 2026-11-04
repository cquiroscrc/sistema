<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");

  if((isset($_GET['salir']))and($_GET['salir']=='si')){
    session_destroy();
    header("location: index.php");
    die;
  }

  if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
    $usuario = $_POST['nombre'];
    //echo $usuario;
  }
  
  if((isset($_POST['contrasena']))and(!empty($_POST['contrasena']))){
    $clave = $_POST['contrasena'];
    //echo $clave;
  }

   function getRealIP()
    {
      if (isset($_SERVER["HTTP_CLIENT_IP"]))
      {
          return $_SERVER["HTTP_CLIENT_IP"];
      }
      elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
      {
          return $_SERVER["HTTP_X_FORWARDED_FOR"];
      }
      elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
      {
          return $_SERVER["HTTP_X_FORWARDED"];
      }
      elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
      {
          return $_SERVER["HTTP_FORWARDED_FOR"];
      }
      elseif (isset($_SERVER["HTTP_FORWARDED"]))
      {
          return $_SERVER["HTTP_FORWARDED"];
      }
      else
      {
          return $_SERVER["REMOTE_ADDR"];
      }

    }

    function verIP(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
    }
   
  //echo  getRealIP()
  if(((isset($usuario))and(!empty($usuario)))and((isset($clave))and(!empty($clave)))){
    $sql = "SELECT * from acc_usuarios
    where usuario='".$usuario."' and password='".$clave."'";
    //echo $sql;
    //exit;
    $acceso=0;
    //busca el valor de usuario y password
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    
    while($fila = odbc_fetch_array($resultado)){
    
        $_SESSION['profile']['nombre'] = $fila['nombre'];
        $_SESSION['profile']['id']=$fila['id'];
        $_SESSION['profile']['tipo'] = $fila['acceso'];
        $maquina=getRealIP();
        $punto=strripos($maquina,".");
        $tama=strlen($maquina);
        $quitar=($tama-$punto)*-1;
        $maquina=substr($maquina, $quitar+1);
        
            
        $_SESSION['profile']['maquina'] =$maquina;

      }
      
      $sql_update = "UPDATE acc_usuarios SET ip='".getRealIP()."', fecha='".date("d/m/y H:i:s")."' WHERE usuario ='".$usuario."'";

      if(($r_update = odbc_exec($id,$sql_update)) === false)
        die("Error en Select: ". odbc_errormsg($id));

      
      header("location: index.php");

    }else{
      
      header("location: login.php?error=1");
    }
  
  
?>
