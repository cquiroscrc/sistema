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
	
	<link rel="icon" href="favicon.ico">

	<style>
		input{
			display: none;
		}
		
	</style>
</head>
<body>	<?php
		  
		   if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
			  $fecha1 = $_REQUEST['Date1'];
			  
			} 
			if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
			$fecha2 = $_REQUEST['Date2'];

			}
			if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
			$tipo = $_REQUEST['tipo'];

			}
			if ($tipo=='dia') {
			  $fecha1=$fecha2;
			}

        echo '<br>';  
		echo '<a class="agregarUsuario" href="caja_resultado.php?Date2='.$fecha2.'&tipo=dia"><img width="48"  height="48" src= "img/back.png">Volver</a>';

		echo '<center>';
  		echo '<h1>Recibos de caja de :'.$fecha1.'</h1>';
		include("db.php");

 		echo '<form class="form-signin" action="caja_agregar_recibo.php" method="POST">
 		 <center><h2>Agregar Recibo</h2></center>
	    	<p>
 
      	  <select class="form-control" id="recibos" name="recibos" required>
	          <optgroup >';
	          	

	          	$sql = "SELECT codigo,nombre FROM clientes where codigo =0 OR codigo > 10000";

               if(($res = odbc_exec($id,$sql)) === false)
      				die("Error en Select: ". odbc_errormsg($id));

   				while($arr = odbc_fetch_array($res)){
              		$cliente=$arr['codigo'];
              		$descripcion=$arr['nombre'];
	          		echo '<option value="'.$cliente.'">'.$descripcion.'</option>';
	          }
	          echo '</optgroup>
          </select>
          <br>
		  <input class="form-control" id="documento" name="documento" placeholder="Documento de recibo" required autofocus>
		  <br>
		  <input class="form-control" id="monto" name="monto" placeholder="Monto de recibo" required autofocus>
		  <br>
		  <input id="Date2" name="Date2" value="'.$fecha2.'">
		  <input id="tipo" name="tipo" value="'.$tipo.'">

          <button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
        </form>';
		?>
<script src="js/bootstrap.min.js"></script>
</html>