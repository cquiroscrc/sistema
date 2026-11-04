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
		
		<a href="sucursal_consultas.php"><img width="48"  height="48" src= "img/back.png">Volver</a>
		<a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    	{var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    	ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    	ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>
    	
		<div id="muestra">
	    
        <?php 
        /// tabla de ubicaciones
        $sql = "SELECT i.CODIGO, i.DESCRIPCION, i.EXISTENCIA, i.ubicacion as ubicacion,e.ubicacion as ubica2, e.cantidad, e.ubicacion as sucubi,
		substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1)
		WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
		WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))      
		Else i.CODIGO END As Filtro FROM inventario i, tbl_exi_suc e WHERE i.CODIGO = e.codigo AND e.cantidad >= i.EXISTENCIA order by Filtro
		";

		


		 if(($resultado = odbc_exec($id,$sql)) === false)
		    die("Error en Select: ". odbc_errormsg($id));

		    
		    echo '<center><table border =1>
		    <h2>Listado de Codigos en Sucusal</h2>
		            <tr class="encabezadoRegistro">
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Cantidad</th>
		                <th>Existencia</th>
		                <th>Ubicacion</th>
		                <th>Ubicacion 2</th>';
		        echo '</tr>';
		    
		         while($arr = odbc_fetch_array($resultado)){
		            echo '<tr>';
		            echo '<td>'.$arr["CODIGO"].'</td><td>'.$arr["DESCRIPCION"].'</td><td>'.$arr["cantidad"].'</td><td>'.$arr["EXISTENCIA"].'</td><td>'.$arr["ubicacion"].'</td><td>'.$arr["ubica2"].'</td>';
		                  		    
		            
		        }
		    	echo '</table></center>';




         ?>
		</div>
		

        
<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Diferencias';
</script> 	
<script src="js/bootstrap.min.js"></script>	
</html>