<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
require_once("db.php");
//require 'PHPMailerAutoload.php';
$mes=date("m");
$dia=date("d");
$conteo=0;
$sql="SELECT NOMBRE,correo, nacimiento FROM clientes WHERE month(nacimiento)= '$mes' and day(nacimiento)='$dia' ";
$entro= 0;
if(($prom2 = odbc_exec($id,$sql)) === false)
  die("Error en Select: ". odbc_errormsg($id));
while($arr5 = odbc_fetch_array($prom2)){
  $entro=1;	
  $nombre[$conteo]=$arr5["NOMBRE"];
  $correo[$conteo]=$arr5["correo"];
  $nacimiento[$conteo]=$arr5["nacimiento"];
  $conteo=$conteo+1;
}

$mail=serialize($correo);
$mail=urlencode($mail); 
$name=serialize($nombre);
$name=urlencode($name); 

if ($entro==0) {
	echo "entro 0";
	echo $nombre[$conteo];
	echo "<br>";
	echo $correo[$conteo];
	//header("location: http://www.lospaticas.com/mail/Enviado.html");	
}else{
	echo "entro 1";
	print_r($nombre);
	echo "<br>";
	echo $correo[$conteo];
	//header("location: http://www.lospaticas.com/mail/smtp.php?correo=".$mail."&nombre=".$name."");	
}
//header("location: www.lospaticas.com");


?>