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

	if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
		$fecha = $_REQUEST['Date2'];

	}  

	

	
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
		
		
    
	    
        <!--<a href="eventos_eliminar_todos.php"><img width="48"  height="48" src= "img/eliminar.png">Limpiar Reporte</a>  -->
        <?php
        echo '<a href="sin_movimiento_imprime.php?fecha='.$fecha.'""><img width="48"  height="48" src= "img/imprime.png">Generar Reporte</a>';
        

         //echo $fecha;
        $sql = "SELECT CODIGO, DESCRIPCION, existencia,costo, venta, ubicacion,
		substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
		WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
		WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
		Else CODIGO END As Filtro FROM inventario where not  EXISTS(select DISTINCT (codigo) from hgeneral where fecha > '$fecha' and hgeneral.codigo = inventario.codigo) AND existencia <> 0 order by Filtro";

		//echo $sql;
		

		 if(($resultado = odbc_exec($id,$sql)) === false)
		    die("Error en Select: ". odbc_errormsg($id));

		    
		    echo '<center><table border =1>
		    <h2>Listado de Codigos sin movimientos desde '.$fecha.' </h2>
		            <tr class="encabezadoRegistro">
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Existencia</th>
		                <th>Costo</th>
		                <th>Venta</th>
		                <th>Fecha</th>
		                <th>Ubicacion</th>';
		        echo '</tr>';
		    
		         while($arr = odbc_fetch_array($resultado)){
		            
		            $sql5="SELECT codigo,max(fecha) as fecha FROM hgeneral where codigo like '".$arr["CODIGO"]."' GROUP BY codigo";
          			if(($prom2 = odbc_exec($id,$sql5)) === false)
              			die("Error en Select: ". odbc_errormsg($id));
          			while($arr5 = odbc_fetch_array($prom2)){
          				//$ultima_fecha=strtotime($arr5['fecha']);
              			$ultima_fecha = date("d-m-Y", strtotime($arr5['fecha']));

            		}	

		            
		            echo '<tr>';
		            echo '<td>'.$arr["CODIGO"].'</td><td>'.$arr["DESCRIPCION"].'</td><td>'.number_format($arr["existencia"],2).'</td><td>'.number_format($arr["costo"],2).'</td><td>'.number_format($arr["venta"],2).'</td><td>'.$ultima_fecha.'</td><td>'.$arr["ubicacion"].'</td>';       		    
		            
		        }
		    	echo '</table></center>';




         ?>
		
		
		</div>
        

<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Sin movimientos';
</script> 	
<script src="js/bootstrap.min.js"></script>	
</html>