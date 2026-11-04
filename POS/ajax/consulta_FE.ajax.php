<?php


    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
    $fechaActual = $_REQUEST['fecha'];
}

echo '<br>';
$data = file_get_contents("http://apis.gometa.org/status/status.json");
$products = json_decode($data, true);

$errores=$products['api-prod']['lasterror'];
$statushacienda=$products['api-prod']['status'];
$actualizacion=$products['api-prod']['last_update'];
echo "<br>";

echo "<center>";
echo "<table> ";
echo "<tr> ";
echo"<td><img width='48'  height='48' src= 'vistas/img/plantilla/hacienda.jpg'></td>";
echo "<td><b> &nbsp;Ultima actualización &nbsp;</b></td> ";
echo "<td style='color:blue'>".$actualizacion." &nbsp;</td> ";
echo "<td><b>Ultimo error &nbsp; </b></td>";
echo "<td style='color:red'>".$errores."&nbsp; </td>";
echo "<td><b>Estado Hacienda &nbsp;</b></td>";
if ($statushacienda=='OK') {
    echo "<td><img width='48'  height='48' src= 'vistas/img/plantilla/check.png'> </td>";
}
if ($statushacienda=='BAD') {
    echo "<td><img width='48'  height='48' src= 'vistas/img/plantilla/cancel-icon.png'></td>";
}
echo "</tr></table> ";
echo "</center>";

echo "<br>";


@$varsubtotal =0;
@$varimpuesto =0;
@$vardescuento =0;
@$vartotal =0;

$codigobusq = '';


