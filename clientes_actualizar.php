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
      $id_cliente = $_REQUEST['id'];        
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
		<a href="clientes_resultado.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
	    <form class="form-signin" action="clientes_update.php" method="POST">
	    	<p>
      		<center>
      		<h2><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp; Cliente</h2></center>
      		<?php 
      		
          $sql = "SELECT * from clientes WHERE codigo='$id_cliente'";

    			if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    			
            while($arr = odbc_fetch_array($resultado)){
            		$nombre=$arr['nombre'];
            		$telefono=$arr['telefono'];
            		$direccion=$arr['direccion'];
            		$cedula=$arr['cedula'];
                $celular=$arr['celular'];
                $correo=$arr['correo'];
                $nacimiento=$arr['nacimiento'];
                $comentario=$arr['comentario'];
            	}

          	  echo '<input type "hidden" id="id" name="id" value="'.$id_cliente.'">';
          	  echo '<br>';
    	      	echo '<input type "text" class="form-control" id="nombre" name="nombre" required placeholder="Nombre" autofocus value="'.$nombre.'">';
    	      	echo '<br>';
              echo '<input type "text" class="form-control" id="cedula" name="cedula" required placeholder="Cedula" autofocus value="'.$cedula.'">';      
              echo '<br>';
    	      	echo '<input type "text" class="form-control" id="telefono" name="telefono" required placeholder="Telefono" autofocus value="'.$telefono.'">';
    		      echo '<br>';
              echo '<input type "text" class="form-control" id="celular" name="celular" required placeholder="Celular" autofocus value="'.$celular.'">';      
              echo '<br>';
              echo '<input type "text" class="form-control" id="correo" name="correo" required placeholder="Correo" autofocus value="'.$correo.'">';      
              echo '<br>';
    		      echo '<input type "text" class="form-control" id="direccion" name="direccion" required placeholder="Direccion" autofocus value="'.$direccion.'">';
    		      echo '<br>';
    		      echo '<input type "text" class="form-control" id="nacimiento" name="nacimiento" required placeholder="Nacimiento" autofocus value="'.$nacimiento.'">';      
              echo '<br>';
              echo '<input type "text" class="form-control" id="comentario" name="comentario" required placeholder="Comentario" autofocus value="'.$comentario.'">';      
      			 echo '<br>';
      			 ?>
    			<button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
  		</form>

<script src="js/bootstrap.min.js"></script>
</html>
</body>