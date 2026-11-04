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
	<title>Agregar Recibos</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	
	<link rel="icon" href="img/favicon.ico">

	<style>
		input{
			display: none;
		}
		
	</style>
</head>
<body>	<?php
		  
        echo '<br>';  
		
		echo '<center>';
  		echo '<h1>Generar recibos de dinero</h1>';
		include("db.php");

 		echo '<form class="form-signin" action="recibo_imprime.php" method="POST">
 		 	<p>
 
      	 
          <input class="form-control" id="nombre" name="nombre" placeholder="Nombre" required autofocus>
		  <br>
		  <input class="form-control" id="descripcion" name="descripcion" placeholder="Descripcion de recibo" required autofocus>
		  <br>
		  <input class="form-control" id="monto" name="monto" placeholder="Monto de recibo" required autofocus>
		  <br>
		  
          <button class="btn btn-lg btn-primary btn-block" type="submit">Imprimir</button>
        </form>';
		?>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Recibo';
</script> 
</html>