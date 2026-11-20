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
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signin.css">
    <link rel="icon" href="favicon.ico">
    
  </head>
<body>

<div align="center"><big style="font-weight: bold;"><big><big><big>   
  
  
  <form class="form-signin" action="ubicaciones_consultas.php" method="POST" name="F;">
  <table align="center" border="0" cellspacing="0">
    
      
  </table>
  <br>
  <label  class="glyphicon glyphicon-inbox" for="ubicacion"> Seleccione la Ubicacion</label>
  <select class="form-control" id="ubicacion" name="ubicacion" onChange="habilitar(this.form)">
      <optgroup >
  <?php 
   $sql="SELECT DISTINCT(ubicacion) FROM inventario order by ubicacion";  
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        echo "<option value='".$arr['ubicacion']."'>".$arr['ubicacion']."</option>";
        $conta++;
      }

   ?>
    </optgroup>
  </select>
  <br>
  <input type "text" class="form-control" id="txt_ubicacion" name="txt_ubicacion"  placeholder="Ubicacion" autofocus >
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
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ubicaciones de codigos';
</script> 
<script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
