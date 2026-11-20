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
	<title>Agregar Egresos</title>
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
  		echo '<h1>Egresos de caja de :'.$fecha1.'</h1>';

		include("db.php");

 		echo '<form class="form-signin" action="caja_agregar_egreso.php" method="POST">
 		 <center><h2>Agregar egreso</h2></center>
	    	<p>
 
      	  <select class="form-control" id="egresos" name="egresos" required>
	          <optgroup >';
	          	

	          	$sql = "SELECT * FROM tbl_TiposEgresos WHERE Tipo = 5";

               if(($res = odbc_exec($id,$sql)) === false)
      				die("Error en Select: ". odbc_errormsg($id));

   				while($arr = odbc_fetch_array($res)){
              		$tipoE=$arr['Tipo'];
              		$descripcion=$arr['Descripcion'];
	          		echo '<option value="'.$tipoE.'">'.$descripcion.'</option>';
	          }
	          echo '</optgroup>
          </select>
          <br>
		  <input class="form-control" id="documento" name="documento" placeholder="Documento de Encomienda" required autofocus>
		  <br>
		  <input class="form-control" id="monto" name="monto" placeholder="Monto de Encomienda" required autofocus>
		  <br>
		  <input class="form-control" id="empresa" name="empresa" placeholder="Empresa Encomienda" required autofocus>
		  <br>
		  <input class="form-control" id="hora" name="hora" placeholder="Hora Encomienda" required autofocus>
		  <br>
		  <input class="form-control" id="lugar" name="lugar" placeholder="Lugar Encomienda" required autofocus>
		  <br>
		  <input class="form-control" id="descripcion" name="descripcion" placeholder="Nombre del Cliente" required autofocus>
		  <br>

		  <input id="Date2" name="Date2" value="'.$fecha2.'">
		  <input id="tipo" name="tipo" value="'.$tipo.'">

          <button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
        </form>';
		?>
<script src="js/bootstrap.min.js"></script>
</html>