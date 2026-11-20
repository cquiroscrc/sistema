<?php
session_start();
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    if((isset($_POST['montodisminuye']))and(!empty($_POST['montodisminuye']))){
        $monto = $_POST['montodisminuye'];
    }
    if((isset($_POST['redondeadisminuye']))and(!empty($_POST['redondeadisminuye']))){
        $redondea = $_POST['redondeadisminuye'];
    }
    $porcentaje=($monto/100)+1;

    $sql= "SELECT CODIGO, DESCRIPCION, VENTA , (VENTA/ $porcentaje) AS CAMBIO FROM inventario";
    // Hacer el script para ver el aumento
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    $nuevo=0;
    foreach ($ejecutar as $key => $arr) {
        $venta=$arr["VENTA"];
        $cambio=$arr["CAMBIO"];
        $codigo=$arr["CODIGO"];

        if (fmod($cambio, $redondea) <> 0) {
            echo $codigo;
            echo "*RE*";
            echo $cambio;
            $nuevo=($cambio-fmod($cambio, $redondea))+$redondea;
            echo "Nuevo = ".$nuevo;
            echo "<br>";
        } else {
            echo "#SI#".ceil($cambio);
            $nuevo=ceil($cambio);
            echo "Nuevo = ".$nuevo;
            echo "<br>";
        }
        $sql_update="UPDATE inventario SET VENTA='$nuevo' WHERE CODIGO='$codigo'";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);


    }


 echo 'Registro Guardado';
      header("location: ../../utilidades");

?>