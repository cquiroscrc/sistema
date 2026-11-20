<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

include "menu.php";
include("db.php");
if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];
    }
if(isset($_REQUEST['provincia']) && !empty($_REQUEST['provincia'])){
      $provincia = $_REQUEST['provincia'];
    }    
if(isset($_REQUEST['canton']) && !empty($_REQUEST['canton'])){
      $canton = $_REQUEST['canton'];
    }
if(isset($_REQUEST['distrito']) && !empty($_REQUEST['distrito'])){
      $distrito = $_REQUEST['distrito'];
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
  
  
  <form class="form-signin" action="clientes_ubicar_otros.php" method="POST" name="F;">
    <center><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Ubicar Clientes Barrio</h1>
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

  <select class="form-control" id="barrio" name="barrio" onChange="habilitar(this.form)">
      <optgroup >
  <?php 
  
   $sql="SELECT DISTINCT B1, Barrio FROM UBICACIONES where P1 = '$provincia' and C1='$canton' and D1='$distrito' and B1 <> 0 order by B1";  
   
     echo $sql;
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        echo "<option value='".$arr['B1']."'>".$arr['B1']." - ".$arr['Barrio']."</option>";
        
      }

   ?>
    </optgroup>
  </select>
  <?php 
    echo '<input type="hidden" id="cliente" class="form-control" name="cliente" required autofocus value="'.$cliente.'">';
    echo '<input type="hidden" id="provincia" class="form-control" name="provincia" required autofocus value="'.$provincia.'">';
    echo '<input type="hidden" id="canton" class="form-control" name="canton" required autofocus value="'.$canton.'">';
    echo '<input type="hidden" id="distrito" class="form-control" name="distrito" required autofocus value="'.$distrito.'">';
   ?>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Seleccionar</button>

  </form>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Clientes U / Barrio';
</script>
<script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
