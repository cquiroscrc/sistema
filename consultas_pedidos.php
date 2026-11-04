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
	
	<link rel="icon" href="favicon.ico">
	<style>
		input{
			display: none;
		}
	</style>
</head>
<body>	
		

	    


		<?php
		include("db.php");
 		echo '<form class="form-signin" action="consultas_pedido_ver.php" method="POST">
	    	<p>
      	  <center><h1>Ver pedido en proceso</h1></center>
      	  <select class="form-control" id="pedidos" name="pedidos">
	          <optgroup >
	          	<option value="">Seleccione el pedido</option>';

	          	$sql_pedido = "SELECT DISTINCT pedido FROM hacer_pedido";


	          	if(($r_pedido = odbc_exec($id,$sql_pedido)) === false)
        			die("Error en Select: ". odbc_errormsg($id));
    
    
    			while($row = odbc_fetch_array($r_pedido)){
                
                    	$p_pedido=$row['pedido'];
              	
	          		echo '<option value="'.$p_pedido.'">'.$p_pedido.'</option>';

	          	
	          }
	          echo '</optgroup>
          </select>
          <br>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
        </form>';
		?>

        
	
	<script src="js/bootstrap.min.js"></script> 
</html>