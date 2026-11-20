<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_REQUEST['error']))&&(!(empty($_REQUEST['error'])))){
        $message = "El monto facturado no puede ser mayor a la existencia";
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
	<title>Compra de codigos</title>
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
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigos = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
          $cantidad = $_REQUEST['cantidad'];      
    } 
    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
          $precio = $_REQUEST['precio'];      
    } 
    
		 echo '<br>';
    
      echo '<a class= "agregarUsuario" href="factura_nueva.php?consecutivo='.$consecutivo.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
    

		    require_once("db.php");
		 
	         echo '<form class="form-signin" action="factura_salva_codigo.php" method="POST">
	    	   	  <center><h1>Codigos a facturar</h1></center>
      	  			<br>
                <input type="hidden" id="fecha_sis" class="form-control" name="fecha_sis" required autofocus value="'.$fecha_sis.'">
	              <label>Factura</label>
                <input type="text" id="consecutivo " class="form-control" name="consecutivo" required autofocus value="'.$consecutivo.'" disabled>
                <input type="hidden" id="consecutivo " class="form-control" name="consecutivo" required autofocus value="'.$consecutivo.'">
    
                <label>Codigo</label>
                <input type="text" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'" disabled>
          			<input type="hidden" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'">

                <label>Codigo</label>
          			<input type="text" id="precio" class="form-control" name="precio" required autofocus value="'.$precio.'" disabled>
                <input type="hidden" id="precio" class="form-control" name="precio" required autofocus value="'.$precio.'">

	          		<label>Existencia</label>
                <input type="text" id="existencia" class="form-control" name="existencia" required autofocus value="'.$cantidad.'" disabled>
                <input type="hidden" id="existencia" class="form-control" name="existencia" required autofocus value="'.$cantidad.'">
                <label>Facturar</label>
          			<input type="text" id="cantidad" class="form-control" name="cantidad" required autofocus>
                <br>
          		  <button class="btn btn-lg btn-primary btn-block" type="submit">Salvar</button>
        		</form>';
        	?>
        <!-- Bootstrap Core JavaScript -->
<script type="text/javascript" language="JavaScript">
      document.title = 'Sistema // Agrega factura';
    </script>
<script src="js/bootstrap.min.js"></script>
	     