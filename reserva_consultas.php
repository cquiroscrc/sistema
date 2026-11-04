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
		
		
    
	    <form class="form-signin" action="reserva_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span>&nbsp;Consulta de codigos en Reserva</h1>

      	  
        	
        	<?php 
			//// *************** Incluye la consulta ******************
			include "consulta_form.php";
			?>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
			
        </form>
        <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    	{var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    	ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    	ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>
    	<a href="reserva_imprime_excel.php"><img width="48"  height="48" src= "img/excel.png">Exportar</a>
		<div id="muestra"> 
        <?php 
        
        /// tabla de ubicaciones
        $sql = "SELECT CODIGO, DESCRIPCION, EXISTENCIA, ubicacion,
		substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
		WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
		WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
		Else CODIGO END As Filtro FROM inventario WHERE reserva=1 order by Filtro
		";

		


		 if(($resultado = odbc_exec($id,$sql)) === false)
		    die("Error en Select: ". odbc_errormsg($id));

		    
		    echo '<center><table border =1>
		    <h2>Listado de Codigos en Reserva</h2>
		            <tr class="encabezadoRegistro">
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Existencia</th>
		                <th>Ubicacion</th>
		                <th>Eliminar</th>';
		        echo '</tr>';
		    	$marcador1='';
	            $marcador2='inicio';
	            $marcador3='inicio';
	            $marcador4='inicio';
	            $contador=1;
	            $marca=1;

		         while($arr = odbc_fetch_array($resultado)){
		         	$marca++;

		         	$codigos=$arr["CODIGO"];

		         	if ($marcador1=='') {
	                  $marcador=$codigos;
	                  $marcador1='inicio';
	                }
	                
	                if ($contador==2) {
	                  $marcador1=$marcador2;
	                  $marcador2=$marcador3;
	                  $marcador3=$marcador4;
	                  $marcador4=$codigos;
	                  
	                }
	                if ($contador==3) {
	                  $marcador1=$marcador2;
	                  $marcador2=$marcador3;
	                  $marcador3=$marcador4;
	                  $marcador4=$codigos;
	                  $contador=1;
	                }


		            echo '<tr>';
		          
		            echo '<td><a name="'.$marca.'"" style="color:black" # </a>'.$arr["CODIGO"].'</td>';
		            // <td>'.$arr["CODIGO"].'</td>
		            echo '<td>'.$arr["DESCRIPCION"].'</td><td>'.$arr["EXISTENCIA"].'</td><td>'.$arr["ubicacion"].'</td>';       
		            $marca2=$marca-4;		    
		            echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar el codigo?\")) 
      				location.href=\"eliminar_reserva.php?marcador=".$marca2."&codigo=".$arr["CODIGO"]."\";
      				'><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
      			 $contador++;	
		        }
		    	echo '</table></center>';
		    	


         ?>
		
		
		</div>
        <?php 
		include	"consulta.php";
		//// *************** Incluye la consulta ******************
		 ?>

<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Reserva';
</script> 	
<script src="js/bootstrap.min.js"></script>	
</html>