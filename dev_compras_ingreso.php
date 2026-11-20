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
	<script>
	  
	  $(function() {
	    var availableTags = [
	    	<?php
	              $sql="SELECT * FROM Proveedores ORDER BY nombre";
	              
	              if(($resultado = odbc_exec($id,$sql)) === false)
	                  die("Error en Select: ". odbc_errormsg($id));
	          
	            while($arr = odbc_fetch_array($resultado)){
	              $cod_proveedor=$arr['codigo'];
	              $nombre_proveedor=$arr['nombre'];
	                echo "'".$cod_proveedor." - ".$nombre_proveedor."',";    
	            } 
	    	?>
	      
	    ];
	    $( "#proveedor" ).autocomplete({
	      source: availableTags
	    });
	  });

  </script>
</head>
<body>
  <form class="form-signin" action="dev_compras_factura.php" method="POST">
	    	<p>
      		<center>
      			<h2><span class="glyphicon glyphicon-share" aria-hidden="true"></span>&nbsp;Devolucion de Compra</h2></center>
      	  
	      	<input type "text" class="form-control" id="consecutivo" name="consecutivo" required placeholder="N° Compra" autofocus>
	      	<br>
	      	<input class="form-control" id="proveedor" name="proveedor" required placeholder="Proveedor" autofocus>
	      	<br>
		    <input type "text" class="form-control" id="subtotal" name="subtotal" required placeholder="Subtotal" autofocus>    
		    <br>
          	<input type "text" class="form-control" id="descuento" name="descuento" required placeholder="Descuento" autofocus>    
          	<br>
          	<input type "text" class="form-control" id="impuesto" name="impuesto" required placeholder="Impuesto" autofocus>    
          	<br>
          	<input type "text" class="form-control" id="total" name="total" required placeholder="Total" autofocus>    
          <br>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Devolucion de Compras';
</script>	
<script src="js/bootstrap.min.js"></script>
</html>