<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
include "menu.php";
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Anular Factura</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	<link rel="icon" href="img/favicon.ico">
</head>

<body>


<center>	
	<table border = '1'>

<tr>
  <?php 
    $ip_premio= $_SERVER['REMOTE_ADDR'];
  
  $high_ip = ip2long ('172.16.12.255'); 
  $low_ip = ip2long ('172.16.12.1'); 
  $ip =ip2long ($ip_premio); 
  if ($ip <= $high_ip && $low_ip <= $ip) 
    {
      $esta="SI";
  }else {
    $esta="NO";
  }
  
/*
    if ($ip_premio=="170.81.32.82") {
        ?>
        <td><img width='500'  height='500' src= 'http://172.16.12.84:84/videostream.cgi'></td>
        <td><img width='500'  height='500' src= 'http://172.16.12.85:85/videostream.cgi'></td>    
        <?php  
    }else{
        if ($ip_premio=="201.195.22.182") {
            ?>
            <td><img width='500'  height='500' src= 'http://172.16.12.84:84/videostream.cgi'></td>
            <td><img width='500'  height='500' src= 'http://172.16.12.85:85/videostream.cgi'></td>    
            <?php 
        }else{
             ?>
              <td><img width='500'  height='500' src= 'http://paticas.no-ip.org:84/videostream.cgi'></td>
              <td><img width='500'  height='500' src= 'http://paticas.no-ip.org:85/videostream.cgi'></td>   
             <?php  
        }
        
    }
    
 */ 
    if ($esta=="SI") {
      ?>
        <td><img width='500'  height='500' src= 'http://172.16.12.84:84/videostream.cgi'></td>
        <td><img width='500'  height='500' src= 'http://172.16.12.85:85/videostream.cgi'></td>    
        <?php 
    }else{
      ?>
        <td><img width='500'  height='500' src= 'http://paticas.no-ip.org:84/videostream.cgi'></td>
        <td><img width='500'  height='500' src= 'http://paticas.no-ip.org:85/videostream.cgi'></td>   
       <?php  
    }

  echo $esta ." ".$ip_premio;


  ?>
  
</tr>
</table>
</center>

<script src="js/jquery.js"></script>
<script type="text/javascript" language="JavaScript">
      document.title = 'Sistema / Camaras';
  </script>
<script src="js/bootstrap.min.js"></script>
