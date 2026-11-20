<?php 
include("db.php");
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
header("Location: /POS/index.php");
if (isset($_SESSION['id_usuario'])) {
	header("Location: /POS/index.php");
}

//Lograr direccion IP

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


//Login

//if (!empty($_POST)) {
if (isset($_POST["ingresar"])) {
	$usuario=addslashes($_POST['user']);
	$password=addslashes($_POST['pass']);
	//$password_encriptado = sha1($password);
	//** Revisa existencia
	if(((isset($usuario))and(!empty($usuario)))and((isset($password))and(!empty($password)))){
    $sql = "SELECT * from acc_usuarios
    where usuario='".$usuario."' and password='".$password."'";
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


	///**** Ingreso
	$sql = "SELECT idusuarios FROM PhpLogin WHERE usuario = '$usuario' AND password = '$password_encriptado'";

	if(($resultado = odbc_exec($id,$sql)) === false)
    		die("Error en Select: ". odbc_errormsg($id));

 	$rows= odbc_num_rows ($resultado) ;
 	if ($rows>0) {
 	   	$arr = odbc_fetch_array($resultado); 
    	$_SESSION['id_usuario']=$arr['idusuarios'];
    	header("Location: menu.php");
    	
 	}else{
 		echo "<script>
				alert('El usuario Incorrecto')
				window.location= 'index.php';
				</script>";
 	}   	
}


//Registro Usuario
if (isset($_POST["registrar"])) {
		$usuario = addslashes($_POST['user']);
		$password = addslashes($_POST['pass']);
		$nombre = addslashes($_POST['nombre']);
		$correo = addslashes($_POST['correo']);
		$acceso = addslashes($_POST['acceso']);
		$maquina=getRealIP();
		//$password_encriptado = sha1($password);
		$sqluser="SELECT id FROM acc_usuarios WHERE usuario = '$usuario'";

		if(($resultadouser = odbc_exec($id,$sqluser)) === false)
    		die("Error en Select: ". odbc_errormsg($id));	

		$filas= odbc_num_rows ($resultadouser ) ;
		
		if ($filas>0) {
			echo "<script>
				alert('El usuario ya existe')
				window.location= 'index.php';
				</script>";
		}else{
			$sqlusuario = "INSERT INTO acc_usuarios (usuario,password,nombre,correo,acceso,ip) VALUES('$usuario','$password','$nombre','$correo','$acceso','$maquina')";
			
			if(($resultadousuario = odbc_exec($id,$sqlusuario)) === false)
    			die("Error en Select: ". odbc_errormsg($id));
			
			if ($resultadousuario>0) {
				echo "<script>
					alert('Registro Exitoso');
					window.location='ingresar.php';
					</script>";
			}else{
				echo "<script>
					alert('Error al registrase');
					window.location='ingresar.php';
					</script>";
			}


		}
			}
 ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Login  - Sistema de Usuarios</title>

		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/ace.min.css" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" />
		<![endif]-->
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
	</head>

	<body class="login-layout">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">
							<div class="center">
								<h1>
									<i class="ace-icon fa fa-leaf green"></i>
									<span class="red">Sistema </span>
									<span class="white" id="id-text2">de Usuarios</span>
								</h1>
								
							</div>

							<div class="space-6"></div>

							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue lighter bigger">
												<i class="ace-icon fa fa-coffee green"></i>
												Ingresa tu Informacion
											</h4>

											<div class="space-6"></div>

											<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" >
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" class="form-control"  name="user" placeholder="Usuario" required />
															<i class="ace-icon fa fa-user"></i>
														</span>
													</label>

													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" name="pass"class="form-control" placeholder="Contraseña" required/>
															<i class="ace-icon fa fa-lock"></i>
														</span>
													</label>

													<div class="space"></div>

													<div class="clearfix">
														

											<button type="submit" name="ingresar" class="width-35 pull-right btn btn-sm btn-primary">
												<i class="ace-icon fa fa-key"></i>
												<span class="bigger-110">Ingresar</span>
											</button>


													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>

											<div class="social-or-login center">
												<span class="bigger-110">Suscribete</span>
											</div>

											<div class="space-6"></div>

											<div class="social-login center">
												
												<a href="https://www.facebook.com/LosPaticas" target="_blank" class="btn btn-primary">
													<i class="ace-icon fa fa-facebook"></i>
												</a>

											</div>
										</div><!-- /.widget-main -->

										<div class="toolbar clearfix">
											<div>
												
											</div>
											<!-- *********** Aqui va el boton para crear usuario
											<!--<div disabled="true">
												<a href="#" data-target="#signup-box" class="user-signup-link">
													Nuevo Registro
													<i class="ace-icon fa fa-arrow-right"></i>
												</a>
											</div> -->
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->

				

	<div id="signup-box" class="signup-box widget-box no-border">
             	<div class="widget-body">
			<div class="widget-main">
				<h4 class="header green lighter bigger">
					<i class="ace-icon fa fa-users blue"></i>
						Registro de Nuevos Usuarios
				</h4>
	<div class="space-6"></div>
		<p>Ingresa los datos solicitados acontinuacion: </p>
		<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" >
			<fieldset>
			            <label class="block clearfix">
					<span class="block input-icon input-icon-right">
						<input type="text" class="form-control"  name="nombre" placeholder="Nombre Completo"  required />
							<i class="ace-icon fa fa-users"></i>
					</span>
				</label>
			
				<label class="block clearfix">
					<span class="block input-icon input-icon-right">
				             	<input type="email" class="form-control" name="correo" placeholder="Email"  required />
					                        <i class="ace-icon fa fa-envelope"></i>
					</span>
				</label>
					<label class="block clearfix">
						<span class="block input-icon input-icon-right">
			                     		<input type="text" class="form-control" name="user" placeholder="Usuario"  required />
                                       				<i class="ace-icon fa fa-user"></i>
  						</span>
				</label>
				<label class="block clearfix">
                     				<span class="block input-icon input-icon-right">
		                      			<input type="password" class="form-control" name="pass" placeholder="Password"  required />
							<i class="ace-icon fa fa-lock"></i>
					</span>
				</label>

				<label class="block clearfix">
					<select class="form-control" id="acceso" name="acceso">
			            <optgroup >
			                              
			              <option value="cierres">Cierres</option>';                
			              <option value="caja">Ventas</option>';                
			              <option value="parcial">Parcial</option>';                
			              <option value="bodega">Bodega</option>';                
			              <option value="mensajeria">Mensajeria</option>';                
			              <option value="eventos">Eventos</option>';                
			              <option value="vochofest">Vocho Fest</option>';
			              <option value="total">Total</option>';                            
			            </optgroup>
			         </select>

							</label>

							
				<div class="space-24"></div>
				<div class="clearfix">
					<button type="reset" class="width-30 pull-left btn btn-sm">
						<i class="ace-icon fa fa-refresh"></i>
							<span class="bigger-110">Reset</span>
					</button>
					
					<button type="submit" name="registrar"   class="width-65 pull-right btn btn-sm btn-success">
						<span class="bigger-110">Registrar</span>
							<i class="ace-icon fa fa-arrow-right icon-on-right"></i>
					</button>
					 </div>
			</fieldset>
		</form>
	</div>

			<div class="toolbar center">
				<a href="#" data-target="#login-box" class="back-to-login-link">
					<i class="ace-icon fa fa-arrow-left"></i>
						Regresar al Login
				</a>
			</div>
		</div><!-- /.widget-body -->
	</div><!-- /.signup-box -->
</div><!-- /.position-relative -->

							<div class="navbar-fixed-top align-right">
								<br />
								&nbsp;
								<a id="btn-login-dark" href="#">Oscuro</a>
								&nbsp;
								<span class="blue">/</span>
								&nbsp;
								<a id="btn-login-blur" href="#">Azul</a>
								&nbsp;
								<span class="blue">/</span>
								&nbsp;
								<a id="btn-login-light" href="#">Claro</a>
								&nbsp; &nbsp; &nbsp;
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="assets/js/jquery-2.1.4.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
			 $(document).on('click', '.toolbar a[data-target]', function(e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible');//hide others
				$(target).addClass('visible');//show target
			 });
			});



			//you don't need this, just used for changing background
			jQuery(function($) {
			 $('#btn-login-dark').on('click', function(e) {
				$('body').attr('class', 'login-layout');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'blue');

				e.preventDefault();
			 });
			 $('#btn-login-light').on('click', function(e) {
				$('body').attr('class', 'login-layout light-login');
				$('#id-text2').attr('class', 'grey');
				$('#id-company-text').attr('class', 'blue');

				e.preventDefault();
			 });
			 $('#btn-login-blur').on('click', function(e) {
				$('body').attr('class', 'login-layout blur-login');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'light-blue');

				e.preventDefault();
			 });

			});
		</script>
	</body>
</html>
