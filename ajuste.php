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
	<title>Ajustes de codigos</title>

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
		   $consecutivo2=0;
		   $sql="SELECT * FROM Consecutivos WHERE tipo= '05'";    

     		if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

          	while($arr = odbc_fetch_array($resultado)){
          		$consecutivo=$arr['numero'];
          	}

          	$sqlin="SELECT * FROM ajutras WHERE Estado=0";    

     		if(($resultadoin = odbc_exec($id,$sqlin)) === false)
              die("Error en Select: ". odbc_errormsg($id));

          	while($arrin = odbc_fetch_array($resultadoin)){
          		$consecutivo2=$arrin['IDMovimiento'];
          	}
          	
          	if ($consecutivo2<>0) {
          		$consecutivo=$consecutivo2;

          	}

          	
          
          ?>
          <h1>Ajustes de codigos # <?php echo $consecutivo .'-'.$id_acceso.'-'.$maquina?></h1>
          <center>
	      <table border = '1'> 
	      <tr>
	      <td><b>Consecutivo</b></td>
	      <td><b>ID</b></td>
	      <td><b>Codigo</b></td>
	      <td><b>Descripcion</b></td>
	      <td><b>Cantidad</b></td>
	      <td><b>Costo</b></td>
	      <td><b>Total</b></td>
	      <td><b>Tipo</b></td>
	      <td><b>Estado</b></td>
	      <td><b>Eliminar</b></td>
	      </tr><tr>
			<?php

  				$sqlrelleno="SELECT d.IDMovimiento,d.IDTipoMovimiento,d.Codigo,d.Cantidad,d.Costo,d.IDBodegaO,d.IDBodegaD,d.ID,i.DESCRIPCION
  				FROM detajutras d, inventario i WHERE IDMovimiento='".$consecutivo."' AND d.Codigo = I.CODIGO  ORDER BY ID";

				//$sqlrelleno="SELECT * FROM detajutras WHERE IDMovimiento='".$consecutivo."' ORDER BY ID";
          		
          		if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
	              die("Error en Select: ". odbc_errormsg($id));

	          	while($relleno = odbc_fetch_array($resultadorelleno)){
	          		echo '<td>'.$consecutivo.'</td>
	      			<td>'.$relleno['ID'].'</td>
	      			<td>'.$relleno['Codigo'].'</td>';
	      			$cod=$relleno['Codigo'];

	      			echo '<td>'.$relleno['DESCRIPCION'].'</td>
	      			<td>'.$relleno['Cantidad'].'</td>
	      			<td>'.number_format($relleno['Costo'],2).'</td>';
	      			$total=$relleno['Cantidad']*$relleno['Costo'];
	      			echo '<td>'.number_format($total,2).'</td>';
	      			$tipo =$relleno['IDTipoMovimiento'];
	      			if ($tipo==1) {
	      				$tipodes='Ajuste Entrada';
	      				echo '<td style="color:green">'.$tipodes.'</td>';
	      				
	      			}else{
	      				$tipodes='Ajuste Salida';
	      				echo '<td style="color:red">'.$tipodes.'</td>';
	      			}
	      			echo '<td>Pendiente</td>';
	      			echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este codigo del ajuste?\")) 
        			location.href=\"ajuste_eliminar.php?codigo=".$cod."&ajuste=".$consecutivo."\";
        			'><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";          
        			echo '</tr><tr>';
	          		
          		}
			?>

	  	  </table>
		
	    <form class="form-signin" action="ajuste_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta de codigos</h1>
      	  
        
        	<?php 
			//// *************** Incluye la consulta ******************
			include "consulta_form.php";
			//// *************** Incluye la consulta ******************
	        echo '<input type="hidden" id="consecutivo" name="consecutivo" value="'.$consecutivo.'">';
          	?>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>
		<?php 
		if ($acceso=='bodega') {
			
		}else{
			?>
			<form class="form-signin" action="ajuste_aplicar.php" method="POST">
				<input type="hidden" class="form-control" id="numajuste" name="numajuste" value=<?php echo $consecutivo; ?>>
				<input type="hidden" class="form-control" id="id_acceso" name="id_acceso" value=<?php echo $id_acceso; ?>>
				<h3>Razón del ajuste</h3> 
				<input type="text" class="form-control" id="razon" name="razon" value><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Aplicar</button>
			</form>
			<?php 
		}
		  
		include	"consulta.php";
		//// *************** Incluye la consulta ******************
 ?>
        
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ajustes de codigos';
</script>	
<script src="js/bootstrap.min.js"></script>	
</html>