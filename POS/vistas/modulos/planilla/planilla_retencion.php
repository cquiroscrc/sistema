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

    if(isset($_REQUEST['id_empleado']) && !empty($_REQUEST['id_empleado'])){
        $id_empleado = $_REQUEST['id_empleado'];
    }
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
        $fecha = $_REQUEST['fecha'];
        $mes = date("m", strtotime($fecha));
        $ano = date("Y", strtotime($fecha));
    }
    if(isset($_REQUEST['id_planilla']) && !empty($_REQUEST['id_planilla'])){
          $id_planilla = $_REQUEST['id_planilla'];
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

    $sql="SELECT * FROM rh_planilla WHERE id_empleado='$id_empleado' AND  year(fecha)=$ano and month(fecha)=$mes";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

    $base=0;
    $extras=0;
    $comision=0;
    $mandados=0;
    $ccss=0;
    $oingresos=0;

    foreach ($respuesta as $key => $arr) {
        $base= $base + $arr["base"];
        $extras= $extras + $arr["extras"];
        $comision= $comision + $arr["comision"];
        $mandados= $mandados + $arr["mandados"];
        $oingresos=$oingresos + $arr["otros_ingresos"];

    }



    $ingresos= $base+$extras+$comision+$mandados+$oingresos;  //Ingresos

    $calculo10=0;
    $calculo15=0;
    $calculo20=0;

    if ($ingresos>$base20) {

        $calculo10= floatval($base15)-floatval($base10);
        $calculo10= $calculo10*0.10;

        $calculo15= floatval($base20)-floatval($base15);
        $calculo15= $calculo15*0.15;

        $calculo20=floatval($ingresos)-floatval($base20);
        $calculo20=$calculo20*0.20;

    }else{
        if ($ingresos>$base15) {

            $calculo10= floatval($base15)-floatval($base10);
            $calculo10= $calculo10*0.10;

            $calculo15= floatval($ingresos)-floatval($base15);
            $calculo15= $calculo15*0.15;

        }else{
            if ($ingresos>$base10) {
                $calculo10=floatval($ingresos)-floatval($base10);
                $calculo10=$calculo10*0.10;
            }
        }
    }
//echo 'Calculo 10=>'.$calculo10;
//echo 'Calculo 15=>'.$calculo15;
//echo 'Calculo 20=>'.$calculo20;
//echo '<=>';

$retencion=floatval($calculo10)+floatval($calculo20)+floatval($calculo15);

if ($retencion==0) {

}else{
    //$retencion=$retencion-floatval($hijos)-floatval($conyuge); //Roberto
    //$retencion=$retencion-floatval($conyuge);// Lico
    $retencion=$retencion-floatval($hijos)-floatval($hijos); //Hernan
}

//echo $retencion;

$sql="SELECT * FROM rh_planilla WHERE id_empleado='$id_empleado' AND id_planilla='$id_planilla' AND fecha='$fecha'";
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
foreach ($respuesta as $key => $arr) {
    $base= $arr["base"];
    $reajuste=$arr["reajuste"];
    $comision=$arr["comision"];
    $mandados=$arr["mandados"];
    $extras=$arr["extras"];
    $oingresos=$arr["otros_ingresos"];
    $vales=$arr["vales"];
    $compras=$arr["compras"];
    $oegresos=$arr["otros_egresos"];
    $ccss=$arr["ccss"];
}

$ingresos= $base+$extras+$comision+$mandados;  //Ingresos
$egresos=$vales+$compras+$oegresos+$ccss+$retencion;
$total=$ingresos+$reajuste+$oingresos-$egresos;

$salxhora=($base+$reajuste)/98;
$salxhora=round($salxhora, 2);


  $sql = "UPDATE rh_planilla set  base='$base',reajuste='$reajuste',extras='$extras',salxhora='$salxhora',comision='$comision',mandados='$mandados',
    otros_ingresos='$oingresos',vales='$vales',compras='$compras',otros_egresos='$oegresos',ccss='$ccss' ,retencion='$retencion', total='$total'  
    WHERE id_empleado='$id_empleado' AND id_planilla='$id_planilla' AND fecha='$fecha'";

  //echo $sql;
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  echo "exito";
