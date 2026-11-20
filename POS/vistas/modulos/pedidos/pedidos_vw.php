<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");


    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];
    }


$cod_topran="";
$sql_precio="SELECT * FROM topran WHERE topran like '%$codigo%'";
//echo $sql_precio;
$precioanterior=0;
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_precio);
foreach ($respuesta as $key => $value) {
  $cod_topran=$value['Codigo'];
}
echo $cod_topran;
?>
