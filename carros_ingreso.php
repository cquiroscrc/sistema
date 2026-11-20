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
		<a href="carros_resultado.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
	    <form class="form-signin" action="carros_agregar.php" method="POST">
	    	<p>
      		<center>
      			<h2><span class="glyphicon glyphicon-barcode" aria-hidden="true"></span>&nbsp; Carro</h2></center>
      	    <input type "text" class="form-control" id="marca" name="marca" required placeholder="Marca" autofocus>
      	    <br>  	
	      	<input type "text" class="form-control" id="tipo" name="tipo" required placeholder="Tipo" autofocus>  	
	      	<br>
	      	<input type "text" class="form-control" id="year" name="year" required placeholder="Año" autofocus>
    		<br>
    		<input type "text" class="form-control" id="placa" name="placa" required placeholder="Placa" autofocus>
    		<br>
    		<input type "text" class="form-control" id="vin" name="vin" required placeholder="vin" autofocus>
            <br>
            <input type "text" class="form-control" id="motor" name="motor" required placeholder="Motor" autofocus>
		    <br>
		    <input type "text" class="form-control" id="tipo2" name="tipo2" required placeholder="Adicional" autofocus>
		    <br>
  			<button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
  		</form>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Carros Agregar';
</script>
<script src="js/bootstrap.min.js"></script>
</html>
</body>