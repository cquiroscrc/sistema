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
    $vendedor = $_REQUEST['vendedor'];      
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
        $vendedor = $_REQUEST['vendedor'];      
    }
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];      
    } 
    $cliente = $_REQUEST['cliente'];    
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
        $cliente = $_REQUEST['cliente'];      
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
    
      echo '<a class= "agregarUsuario" href="devolucion_factura.php?consecutivo='.$consecutivo.
      '&vendedor='.$vendedor.'&cliente='.$cliente.'&fecha='.$fecha.'&subtotal='.$subtotal.'&descuento='
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
		          $venta=$arr["VENTA"];
              $potencia=$arr["potencia"];
              if ($potencia<>0) {
                $potencia=$potencia*100;
              }
              $venta=$venta+$potencia;
		         
	          
	         } 
	          //Percent = txtFields(1) / txtFields(0)  'Sub/Descuento/Lineas
            //NCost = txtCosto - (txtCosto * Percent)
	         echo '<form class="form-signin" action="devolucion_salva_codigo.php" method="POST">
	    	   	  <center><h1>Ingreso de codigos</h1></center>
      	  			<br>
      	  			<br>
					<label>Codigo</label>
          			<input type="text" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'" disabled>
          			<input type="hidden" id="codigo" class="form-control" name="codigo" required autofocus value="'.$codigos.'">
          			<label>Descripcion</label>
          			<input type="text" id="descripcion" class="form-control" name="descripcion" required autofocus value="'.$descripcion.'" disabled>
          			<input type="hidden" id="descripcion" class="form-control" name="descripcion" required autofocus value="'.$descripcion.'">
	          		<label>Venta Anterior</label>
          			<input type="text" id="venta" class="form-control" name="venta" required autofocus value="'.$venta.'" disabled>
                <input type="hidden" id="preciosugerido" class="form-control" name="preciosugerido" required autofocus value="'.$venta.'">
	          		<label>Venta</label>
          			<input type="text" id="precio" class="form-control" name="precio" placeholder="Precio Venta"required autofocus >
          			<label>Cantidad</label>
          			<input type="text" id="cantidad" class="form-control" name="cantidad" placeholder="cantidad" required autofocus ">
	          		<br>
					<input type="hidden" id="fecha" class="form-control" name="fecha" required autofocus value="'.$fecha.'">               
          <input type="hidden" id="consecutivo" class="form-control" name="consecutivo" required autofocus value="'.$consecutivo.'">	          		
					<input type="hidden" id="vendedor" class="form-control" name="vendedor" required autofocus value="'.$vendedor.'">
          <input type="hidden" id="cliente" class="form-control" name="cliente" required autofocus value="'.$cliente.'">
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
	     