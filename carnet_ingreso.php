<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
     include "menu.php"; 
     require("db.php");
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Ingreso de codigos</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	
	<link rel="icon" href="favicon.ico">
	<style>
		input{
			display: none;
		}
	</style>
	

</head>
<body>	
		<br>
		<a href="index.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
	    <form class="form-signin" action="carnet_agregar.php" method="POST">
	    	<p>
      		<center>
      			<h2><span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span>&nbsp; Cliente Nuevo</h2></center>
      		<p>Nombre Cliente</p>
	      	<input type "text" class="form-control" id="nombre" name="nombre" required placeholder="Nombre" autofocus>  	
	      	<br>
	      	<p>Cedula</p>
	      	<input type "text" class="form-control" id="cedula" name="cedula" required placeholder="Cédula" autofocus>
    		<br>
    		<p>Telefono fijo</p>
	      	<input type "text" class="form-control" id="telefono" name="telefono" required placeholder="Telefono" autofocus>
    		<br>
    		<p>Telefono celular</p>
    		<input type "text" class="form-control" id="celular" name="celular" required placeholder="Celular" autofocus>
    		<br>
    		<p>Correo</p>
    		<input type "text" class="form-control" id="correo" name="correo" required placeholder="Correo" autofocus>
    		<br>
    		<p>Fecha de nacimiento dd-mm-yyyy</p>
    		<input type "text" class="form-control" id="nacimiento" name="nacimiento" required placeholder="Nacimiento   dd-mm-yyyy" autofocus>
    		<br>
        <p>Provincia</p>
        <input type "text" class="form-control" id="provincia" name="provincia" required placeholder="Provincia" autofocus>
        <br>
        <p>Canton</p>
        <input type "text" class="form-control" id="canton" name="canton" required placeholder="canton" autofocus>
        <br>
        <p>Distrito</p>
        <input type "text" class="form-control" id="distrito" name="distrito" required placeholder="distrito" autofocus>
        <br>
        <p>Otras señas</p>
        <input type "text" class="form-control" id="direccion" name="direccion" required placeholder="Dirección" autofocus>
        <br>
    		<p>Comentario</p>
    		<input type "text" class="form-control" id="comentario" name="comentario" required placeholder="Comentario" autofocus>
    		<br>
  			<button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
  		</form>

<script src="js/bootstrap.min.js"></script>
</html>
</body>