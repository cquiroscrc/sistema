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
	<link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
    <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script> <!--  Imagenes fancy -->
	<link rel="icon" href="favicon.ico">
	<style>
		input{
			display: none;
		}
	</style>
</head>
<body>	
		
		
    
	    <form class="form-signin" action="eventos_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-compressed" aria-hidden="true"></span>&nbsp;Consulta Eventos</h1>

      	  
        	
        	<?php 
			//// *************** Incluye la consulta ******************
			include "consulta_form.php";
			?>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
			
        </form>
        <a href="eventos_imprime.php"><img width="48"  height="48" src= "img/imprime.png">Generar Reporte</a>
        
        <!--<a href="eventos_eliminar_todos.php"><img width="48"  height="48" src= "img/eliminar.png">Limpiar Reporte</a>  -->
        <?php
        echo "<a href='eventos_pdf.php'><img width='48'  height='48' src= 'img/pdf.png'>Generar PDF</a>";
         echo "<a class = 'echo_link' href='#' onclick='if(confirm(\"Desea limpiar el reporte?\")) 
      				location.href=\"eventos_eliminar_todos.php\";
      				'><img width='48'  height='48' src= 'img/eliminar.png'>Limpiar Reporte</a>"; 
        /// tabla de ubicaciones
        $sql = "SELECT CODIGO, DESCRIPCION, Cantidad, ubicacion,
		substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
		WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
		WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
		Else CODIGO END As Filtro FROM tbl_TrasOUT order by Filtro
		";

		


		 if(($resultado = odbc_exec($id,$sql)) === false)
		    die("Error en Select: ". odbc_errormsg($id));

		    
		    echo '<center><table border =1>
		    <h2>Listado de Codigos para Eventos</h2>
		            <tr class="encabezadoRegistro">
		            	<th>Id</th>
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Existencia</th>
		                <th>Ubicacion</th>
		                <th>Imagen</th>
		                <th>Eliminar</th>';
		        echo '</tr>';
		    	$conteo=1;
		         while($arr = odbc_fetch_array($resultado)){

		            echo '<tr>';
		            echo '<td>'.$conteo.'</td>';
		            $conteo++;
		            $codigos=$arr["CODIGO"];
		            $descripcion=$arr["DESCRIPCION"];
		            echo '<td>'.$arr["CODIGO"].'</td><td>'.$arr["DESCRIPCION"].'</td><td>'.$arr["Cantidad"].'</td><td>'.$arr["ubicacion"].'</td>'; 
		            $path='subida_cat/';

		          if (substr($codigos,0,1)==1) {
		            $directorio='1/';
		          }
		          if (substr($codigos,0,1)==2) {
		            $directorio='2/';
		          }
		          if (substr($codigos,0,1)==3) {
		            $directorio='3/';
		          }
		          if (substr($codigos,0,1)==4) {
                	$directorio='4/';
              	  }
		          if (substr($codigos,0,1)==9) {
		            $directorio='9/';
		          }
		          if (substr($codigos,0,1)==0) {
		            $directorio='0/';
		          }
		          $codigos1=rtrim($codigos);
		          if (substr($codigos,-2)=='EU') {
		             $codigos1=substr($codigos,0,-2);
		             $codigos1=rtrim($codigos1);
		             
		          } 
		          if (substr($codigos,-2)=='GR') {
		             $codigos1=substr($codigos,0,-2);
		             $codigos1=rtrim($codigos1);
		          }
		          if (substr($codigos,-2)=='TW') {
		             $codigos1=substr($codigos,0,-2);
		             $codigos1=rtrim($codigos1);
		             
		          }
		          if (substr($codigos,-2)=='JP') {
		             $codigos1=substr($codigos,0,-2);
		             $codigos1=rtrim($codigos1);
		          }
		          if (substr($codigos,-2)=='ZL') {
		             $codigos1=substr($codigos,0,-2);
		             $codigos1=rtrim($codigos1);
		          }

		          $foto=substr ( $codigos1 , 1 );
		          $foto=rtrim($foto);
		          
		          if (substr($foto,0,1)==' ') {
		          	//echo 'FOTOS';
		          	$foto=substr($foto, 2, strlen($foto));
		          }
		          
		          $filename = $path.$directorio.$foto.".jpg";
		          $archivo=$filename;

		          if (file_exists($filename)) {
		              $archivo=$filename;
		          } else {
		              $archivo=$path."inf.png";
		          }

          		echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';	    
		            echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar el codigo?\")) 
      				location.href=\"eventos_eliminar.php?codigo=".$arr["CODIGO"]."\";
      				'><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
		        }
		    	echo '</table></center>';




         ?>
		
		
		</div>
        <?php 
		include	"consulta.php";
		//// *************** Incluye la consulta ******************
		 ?>

<script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>

<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Eventos';
</script> 	
<script src="js/bootstrap.min.js"></script>	
</html>