<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
      
     
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
	<title>Ganando con Los Paticas</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	<link href="/docs/4.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="icon" href="favicon.ico">
	<style>
		input{
			display: none;
		}
        body {
          background: linear-gradient(45deg, #0856fb, #777777);
          font-family: sans-serif;
        }
        p{
            color:white;
        }
        h2{
            color:red;
        }
	</style>
	

</head>
<body>	
				    
	    <form class="form-signin" action="procesar.php" method="POST">
	    	<center>
                <img src="logo.png" alt="Los Paticas" style="width:95px;height:76px;">
      			<h2><span  aria-hidden="true"></span>Rifa Los Paticas </h2></center>
      		<p>Nombre Completo</p>
	      	<input type "text" class="form-control" id="nombre" name="nombre" required placeholder="Nombre" autofocus>  	
	      	<br>
	      	<p>Identificación</p>
	      	<input type "text" class="form-control" id="cedula" name="cedula" required placeholder="Identificación" autofocus>
    		<br>
    		<p>Telefono celular</p>
    		<input type "text" class="form-control" id="celular" name="celular" required placeholder="Celular" autofocus>
    		<br>
    		<p>Correo</p>
    		<input type "text" class="form-control" id="correo" name="correo" required placeholder="Correo" autofocus>
    		<br>
  			<button class="btn btn-lg btn-danger btn-block" type="submit">Enviar</button>
  		</form>
      <!--<a href="https://api.whatsapp.com/send?phone=50688240452&text=Hola%20me%20gustar%C3%ADa%20obtener%20informaci%C3%B3n,%20sobre%20programacion">WhastApp</a>-->
<script src="js/bootstrap.min.js"></script>
</html>
</body>