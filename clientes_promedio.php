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
    <title>Cambio de Promedio</title>
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
  
  
  <form class="form-signin" action="clientes_promedio_resultado.php" method="POST" name="F;">
  <table align="center" border="0" cellspacing="0">
    
    <tbody>
      <tr>
      
      
      <td >
        
        <center>
        Meses a estudiar
        </center>
        <select class="form-control" id="estudio" name="estudio">
            <optgroup >
              <option value="1">Enero</option>';                
              <option value="2">Febrero</option>';                
              <option value="3">Marzo</option>';                
              <option value="4">Abril</option>';                
              <option value="5">Mayo</option>';                
              <option value="6">Junio</option>';                
              <option value="7">Julio</option>';                
              <option value="8">Agosto</option>';                
              <option value="9">Septiembre</option>';                
              <option value="10">Octubre</option>';                
              <option value="11">Noviembre</option>';                
              <option value="12">Diciembre</option>';                
            </optgroup>
          </select>
      </td>

      </tr>
    </tbody>
  </table>
   <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Estudiar</button>

  </form>


<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Meses Clientes';
</script>
<script src="js/jquery.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
</BODY>
</HTML>
