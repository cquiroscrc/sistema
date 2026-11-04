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
	<title>Consulta de codigos</title>

	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/anular.css">
	
	<link rel="icon" href="img/favicon.ico">
	<style>
		input{
			display: none;
		}
	</style>
</head>
<body>	
		
		
	    <form class="form-signin" action="inventario_resultado_orden.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta de codigos Inventario</h1>
      	  
        
        	<?php 
			//// *************** Incluye la consulta ******************
			include "consulta_form.php";
			?>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>

		

        <?php 
		include	"consulta.php";
		//// *************** Incluye la consulta ******************
		 ?>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Estudio';
</script>		
<script src="js/bootstrap.min.js"></script>	
</html>