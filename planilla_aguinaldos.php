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
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.inputmask.bundle.min.js"></script>
    <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signin.css">
    <link rel="StyleSheet" href="css/calendar.css">
    <link rel="icon" href="img/favicon.ico">
    

  

  </head>
<body>

<div align="center"><big style="font-weight: bold;"><big><big><big>   
  
  
  <form class="form-signin" action="planilla_aguinaldos_resultado.php" method="POST" name="F;">

    <center><h1><span class="glyphicon glyphicon-gift" aria-hidden="true"></span>&nbsp;Aguinaldos</h1>
      </center>
    

  

  <select class="form-control" id="datos" name="datos" onChange="habilitar(this.form)">
      <optgroup >
      <?php 
        
        $sql = "SELECT DISTINCT YEAR(fecha) as ano FROM rh_planilla order by ano asc";

               
              if(($r = odbc_exec($id,$sql)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
              
              
              while($row = odbc_fetch_array($r)){
                  $ano=$row['ano'];
                  echo '<option value="'.$ano.'">'.$ano.'</option>';
                }
               
       ?>  
    </optgroup>
  </select>
  <br>
  <select class="form-control" id="empleados" name="empleados" onChange="habilitar(this.form)">
      <optgroup >
      <?php 
        
        $sql = "SELECT * FROM rh_empleados where estado ='a'";

                if(($r = odbc_exec($id,$sql)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
                
                echo '<option value="todos">Todos los empleados</option>';          
                while($row = odbc_fetch_array($r)){
                  echo '<option value="'.$row["id_empleado"].'">'.$row["nombre"].' '.$row["apellido"].'</option>';
                }
              
       ?>  
    </optgroup>
  </select>
  <br>
  <select class="form-control" id="tipo" name="tipo" onChange="habilitar(this.form)">
      <optgroup >
        <option value="parcial">Parcial</option>
        <option value="total">Total</option>
    </optgroup>
  </select>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>

  </form>
 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Aguinaldos';
</script> 
  <script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
