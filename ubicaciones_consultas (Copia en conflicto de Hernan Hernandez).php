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
  		$ubicacion = $_REQUEST['ubicacion'];        
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

      	  
        	
        	<table WIDTH=400 border =1>
        	<tr>
                    <td>0= Grupo Ns </td>
                    <td>1= Volkswagen</td>
                    <td>2= Daihatsu</td>
                    <td>3= Empi</td>
                    <td>4= Otras marcas</td>
            </tr>
                    <td>5= Familia VW</td>
                    <td>6= Descripcion</td>
                    <td>9= Accesorios</td>
          </tr>
          </table>
      		</center>
      	  
      	  	<br>
      	  	
			<input class="form-control" type="text" id="marca" name="marca" value=" " onkeyup="onChange()" autofocus/>
    		
    		
         	
          <input class="form-control" data-inputmask="'mask': '1### ### ### ######'" id="codigo1" name="codigovw" visible="false"  autofocus>
          <input class="form-control" data-inputmask="'mask': '### ### #####'" id="codigof" name="codigovwf" visible="false"  autofocus>
          <input class="form-control" data-inputmask="'mask': '1  N ### ### ###'" id="codigo6" name="codigovwn" visible="false"  autofocus>
          <input class="form-control" data-inputmask="'mask': '2##### ##### #####'" id="codigo2" name="codigod" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '###############'" id="codigo3" name="codigoa" visible="false" autofocus>
           <input class="form-control" data-inputmask="'mask': '4##### ##### #####'" id="codigo4" name="codigoo" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '3##############'" id="codigo5" name="codigou" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '# ###################'" id="codigo7" name="codigodes" visible="false" autofocus>
          <br>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
			<?php 
      	  	echo '<input type="hidden" class="form-control" id="ubicacion" name="ubicacion"  value="'.$ubicacion.'" >';
      	  	 ?>
        </form>

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
        <script languaje="javascript">
        	 
        	 function onChange() {
            	
            	if ($("input").val() == 1){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo1').style.display='block'; 
            		document.getElementById('codigo1').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 2){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='block'; 
			    	document.getElementById('codigo2').focus();
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 3){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo5').style.display='block'; 
            		document.getElementById('codigo5').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 9){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo3').style.display='block'; 
            		document.getElementById('codigo3').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 	
            	}
            	if ($("input").val() == 4){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo4').style.display='block'; 
            		document.getElementById('codigo4').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 	
            	}
            	if ($("input").val() == 0){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo6').style.display='block'; 
            		document.getElementById('codigo6').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 5){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigof').style.display='block'; 
            		document.getElementById('codigof').focus();
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 6){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo7').style.display='block'; 
            		document.getElementById('codigo7').focus();
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigof').style.display='none'; 	
            	}
            	
        	}

			
		</script>
	
		<script>  //script de la mascara
			$(document).ready(function(){
			    $(":input").inputmask();
			    document.getElementById('codigof').style.display='none'; 
			    document.getElementById('codigo1').style.display='none'; 
		    	document.getElementById('codigo2').style.display='none'; 
		    	document.getElementById('codigo3').style.display='none'; 
		    	document.getElementById('codigo4').style.display='none'; 
		    	document.getElementById('codigo5').style.display='none';
		    	document.getElementById('codigo6').style.display='none';
		    	document.getElementById('codigo7').style.display='none'; 
			});
		</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Cons Ubicaciones';
</script>	
<script src="js/bootstrap.min.js"></script>	
</html>