if ($fechaActual != ""){




    $sql="SELECT D.FECHA_HORA,D.NUMERO_DOC,D.EMAIL_RECEPTOR,D.TIPO_DOC,D.ESTADO,D.TOTALCOMPROBANTE,D.ENVIADO,D.ACEPTADO,D.ULTIMO_ERROR,D.FECHA_HORA,F.nfac,F.Nombre,F.vendedor FROM DOC_ELECTRONICO_PROCESADO D, hfacturas F WHERE D.FECHA_HORA BETWEEN '$fechaActual 00:00:00.000' AND '$fechaActual 23:59:59.000' AND D.NUMERO_DOC=F.nfac order by D.NUMERO_DOC DESC";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);






    //ingresa nombres de cuadro
    echo "<center>";
    echo "<H1> REPORTE DE FACTURAS DEL DIA ".$fechaActual."</H1>";
    echo '<table id="FE" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">';
    echo "<thead> ";
    echo "<td><b>Id</b></td> ";
    echo "<td><b>Fecha</b></td> ";
    echo "<td><b>Factura</b></td>";
    echo "<td><b>Nombre</b></td>";
    echo "<td><b>Vend</b></td>";
    echo "<td><b>Correo</b></td>";
    echo "<td><b>Tipo</b></td> ";
    echo "<td><b>Total</b></td> ";
    echo "<td><b>Estado</b></td> ";
    echo "<td><b>Env</b></td> ";
    echo "<td><b>Acep</b></td> ";
    echo "<td><b>Error</b></td> ";

    echo "</thead> ";
    echo "<tbody> ";
    $id_f=1;

    foreach ($respuesta as $key => $arr) {
        //ingresa datos en cuadro
        echo "<td>".$id_f."</td> ";
        echo "<td>".$arr["FECHA_HORA"]."</td> ";

        echo "<td><a href='../POS/vistas/modulos/pdf.php?factura=".$arr["NUMERO_DOC"]."' target = '_blank'>".$arr["NUMERO_DOC"]."</a></td>";
        echo "<td>".$arr["Nombre"]."</td>";
        echo "<td>".$arr["vendedor"]."</td>";


        if ($arr["EMAIL_RECEPTOR"]=="facturaelectronica@lospaticas.com") {
            echo "<td style='color:grey'>Correo Local</td>";
        }else{

            echo "<td style='color:blue'>".$arr["EMAIL_RECEPTOR"]."</td>";
        }


        //echo "<td>".$arr["TIPO_DOC"]."</td>";
        if ($arr["TIPO_DOC"]==="FE") {
            echo "<td style='color:green'>".$arr["TIPO_DOC"]."</td>";
        }
        if ($arr["TIPO_DOC"]==="NC") {
            echo "<td style='color:purple'>".$arr["TIPO_DOC"]."</td>";
        }
        if ($arr["TIPO_DOC"]==="TE") {
            echo "<td style='color:orange'>".$arr["TIPO_DOC"]."</td>";
        }
        echo "<td>".number_format($arr["TOTALCOMPROBANTE"],2)."</td>";

        if ($arr["ESTADO"]=="aceptado") {
            echo "<td style='color:green'>".$arr["ESTADO"]."</td>";
        }
        if ($arr["ESTADO"]=="aceptado-previo") {
            echo "<td style='color:blue'>".$arr["ESTADO"]."</td>";
        }
        if ($arr["ESTADO"]=="VACIO") {
            echo "<td style='color:red'>".$arr["ESTADO"]."</td>";
        }
        if($arr["ESTADO"]=="rechazado"){
            echo "<td style='color:red'>".$arr["ESTADO"]."</td>";
        }

        //echo "<td>".$arr["ESTADO"]."</td>";
        echo "<td>".$arr["ENVIADO"]."</td>";
        echo "<td>".$arr["ACEPTADO"]."</td>";
        echo "<td>".$arr["ULTIMO_ERROR"]."</td>";



        echo "</tr> ";

        $id_f=$id_f+1;
    }
    echo '</tbody>';
    echo "<tfoot> ";
    echo "<td><b>Id</b></td> ";
    echo "<td><b>Fecha</b></td> ";
    echo "<td><b>Factura</b></td>";
    echo "<td><b>Nombre</b></td>";
    echo "<td><b>Vend</b></td>";
    echo "<td><b>Correo</b></td>";
    echo "<td><b>Tipo</b></td> ";
    echo "<td><b>Total</b></td> ";
    echo "<td><b>Estado</b></td> ";
    echo "<td><b>Env</b></td> ";
    echo "<td><b>Acep</b></td> ";
    echo "<td><b>Error</b></td> ";
    echo "</tfoot> ";

    echo '</table>';

    echo "<H1>BITACORA FE</H1>";
    echo '<table id="BITACORA" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">';
    echo "<thead> ";
    echo "<td><b>Id</b></td> ";
    echo "<td><b>Fecha</b></td> ";
    echo "<td><b>Texto</b></td>";
    echo "<td><b>Documento</b></td>";
    echo "<td><b>Tipo</b></td> ";
    echo "</thead> ";
    echo "<tbody> ";
    $id_f=1;

    $sqlBitacora="select top  100 * from BITACORA order by FECHA_HORA desc";
    $respuesta1 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlBitacora);
    foreach ($respuesta1 as $key => $arr) {
        //ingresa datos en cuadro
        echo "<td>" . $id_f . "</td> ";
        echo "<td>" . $arr["FECHA_HORA"] . "</td> ";
        echo "<td>" . $arr["TEXTO"] . "</td>";
        echo "<td>" . $arr["DOCUMENTO"] . "</td>";
        echo "<td>" . $arr["TIPO_DOCUMENTO"] . "</td>";
        echo "</tr> ";
        $id_f=$id_f+1;
    }
    echo '</tbody>';
    echo "<tfoot> ";
    echo "<td><b>Id</b></td> ";
    echo "<td><b>Fecha</b></td> ";
    echo "<td><b>Texto</b></td>";
    echo "<td><b>Documento</b></td>";
    echo "<td><b>Tipo</b></td> ";
    echo "</tfoot> ";

    echo '</table>';

    //FIN
    echo '</center>';

}
?>

<script>

    $(document).ready(function() {


        var table = $('#FE').DataTable({
            "deferRender": true,
            "retrieve": true,
            "processing": true,
            "lengthMenu": [[5,10,20, -1],[5,10,50,"Mostrar Todo"]],
            "language": {

                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });

        var table = $('#BITACORA').DataTable({
            "deferRender": true,
            "retrieve": true,
            "processing": true,
            "lengthMenu": [[5,10,20, -1],[5,10,50,"Mostrar Todo"]],
            "language": {

                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });



        //for table row


    } );

</script>