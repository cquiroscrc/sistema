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
	<title>Reporte para pedidos</title>
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
<body>	
		

	    <form class="form-signin" action="reporte_pedido_imprime.php" method="POST">
	    	<p>
      	  <center><h1>Reporte para pedidos</h1></center>
      	  <select class="form-control" id="tipo" name="tipo" onChange="habilitar(this.form)">
	          <optgroup >
	          	<option value="">Seleccione una marca</option>
	            <option value="vw">Volkswagen</option>
	            <option value="dai">Daihatsu</option>
	            <option value="acc">Accesorios</option>
	            <option value="vol">Volvo</option>
	            <option value="us">Empi</option>
	          </optgroup>
          </select>
          <br>
          <select class="form-control" id="mov" name="mov" >
	          <optgroup >
	            <option value="1">Movimientos (ABC)</option>
	            <option value="2">Medio Movimiento (DEFGH)</option>
	            <option value="3">Sin Movimiento (IJKL)</option>
	            <option value="4">Todos</option>
	          </optgroup>
          </select>
          <br>
          <select class="form-control" id="formato" name="formato" >
	          <optgroup >
	            <option value="1">Formato PDF</option>
	            <option value="2">Formato XLS</option> 
	          </optgroup>
          </select>
			<br>
		 
		  
          
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
         	
         	<div style="width: 120px; height: 100px" id="carga" class="precarga" visible="false" ><h1>Cargando...</h1>
				<img src="img/103.gif" alt="Cargando" border="0" />

			</div>

        </form>

		<script languaje="javascript">
			
			function habilitar(form)
			{	
				 if (form.tipo[0].selected == false)
			    {
			    setTimeout(function(){
			    	document.getElementById('carga').style.display='block'; 
				}, 10000);
			    
			    }
			}
		</script>

        <script>  //script de la mascara
			$(document).ready(function(){
			    
			    document.getElementById('carga').style.display='none'; 
			    
			});
		</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Rep Pedido';
</script>		
<script src="js/bootstrap.min.js"></script>
</html>