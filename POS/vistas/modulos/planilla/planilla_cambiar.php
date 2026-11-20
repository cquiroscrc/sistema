<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    $extras=0;
    $comision=0;
    $mandados=0;
    $oingresos=0;
    $vales=0;
    $compras=0;
    $oegresos=0;
    $hijos=0;
    $conyuge=0;
    $base20=0;

    if(isset($_REQUEST['id_empleado']) && !empty($_REQUEST['id_empleado'])){
        $id_empleado = $_REQUEST['id_empleado'];
    }
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];
    }
    if(isset($_REQUEST['id_planilla']) && !empty($_REQUEST['id_planilla'])){
          $id_planilla = $_REQUEST['id_planilla'];
    }
    if(isset($_REQUEST['extras']) && !empty($_REQUEST['extras'])){
        $extras = $_REQUEST['extras'];
    }
    if(isset($_REQUEST['comision']) && !empty($_REQUEST['comision'])){
        $comision = $_REQUEST['comision'];
//        Recalcula la comision para lo que es CCSS  / Planilla.php myModificar  **** Aqui quitar esta formula
        $comision= $comision*1.1175;
    }
    if(isset($_REQUEST['mandados']) && !empty($_REQUEST['mandados'])){
        $mandados = $_REQUEST['mandados'];
    }
    if(isset($_REQUEST['oingresos']) && !empty($_REQUEST['oingresos'])){
        $oingresos = $_REQUEST['oingresos'];
    }
    if(isset($_REQUEST['vales']) && !empty($_REQUEST['vales'])){
        $vales = $_REQUEST['vales'];
    }
    if(isset($_REQUEST['compras']) && !empty($_REQUEST['compras'])){
        $compras = $_REQUEST['compras'];
    }
    if(isset($_REQUEST['oegresos']) && !empty($_REQUEST['oegresos'])){
        $oegresos = $_REQUEST['oegresos'];
    }
    $sql="SELECT * FROM tbl_Utiles WHERE IDUtilidad='12'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
        $valccss=$arr["Valor"];
        $valccss= floatval($valccss) / 100;
    }

    $sql="SELECT * FROM tbl_Utiles WHERE IDUtilidad='14'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
        $base10=$arr["Valor"];
    }
    $sql="SELECT * FROM tbl_Utiles WHERE IDUtilidad='15'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
        $base15=$arr["Valor"];
    }
    $sql="SELECT * FROM tbl_Utiles WHERE IDUtilidad='16'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
        $base20=$arr["Valor"];
    }
    $sql="SELECT * FROM tbl_Utiles WHERE IDUtilidad='17'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
        $conyuge=$arr["Valor"];
    }
    $sql="SELECT * FROM tbl_Utiles WHERE IDUtilidad='18'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
        $hijos=$arr["Valor"];
    }

    $sql="SELECT * FROM rh_empleados WHERE id_empleado='$id_empleado'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
        $base= $arr["base"]/2;
        $reajuste=$arr["reajuste"]/2;
    }

    $salxhora=($base+$reajuste)/98;
    $salxhora=round($salxhora, 2);
    $totalextras=$extras*$salxhora*1.5; //Extras

    $ingresos= $base+$totalextras+$comision+$mandados;  //Ingresos

    $ccss=floatval($ingresos) * floatval($valccss);  //CCSS

    $retencion=0;
    $egresos=$vales+$compras+$oegresos+$ccss+$retencion;
    $total=$ingresos+$reajuste+$oingresos-$egresos;

  $sql = "UPDATE rh_planilla set  base='$base',reajuste='$reajuste',extras='$totalextras',salxhora='$salxhora',comision='$comision',mandados='$mandados',
    otros_ingresos='$oingresos',vales='$vales',compras='$compras',otros_egresos='$oegresos',ccss='$ccss' ,retencion='$retencion', total='$total'  
    WHERE id_empleado='$id_empleado' AND id_planilla='$id_planilla' AND fecha='$fecha'";

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  echo "exito";
