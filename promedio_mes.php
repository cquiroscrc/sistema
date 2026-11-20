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
function ultimo_mes() { 
      $month = date('m');
      $year = date('Y');
      $day = date("d", mktime(0,0,0, $month+1, 0, $year));
 
      return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
      };
 
  $fin=ultimo_mes();
?>

<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    <title>Reporte de promedio</title>
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
  
  
  <form class="form-signin" action="consulta_mes.php" method="post" name="F;">
  <table align="center" border="0" cellspacing="0">
    <center><h1><span class="glyphicon glyphicon-usd" aria-hidden="true"></span>&nbsp;Promedio de Ventas</h1>
      <h2> Mensuales</h2></center>
    

    <tbody>
      <tr>
      <td >
        <script>
        DrawCalFld('Date1', 'today');

        </script>
       
      </td>
      
      <td >
        <script>
        
         
          var date = new Date();
          var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);
          
          var mm = ultimoDia.getMonth() + 1;
          var dd = ultimoDia.getDate();
          var yyyy = ultimoDia.getFullYear();
          Cal_ImgDir='images/'; var DT=Cal_Today(); Cal_Delim='/'; Cal_MD=1;
          //DrawCalFld('Date2', Cal_DTFmt(Cal_GetMonthLen(DT[2]), DT[1], DT[2])); // 01.02.2003 today , '01.02.2003'
          DrawCalFld('Date2', Cal_DTFmt(dd, mm, yyyy));
         
          
        </script>
      </td>
      </tr>

          
    </tbody>
  </table>
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
