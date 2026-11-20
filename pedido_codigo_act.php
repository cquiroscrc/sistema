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

    if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
          $factura = $_REQUEST['fact'];      
    } 
    
     
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
	    $id_codigos = $_REQUEST['id'];	      
	  }
	if(isset($_REQUEST['marcador']) && !empty($_REQUEST['marcador'])){
	    $marcador = $_REQUEST['marcador'];	      
	  }
	if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
	    $proveedor = $_REQUEST['proveedor'];	      
	  }
    
		 echo '<br>';
    
      echo '<a class= "agregarUsuario" href="pedido_mostrar.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
    

		    require_once("db.php");
			$sql="SELECT * FROM Pedidos WHERE ID ='$id_codigos'";  

      		if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
		      while($arr = odbc_fetch_array($resultado)){
		      
		          $codigos=$arr["Codigo"];
		          $precio=$arr["Precio"];
		          $cantidad=$arr["Cantidad"];
		          
		         
	          
	         } 
	          //Percent = txtFields(1) / txtFields(0)  'Sub/Descuento/Lineas
            //NCost = txtCosto - (txtCosto * Percent)
	         echo '<form class="form-signin" action="pedido_actualiza_codigo.php" method="POST">
	    	   	  <center><h1>Actualizar codigos</h1></center>
      	  			<br>
					     <label>ID</label>
                <input type="text" id="ID" class="form-control" name="ID" required autofocus value="'.$id_codigos.'" disabled>
                <input type="hidden" id="ID" class="form-control" name="ID" required autofocus value="'.$id_codigos.'">
                <label>Factura</label>
                <input type="text" id="fact" class="form-control" name="fact" required autofocus value="'.$factura.'" disabled>
                <input type="hidden" id="fact" class="form-control" name="fact" required autofocus value="'.$factura.'">
                <input type="hidden" id="proveedor" class="form-control" name="proveedor" required autofocus value="'.$proveedor.'">
                <input type="hidden" id="marcador" class="form-control" name="marcador" required autofocus value="'.$marcador.'">
                <label>Codigo</label>
          			<input type="text" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'">
          			<label>Precio</label>
          			<input type="text" id="precio" class="form-control" name="precio" required autofocus value="'.$precio.'">
	          		<label>Cantidad</label>
          			<input type="text" id="cantidad" class="form-control" name="cantidad" required autofocus value="'.$cantidad.'" >
                <br>
          		  <button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
        		</form>';
        	?>
        <!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
	     