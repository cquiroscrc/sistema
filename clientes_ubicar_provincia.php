i<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

include "menu.php";
include("db.php");
if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
      $cliente = $_REQUEST['datos'];
    }
?>

<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    
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
  
  
  <form class="form-signin" action="clientes_ubicar_canton.php" method="POST" name="F;">
    <center><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Ubicar Clientes Provincia</h1>
       <?php 
    $sqlC="SELECT * FROM clientes where codigo ='$cliente'";  
      
      if(($resultadoC = odbc_exec($id,$sqlC)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arrC = odbc_fetch_array($resultadoC)){
        
        echo '<h2>'.$arrC["nombre"].'</h2>';
      }

   ?>
      
      </center>  
  <br>

  <select class="form-control" id="provincia" name="provincia" onChange="habilitar(this.form)">
      <optgroup >
  <?php 
  
   $sql="SELECT DISTINCT P1, Provincia FROM UBICACIONES order by P1";  
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        echo "<option value='".$arr['P1']."'>".$arr['P1']." - ".$arr['Provincia']."</option>";
        
      }

   ?>
    </optgroup>
  </select>
  <?php 
    echo '<input type="hidden" id="cliente" class="form-control" name="cliente" required autofocus value="'.$cliente.'">';
   ?>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Seleccionar</button>

  </form>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Clientes U / Provincia';
</script>
<script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
