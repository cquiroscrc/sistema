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
		

	    <form class="form-signin" action="pedir_stock_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta de codigos</h1>
      	  	<h2>Bajo Stock</h2>
        
        	<table WIDTH=400 border =1>
        	<tr>
                    
                    <td>1= Volkswagen</td>
                    <td>2= Daihatsu</td>
                    <td>3= Empi</td>
                    <td>4= Grupo Ns</td>
            </tr>
                    <td>5= Familia VW</td>
                    <td>6= Descripcion</td>
                    <td>7= Volvo</td>
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
          <input class="form-control" data-inputmask="'mask': '0### ### ### #####'" id="codigo4" name="codigov" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '3##############'" id="codigo5" name="codigou" visible="false" autofocus>
          <input class="form-control" data-inputmask="'mask': '# ###################'" id="codigo7" name="codigodes" visible="false" autofocus>
          <br>

          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>


		

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
            	if ($("input").val() == 7){
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
            	if ($("input").val() == 4){
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
    document.title = 'Sistema / B_Stock';
</script> 
	<script src="js/bootstrap.min.js"></script> 
</html>