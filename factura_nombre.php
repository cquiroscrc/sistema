<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
     
    }
     include "menu.php"; 
     require("db.php");
    $vendedor=0;
    $mensajeria=0;
    $descuento=0;
    $aparte='no'; 
    
    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
  		$vendedor = $_REQUEST['vendedor'];  
	}
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
  		$consecutivo = $_REQUEST['consecutivo'];  
	}

	if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
  		$descuento = $_REQUEST['descuento'];  
	}
	if(isset($_REQUEST['mensajeria']) && !empty($_REQUEST['mensajeria'])){
  		$mensajeria = $_REQUEST['mensajeria'];  
	}

	if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
  		$cliente = $_REQUEST['cliente'];  
  		// sacar nombre, descuento 
  		if ($cliente =='contado' ) {
			$cliente=0;			
		}
  		$sqlcliente="SELECT * FROM clientes WHERE codigo = ".$cliente."";
  					
	  		if(($r_cliente = odbc_exec($id,$sqlcliente)) === false)
	          die("Error en Select: ". odbc_errormsg($id));

	      	while($res_cliente = odbc_fetch_array($r_cliente)){
	      		$nombre = $res_cliente['nombre'];	
			}
			if ($cliente > 10000) {
				// Credito
				$tipo='credito';
			}
			if ($cliente < 10000) {
				// frecuente
				$tipo='frecuente';
			}
			if ($cliente == 0) {
				// contado
				$tipo='contado';
			}

	}

?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Ingreso de nombre</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link rel="stylesheet" href="css/anular.css">
	<link rel="icon" href="favicon.ico">

	<style>
		input{
			display: none;
		}
		.botones{
			
			border:0px;
			background-color: #66ccff;
		}
	</style>

	<script>

    function F_mandados(consecutivo,cliente,descuento,vendedor) {

        var x;
        if (cliente==0) {cliente='contado';}
        var mensajeria = prompt("Seleccione la cantidad de mandados");

        if (mensajeria != null) {

            //x =  mensajeria + " mandados " + consecutivo + " Consecutivo "+ cliente +" cliente "+descuento;

            //document.getElementById("demo").innerHTML = x;
            
            if (mensajeria) { document.location="factura_mandados.php?consecutivo="+consecutivo+"&cliente="+cliente+"&descuento="+descuento+"&mensajeria="+mensajeria+"&vendedor="+vendedor; }
        }

    }

    function F_mixto(consecutivo,cliente,mensajeria,vendedor) {

        var x;
        if (cliente==0) {cliente='contado';}
        var mixto = prompt("Seleccione el monto del descuento");

        if (mixto!= null) {

           // x =  mixto + " mandados " + consecutivo + " Consecutivo "+ cliente +" cliente "+descuento;

           // document.getElementById("demo").innerHTML = x;
            
          if (mixto) { document.location="factura_mixto.php?consecutivo="+consecutivo+"&cliente="+cliente+"&descuento="+mixto+"&mensajeria="+mensajeria+"&vendedor="+vendedor; }
        }

    }
    function F_nombre(consecutivo,cliente,mensajeria,vendedor,descuento) {

        var x;
        if (cliente==0) {cliente='contado';}
        var nombre = prompt("Digite el nombre del cliente","CLIENTE DE CONTADO");

        if (nombre!= null) {

           // x =  mixto + " mandados " + consecutivo + " Consecutivo "+ cliente +" cliente "+descuento;

           // document.getElementById("demo").innerHTML = x;
            
          if (nombre) { document.location="factura_finaliza.php?consecutivo="+consecutivo+"&cliente="+cliente+"&descuento="+descuento+"&mensajeria="+mensajeria+"&vendedor="+vendedor+"&nombre="+nombre.toUpperCase();}
        }

    }
	</script>
	
	
</head>


