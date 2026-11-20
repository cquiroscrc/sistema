<?php
session_start();
    
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");
$cuenta='';
$guion='';
if(isset($_REQUEST['fini']) && !empty($_REQUEST['fini'])){
          $fini = $_REQUEST['fini'];
}
if(isset($_REQUEST['ffin']) && !empty($_REQUEST['ffin'])){
    $ffin = $_REQUEST['ffin'];
}
if(isset($_REQUEST['tipor']) && !empty($_REQUEST['tipor'])){
    $tipor = $_REQUEST['tipor'];
}
if(isset($_REQUEST['cuenta']) && !empty($_REQUEST['cuenta'])){
    $cuenta = $_REQUEST['cuenta'];
    $guion="-";
}


            if ($tipor=="VENTAS") {
                $url="fini=".$fini."&ffin=".$ffin;
                header("Location:../../../extensiones/tcpdf/pdf/Reporte_mensual_ventas.php?$url");
            }
            if ($tipor=="COMPRAS") {
                $url="fini=".$fini."&ffin=".$ffin;
                header("Location:../../../extensiones/tcpdf/pdf/Reporte_mensual_compras.php?$url");

            }
            if ($tipor=="CONTABLE") {

                ?>
                    <script>
                        swal({
                            type: "error",
                            title: "Este reporte no tiene version en PDF",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){

                        });
                    </script>

                <?php


            }
            if ($tipor=="FINANCIERO") {


            }
            if ($tipor=="BANCOS") {


            }
            if ($tipor=="EGRESOS") {


            }
            if ($tipor=="D151") {

            }
            if ($tipor=="Cabys") {
                header("Location:../../../extensiones/tcpdf/pdf/Cabys.php");
            }
            if ($tipor=="Conteo") {
                $url="fini=".$fini."&ffin=".$ffin;
                header("Location:../../../extensiones/tcpdf/pdf/conteo.php?$url");
            }
            if ($tipor=="Garantias") {
                $url="fini=".$fini."&ffin=".$ffin;
                header("Location:../../../extensiones/tcpdf/pdf/garantia.php?$url");
            }
            if ($tipor=="Vendido") {
                $url="fini=".$fini."&ffin=".$ffin;
                header("Location:../../../extensiones/tcpdf/pdf/vendido.php?$url");
            }
            if ($tipor=="Woocommerce") {
                $url="fini=".$fini."&ffin=".$ffin;
                header("Location:../../../extensiones/tcpdf/pdf/woocommerce.php?$url");
            }