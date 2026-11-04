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
    
    if((isset($_GET['no']))&&(!(empty($_GET['no'])))){
        $message = "No existe este reporte para estos datos";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
include "menu.php";
include("db.php");
?>

<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    <title>Reporte de Metas</title>
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.inputmask.bundle.min.js"></script>
    <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signin.css">
    <link rel="StyleSheet" href="css/calendar.css">
    <link rel="icon" href="favicon.ico">
    

  

  </head>
<body>

<div align="center"><big style="font-weight: bold;"><big><big><big>   
  
  
  <form class="form-signin" action="rep_articulos_resultados.php" method="POST" name="F;">

    <center><h1><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&nbsp;Reporte Articulos</h1>
      </center>
    

  <br>

  <select class="form-control" id="datos" name="datos" onChange="habilitar(this.form)">

    
      <optgroup >
      <?php             
        include("db.php");
        $sql = "SELECT * FROM Marcas";

        if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

        while($arr = odbc_fetch_array($res)){
            $descripcion=$arr['descripcion'];
            $marca=$arr['codigo'];
            echo '<option value="'.$marca.'">'.$descripcion.'</option>'; 
        }
        echo '<option value="TT">Todas las marcas</option>';
      echo '</optgroup>'
    ?>
    </select>
    
  <?php 
    if ($acceso<>'ventas') {
   ?>
  <br>
  <select class="form-control" id="tipos" name="tipos" onChange="habilitar(this.form)">
      <optgroup >
      <option value='ventas'>Venta</option>
      <option value='costos'>Costos</option>
      <option value='ubicaciones'>Ubicaciones</option>
      <option value='negativos'>Negativos</option>
    </optgroup>
  </select>
  <?php 
  }
  else{
   ?>
   <br>
    <select class="form-control" id="tipos" name="tipos" onChange="habilitar(this.form)">
      <optgroup >
      <option value='ventas'>Venta</option>
    </optgroup>
    </select>
  <?php 
  }
   ?>
  
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>

  </form>
  <script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
