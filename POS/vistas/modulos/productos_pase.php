<?php
require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

    if(isset($_REQUEST['codigoorigen']) && !empty($_REQUEST['codigoorigen'])){
        $codigoorigen = $_REQUEST['codigoorigen'];
    } 
    if(isset($_REQUEST['codigodestino']) && !empty($_REQUEST['codigodestino'])){
        $codigodestino = $_REQUEST['codigodestino'];
    } 
    

   //Update Hgeneral
   //  Pasa todo el historial del origen al destino

  $sql_update_h="UPDATE hgeneral SET CODIGO='$codigodestino' WHERE CODIGO='$codigoorigen'";
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update_h);


  //  Revisa si exixte
  $sql="SELECT * FROM inventario WHERE CODIGO='$codigodestino'";
  $exite='no';
    echo $sql;

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  foreach ($respuesta as $key => $arr) {
    $exite='si';
    $existenciadestino=$arr['EXISTENCIA'];

    //   Existencia
    $sql="SELECT * FROM inventario WHERE CODIGO='$codigoorigen'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $existenciaorigen=$arr['EXISTENCIA'];
    }
    $existenciatotal=$existenciaorigen+$existenciadestino;

//    Actualiza cantidades
    $sql_update="UPDATE inventario SET EXISTENCIA='$existenciatotal' WHERE CODIGO='$codigodestino'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);


    $sql_eliminar="DELETE FROM Inventario where CODIGO='$codigoorigen'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_eliminar);

  }

  if ($exite=='no') {
    $sql_update_i="UPDATE Inventario SET CODIGO='$codigodestino' where CODIGO='$codigoorigen'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update_i);

  }

?>
