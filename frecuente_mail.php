<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
require_once("db.php");
//require 'PHPMailerAutoload.php';
$mes=date("m");
$dia=date("d");
$sql="SELECT NOMBRE,correo, nacimiento FROM clientes WHERE month(nacimiento)= '$mes' and day(nacimiento)='$dia' ";

if(($prom2 = odbc_exec($id,$sql)) === false)
  die("Error en Select: ". odbc_errormsg($id));
while($arr5 = odbc_fetch_array($prom2)){
  $nombre=$arr5["NOMBRE"];
  $correo=$arr5["correo"];
  $nacimiento=$arr5["nacimiento"];
}
//header("location: www.lospaticas.com");
 header("location: index.php");

?>