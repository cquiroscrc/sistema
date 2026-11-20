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
    $txt_ubicacion=null;
    if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
  		$ubicacion = strtoupper($_REQUEST['ubicacion']);        
	} 
	$parcial=0;
	if(isset($_REQUEST['txt_ubicacion']) && !empty($_REQUEST['txt_ubicacion'])){
  		$ubicacion = $_REQUEST['txt_ubicacion'];
  		$parcial=1;
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
		
		<a href="ubicaciones_seleccion.php"><img width="48"  height="48" src= "img/back.png">Volver</a> 	
		<?php 
		if ($parcial==1) {
				
		}else{
		 echo '<a href="ubicaciones_eliminar.php?ubicacion='.$ubicacion.'"><img width="48"  height="48" src= "img/cut.png">Vaciar</a>';
		}
		  ?>
		<a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
	    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
	    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
	    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Listado</a>

	    <form class="form-signin" action="ubicaciones_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span>&nbsp;Consulta de codigos</h1>

      	  
        	
        	<?php 
//// *************** Incluye la consulta ******************
include "consulta_form.php";
?>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
			<?php 
      	  	echo '<input type="hidden" class="form-control" id="ubicacion" name="ubicacion"  value="'.$ubicacion.'" >';
      	  	 ?>
        </form>
<?php 
include	"consulta.php";
//// *************** Incluye la consulta ******************
 ?>
        <?php 
        /// tabla de ubicaciones
        if ($parcial==1) {
        	$sql = "SELECT CODIGO,DESCRIPCION,EXISTENCIA,ubicacion,
			substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
			WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
			WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
			Else CODIGO END As Filtro  FROM inventario WHERE ubicacion like '$ubicacion%' order by Filtro";	
        }else{
        	$sql = "SELECT CODIGO,DESCRIPCION,EXISTENCIA,ubicacion,
			substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
			WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
			WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
			Else CODIGO END As Filtro  FROM inventario WHERE ubicacion ='$ubicacion' order by Filtro";

        }
        

		


		 if(($resultado = odbc_exec($id,$sql)) === false)
		    die("Error en Select: ". odbc_errormsg($id));

		    echo '<div id="muestra">';
		    echo '<center><table border =1>
		    <h2>Listado de Codigos en la ubicacion '.$ubicacion.'</h2>
		            <tr class="encabezadoRegistro">
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Existencia</th>
		                <th>Ubicacion</th>';
		        echo '</tr>';
		    
		         while($arr = odbc_fetch_array($resultado)){
		            echo '<tr>';
		            echo '<td>'.$arr["CODIGO"].'</td><td>'.$arr["DESCRIPCION"].'</td><td>'.$arr["EXISTENCIA"].'</td><td>'.$arr["ubicacion"].'</td>';       		    
		        }
		    	echo '</table></center>';




         ?>
		
		
		</div>
       

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Cons Ubicaciones';
</script>	
<script src="js/bootstrap.min.js"></script>	
</html>
