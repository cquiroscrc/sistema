<?php
session_start();

include("db.php");


if((isset($_GET['id']))&&(!empty($_GET['id']))){
    $id_empleado =$_GET['id'];
	
}

if((isset($_GET['planilla']))&&(!empty($_GET['planilla']))){
    $planilla =$_GET['planilla'];
}

if((isset($_GET['fecha']))&&(!empty($_GET['id']))){
    $fecha =$_GET['fecha'];
    $sql = "DELETE FROM rh_planilla WHERE id_empleado='".$id_empleado."' AND id_planilla='".$planilla."' AND fecha='".$fecha."'";
}

if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

  header("location: planilla_planilla.php?fecha=".$fecha."&planilla=".$planilla);

?>
