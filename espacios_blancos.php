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
		
		
    
	    
        
        <?php
         
        $sql = "SELECT CODIGO, DESCRIPCION, Existencia, ubicacion,
		substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
		WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
		WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
		Else CODIGO END As Filtro FROM Inventario WHERE CODIGO LIKE '% ' order by Filtro
		";

		


		 if(($resultado = odbc_exec($id,$sql)) === false)
		    die("Error en Select: ". odbc_errormsg($id));

		    
		    echo '<center><table border =1>
		    <h2>Listado de Codigos con espacios en blanco</h2>
		            <tr class="encabezadoRegistro">
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Existencia</th>
		                <th>Ubicacion</th>
		                <th>Eliminar</th>';
		        echo '</tr>';
		    
		         while($arr = odbc_fetch_array($resultado)){
		            echo '<tr>';
		            echo '<td>'.$arr["CODIGO"].'</td><td>'.$arr["DESCRIPCION"].'</td><td>'.$arr["Existencia"].'</td><td>'.$arr["ubicacion"].'</td>';       		    
		            echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Arreglar el codigo?\")) 
      				location.href=\"espacios_eliminar.php?codigo=".$arr["CODIGO"]."\";
      				'><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
		        }
		    	echo '</table></center>';




         ?>
		
		
		</div>
        
<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Blancos';
</script> 	
<script src="js/bootstrap.min.js"></script>	
</html>