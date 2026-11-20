<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
     date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

     if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 

     require("db.php");
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cliente Tipo</title>
	<script src="js/jquery.min.js"></script>

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	
	<link rel="icon" href="favicon.ico">
	
	

</head>
<body>	
		
       
    <input class="btn btn-lg btn-primary btn-block" type='button' id='btn' value='Cerrar' />
    <div class="container">
	    <form class="form-signin" action="factura_cliente_ingreso.php" method="POST">
	    	<p>
      		<center>
      			<h2><span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span>&nbsp; Seleccionar tipo de cliente</h2></center>
                <select class="form-control" id="tipo" name="tipo">
                    <optgroup >
                      <option value="Nfisico">Nacional Fisico</option>
                      <option value="Njuridico">Nacional Juridico</option>
                      <option value="Efisico">Extrangero </option>
                      <option value="Dimex">Dimex</option>
                    </optgroup>
                </select>
      		
    		<br>
  			<button class="btn btn-lg btn-primary btn-block" type="submit">Seleccionar</button>
  		</form>
</div>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Tipo Cliente';
</script> 
<script type="text/javascript">
    $(document).ready(function () {
        $('#btn').click(function () {
            window.opener.location.reload(true);
            window.close();
        });
    });
</script>
