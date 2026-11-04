<?php
session_start();

include("db.php");

if((isset($_GET['ruta']))&&(!empty($_GET['ruta']))){
    $ruta =$_GET['ruta'];
}

if((isset($_GET['id']))&&(!empty($_GET['id']))){
    $id_emp =$_GET['id'];
	$sql = "DELETE FROM rh_empleados WHERE id_empleado='".$id_emp."'";

    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));


}else{
    //No Hace nada
}
echo $id;


header("location: planilla.php?msg=Eliminado");

