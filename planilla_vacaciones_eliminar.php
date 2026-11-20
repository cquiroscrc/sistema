<?php
session_start();

include("db.php");


if((isset($_GET['id']))&&(!empty($_GET['id']))){
    $id_empleado =$_GET['id'];
	
}
if((isset($_GET['fecha']))&&(!empty($_GET['fecha']))){
    $fecha =$_GET['fecha'];
	
}

$sql = "DELETE FROM vacaciones WHERE id_empleado='".$id_empleado."' AND fecha='".$fecha."'";

//echo $sql;
//exit;
if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

  header("location: planilla_vacaciones_reporte.php");
?>
