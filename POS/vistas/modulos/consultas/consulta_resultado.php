<?php
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if (isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])) {
    $Factura = $_REQUEST['Factura'];
}
if (isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])) {
    $tipo = $_REQUEST['tipo'];
}
?>
<style>
    table.dataTable tr.odd {
        background-color: #d6d6d6;
    }

    table.dataTable tr.even {
        background-color: white;
    }
</style>
<?php

if ($tipo=='documento') {

?>

<table id="codigos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

    <thead>
        <th style="width:10px">#</th>
        <th>Fecha</th>
        <th>Documento</th>
        <th>Movimiento</th>
        <th>Cliente/Proveedor</th>
        <th>Vendedor</th>
        <th>Estado</th>
        <th>Tipo</th>
        <th>Acciones</th>
    </thead>
    <tbody>

    <?php
    $sql = "SELECT DISTINCT Factura, fecha, movimiento, codcliente,vendedor,estado,tipo FROM hgeneral WHERE Factura like '%$Factura%' ORDER BY fecha DESC";

    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);


    $i = 1;
    foreach ($ejecutar as $key => $arr) {

        $codigos= $arr["Factura"];
        $fecha=$arr["fecha"];
        $cliente=$arr["codcliente"];
        echo '<tr>';
        echo "<td>" . $i . "</td>";
        echo '<td>' . $arr["fecha"] . '</td>';
        echo '<td>' . $arr["Factura"] . '</td>';
        echo '<td>' . $arr["movimiento"] . '</td>';
        echo '<td>' . $arr["codcliente"] . '</td>';
        echo '<td>' . $arr["vendedor"] . '</td>';
        echo '<td>' . $arr["estado"]. '</td>';
        echo '<td>' . $arr["tipo"] . '</td>';
        echo "<td>";
        echo '<button class="btn btn-success" onclick="myVer('."'".$codigos."'".','."'".$fecha."'".','."'".$cliente."'".')"><i class="fa fa-pencil"></i></button></div>';
        echo "</td>";
        echo '</tr>';
        $i = $i + 1;
    }

    ?>

    </tbody>
    <tfoot>
    <tr>
        <th style="width:10px">#</th>
        <th>Fecha</th>
        <th>Documento</th>
        <th>Movimiento</th>
        <th>Cliente</th>
        <th>Vendedor</th>
        <th>Estado</th>
        <th>Tipo</th>
        <th>Acciones</th>
    </tr>
    </tfoot>
</table>
<?php
}else{
?>
    <table id="codigos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

        <thead>
        <th style="width:10px">#</th>
        <th>Fecha</th>
        <th>Documento</th>
        <th>Nombre</th>
        <th>Cliente</th>
        <th>Monto</th>
        </thead>
        <tbody>

        <?php
        $sql = "SELECT c.NFac, c.IDCliente, c.Fecha, c.Total, n.codigo, n.nombre FROM cxc c, clientes n WHERE c.NFac like 'R%$Factura%' AND c.IDCliente= n.codigo ORDER BY c.Fecha,c.NFac";

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);


        $i = 1;
        foreach ($ejecutar as $key => $arr) {
            $fecha_sis=strtotime($arr['Fecha']);

            echo '<tr>';
            echo "<td>" . $i . "</td>";
            echo '<td>' . date("d-m-Y", $fecha_sis). '</td>';
            echo '<td>' . $arr['NFac']. '</td>';
            echo '<td>' . $arr['nombre'] . '</td>';
            echo '<td>' . $arr['IDCliente'] . '</td>';
            echo '<td>' . number_format($arr['Total'],2). '</td>';
            echo '</tr>';
            $i = $i + 1;
        }

        ?>

        </tbody>
        <tfoot>
        <tr>
            <th style="width:10px">#</th>
            <th>Fecha</th>
            <th>Documento</th>
            <th>Nombre</th>
            <th>Cliente</th>
            <th>Monto</th>
        </tr>
        </tfoot>
    </table>
    <?php

}

?>
<script>
    $(document).ready(function () {

        var table = $('#codigos').DataTable({
            "deferRender": true,
            "retrieve": true,
            "processing": true,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 50, "Mostrar Todo"]],
            "language": {

                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });


        //for table row


    });

</script>



