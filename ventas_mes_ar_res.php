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

if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
$fecha2 = $_REQUEST['Date2'];

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
		
		
    
	    
        
        
        <!--<a href="eventos_eliminar_todos.php"><img width="48"  height="48" src= "img/eliminar.png">Limpiar Reporte</a>  -->
        <?php
        echo "<a href='ventas_mes_pdf.php?Date1=".$fecha1."&Date2=".$fecha2."'><img width='48'  height='48' src= 'img/pdf.png'>Generar PDF</a>";
         
        /// tabla de ubicaciones
        $sql = "SELECT h.codigo,i.DESCRIPCION,SUM(h.cantidad) as veces FROM hgeneral h INNER JOIN inventario i on h.codigo = i.codigo
				WHERE h.movimiento = 'VENTA' AND 
				h.fecha BETWEEN '".$fecha1." 00:00:00.000' AND '".$fecha2." 23:59:59.999' GROUP BY h.codigo,i.CODIGO,i.DESCRIPCION ORDER BY veces DESC";

		


		 if(($resultado = odbc_exec($id,$sql)) === false)
		    die("Error en Select: ". odbc_errormsg($id));

		    
		    echo '<center><table border =1>
		    <h2>Listado de Mayores ventas desde '.$fecha1.' hasta '.$fecha2.'</h2>
		            <tr class="encabezadoRegistro">
		            	<th>Id</th>
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Cantidad</th>
		                <th>Imagen</th>';
		        echo '</tr>';
		    	$conteo=1;
		         while($arr = odbc_fetch_array($resultado)){

		            echo '<tr>';
		            echo '<td>'.$conteo.'</td>';
		            $conteo++;
		            $codigos=$arr["codigo"];
		            $descripcion=$arr["DESCRIPCION"];
		            echo '<td>'.$codigos.'</td>';
		            echo '<td>'.$descripcion.'</td>';
		            echo '<td>'.$arr["veces"].'</td>'; 
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
		            
		        }
		    	echo '</table></center>';




         ?>
		
		
		</div>
        
<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema // Articulos';
</script> 	
<script src="js/bootstrap.min.js"></script>	
</html>