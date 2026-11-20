<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");



$sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);

for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {
    $FacturaActual=$respuesta[$i]["numero"];
    $FacturaActual=str_pad($FacturaActual, 14, "0", STR_PAD_LEFT);
    $FacturaActual='F-'.$FacturaActual;
    $FacturaActual=trim($FacturaActual);

}

$fechas="SELECT * FROM fechas";
$fecha = ControladorUtilitaria::ctrMostrarUtilitariaVar($fechas);
foreach ($fecha as $key => $value) {
    $fechaActual=$value["Fecha"];
}
$fechaActual=strtotime($fechaActual);
$fechaReportes=date("m/d/Y", $fechaActual);
$fechaActual = date("d/m/Y", $fechaActual);
//Mensajes
$UserActual=$_SESSION["usuario"];
$sql_mensajes="SELECT * FROM Mensajes WHERE Recibe='$UserActual' AND ESTADO ='SL'";
$men=0;
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_mensajes);
$html='';
foreach ($ejecutar as $key => $value) {
    $enviaMen=$value["Envia"];
    $asuntoMen=$value["Asunto"];
    $fechaMen=$value["Fecha"];

    $sql_perfiles="SELECT * FROM acc_usuarios WHERE usuario='$enviaMen'";
    $ejecutar2 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_perfiles);
    foreach ($ejecutar2 as $key => $value2) {
        $nombreMen=$value2["nombre"];
        $fotoMen=$value2["foto"];
    }
    if($fotoMen != ""){

    }else{
        $fotoMen="vistas/img/usuarios/default/anonymous.png";
    }
     $html= $html.'<div class="media">
                        <img src="'.$fotoMen.'" style="width:32px;height:32px;" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="small-body">
                          <h3 id="mensajesEnvia" class="dropdown-item-title">
                          '.$nombreMen.'
                        </h3>
                        <p id="mensajesAsunto" class="text-sm">'.$asuntoMen.' <span class="float-right"><i class="fa fa-star text-yellow"></i></span></p>
                        <p class="text-sm text-muted"><i class="fa fa-bell"></i>'.$fechaMen.'></p>
                        </div></div>';
    $men=$men+1;

}
$array= array( "mensaje" => $men,
               "fecha" => $fechaReportes,
               "factura" =>$FacturaActual,
               "fecha_actual" =>$fechaActual,
                "html" => $html);

echo json_encode($array);
?>


