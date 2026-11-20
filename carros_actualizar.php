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

    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $placa = $_REQUEST['id'];        
    }


?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Usuario</title>
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
	    <form class="form-signin" action="carros_update.php" method="POST">
	    	<p>
      		<center>
      		<h2><span class="glyphicon glyphicon-barcode" aria-hidden="true"></span>&nbsp; Carro</h2></center>
      		<?php 
      		
          $sql = "SELECT * from VAG WHERE Placa='$placa'";

    			if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    			
            while($arr = odbc_fetch_array($resultado)){
            		$marca=$arr['Marca'];
            		$tipo=$arr['Tipo'];
            		$year=$arr['Year'];
            		$vin=$arr['Vin'];
                $motor=$arr['Motor'];
                $tipo2=$arr['Tipo2'];
              }

          	  echo '<input type "hidden" id="placa" name="placa" value="'.$placa.'">';
          	  echo '<br>';
    	      	echo '<input type "text" class="form-control" id="marca" name="marca" required placeholder="Marca" autofocus value="'.$marca.'">';
    	      	echo '<br>';
              echo '<input type "text" class="form-control" id="tipo" name="tipo" required placeholder="Tipo" autofocus value="'.$tipo.'">';      
              echo '<br>';
    	      	echo '<input type "text" class="form-control" id="year" name="year" required placeholder="Año" autofocus value="'.$year.'">';
    		      echo '<br>';
              echo '<input type "text" class="form-control" id="vin" name="vin" required placeholder="Vin" autofocus value="'.$vin.'">';      
              echo '<br>';
              echo '<input type "text" class="form-control" id="motor" name="motor" required placeholder="Motor" autofocus value="'.$motor.'">';      
              echo '<br>';
    		      echo '<input type "text" class="form-control" id="tipo2" name="tipo2" required placeholder="Adicional" autofocus value="'.$tipo2.'">';
    		      echo '<br>';
    		      
      			 ?>
    			<button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
  		</form>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Carros Actualizar';
</script>
<script src="js/bootstrap.min.js"></script>
</html>
</body>