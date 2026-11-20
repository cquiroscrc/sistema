<?php
session_start();

$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Ingresar a sistema</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->

    <link href="css/signin.css" rel="stylesheet">
    
    
    <script src="js/ie-emulation-modes-warning.js"></script>

  </head>

  <body>

  
      <div class="container">

        <form class="form-signin" action="db_procesar.php" method="POST">
          <?php
          if(isset($_SESSION['nombre'])){
          echo "<h2> Bienvenido ". $_SESSION['nombre'] ."</h2>";
          
          }else {}

         

          ?>
          <h2 class="form-signin-heading">Ingreso al Sistema</h2>
          <label for="inputEmail" class="sr-only">Usuario</label>
          <input type="text" id="inputEmail" class="form-control" name="nombre" placeholder="Nombre" required autofocus>
          <label for="inputPassword" class="sr-only">Contraseña</label>
          <input type="password" id="inputPassword" class="form-control" name="contrasena" placeholder="Contraseña" required>
          <div class="checkbox">
            <!--
            <label>
              <input type="checkbox" value="remember-me"> Remember me
            </label>
          -->
          </div>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Ingresar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->
       
      </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>