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
      $id_premio = $_REQUEST['id'];        
    }

echo $id_premio
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
	
	<link rel="icon" href="img/favicon.ico">
	<style>
		input{
			display: none;
		}
	</style>
	

</head>
<body>	
		<br>
		<a href="premios_resultado.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
	    <form class="form-signin" action="premios_update.php" method="POST">
	    	<p>
      		<center>
      		<h2><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp; Premio</h2></center>
      		<?php 
      		
          $sql = "SELECT * from tbl_premios WHERE ID='$id_premio'";

    			if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    			
            while($arr = odbc_fetch_array($resultado)){
            		$premio=$arr['Premio'];
            		
            	}

          	  echo '<input type "hidden" id="id" name="id" value="'.$id_premio.'">';
          	  echo '<br>';
    	      	echo '<input type "text" class="form-control" id="premio" name="premio" required placeholder="Premio" autofocus value="'.$premio.'">';
    	      	echo '<br>';
              
      			 ?>
    			<button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
  		</form>

<script src="js/bootstrap.min.js"></script>
</html>
</body>