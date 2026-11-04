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
 
 	include("db.php");

    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $id_usuario = $_REQUEST['id'];        
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
		<a href="usuarios_resultado.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
	    <form class="form-signin" action="usuarios_update.php" method="POST">
	    	<p>
      		<center>
      		<h2><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp; Usuario</h2></center>
      		<?php 
      		$sql = "SELECT * from acc_usuarios WHERE id='$id_usuario'";

      
      if(($result = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    
          while($row = odbc_fetch_array($result)){    
			
        		$nombre=$row['nombre'];
        		$usuario=$row['usuario'];
        		$password=$row['password'];
        		$acceso=$row['acceso'];
        	}

      	  	echo '<input type "hidden" id="id" name="id" value="'.$id_usuario.'">';
      	  	echo '<br>';
	      	echo '<input type "text" class="form-control" id="nombre" name="nombre" required placeholder="Nombre" autofocus value="'.$nombre.'">';
	      	echo '<br>';
	      	echo '<input class="form-control" id="usuario" name="usuario" required placeholder="Usuario" autofocus value="'.$usuario.'">';
		    echo '<br>';
		    echo '<input type "text" class="form-control" id="password" name="password" required placeholder="Password" autofocus value="'.$password.'">';
		    echo '<br>';
		    //echo '<input type "text" class="form-control" id="acceso" name="acceso" required autofocus value="Acceso '.$acceso.'">';
		    
		    echo '</table>';
  			echo '<select class="form-control" id="acceso" name="acceso">
      			<optgroup>
      				<option value="ventas">Acceso ventas</option>
      				<option value="parcial">Acceso parcial</option>
  					<option value="total">Acceso total</option>
  					<option value="caja">Acceso caja</option>
  					<option selected value="'.$acceso.'">Acceso '.$acceso.'</option>
    			</optgroup>
  			</select>
  			<br>';
  			 ?>
  			<button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
  		</form>

<script src="js/bootstrap.min.js"></script>
</html>
</body>