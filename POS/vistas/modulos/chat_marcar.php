<?php
require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
    $idmensaje = $_REQUEST['id'];
}
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
    $tipo = $_REQUEST['tipo'];
}

if ($tipo=='Enviado') {

}else{
    $sql_update="UPDATE Mensajes SET Estado='L' WHERE Id=$idmensaje";
    $ejecutar=ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);
    echo $sql_update;
}


