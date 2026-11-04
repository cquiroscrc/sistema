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
	<title>Actualizacion de codigos</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/anular.css">
	
	<link rel="icon" href="favicon.ico">

</head>
<body>	

	<?php
		
		  if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
		      $codigo = $_REQUEST['codigo'];
		      
		    } 
          if(isset($_REQUEST['sele']) && !empty($_REQUEST['sele'])){
		      $cod = $_REQUEST['sele'];
		      
		    }
		    echo '<br>';
		    echo '<a class="agregarUsuario" href="descripcion_resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          

		    require_once("db.php");
			$sql="SELECT * FROM inventario WHERE CODIGO ='$codigo'";  

      		if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
		      while($arr = odbc_fetch_array($resultado)){
		      
		          $codigos=$arr["CODIGO"];
		          $descripcion=$arr["DESCRIPCION"];
		          $existencia=$arr["EXISTENCIA"];
		          $costo=$arr["COSTO"];
		          $venta=$arr["VENTA"];
		          $bodega=$arr["BODEGA"];
		          $tecnica=$arr["DESC_ALT"];
		          $descuento=$arr["descuento"];
		          $proveedor=$arr["proveedor"];
		          $iv=$arr["IV"];
		          $stock=$arr["stock"];
		          $ubicacion=$arr["ubicacion"];
		          $costodol=$arr["costodol"];
		          $factorcosto=$arr["factorcosto"];
		          $factorprecio=$arr["factorprecio"];
		          $fpanterior=$arr["fpAnterior"];
		          $potencia=$arr["potencia"];
		          $carro=$arr["Carro"];
		          $reserva=$arr["Reserva"];
	          
	         } 
		//<input type="text" id="tecnica" class="form-control" name="tecnica" placeholder="tecnica" required autofocus value="'.$tecnica.'">
	         echo '<form class="form-signin" action="descripcion_salva.php" method="POST">
	    	   	  <center><h1>Actualizacion de codigos</h1></center>
      	  			<br>
      	  			<label>Codigo</label>
      	  			<br>
      	  			<h2>'.$codigos.'</h2>
          			<input type="hidden" id="codigo" class="form-control" name="codigo" placeholder="codigo" required autofocus value="'.$codigos.'">	          	
					<br>
          			<label>Descripcion</label>
          			<input type="text" id="descripcion" class="form-control" name="descripcion" placeholder="descripcion" required autofocus value="'.$descripcion.'">			
					<br>
          			<label>Descripcion Tecnica</label>
          			<textarea  name="tecnica">'.$tecnica.'</textarea>
          			
					<br>
					<input type="hidden" id="cod" class="form-control" name="cod" placeholder="cod" required autofocus value="'.$cod.'">
          			
          			<input type="hidden" id="act" class="form-control" name="act" placeholder="act" required autofocus value="1">

	          		<br>
          		  <button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
        		</form>';
	     ?>
	     <script src="js/bootstrap.min.js"></script> 
  </body>
</html>