<?php 

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

require_once("db.php");


if(isset($_REQUEST['mensaje']) && !empty($_REQUEST['mensaje'])){
    $mensaje = $_REQUEST['mensaje'];
}

if(isset($_REQUEST['asunto']) && !empty($_REQUEST['asunto'])){
    $asunto = $_REQUEST['asunto'];
}

$conteo=0;
$entro=0;
//$sql="SELECT NOMBRE,correo FROM clientes WHERE codigo BETWEEN 2151 AND 2200 AND comentario <>'**DESACTIVADO**' ";

$sql="SELECT NOMBRE,correo FROM clientes WHERE codigo BETWEEN 2476 AND 2500 AND comentario <>'**DESACTIVADO**' ";

if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

while($arr = odbc_fetch_array($res)){

	
  		$entro=1;
       	$nombre[$conteo]= $arr["NOMBRE"];
		$correo[$conteo] = $arr["correo"];
		//$correo[$conteo]="hhernandezv@lospaticas.com";
		$conteo++;
  	 
	
}	

$mail=serialize($correo);
$mail=urlencode($mail); 
$name=serialize($nombre);
$name=urlencode($name);
if ($entro==1) {
	
	header("location: http://lospaticas.com/mensaje.php?nombre=".$name."&correo=".$mail."&asunto=".$asunto."&mensaje=".$mensaje."");	
}else{
	header("location: index.php");	
	
}

  
?>