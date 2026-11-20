<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

$punto=$_SESSION["ip"];
$usuario = $_SESSION["usuario"];
$fecha = date("d/m/Y h:i:sa");
// ESTO es el contador, se puso en 1 el 28 de mayo 2020
$fp = fopen(__DIR__ ."/contador.hhv", "a");
fwrite($fp, $punto.' - '.$usuario.' - '.$fecha . PHP_EOL);
fclose($fp);

?>

<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    
 <style type="text/css">
   
   iframe {
    
    
    width:87%; 
  }
 </style>

  </head>
<body>
 
<!-- <center>  

<iframe style ="background-color: white;" height="640" src="<?php print ("https://www.partslink24.com/partslink24/user/login.do"); ?>"></iframe>
</center> -->
<script> 
<!--
window.location.replace('https://www.partslink24.com/partslink24/user/login.do'); 
//-->
</script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / PartLink24';
</script>
<script src="js/bootstrap.min.js"></script>
</BODY>
</HTML>
