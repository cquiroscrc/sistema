<?php

    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    try {
        
        $proveedor=0;
        $descuento=0;
        $Adicional="";
        if((isset($_POST['nfac']))and(!empty($_POST['nfac']))){
            $nfac = $_POST['nfac'];
        }

        if((isset($_POST['subtotal']))and(!empty($_POST['subtotal']))){
            $subtotal = $_POST['subtotal'];
        }

        if((isset($_POST['descuento']))and(!empty($_POST['descuento']))){
            $descuento = $_POST['descuento'];
        }

        if((isset($_POST['impuesto']))and(!empty($_POST['impuesto']))){
            $impuesto = $_POST['impuesto'];
        }
        if((isset($_POST['total']))and(!empty($_POST['total']))){
            $total = $_POST['total'];
        }
        if((isset($_POST['proveedor']))and(!empty($_POST['proveedor']))){
            $proveedor = $_POST['proveedor'];
        }
        if((isset($_POST['vendedor']))and(!empty($_POST['vendedor']))){
            $vendedor = $_POST['vendedor'];
        }
        if((isset($_POST['Nombre']))and(!empty($_POST['Nombre']))){
            $Nombre = $_POST['Nombre'];
        }
        if((isset($_POST['Adicional']))and(!empty($_POST['Adicional']))){
            $Adicional = $_POST['Adicional'];
        }
        if((isset($_POST['Exoneracion']))and(!empty($_POST['Exoneracion']))){
            $Exoneracion = $_POST['Exoneracion'];
        }
        if((isset($_POST['t_pago']))and(!empty($_POST['t_pago']))){
            $t_pago = $_POST['t_pago'];
        }
        
        $numerodocumento='';
        $identificacion='';
        $fechaemision='';
        $fechavencimiento='';
        $documentocodigo='';
        $documentodescripcion='';
        $nombreinstitucion='';
        $porcentaje=0;
        $montoexoneracion=0;

        if((isset($_POST['numerodocumento']))and(!empty($_POST['numerodocumento']))){
            $numerodocumento = $_POST['numerodocumento'];
        }              
        if((isset($_POST['identificacion']))and(!empty($_POST['identificacion']))){
            $identificacion = $_POST['identificacion'];
        }
        if((isset($_POST['fechaemision']))and(!empty($_POST['fechaemision']))){
            $fechaemision = $_POST['fechaemision'];
        }
        if((isset($_POST['fechavencimiento']))and(!empty($_POST['fechavencimiento']))){
            $fechavencimiento = $_POST['fechavencimiento'];
        }
        if((isset($_POST['documentocodigo']))and(!empty($_POST['documentocodigo']))){
            $documentocodigo = $_POST['documentocodigo'];
        }
        if((isset($_POST['documentodescripcion']))and(!empty($_POST['documentodescripcion']))){
            $documentodescripcion = $_POST['documentodescripcion'];
        }
        if((isset($_POST['nombreinstitucion']))and(!empty($_POST['nombreinstitucion']))){
            $nombreinstitucion = $_POST['nombreinstitucion'];
        }
        if((isset($_POST['porcentaje']))and(!empty($_POST['porcentaje']))){
            $porcentaje = $_POST['porcentaje'];
            $porcentaje=$porcentaje*100;
        }
        if((isset($_POST['montoexoneracion']))and(!empty($_POST['montoexoneracion']))){
            $montoexoneracion = $_POST['montoexoneracion'];
        }



 
        
        $inserta = "INSERT INTO hprefacturas (nfac,subtotal,descuento,impuesto,total,proveedor,vendedor,Nombre,Adicional,Exoneracion,numerodocumento, identificacion,fechaemision,fechavencimiento,documentocodigo,documentodescripcion,nombreinstitucion,porcentaje,montoexoneracion,medio_pago) Values ('$nfac' ,'$subtotal','$descuento','$impuesto','$total','$proveedor','$vendedor','$Nombre','$Adicional', '$Exoneracion','$numerodocumento', '$identificacion',
        '$fechaemision','$fechavencimiento','$documentocodigo','$documentodescripcion','$nombreinstitucion','$porcentaje','$montoexoneracion','$t_pago')";
 
 echo $inserta;


        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);

}
catch (Exception $e) {
        echo ('Error ocurrido:' + $e->message);   
        die();
    }
      
?>