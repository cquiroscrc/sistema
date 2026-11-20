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
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];      
    } 

     require("db.php");
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cliente Nuevo</title>
	<script src="js/jquery.min.js"></script>

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	
	<link rel="icon" href="favicon.ico">
	
	

</head>
<body>	
		
       
    <input class="btn btn-lg btn-primary btn-block" type='button' id='btn' value='Cerrar' />
    <div class="container">
	    <form class="form-signin" action="factura_cliente_agregar.php" method="POST">
	    	<p>
      		<center>
      			<h2><span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span>&nbsp; Incluir cliente</h2></center>
      		<p>Codigo Cliente</p>
            <?php 
                // busqueda de consecutivo
                $sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='07'";       // Consecutivo factura
                
                if(($res_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
              
                while($arr_consecutivo = odbc_fetch_array($res_consecutivo)){
                  $concliente=$arr_consecutivo["numero"];
                  
                }
             ?>
      	   	<input type "text" class="form-control" id="carnet" name="carnet" required value="<?php echo $concliente; ?>" disabled>  	
	      	<br>
	      	<p>Nombre Cliente</p>
	      	<input type "text" class="form-control" id="nombre" name="nombre" required placeholder="Nombre" autofocus>  	
	      	<br>
          <p>Indentificacion</p>
          <?php 
           // leyenda de tipos de identificacion <p>Identificacion</p>
          if ($tipo=="Nfisico") {
              echo "<p>(9 digitos segidos X-XXX-XXX *** NO GUIONES NI ESPACIOS ***)</p>";
          }elseif ($tipo=="Njuridico") {
            echo "<p>(10 digitos segidos 3-101-XXXXXX *** NO GUIONES NI ESPACIOS ***)</p>";
          }elseif ($tipo=="Efisico") {
            echo "<p>(Entre 8 y 10 digitos segidos *** NO GUIONES NI ESPACIOS ***)</p>";
          }elseif ($tipo=="Dimex") {
            echo "<p>(12 digitos segidos *** NO GUIONES NI ESPACIOS ***)</p>";
          }
           ?>
	      	
          <input type="hidden" class="form-control" id="tipo" name="tipo" value="<?php echo $tipo;?>">
          <input type="hidden" class="form-control" id="carnet" name="carnet" value="<?php echo $concliente;?>">
          <input type="hidden" class="form-control" id="consecutivo" name="consecutivo" value="<?php echo $consecutivo;?>">
	      	<input type "text" class="form-control" id="cedula" name="cedula" required placeholder="Identificació" autofocus>
    		<br>
    		<p>Telefono fijo</p>
	      	<input type "text" class="form-control" id="telefono" name="telefono" required placeholder="Telefono" autofocus>
    		<br>
    		<p>Telefono celular</p>
    		<input type "text" class="form-control" id="celular" name="celular" required placeholder="Celular" autofocus>
    		<br>
    		<p>Correo</p>
    		<input type "text" class="form-control" id="correo" name="correo" required placeholder="Correo" autofocus>
    		<br>
    		<p>Direccion</p>
    		<input type "text" class="form-control" id="direccion" name="direccion" required placeholder="Dirección" autofocus>
    		<br>
    		<p>Fecha de nacimiento</p>
    		<input type "text" class="form-control" id="nacimiento" name="nacimiento" required placeholder="Nacimiento   dd-mm-yyyy" autofocus>
    		<br>
    		
  			<button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
  		</form>
</div>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Cliente Nuevo';
</script> 
<script type="text/javascript">
    $(document).ready(function () {
        $('#btn').click(function () {
            window.opener.location.reload(true);
            window.close();
        });
    });
</script>
