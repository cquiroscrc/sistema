<?php

    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    $descuento=0;
    $impuesto=0;

    if((isset($_POST['nfac']))and(!empty($_POST['nfac']))){
        $nfac = $_POST['nfac'];
    }

    if((isset($_POST['codigo']))and(!empty($_POST['codigo']))){
        $codigo = $_POST['codigo'];
    }

    if((isset($_POST['cantidad']))and(!empty($_POST['cantidad']))){
        $cantidad = $_POST['cantidad'];
    }

    if((isset($_POST['precio']))and(!empty($_POST['precio']))){
        $precio = $_POST['precio'];
    }
    if((isset($_POST['descuento']))and(!empty($_POST['descuento']))){
        $descuento = $_POST['descuento'];
    }
    if((isset($_POST['impuesto']))and(!empty($_POST['impuesto']))){
        $impuesto = $_POST['impuesto'];
    }

    $no_esta=true;
    $consulta="SELECT * FROM tmp_devol WHERE nfac='$nfac' AND codigo='$codigo'";
    $respuesta= ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);   

    foreach ($respuesta as $key => $valor) {
        $no_esta=false;
        $actualizar="UPDATE tmp_devol SET cantidad='$cantidad' WHERE nfac= '$nfac' AND codigo='$codigo'";
        $resultado= ControladorUtilitaria::ctrMostrarUtilitariaVar($actualizar);   

    }

    if ($no_esta==true) {
        $inserta = "INSERT INTO tmp_devol (nfac,codigo,cantidad,precio,descuento,impuesto) Values ('$nfac' ,'$codigo',$cantidad,$precio,$descuento,$impuesto)";

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);
    }
      
?>