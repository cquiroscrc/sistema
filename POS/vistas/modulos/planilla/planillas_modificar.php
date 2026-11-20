<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
    $id_empleado = $_REQUEST['id'];
}
if(isset($_REQUEST['planilla']) && !empty($_REQUEST['planilla'])){
    $planilla = $_REQUEST['planilla'];
}
if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
    $fecha = $_REQUEST['fecha'];
}
$sql= "SELECT p.extras,p.salxhora, p.vales,p.comision,p.mandados,p.otros_ingresos,p.compras,p.otros_egresos,p.id_planilla, p.fecha,p.id_empleado,e.nombre,e.apellido 
FROM rh_planilla p, rh_empleados e where e.id_empleado = p.id_empleado AND p.id_planilla ='$planilla' and p.fecha='$fecha' AND p.id_empleado='$id_empleado'";


$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

echo utf8_decode(json_encode($ejecutar));
