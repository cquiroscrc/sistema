<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
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
	<title>Ajuste de codigos</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	
	<link rel="icon" href="favicon.ico">

</head>
<body>	

	<?php
      
		
		  if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
		      $consecutivo = $_REQUEST['consecutivo'];
		      
		    } 
          if(isset($_REQUEST['sele']) && !empty($_REQUEST['sele'])){
		      $cod = $_REQUEST['sele'];
		      
		    }
		    

		    require_once("db.php");
			$sql="SELECT * FROM inventario WHERE CODIGO ='$cod'";  

      		if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
		      while($arr = odbc_fetch_array($resultado)){
		      
		          $codigos=$arr["CODIGO"];
		          $descripcion=$arr["DESCRIPCION"];
		          $existencia=$arr["EXISTENCIA"];
		          $costo=$arr["COSTO"];
		         
	          
	         } 
	          
	         echo '<form class="form-signin" action="ajuste_salva_codigo.php" method="POST">
	    	   	  <center><h1>Actualizacion de codigos</h1></center>
      	  			<br>
					<label>Codigo</label>
          			<input type="text" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'" disabled>
          			<input type="hidden" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'">
          			<label>Descripcion</label>
          			<input type="text" id="descripcion" class="form-control" name="descripcion" required autofocus value="'.$descripcion.'" disabled>
          			<input type="hidden" id="descripcion" class="form-control" name="descripcion" required autofocus value="'.$descripcion.'">
          			<input type="hidden" id="costo" class="form-control" name="costo" required autofocus value="'.$costo.'">
	          		<label>Existencia</label>
          			<input type="text" id="existencia" class="form-control" name="existencia" required autofocus value="'.$existencia.'" disabled>
          			<input type="hidden" id="existencia" class="form-control" name="existencia" required autofocus value="'.$existencia.'" >
          			<input type="hidden" id="ajuste" class="form-control" name="ajuste" required autofocus value="'.$consecutivo.'" >
	          		<label>Tipo Ajuste</label>
          	  		<select class="form-control" id="tipo" name="tipo">
		         		<optgroup >
		          		<option value="2">Ajuste Salida</option>
		            	<option value="1">Ajuste Entrada</option>
		          		</optgroup>
          			</select>
					<label>Cantidad</label>
          			<input type="text" id="cantidad" class="form-control" name="cantidad" placeholder="cantidad" required autofocus ">
	          		<br>
          		  <button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
        		</form>';
        	?>
        <!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
	     