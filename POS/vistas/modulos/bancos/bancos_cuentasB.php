<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['cuenta']) && !empty($_REQUEST['cuenta'])){
    $cuenta = $_REQUEST['cuenta'];
} 

$sql="SELECT * FROM tbl_Cuentas WHERE Cuenta='$cuenta'";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

echo json_encode($ejecutar);





