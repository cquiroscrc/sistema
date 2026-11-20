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

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
		$codigoOrigen = $_REQUEST['codigo'];
	} 
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
	
	<link rel="icon" href="favicon.ico">
	<style>
		input{
			display: none;
		}
	</style>
</head>
<body>	
		
		<a href="pase_consulta_simple.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
	    <form class="form-signin" action="pase_consulta_simple_resultado_destino.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta de codigos Destino</h1>
      	  <?php 
      	  		echo "<h3>Origen ".$codigoOrigen."</h3>";
      	   ?>
        
        	
          <?php
           
			//// *************** Incluye la consulta ******************
			include "consulta_form.php";
		 
          echo "<input type='hidden' id='codigoorigen' class='form-control' name='codigoorigen' required autofocus value='".$codigoOrigen."'>";

           ?>
          <br>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>

		

        <?php 
			include	"consulta.php";
			//// *************** Incluye la consulta ******************
		?>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pase Consulta';
</script>		
<script src="js/bootstrap.min.js"></script>	
</html>