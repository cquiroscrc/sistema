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
		<a href="usuarios_resultado.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
	    <form class="form-signin" action="usuarios_agregar.php" method="POST">
	    	<p>
      		<center>
      			<h2><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp; Usuario</h2></center>
      	  
	      	<input type "text" class="form-control" id="nombre" name="nombre" required placeholder="Nombre" autofocus>  	
	      	<br>
	      	<input class="form-control" id="usuario" name="usuario" required placeholder="Usuario" autofocus>
		    <br>
		    <input type "text" class="form-control" id="password" name="password" required placeholder="Password" autofocus>    
		    <br>
		    </table>
  			<select class="form-control" id="acceso" name="acceso">
      			<optgroup >
      				<option value='ventas'>Acceso ventas</option>
      				<option value='parcial'>Acceso parcial</option>
  					<option value='total'>Acceso total</option>
  					<option value='caja'>Acceso caja</option>
  					<option value='bodega'>Acceso bodega</option>
  					<option value='mensajeria'>Acceso Mensajero</option>
    			</optgroup>
  			</select>
  			<br>
  			<button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
  		</form>

<script src="js/bootstrap.min.js"></script>
</html>
</body>