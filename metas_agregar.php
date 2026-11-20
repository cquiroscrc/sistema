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
	    <form class="form-signin" action="metas_aplicar.php" method="POST">
	    	<p>
      		<center>
      			<h2><span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span>&nbsp; Meta Mensual</h2></center>
      	  	<p>Mes</p>
	      	<input type "text" class="form-control" id="mes" name="mes" required placeholder="Mes" autofocus>  	
	      	<br>
	      	<p>Año</p>
	      	<input class="form-control" id="ano" name="ano" required placeholder="Año" autofocus>
			<br>
			<p>Meta</p>
		    <input type "text" class="form-control" id="meta" name="meta" required placeholder="Meta" autofocus>    
		    <br>
		    
          <button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Metas';
</script>	
<script src="js/bootstrap.min.js"></script>
</html>