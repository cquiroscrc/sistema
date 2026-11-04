<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

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
  
  
  <form class="form-signin" action="clientes_activar_resultados.php" method="POST" name="F;">
    <center><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Desactivar Clientes</h1>
      </center>  
  <br>

  <select class="form-control" id="datos" name="datos" onChange="habilitar(this.form)">
      <optgroup >
  <?php 
  $totalclientes=0;
   $sql="SELECT * FROM clientes ";  
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        echo "<option value='".$arr['codigo']."'>".$arr['codigo']." - ".$arr['nombre']."</option>";
        $totalclientes++;
      }

   ?>
    </optgroup>
  </select>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>

  </form>

<?php 
  $desactivados=0;

  $sql2="SELECT * FROM clientes where comentario ='**DESACTIVADO**'";  
      
      if(($resultado2 = odbc_exec($id,$sql2)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr2 = odbc_fetch_array($resultado2)){
        $desactivados++;
      }

echo 'Total clientes '.$totalclientes.' Desactivados '.$desactivados;
 ?>



<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Clientes Activar';
</script>
<script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
