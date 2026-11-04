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
      $id_codigo = $_REQUEST['id'];        
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
		<a href="ventas_perdidas_resultado.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
	    <form class="form-signin" action="ventas_perdidas_update.php" method="POST">
	    	<p>
      		<center>
      		<h2><span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span>&nbsp; Producto</h2></center>
      		<?php 
      		
          $sql = "SELECT * from tmpTras WHERE codigo='$id_codigo'";

    			if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    			
            while($arr = odbc_fetch_array($resultado)){
            		$codigo=$arr['codigo'];
            		$descripcion=$arr['descripcion'];
            		$proveedor=$arr['ubicacion'];
            		
            	}
              echo '<input type "hidden" id="id" name="id" value="'.$codigo.'">';
          	  echo '<input type "text" class="form-control" id="codigo" name="codigo" required placeholder="Codigo" autofocus value="'.$codigo.'">';
    	      	echo '<br>';
              echo '<input type "text" class="form-control" id="descripcion" name="descripcion" required placeholder="Descripcion" autofocus value="'.$descripcion.'">';      
              echo '<br>';
    	      	echo '<input type "text" class="form-control" id="proveedor" name="proveedor" required placeholder="Proveedor" autofocus value="'.$proveedor.'">';      
              echo '<br>';
      			 echo '<br>';
      			 ?>
    			<button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
  		</form>
<script type="text/javascript" language="JavaScript">
      document.title = 'Sistema / Perdidas Actualizar';
</script>
<script src="js/bootstrap.min.js"></script>
</html>
</body>