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
	<title>Devolucion Factura</title>

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
    	  if (substr($consecutivo,0,1)<>'D') {
    	  	$consecutivo='D-000000000'.$consecutivo; 	
    	  }
    	  if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
          	$fecha = $_REQUEST['fecha'];      
    	  } 
    	  
    	  $vendedor= $_REQUEST['vendedor'];
    	  if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
          	$vendedor = $_REQUEST['vendedor']; 
          	
          	if (strlen($vendedor)>3) {
          	  $vendedor = strstr($vendedor, ' ', true); // Desde PHP 5.3.0   
          	  }  
          
    	  } 

    	  $cliente=0;
    	  if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
          	$cliente = $_REQUEST['cliente'];  
          	if (strlen($vendedor)>5) {
          	  $cliente = strstr($cliente, ' ', true); // Desde PHP 5.3.0     
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
    	  // QUITAR ESTO UNA VEZ TERMINADO // del AND en adelante
    	  $sql_existencia="SELECT * FROM hfacturas WHERE nfac='$consecutivo'";
    	  if(($r_existencia = odbc_exec($id,$sql_existencia)) === false)
	         die("Error en Select: ". odbc_errormsg($id));

	          	while($r_exis = odbc_fetch_array($r_existencia)){
	          		?>
	          		<script language="javascript">
		          		alert("Devolucion ya existe, Verifique los datos");
		          		document.location=('devolucion_ingreso.php');
	          		</script>
	          		<?php 
	          		
	          	}
	       
          
          ?>
          <center>
          	<h1><span class="glyphicon glyphicon-level-up" aria-hidden="true"></span>&nbsp;Devoluciones</h1>
          	<br>
	      <table border = '1'> 
		      <tr>
			      <td><b>Consecutivo</b></td>
			      <td><b>Fecha</b></td>
			      <td><b>Vendedor</b></td>
			      <td><b>Cliente</b></td>
			      <td><b>Subtotal</b></td>
			      <td><b>Descuento</b></td>
			      <td><b>Impuesto</b></td>
			      <td><b>Total</b></td></tr>
		      <tr>
		          <td><?php echo $consecutivo?></td>
		          <td><?php echo $fecha?></td>
		          <td><?php echo $vendedor?></td>
		          <td><?php echo $cliente?></td>
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
  				FROM mcompras m, inventario i WHERE Factura='".$consecutivo."' AND m.Proveedor='".$vendedor."' AND m.Codigo = I.CODIGO  ORDER BY ID";


				//$sqlrelleno="SELECT * FROM mcompras WHERE Factura='".$consecutivo."' AND Proveedor='".$vendedor."' ORDER BY ID";
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
	      			$totalcod=$relleno['Cantidad']*$relleno['Precio'];
	      			echo '<td>'.number_format($totalcod,2).'</td>';
	      			$csubtotal=$csubtotal+$totalcod;
	      			
	      			echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este codigo de la devolucion?\")) 
        			location.href=\"devolucion_eliminar.php?codigo=".$cod."&consecutivo=".$consecutivo."&cliente=".$cliente."&vendedor=".$vendedor."&fecha=".$fecha."&subtotal=".$subtotal."&descuento=".$descuento."&impuesto=".$impuesto."&total=".$total."\";
        			'><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";          
        			echo '</tr><tr>';
	          		
          		}
          		$csubtotal=$csubtotal-$descuento;
          		$cimpuesto=$csubtotal*0.13;

          		$ctotal=$csubtotal+$cimpuesto;
          		echo '<td><b>Subtotal</b></td><td><b>'.number_format($csubtotal,2).'</b></td><td><b>Descuento</b></td><td><b>'.number_format($descuento,2).'</b></td><td><b>Impuesto</b></td><td><b>'.number_format($cimpuesto,2).'</b></td><td><b>Total</b></td><td><b>'.number_format($ctotal,2).'</b></td><td>Comparar</td>';

			?>

	  	  </table>
		<br>
	    <form class="form-signin" action="devolucion_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-level-up" aria-hidden="true"></span>&nbsp;Consulta de codigos</h1>
      	  
        
        	<table WIDTH=400 border =1>
        	<tr>
                    
                    <td>1= Volkswagen</td>
                    <td>2= Daihatsu</td>
                    <td>3= Empi</td>
                    <td>4= Grupo Ns</td>
            </tr>
                    <td>5= Familia VW</td>
                    <td>6= Descripcion</td>
                    <td>7= Volvo</td>
                    <td>9= Accesorios</td>
          </tr>
          </table>
      		</center>
      	  
      	  	<br>
			<input class="form-control" type="text" id="marca" name="marca" value=" " onkeyup="onChange()" autofocus/>
    		
    		
         
          <input class="form-control" data-inputmask="'mask': '1### ### ### ######'" id="codigo1" name="codigovw" visible="false"  autofocus>
          <input class="form-control" data-inputmask="'mask': '### ### #####'" id="codigof" name="codigovwf" visible="false"  autofocus>
          <input class="form-control" data-inputmask="'mask': '1  N ### ### ###'" id="codigo6" name="codigovwn" visible="false"  autofocus>
          <input class="form-control" data-inputmask="'mask': '2##### ##### #####'" id="codigo2" name="codigod" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '###############'" id="codigo3" name="codigoa" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '0### ### ### #####'" id="codigo4" name="codigov" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '3##############'" id="codigo5" name="codigou" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '# ###################'" id="codigo7" name="codigodes" visible="false" autofocus>
		  
		  <input type="hidden" class="form-control" id="fecha" name="fecha" value=<?php echo $fecha; ?>>	
		  <input type="hidden" class="form-control" id="consecutivo" name="consecutivo" value=<?php echo $consecutivo; ?>>
		  <input type="hidden" class="form-control" id="vendedor" name="vendedor" value=<?php echo $vendedor; ?>>
		  <input type="hidden" class="form-control" id="cliente" name="cliente" value=<?php echo $cliente; ?>>
		  <input type="hidden" class="form-control" id="subtotal" name="subtotal" value=<?php echo $subtotal; ?>>
		  <input type="hidden" class="form-control" id="descuento" name="descuento" value=<?php echo $descuento; ?>>
		  <input type="hidden" class="form-control" id="impuesto" name="impuesto" value=<?php echo $impuesto; ?>>
		  <input type="hidden" class="form-control" id="total" name="total" value=<?php echo $total; ?>>
          <br>
          
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>

		<form class="form-signin" action="devolucion_aplicar.php" method="POST">
			<input type="hidden" class="form-control" id="fecha" name="fecha" value=<?php echo $fecha; ?>>
			<input type="hidden" class="form-control" id="consecutivo" name="consecutivo" value=<?php echo $consecutivo; ?>>
			<input type="hidden" class="form-control" id="vendedor" name="vendedor" value=<?php echo $vendedor; ?>>
			<input type="hidden" class="form-control" id="cliente" name="cliente" value=<?php echo $cliente; ?>>
			<input type="hidden" class="form-control" id="subtotal" name="subtotal" value=<?php echo $subtotal; ?>>
		  	<input type="hidden" class="form-control" id="descuento" name="descuento" value=<?php echo $descuento; ?>>
		  	<input type="hidden" class="form-control" id="impuesto" name="impuesto" value=<?php echo $impuesto; ?>>
		  	<input type="hidden" class="form-control" id="total" name="total" value=<?php echo $total; ?>>
          	
			<button class="btn btn-lg btn-primary btn-block" type="submit">Aplicar</button>
		</form>

        <script languaje="javascript">
        	 
        	 function onChange() {
            	
            	if ($("input").val() == 1){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo1').style.display='block'; 
            		document.getElementById('codigo1').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 2){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='block'; 
			    	document.getElementById('codigo2').focus();
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 3){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo5').style.display='block'; 
            		document.getElementById('codigo5').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 9){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo3').style.display='block'; 
            		document.getElementById('codigo3').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 	
            	}
            	if ($("input").val() == 7){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo4').style.display='block'; 
            		document.getElementById('codigo4').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 	
            	}
            	if ($("input").val() == 4){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo6').style.display='block'; 
            		document.getElementById('codigo6').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 5){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigof').style.display='block'; 
            		document.getElementById('codigof').focus();
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 6){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo7').style.display='block'; 
            		document.getElementById('codigo7').focus();
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigof').style.display='none'; 	
            	}
            	
        	}

			
		</script>
	
		<script>  //script de la mascara
			$(document).ready(function(){
			    $(":input").inputmask();
			    document.getElementById('codigof').style.display='none'; 
			    document.getElementById('codigo1').style.display='none'; 
		    	document.getElementById('codigo2').style.display='none'; 
		    	document.getElementById('codigo3').style.display='none'; 
		    	document.getElementById('codigo4').style.display='none'; 
		    	document.getElementById('codigo5').style.display='none';
		    	document.getElementById('codigo6').style.display='none';
		    	document.getElementById('codigo7').style.display='none'; 
			});
		</script>

	
<script src="js/bootstrap.min.js"></script>	
</html>