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
if(isset($_REQUEST['barrio']) && !empty($_REQUEST['barrio'])){
      $barrio = $_REQUEST['barrio'];
    } 
if(isset($_REQUEST['otro']) && !empty($_REQUEST['otro'])){
      $otro = $_REQUEST['otro'];
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
  
  
  <form class="form-signin" action="clientes_ubicar_update.php" method="POST" name="F;">
    <center><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Ubicar Clientes Revisar</h1>
       <?php 
    $sqlC="SELECT * FROM clientes where codigo ='$cliente'";  
      
      if(($resultadoC = odbc_exec($id,$sqlC)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arrC = odbc_fetch_array($resultadoC)){
        
        echo '<h2>'.$arrC["nombre"].'</h2>';
        echo '<h2>'.$arrC["cedula"].'</h2>';
      }

   ?>

      
      </center>  
  <br>
  
  <?php
    /// poner nombres ****
      $sqlP="SELECT * FROM UBICACIONES where P1 ='$provincia'";     
      if(($resultadoP = odbc_exec($id,$sqlP)) === false)
              die("Error en Select: ". odbc_errormsg($id));
        while($arrP = odbc_fetch_array($resultadoP)){
          $provincia_nombre= $arrP["Provincia"];
        }
      $sqlCan="SELECT * FROM UBICACIONES where C1 ='$canton' and P1=$provincia";     
      if(($resultadoCan = odbc_exec($id,$sqlCan)) === false)
              die("Error en Select: ". odbc_errormsg($id));
        while($arrCan = odbc_fetch_array($resultadoCan)){
          $canton_nombre= $arrCan["Canton"];
        }
      $sqlD="SELECT * FROM UBICACIONES where D1 ='$distrito' and C1='$canton' and P1='$provincia'";     
      if(($resultadoD = odbc_exec($id,$sqlD)) === false)
              die("Error en Select: ". odbc_errormsg($id));
        while($arrD = odbc_fetch_array($resultadoD)){
          $distrito_nombre= $arrD["Distrito"];
        }
      $sqlB="SELECT * FROM UBICACIONES where B1 ='$barrio' and D1='$distrito' and C1='$canton' and P1='$provincia'";     
      if(($resultadoB = odbc_exec($id,$sqlB)) === false)
              die("Error en Select: ". odbc_errormsg($id));
        while($arrB = odbc_fetch_array($resultadoB)){
          $barrio_nombre= $arrB["Barrio"];
        }    

    echo '<input type="text" id="provincia" class="form-control" name="provincia" required autofocus value="'.$provincia_nombre.'"disabled>';
    echo '<input type="text" id="canton" class="form-control" name="canton" required autofocus value="'.$canton_nombre.'"disabled>';
    echo '<input type="text" id="distrito" class="form-control" name="distrito" required autofocus value="'.$distrito_nombre.'"disabled>';
    echo '<input type="text" id="barrio" class="form-control" name="barrio" required autofocus value="'.$barrio_nombre.'"disabled>';
    echo '<input type="text" id="otro" class="form-control" name="otro" required autofocus value="'.$otro.'"disabled>';
    ////  CODIGOS *****
    echo '<input type="hidden" id="cliente" class="form-control" name="cliente" required autofocus value="'.$cliente.'">';
    echo '<input type="hidden" id="provincia" class="form-control" name="provincia" required autofocus value="'.$provincia.'">';
    echo '<input type="hidden" id="canton" class="form-control" name="canton" required autofocus value="'.$canton.'">';
    echo '<input type="hidden" id="distrito" class="form-control" name="distrito" required autofocus value="'.$distrito.'">';
    echo '<input type="hidden" id="barrio" class="form-control" name="barrio" required autofocus value="'.$barrio.'">';
    echo '<input type="hidden" id="otro" class="form-control" name="otro" required autofocus value="'.$otro.'">';
   ?>
   

  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Aplicar</button>

  </form>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Clientes U /Revisar';
</script>
<script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
