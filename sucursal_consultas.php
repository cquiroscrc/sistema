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
    include("db.php");
    
	
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Consulta de codigos</title>

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
		
		<a href="sucursal_diferencias.php"><img width="48"  height="48" src= "img/devolucion.png">Revisar</a>
		<a href="sucursal_imprime_excel.php"><img width="48"  height="48" src= "img/excel.png">Exportar</a>
	    <form class="form-signin" action="sucursal_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span>&nbsp;Consulta de codigos en sucursal</h1>

      	  
        	
        	<?php 
			//// *************** Incluye la consulta ******************
			include "consulta_form.php";
			?>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
			
        </form>

        <?php 
        /// tabla de ubicaciones
        $sql = "SELECT i.CODIGO, i.DESCRIPCION, i.EXISTENCIA, i.ubicacion as ubicacion,e.ubicacion as ubica2, e.cantidad, e.ubicacion as sucubi,
		substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1)
		WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
		WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))      
		Else i.CODIGO END As Filtro FROM inventario i, tbl_exi_suc e WHERE i.CODIGO = e.codigo order by Filtro
		";

		


		 if(($resultado = odbc_exec($id,$sql)) === false)
		    die("Error en Select: ". odbc_errormsg($id));

		    
		    echo '<center><table border =0>
		    <h2>Listado de Codigos en Sucusal</h2>
		            <tr class="encabezadoRegistro">
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Cantidad</th>
		                <th>Existencia</th>
		                <th>Ubicacion</th>
		                <th>Ubicacion 2</th>
		                <th>Editar</th>
		                <th>Eliminar</th>';
		        echo '</tr>';
		    
		         while($arr = odbc_fetch_array($resultado)){
		            echo '<tr>';
		            echo '<td>'.$arr["CODIGO"].'</td><td>'.$arr["DESCRIPCION"].'</td><td>'.$arr["cantidad"].'</td><td>'.$arr["EXISTENCIA"].'</td><td>'.$arr["ubicacion"].'</td><td>'.$arr["ubica2"].'</td>';
		            echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea cambiar la ubicacion de codigo?\")) 
      				location.href=\"sucursal_ubicacion.php?codigo=".$arr["CODIGO"]."\";
      				'><img width='48'  height='48' src= 'img/editar.png'></a></td>";       		    
		            echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar el codigo?\")) 
      				location.href=\"sucursal_eliminar.php?codigo=".$arr["CODIGO"]."\";
      				'><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
		        }
		    	echo '</table></center>';




         ?>
		
		

        <?php 
		include	"consulta.php";
		//// *************** Incluye la consulta ******************
		 ?>
<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Sucursal';
</script> 	
<script src="js/bootstrap.min.js"></script>	
</html>