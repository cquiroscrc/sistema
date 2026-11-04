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
  
  
  <form class="form-signin" action="consultas_minimos.php" method="POST" name="F;">

    <center><h1><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&nbsp;Reporte Stock Minimo</h1>
      </center>
    

  

  <select class="form-control" id="datos" name="datos" onChange="habilitar(this.form)">
      <optgroup >
      <option value='1'>Volkswagen</option>
      <option value='2'>Daihatsu</option>
      <option value='4'>Volvo</option>
      <option value='9'>Accesorios</option>
      <option value='3'>Usados</option>
      <option value='5'>Todos</option>
    </optgroup>
  </select>
 
  <p>Stock Minimo</p>
  <select class="form-control" id="minimo" name="minimo" onChange="habilitar(this.form)">
      <optgroup >
      <option value='1'>1 Mes</option>
      <option value='2'>2 Meses</option>
      <option value='3'>3 Meses</option>
      <option value='4'>4 Meses</option>
      <option value='5'>5 Meses</option>
      <option value='6'>6 Meses</option>
    </optgroup>
  </select>
 
  <p>Stock Maximo</p>
  <select class="form-control" id="maximo" name="maximo" onChange="habilitar(this.form)">
      <optgroup >
      <option value='1'>1 Mes</option>
      <option value='2'>2 Meses</option>
      <option value='3'>3 Meses</option>
      <option value='4'>4 Meses</option>
      <option value='5'>5 Meses</option>
      <option value='6'>6 Meses</option>
      <option value='7'>7 Meses</option>
      <option value='8'>8 Meses</option>
      <option value='9'>9 Meses</option>
      <option value='10'>10 Meses</option>
      <option value='11'>11 Meses</option>
      <option value='12'>1 Año</option>
    </optgroup>
  </select>
 
  <p>Promedio mensual</p>
  <select class="form-control" id="mensual" name="mensual" onChange="habilitar(this.form)">
      <optgroup >
      <option value='2'>2 x mes</option>
      <option value='1.5'>1.5 x mes</option>
      <option value='1.25'>1.25 x mes</option>
      <option value='1'>1 x mes</option>
      <option value='0.5'>0,5 x mes</option>
      <option value='0.25'>0,25 x mes</option>
    </optgroup>
  </select>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>

  </form>
  <script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
