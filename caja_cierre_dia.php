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
<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    <title>Cierre diario</title>
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.inputmask.bundle.min.js"></script>
    <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signin.css">
    <link rel="StyleSheet" href="css/calendar.css">
    <link rel="icon" href="favicon.ico">
    
    <style>
      .CalFld{
      	background:#FFFFD8;
        padding:1px;
      	margin:auto;
      }
    </style>

  </head>
<body>








<div align="center"><big style="font-weight: bold;"><big><big><big>   
  
  
  <form class="form-signin" action="caja_resultado.php" method="POST" name="F;">
  <table align="center" border="0" cellspacing="0">
    <h2><span class="glyphicon glyphicon-usd" aria-hidden="true"></span>&nbsp;Egresos de caja</h2>
    <label>Seleccione la fecha</label>
    
    <tbody>
      <br>
      <tr>
      
      
      <td >
        <script>
          DrawCalFld('Date2', 'today');
        </script>
      </td>

      </tr>
    </tbody>
  </table>
  
   <input type="hidden" name="tipo" value="dia">
   <br>
   
  
  <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>

  </form>

   




	 

<script>
W.F=D.F;

function Date1OnChange(){
if (F.Date2) Cal_SetDate(F.Date2, this.value);
}

F.Date1.onchange=Date1OnChange;
</script>
<script src="js/bootstrap.min.js"></script>
</BODY>
</HTML>
