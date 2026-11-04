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
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Ingreso de codigos</title>
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
	<?php 
		if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
		  $factura = $_REQUEST['fact'];
		  
		} 

		if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
		  $proveedor = $_REQUEST['proveedor'];
		  
		}
		$sql="SELECT * FROM Pedidos WHERE Factura='$factura'" ;
		if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
     	while($arr = odbc_fetch_array($resultado)){
    		$factor=$arr['factor'];
    	}

		echo '<a class= "agregarUsuario" href="pedido_mostrar.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
		echo '<a class= "agregarUsuario" href="pedido_precio.php?fact='.$factura.'&proveedor='.$proveedor.'&factor='.$factor.'"><img width="48"  height="48" src= "img/adelante.png">Avanzar sin Cambios</a>';
		
	 ?>
	    <form class="form-signin" action="pedido_factor.php" method="POST">
	    	<p>
      		<center>
      			<h2><span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span>&nbsp; Pedido</h2></center>
      			<h2>&nbsp; <?php echo $factura." - ".$proveedor ?></h2></center>
      	  
	      	<input type "text" class="form-control" id="monto" name="monto" required placeholder="Monto de facturas" autofocus>  	
	      	<br>
	      	<input type "text" class="form-control" id="tc" name="tc" required placeholder="Tipo de Cambio" autofocus>
			<br>
		    <input type "text" class="form-control" id="aduana" name="aduana" required placeholder="Gastos Aduanales" autofocus>    
		    <br>
          	<input type "text" class="form-control" id="seguro" name="seguro" required placeholder="Seguro y Fletes" autofocus>    
          	<br>
          	<input type "text" class="form-control" id="impuesto" name="impuesto" required placeholder="Impuesto de Ventas" autofocus>     	
          	<?php 
	          	echo '<input type="hidden" id="fact"  name="fact" required autofocus value="'.$factura.'">';
	          	echo '<input type="hidden" id="proveedor"  name="proveedor" required autofocus value="'.$proveedor.'">';
          	 ?>
          	<br>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Aplicar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
		</form>
		<br>
		<form class="form-signin" action="pedido_factor.php" method="POST">
	    	<p>
      		<center>
      			<h2>Factor</h2></center>
      			
      	  
	      	
	      		<input type "text" class="form-control" id="factor" name="factor" required autofocus value="<?php echo $factor; ?>">  	
          	<?php 
          		
          		//echo '<input type="text" class="form-control" id="factor" name="factor" required autofocus value="'.$factor'">';
	          	echo '<input type="hidden" id="fact"  name="fact" required autofocus value="'.$factura.'">';
	          	echo '<input type="hidden" id="proveedor"  name="proveedor" required autofocus value="'.$proveedor.'">';
          	 ?>
          	<br>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Aplicar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
		</form>
<script src="js/bootstrap.min.js"></script>
</html>