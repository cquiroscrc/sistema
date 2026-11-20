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
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Compras Factura</title>

	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/anular.css">
	
	<link rel="icon" href="favicon.ico">
	<style>
		input{
			display: none;
		}
	</style>
</head>
<body>	
		<?php
		 
		  require("db.php");

		  if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
          	$consecutivo = $_REQUEST['consecutivo'];      
    	  } 
    	  if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          	$proveedor = $_REQUEST['proveedor']; 
          	if (strlen($proveedor)>5) {
          	  	$proveedor = strstr($proveedor, ' ', true); // Desde PHP 5.3.0
          	}  
          	
			

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

    	  $sql_existencia="SELECT * FROM compras WHERE Factura='$consecutivo' AND proveedor='$proveedor'";
    	  if(($r_existencia = odbc_exec($id,$sql_existencia)) === false)
	         die("Error en Select: ". odbc_errormsg($id));

	          	while($r_exis = odbc_fetch_array($r_existencia)){
	          		?>
	          		<script language="javascript">
		          		alert("Factura ya existe, Verifique los datos");
		          		document.location=('compras_ingreso.php');
	          		</script>
	          		<?php 
	          		
	          	}
	       
          
          ?>

          <center>
          	<h1><span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span>&nbsp;Compras</h1>
          	<br>
	      <table border = '1'> 
		      <tr>
			      <td><b>Consecutivo</b></td>
			      <td><b>Proveedor</b></td>
			      <td><b>Subtotal</b></td>
			      <td><b>Descuento</b></td>
			      <td><b>Impuesto</b></td>
			      <td><b>Total</b></td></tr>
		      <tr>
		          <td><?php echo $consecutivo?></td>
		          <td><?php echo $proveedor?></td>
		          <td><?php echo number_format($subtotal,2)?></td>
		          <td><?php echo number_format($descuento,2)?></td>
		          <td><?php echo number_format($impuesto,2)?></td>
		          <td><?php echo number_format($total,2)?></td>
	      	  </tr>
      	  </table>
          
          <br>
	      <table border = '1'> 
	      <tr>
	      <td><b>Consecutivo</b></td>
	      <td><b>ID</b></td>
	      <td><b>Codigo</b></td>
	      <td><b>Descripcion</b></td>
	      <td><b>Cantidad</b></td>
	      <td><b>Venta</b></td>
	      <td><b>Costo</b></td>
	      <td><b>Total</b></td>
	      <td><b>Eliminar</b></td>
	      </tr><tr>

	      
			<?php
				//Percent = txtFields(1) / txtFields(0)  'Sub/Descuento/Lineas
    	  		//NCost = txtCosto - (txtCosto * Percent)

				$csubtotal=0;
				$cimpuesto=0;
				$ctotal=0;
				$sqlrelleno="SELECT m.ID,m.Factura,m.Codigo,m.Proveedor,m.Precio,m.Cantidad,m.PrecioAnterior,m.PrecioSugerido,i.DESCRIPCION
  				FROM mcompras m, inventario i WHERE Factura='".$consecutivo."' AND m.Proveedor='".$proveedor."' AND m.Codigo = I.CODIGO  ORDER BY ID";

				//$sqlrelleno="SELECT * FROM mcompras WHERE Factura='".$consecutivo."' AND Proveedor='".$proveedor."' ORDER BY ID";
          		if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
	              die("Error en Select: ". odbc_errormsg($id));

	          	while($relleno = odbc_fetch_array($resultadorelleno)){
	          		echo '<td>'.$consecutivo.'</td>
	      			<td>'.$relleno['ID'].'</td>
	      			<td>'.$relleno['Codigo'].'</td>';
	      			$cod=$relleno['Codigo'];

	      			echo '<td>'.$relleno['DESCRIPCION'].'</td>
	      			<td>'.$relleno['Cantidad'].'</td>
	      			<td>'.number_format($relleno['Precio'],2).'</td>
	      			<td>'.number_format($relleno['PrecioSugerido'],2).'</td>';
	      			$totalcod=$relleno['Cantidad']*$relleno['PrecioSugerido'];
	      			echo '<td>'.number_format($totalcod,2).'</td>';
	      			$csubtotal=$csubtotal+$totalcod;
	      			
	      			echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este codigo de la compra?\")) 
        			location.href=\"compras_eliminar.php?codigo=".$cod."&consecutivo=".$consecutivo."&proveedor=".$proveedor."&subtotal=".$subtotal."&descuento=".$descuento."&impuesto=".$impuesto."&total=".$total."\";
        			'><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";          
        			echo '</tr><tr>';
	          		
          		}
          		
          		$cimpuesto=$csubtotal*0.13;
          		$ctotal=$csubtotal+$cimpuesto;
          		echo '<td><b>Subtotal</b></td><td><b>'.number_format($csubtotal,2).'</b></td><td><b>Impuesto</b></td><td><b>'.number_format($cimpuesto,2).'</b></td><td><b>Total</b></td><td><b>'.number_format($ctotal,2).'</b></td>';

			?>

	  	  </table>
		<br>
	    <form class="form-signin" action="compra_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta de codigos</h1>
      	  
        
        	<?php 
			//// *************** Incluye la consulta ******************
			include "consulta_form.php";
			?>
			
		  <input type="hidden" class="form-control" id="consecutivo" name="consecutivo" value=<?php echo $consecutivo; ?>>
		  <input type="hidden" class="form-control" id="proveedor" name="proveedor" value=<?php echo $proveedor; ?>>
		  <input type="hidden" class="form-control" id="subtotal" name="subtotal" value=<?php echo $subtotal; ?>>
		  <input type="hidden" class="form-control" id="descuento" name="descuento" value=<?php echo $descuento; ?>>
		  <input type="hidden" class="form-control" id="impuesto" name="impuesto" value=<?php echo $impuesto; ?>>
		  <input type="hidden" class="form-control" id="total" name="total" value=<?php echo $total; ?>>
          <br>
          
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>

		<form class="form-signin" action="compras_aplicar.php" method="POST">
			<input type="hidden" class="form-control" id="consecutivo" name="consecutivo" value=<?php echo $consecutivo; ?>>
			<input type="hidden" class="form-control" id="proveedor" name="proveedor" value=<?php echo $proveedor; ?>>
			<input type="hidden" class="form-control" id="subtotal" name="subtotal" value=<?php echo $subtotal; ?>>
		  	<input type="hidden" class="form-control" id="descuento" name="descuento" value=<?php echo $descuento; ?>>
		  	<input type="hidden" class="form-control" id="impuesto" name="impuesto" value=<?php echo $impuesto; ?>>
		  	<input type="hidden" class="form-control" id="total" name="total" value=<?php echo $total; ?>>
          	<input type="hidden" class="form-control" id="id_acceso" name="id_acceso" value=<?php echo $id_acceso; ?>>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Aplicar</button>
		</form>

        <?php 
		include	"consulta.php";
		//// *************** Incluye la consulta ******************
		 ?>
	
<script src="js/bootstrap.min.js"></script>	
</html>