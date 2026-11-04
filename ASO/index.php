<?php 

session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

if (isset($_SESSION['Nombre'])) {
	header("Location:home.php");
}

require("cnx/conexion.php");
require("cnx/utilitaria.controlador.php");
require("cnx/utilitaria.modelo.php");


$error = 1;
if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
    $message = "Datos erroneos, digitelos de nuevo";
    echo "<script type='text/javascript'>alert('$message');</script>";
    // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
    //$resultado = $_GET['resultado'];
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



if (isset($_POST["user"])) {
	$usuario=addslashes($_POST['user']);
	$password=addslashes($_POST['pass']);
	//$password_encriptado = sha1($password);
	//** Revisa existencia
	if(((isset($usuario))and(!empty($usuario)))and((isset($password))and(!empty($password)))){
		$sql = "SELECT * from ASO_Ingreso where Usuario='$usuario' and Password='$password'";
		$acceso=0;
		//busca el valor de usuario y password
		$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        foreach ($respuesta as $key => $value) {
			$maquina=getRealIP();
		    $_SESSION['Nombre'] = $value['Nombre'];
		    $_SESSION['Ingreso']=$maquina;
		    $_SESSION['Tipo'] = $value['Tipo'];
		   $acceso=1;
		  }
	  
	    $sql_update = "UPDATE ASO_Ingreso SET Ingreso='".$maquina."', Fecha='".date("d/m/y H:i:s")."' WHERE Usuario ='".$usuario."'";
	    echo $sql_update;

	    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);  
	     if ($acceso==1) {
	     	header("location: home.php");
	     }else{
	     	 header("location: index.php?error=1");
	     }
	      //header("location: home.php");

    }else{
      
      header("location: index.php?error=1");
    }


	
}



?>
<!DOCTYPE html>
<head>
	<title>APAHNC</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-form-title" style="background-image: url(images/Logo.png);">
					<span class="login100-form-title-1">
						
					</span>
				</div>

				<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" class="login100-form validate-form">
					<div class="wrap-input100 validate-input m-b-26" data-validate="El usuario es obligatorio">
						<span class="label-input100">Usuario</span>
						<input class="input100" type="text" name="user" placeholder="Digite el Usuario">
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-18" data-validate = "La contraseña es obligatoria">
						<span class="label-input100">Contraseña</span>
						<input class="input100" type="password" name="pass" placeholder="Digite la contraseña">
						<span class="focus-input100"></span>
					</div>

					

					<div class="container-login100-form-btn">
						<button type="submit" name="ingresar" class="login100-form-btn">
							Ingresar
						</button>
					</div>
					<div>
						
					</div>
				</form>
			</div>
		</div>
	</div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>