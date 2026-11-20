<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
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
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	
	<link rel="icon" href="favicon.ico">

</head>
<body>	

	<?php
      
		
		  if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
		      $codigo = $_REQUEST['codigo'];
		      
		    } 
          if(isset($_REQUEST['s_pedido']) && !empty($_REQUEST['s_pedido'])){
		      $s_pedido = $_REQUEST['s_pedido'];
		      
		    } 
		    echo '<br>';
		    echo '<a href="gerencial_pedido_resultado.php?s_pedido='.$s_pedido.'&codigo='.$codigo.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
		   
		 
	          
	         echo '<form class="form-signin" action="gerencial_pedido_nuevo.php" method="POST">
	    	   	  <center><h1>Agregar codigos</h1></center>
      	  			<br>';
      	  			 ?>
	      	  			 <table WIDTH=400 border =1>
				        	<tr>
				                    
				                    <td>1= Volkswagen</td>
				                    <td>2= Daihatsu</td>
				                    <td>3= Empi</td>
				                    <td>4= Grupo Ns</td>
				                    <td>7= Volvo</td>
				                    <td>9= Accesorios</td>
				          </tr>
				        </table>
				        <br>
				        <label>Codigo</label>
				        <br>
				    	<input class="form-control" type="text" id="marca" name="marca" value=" " onkeyup="onChange()" autofocus/>
				        <input class="form-control" data-inputmask="'mask': '1### ### ### ######'" id="codigo1" name="codigovw" visible="false"  autofocus>
				        <input class="form-control" data-inputmask="'mask': '### ### #####'" id="codigof" name="codigovwf" visible="false"  autofocus>
				        <input class="form-control" data-inputmask="'mask': '1  N ### ### ###'" id="codigo6" name="codigovwn" visible="false"  autofocus>
				        <input class="form-control" data-inputmask="'mask': '2##### ##### #####'" id="codigo2" name="codigod" visible="false" autofocus>
				        <input class="form-control" data-inputmask="'mask': '###############'" id="codigo3" name="codigoa" visible="false" autofocus>
				        <input class="form-control" data-inputmask="'mask': '0### ### ### #####'" id="codigo4" name="codigov" visible="false" autofocus>
				        <input class="form-control" data-inputmask="'mask': '3##############'" id="codigo5" name="codigou" visible="false" autofocus>
				        <input class="form-control" data-inputmask="'mask': '###################'" id="codigo7" name="codigodes" visible="false" autofocus>
				
				    <?php 

      	  	echo'
          			
					<br>
					<label>Cantidad</label>
          			<input type="text" id="cantidad" class="form-control" name="cantidad" placeholder="Cantidad" required autofocus >
          			<input type="hidden" id="codigo" class="form-control" name="codigo" placeholder="codigo" required autofocus value="'.$codigo.'">
          			<input type="hidden" id="s_pedido" class="form-control" name="s_pedido" placeholder="s_pedido" required autofocus value="'.$s_pedido.'">
	          		<br>
          		  <button class="btn btn-lg btn-primary btn-block" type="submit">Salvar</button>
        		</form>';
	     ?>
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
	     <script src="js/bootstrap.min.js"></script>
  </body>
</html>