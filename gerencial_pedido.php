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
$s_proveedor='';
$pedido='';
if(isset($_REQUEST['s_proveedor']) && !empty($_REQUEST['s_proveedor'])){
  $s_proveedor = $_REQUEST['s_proveedor'];
} 

if(isset($_REQUEST['pedido']) && !empty($_REQUEST['pedido'])){
  $pedido = $_REQUEST['pedido'];
} 

$s_pedido='';
if(isset($_REQUEST['s_pedido']) && !empty($_REQUEST['s_pedido'])){
  $s_pedido = $_REQUEST['s_pedido'];
} 

if ($s_pedido=='') {
	$s_pedido=$pedido.'-'.$s_proveedor;
}

if ($s_pedido=='-') {
	$message = "No ha seleccionado un pedido existente o creado pedido";
	echo "<script type='text/javascript'>alert('$message');</script>";
	?>
	<script language="javascript">window.location="gerencial_pedido_agregar.php"</script>;
	<?php 
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
		

	    <form class="form-signin" action="gerencial_pedido_resultado.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta de codigos</h1>
      	  	<h2><?php echo $s_pedido ?></h2>
        
        	<?php 
			//// *************** Incluye la consulta ******************
			include "consulta_form.php";
			?>
 	       <?php 
	    		echo '<input type="hidden" id="s_pedido"  name="s_pedido" required autofocus value="'.$s_pedido.'">' ;  
	    	 ?>	  
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>


		

        <?php 
		include	"consulta.php";
		//// *************** Incluye la consulta ******************
		 ?>

	<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pedidos C';
</script>
	<script src="js/bootstrap.min.js"></script> 
</html>