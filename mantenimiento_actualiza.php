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
		    echo '<a href="mantenimiento_resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          

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
		          $marca1=$arr['MARCA'];
	          
	         } 
	          
	         echo '<form class="form-signin" action="mantenimiento_salva.php" method="POST">
	    	   	  <center><h1>Actualizacion de codigos</h1></center>
      	  			<br>
      	  			<label>Codigo</label>
          			<input type="text" id="codigo" class="form-control" name="codigo" placeholder="codigo" required autofocus value="'.$codigos.'">
					<br>
          			<label>Descripcion</label>
          			<input type="text" id="descripcion" class="form-control" name="descripcion" placeholder="descripcion" required autofocus value="'.$descripcion.'">
					<br>
          			<label>Descripcion Tecnica</label>
          			<textarea  name="tecnica">'.$tecnica.'</textarea>
					<br>
					<label>Existencia</label>
          			<input type="text" id="existencia" class="form-control" name="existencia" placeholder="existencia" required autofocus value="'.$existencia.'">
					<br>
					';
					if ($acceso=='caja') { 
						
						echo '
						
	          			<input type="hidden" id="costo" class="form-control" name="costo" placeholder="costo" required autofocus value ="'.$costo.'">
						
	          			<input type="hidden" id="venta" class="form-control" name="venta" placeholder="venta" required autofocus value="'.$venta.'">          			
						
	          			<input type="hidden" id="descuento" class="form-control" name="descuento" placeholder="descuento" required autofocus value="'.$descuento.'">
						<input type="hidden" id="iv" class="form-control" name="iv" placeholder="iv" required autofocus value="'.$iv.'">
	          			
	          			<input type="hidden" id="proveedor" class="form-control" name="proveedor" placeholder="proveedor" required autofocus value="'.$proveedor.'">
						<input type="hidden" id="bodega" class="form-control" name="bodega" placeholder="bodega" required autofocus value="'.$bodega.'">
						';


					}else{
					echo '
					<label>Costo</label>
          			<input type="text" id="costo" class="form-control" name="costo" placeholder="costo" required autofocus value ="'.$costo.'">
					<br>
          			<label>venta</label>
          			<input type="text" id="venta" class="form-control" name="venta" placeholder="venta" required autofocus value="'.$venta.'">          			
					<br>
          			<label>Descuento</label>
          			<input type="text" id="descuento" class="form-control" name="descuento" placeholder="descuento" required autofocus value="'.$descuento.'">
					<br>
					<label>Impuesto</label>
          			<input type="text" id="iv" class="form-control" name="iv" placeholder="iv" required autofocus value="'.$iv.'">
          			<br>
					<label>Proveedor</label>
          			<input type="text" id="proveedor" class="form-control" name="proveedor" placeholder="proveedor" required autofocus value="'.$proveedor.'">
					<br>
					<label>Bodega</label>
          			<input type="text" id="bodega" class="form-control" name="bodega" placeholder="bodega" required autofocus value="'.$bodega.'">
					<br>';
					}
          			echo'
          			<label>Ubicacion</label>
          			<input type="text" id="ubicacion" class="form-control" name="ubicacion" placeholder="ubicacion" required autofocus value="'.$ubicacion.'">

					<input type="hidden" id="cod" class="form-control" name="cod" placeholder="cod" required autofocus value="'.$cod.'">
          			
          			<input type="hidden" id="act" class="form-control" name="act" placeholder="act" required autofocus value="1">
          			<br>
          			<label>Marca</label>';
          			
          			?>
	          		<select class="form-control" id="marca" name="marca" >
            			<optgroup >
              		<?php             
              			
              			$sql = "SELECT * FROM Marcas";

               			if(($res = odbc_exec($id,$sql)) === false)
              				die("Error en Select: ". odbc_errormsg($id));

              			while($arr = odbc_fetch_array($res)){
                  			$descripcion=$arr['descripcion'];
                  			$marca=$arr['codigo'];
                  			if ($marca==$marca1) {
                  				echo '<option value="'.$marca.'" selected>'.$descripcion.'</option>';
                  			}else{
                			echo '<option value="'.$marca.'">'.$descripcion.'</option>';
                			}
            			}
            			echo '</optgroup>'
          			?>
          			</select>
          			<br>
          			<?php  
          			echo'
          		  <button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
        		</form>';
	     ?>
	     <script src="js/bootstrap.min.js"></script>
  </body>
</html>