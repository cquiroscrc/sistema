<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];
    }
    if(isset($_REQUEST['planilla']) && !empty($_REQUEST['planilla'])){
          $id_planilla = $_REQUEST['planilla'];
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

    $sql_prin="SELECT * FROM rh_empleados WHERE estado='a'";
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_prin);

    foreach ($ejecutar as $key => $arr) {
        $base= $arr["base"]/2;
        $reajuste=$arr["reajuste"]/2;
        $id_empleado=$arr["id_empleado"];
        $salxhora=($base+$reajuste)/98;
        $salxhora=round($salxhora, 2);
        $totalextras=0;
        $extras=0;
        $comision=0;
        $mandados=0;
        $oingresos=0;
        $vales=0;
        $compras=0;
        $oegresos=0;
        $retencion=0;

        $ingresos= $base+$totalextras+$comision+$mandados;  //Ingresos
        $ccss=floatval($ingresos) * floatval($valccss);  //CCSS
        $egresos=$vales+$compras+$oegresos+$ccss+$retencion;
        $total=$ingresos+$reajuste+$oingresos-$egresos;

        $sql="INSERT INTO rh_planilla (id_planilla,fecha,id_empleado,base,reajuste,salxhora,extras,vales,compras,otros_egresos,comision,mandados,ccss,otros_ingresos,total,retencion)
        values ('$id_planilla','$fecha','$id_empleado','$base','$reajuste','$salxhora','$extras','$vales','$compras','$oegresos','$comision','$mandados','$ccss','$oingresos','$total','$retencion')";
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    }

  echo "exito";
