<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "No ha seleccionado un pedido existente o creado pedido";
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
	<title>Agregar a pedido</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/anular.css">
	
	<link rel="icon" href="favicon.ico">

</head>
<body>	

	<?php
      
		
		  

        echo '<br>';
		   

		    require_once("db.php"); //SQLSERVER
		    

        echo '<form class="form-signin" action="gerencial_pedido.php" method="POST">
	    	<p>
      	  <center><h1>Crear pedido</h1></center>
      	  <select class="form-control" id="s_proveedor" name="s_proveedor">
	          <optgroup >
	          	<option value="">Seleccione el proveedor</option>';
	          	$sql_prove ="SELECT * FROM Proveedores WHERE tipo ='E'";
	          	
	          	if(($r_prove = odbc_exec($id,$sql_prove)) === false)
              		die("Error en Select: ". odbc_errormsg($id));

              	while($a_prove = odbc_fetch_array($r_prove)){
	            	$c_prove=$a_prove["codigo"];
	          		$n_prove=$a_prove["nombre"];

	          		echo '<option value="'.$c_prove.'">'.$n_prove.'</option>';

	          	}
	          echo '</optgroup>
          </select>
          <br>
          <input class="form-control"  id="pedido" name="pedido" placeholder="Descripcion del Pedido" autofocus>
          
          <center><h1>Seleccionar Pedido</h1></center>
          <br>
          <select class="form-control" id="s_pedido" name="s_pedido">
            <optgroup >
              <option value="">Seleccione el pedido</option>';

              $sql_pedido = "SELECT DISTINCT pedido FROM hacer_pedido";

                if(($r_pedido = odbc_exec($id,$sql_pedido)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
    
    
                while($row = odbc_fetch_array($r_pedido)){
                
                    $p_pedido=$row['pedido'];
                
                    echo '<option value="'.$p_pedido.'">'.$p_pedido.'</option>';

              
            }
            echo '</optgroup>

          </select>
          <br>
          <select style="display:none;" class="form-control" id="t_pedido" name="t_pedido">
            <optgroup >
              <option value="">Seleccione tipo</option>
              <option value="PEDIR">PEDIR</option>
              <option value="NO PEDIR">NO PEDIR</option>
              
          </select>
          <br>
          
          <button class="btn btn-lg btn-primary btn-block" type="submit">Aceptar</button>
          

        </form>';


     ?>

		<script>  //script de la mascara
			$(document).ready(function(){
			    $(":input").inputmask();
			});
		</script>

	
	<script src="js/bootstrap.min.js"></script> 
</html>