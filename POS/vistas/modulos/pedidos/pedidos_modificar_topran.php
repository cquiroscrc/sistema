<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['topran']) && !empty($_REQUEST['topran'])){
          $cod_topran = $_REQUEST['topran'];
    }
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];
    }



$sql_precio="SELECT * FROM topran WHERE Codigo='$codigo'";
$esta=0;
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_precio);
foreach ($respuesta as $key => $value) {
  $esta=1;
}
echo 'Topran '.$cod_topran.' - '.$codigo.' - '.$esta;
if ($esta==1) {
  $sql_update="UPDATE topran SET Codigo='$codigo',topran='$cod_topran' WHERE Codigo='$codigo'";
}
if ($esta==0) {
  $sql_update="INSERT INTO topran (Codigo,topran) VALUES ('$codigo','$cod_topran')";
}

$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);

?>
