<?php 

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

require_once("db.php");


$mes=date("m");
$dia=date("d");

$conteo=0;
$entro=0;
$sql="SELECT NOMBRE,correo, nacimiento FROM clientes WHERE month(nacimiento)= '$mes' and day(nacimiento)='$dia' AND comentario <>'**DESACTIVADO**' ";
if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

while($arr = odbc_fetch_array($res)){

	if (!preg_match("^([a-zA-Z0-9._]+)@([a-zA-Z0-9.-]+).([a-zA-Z]{2,4})$",$arr["correo"])){ 
      //return FALSE; 
  	
  	} else { 
  		$entro=1;
       	$nombre[$conteo]= $arr["NOMBRE"];
		$correo[$conteo] = $arr["correo"];
		//$correo[$conteo]="hhernandezv@lospaticas.com";
		$conteo++;
  	} 
	
}	

$mail=serialize($correo);
$mail=urlencode($mail); 
$name=serialize($nombre);
$name=urlencode($name);
if ($entro==1) {
	header("location: http://lospaticas.com/cumple.php?nombre=".$name."&correo=".$mail."");	
}else{
	header("location: index.php");	
}

  
?>