<body>	
		<br>
		<?php  
	    	echo '<a href="factura_nueva.php?consecutivo='.$consecutivo.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
	    	$sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura
		    if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
		        die("Error en Select: ". odbc_errormsg($id));
		    
		    while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
		        $factura=$arr_consecutivo["numero"];
		    }
	    ?>
	    <h3>&nbsp&nbsp<span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span>&nbsp;<?php echo $factura ?>&nbsp&nbsp<span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;<?php echo $vendedor ?></h3>
	    
	    <center>

	    	<h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;<?php echo $cliente.' - '.$nombre ?></h1>
          	<div >
          	<table><tr>
          		<?php 
          		if ($cliente ==0 ) {
					$cliente='contado';			
				}
				// Finalizar Factura
				$vendedor=intval($vendedor);
				if ($cliente=='contado') {
					// codigo de nombre
					$cliente=0;
					echo'<td class="botones">
					<a style="color:black" href="#N" onclick="F_nombre('.$consecutivo.','.$cliente.','.$mensajeria.','.$vendedor.','.$descuento.')"><img width="48"  height="48" src= "img/check.png">Finalizar</a></td>';
          		
				}else{
          		echo "<td class='botones'><a style='color:black' href='#' onclick='if(confirm(\"Desea finalizar la factura?\")) 
        			location.href=\"factura_finaliza.php?cliente=".$cliente."&consecutivo=".$consecutivo."&mensajeria=".$mensajeria."&descuento=".$descuento."&vendedor=".$vendedor."\";
        			'><img width='48'  height='48' src= 'img/check.png'>Finalizar</a></td>";          
        		}	
				// Seleccionar Vendedor
				if ($cliente ==0 ) {
					$cliente='contado';			
				}
				echo'<td class="botones">
          			<a style="color:black" href="factura_vendedor.php?consecutivo='.$consecutivo.'&cliente='.$cliente.'&tipo='.$tipo.'&mensajeria='.$mensajeria.'&vendedor='.$vendedor.'&descuento='.$descuento.'"><img width="48"  height="48" src= "img/vendedor.png">Vendedor</a>
          		</td>';        	
          		// Descuento efectivo  *************************
          		echo'<td class="botones">
          			<a style="color:black" href="factura_efectivo.php?consecutivo='.$consecutivo.'&cliente='.$cliente.'&tipo='.$tipo.'&mensajeria='.$mensajeria.'&vendedor='.$vendedor.'"><img width="48"  height="48" src= "img/efectivo.png">Efectivo</a>
          		</td>';
          		// Descuento tarjeta
          		echo'<td class="botones">
          			<a style="color:black" href="factura_tarjeta.php?consecutivo='.$consecutivo.'&cliente='.$cliente.'&tipo='.$tipo.'&mensajeria='.$mensajeria.'&vendedor='.$vendedor.'"><img width="48"  height="48" src= "img/tarjeta.png">Tarjeta</a>
          		</td>';
          		if ($cliente =='contado' ) {
					$cliente=0;			
				}
				// Descuento mixto

          		echo'<td class="botones">
          			<a style="color:black" href="#" onclick="F_mixto('.$consecutivo.','.$cliente.','.$mensajeria.','.$vendedor.')"><img width="48"  height="48" src= "img/pago.png">Mixto</a></td>';
          		
          		// Mandados
          		echo'<td class="botones">
          			<a style="color:black" href="#" onclick="F_mandados('.$consecutivo.','.$cliente.','.$descuento.','.$vendedor.')"><img width="48"  height="48" src= "img/mandado.png">Mandados</a>
          		</td>';
          		// Devolucion
          		echo "<td class='botones'><a style='color:black' href='#' onclick='if(confirm(\"Desea hacer la devolución?\")) 
        			location.href=\"factura_devolucion.php?cliente=".$cliente."&consecutivo=".$consecutivo."&mensajeria=".$mensajeria."&descuento=".$descuento."&vendedor=".$vendedor."\";
        			'><img width='48'  height='48' src= 'img/devolucion.png'>Devolución</a></td>";          
				
          		
          		
          		 ?>
          	</tr></table>


          	<?php 
///  Vendedor


//// Cuadro productos
	    		echo'<br>
	    		  <div>
	    		  <center>
			      <table border=1> 
			      <tr>
			      <th>Consecutivo</th>
			      <th><b>ID</b></th>
			      <th><b>Codigo</b></th>
			      <th><b>Descripcion</b></th>
			      <th><b>Cantidad</b></th>
			      <th><b>Venta</b></th>
			      <th><b>Total</b></th>
			      </tr><tr>';

	    		$csubtotal=0;
				$cimpuesto=0;
				$ctotal=0;
				$sqlrelleno="SELECT m.ID,m.Factura,m.Codigo,m.Vendedor,m.Precio,m.Cantidad,m.Fecha,i.DESCRIPCION, i.EXISTENCIA
  				FROM tmp_factura m, inventario i WHERE Factura='".$consecutivo."' AND m.Codigo = I.CODIGO  ORDER BY ID";

				//$sqlrelleno="SELECT * FROM mcompras WHERE Factura='".$consecutivo."' AND Proveedor='".$proveedor."' ORDER BY ID";
          		if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
	              die("Error en Select: ". odbc_errormsg($id));

	          	while($relleno = odbc_fetch_array($resultadorelleno)){
	          		$id=$relleno['ID'];
	          		$cod=$relleno['Codigo'];
	          		$existencia_actual=$relleno['EXISTENCIA'];
	      			$descripcion=$relleno['DESCRIPCION'];
	      			$cantidad=$relleno['Cantidad'];
	      			$precio=$relleno['Precio'];
	      			if ($existencia_actual>=$cantidad) {
		      			echo '<td>'.$consecutivo.'</td>
		      			<td>'.$id.'</td>
		      			<td>'.$cod.'</td>';
		      			echo '<td>'.$descripcion.'</td>
		      			<td>'.$cantidad.'</td>
		      			<td>'.number_format($precio,2).'</td>';
		      			$totalcod=$cantidad*$precio;
		      			echo '<td>'.number_format($totalcod,2).'</td>';
		      			$csubtotal=$csubtotal+$totalcod;
		      			echo '</tr><tr>';	
	      			}
	          		
	          		
          		}// calculo de la factura
	      		$cimpuesto=($csubtotal-$descuento)*0.13;
          		$ctotal=$csubtotal-$descuento+$cimpuesto+$mensajeria;
          		//echo '<td><b>Subtotal</b></td><td><b>'.number_format($csubtotal,2).'</b></td><td><b>Impuesto</b></td><td><b>'.number_format($cimpuesto,2).'</b></td><td><b>Total</b></td><td><b>'.number_format($ctotal,2).'</b></td>';
	
          		echo '</table></centrar></div>';
/// Cuadro
				echo'<br>
	    		  <div>
	    		  <center>
			      <table border=1> 
			      <tr>
			      <th>Subtotal</th>
			      <td>'.number_format($csubtotal,2).'</td><tr>
			      <th>Descuento</th>
			      <td>'.number_format($descuento,2).'</td><tr>
			      <th>Impuesto</th>
			      <td>'.number_format($cimpuesto,2).'</td><tr>
			      <th>Mensajería</th>
			      <td>'.number_format($mensajeria,2).'</td><tr>
			      <th><b>Total</b></th>
			      <td>'.number_format($ctotal,2).'</td>
			      </tr><tr>';		          		

	    ?>
	    <p id="demo"></p>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Nombre Factura';
</script>	
<script src="js/bootstrap.min.js"></script>
</html>