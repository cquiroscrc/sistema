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
	if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
        $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
        $proveedor = $_REQUEST['proveedor'];      
    } 
    if(isset($_REQUEST['subtotal']) && !empty($_REQUEST['subtotal'])){
        $subtotal = $_REQUEST['subtotal'];      
    }   
    $descuento=0;
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
        $descuento = $_REQUEST['descuento'];      
    } 
    $impuesto=0;
    if(isset($_REQUEST['impuesto']) && !empty($_REQUEST['impuesto'])){
        $impuesto = $_REQUEST['impuesto'];      
    } 
    if(isset($_REQUEST['total']) && !empty($_REQUEST['total'])){
        $total = $_REQUEST['total'];      
    } 

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion'];      
    } 
    
    if(isset($_REQUEST['precioanterior']) && !empty($_REQUEST['precioanterior'])){
          $precioanterior = $_REQUEST['precioanterior'];      
    } 
    
     
    if(isset($_REQUEST['sele']) && !empty($_REQUEST['sele'])){
	    $cod = $_REQUEST['sele'];	      
	}
		 echo '<br>';
    
      echo '<a class= "agregarUsuario" href="compras_factura.php?consecutivo='.$consecutivo.
      '&proveedor='.$proveedor.'&subtotal='.$subtotal.'&descuento='
      .$descuento.'&impuesto='.$impuesto.'&total='.$total.'">
      <img width="48"  height="48" src= "img/back.png">Volver</a>';
    

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
	          //Percent = txtFields(1) / txtFields(0)  'Sub/Descuento/Lineas
            //NCost = txtCosto - (txtCosto * Percent)
	         echo '<form class="form-signin" action="compras_salva_codigo.php" method="POST">
	    	   	  <center><h1>Ingreso de codigos</h1></center>
      	  			<br>
      	  			<br>
					<label>Codigo</label>
          			<input type="text" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'" disabled>
          			<input type="hidden" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'">
          			<label>Descripcion</label>
          			<input type="text" id="descripcion" class="form-control" name="descripcion" required autofocus value="'.$descripcion.'" disabled>
          			<input type="hidden" id="descripcion" class="form-control" name="descripcion" required autofocus value="'.$descripcion.'">	          		
	          		<label>Venta</label>
          			<input type="text" id="precio" class="form-control" name="precio" placeholder="Precio Venta"required autofocus >
                <label>Costo</label>
                <input type="text" id="preciosugerido" class="form-control" name="preciosugerido" placeholder="Precio Costo" required autofocus >
          			<label>Cantidad</label>
          			<input type="text" id="cantidad" class="form-control" name="cantidad" placeholder="cantidad" required autofocus ">
	          		<br>
					<input type="hidden" id="consecutivo" class="form-control" name="consecutivo" required autofocus value="'.$consecutivo.'">	          		
					<input type="hidden" id="proveedor" class="form-control" name="proveedor" required autofocus value="'.$proveedor.'">
					<input type="hidden" id="precioanterior" class="form-control" name="precioanterior" required autofocus value="'.$precioanterior.'">
					<input type="hidden" id="subtotal" class="form-control" name="subtotal" required autofocus value="'.$subtotal.'">
					<input type="hidden" id="descuento" class="form-control" name="descuento" required autofocus value="'.$descuento.'">
					<input type="hidden" id="impuesto" class="form-control" name="impuesto" required autofocus value="'.$impuesto.'">
					<input type="hidden" id="total" class="form-control" name="total" required autofocus value="'.$total.'">
          		  <button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
        		</form>';
        	?>
        <!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
	     