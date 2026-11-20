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
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Anular Factura</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	<link rel="icon" href="img/favicon.ico">
</head>

<body>	<?php

    
	     
		  
		echo '<center>';
 		echo '<form class="form-signin" action="consulta_ver_factura.php" method="POST">
 		 <center><h2><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta Factura</h2></center>
	    	<p>
          <br>
		  <input class="form-control" id="documento" name="documento" placeholder="Factura a consultar" required autofocus>
		  <br>
		  <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
        </form>';

       
		?>
<script src="js/jquery.js"></script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Consulta Reimprimir';
</script>
<script src="js/bootstrap.min.js"></script>
</html>