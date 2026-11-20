<?php
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if (isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])) {
    $Factura = $_REQUEST['Factura'];
}
if (isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])) {
    $fecha = $_REQUEST['fecha'];
}
if (isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])) {
    $cliente = $_REQUEST['cliente'];
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

<table id="codigos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

    <thead>
        <th style="width:10px">#</th>
        <th>Factura</th>
        <th>Fecha</th>
        <th>Movimiento</th>
        <th>Código</th>
        <th>Cantidad</th>
        <th>Anterior</th>
        <th>Actual</th>
    </thead>
    <tbody>

    <?php
    $sql = "SELECT * from hgeneral where Factura = '$Factura' ORDER by FECHA DESC";

    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);


    $i = 1;
    foreach ($ejecutar as $key => $prome_mov) {
        echo '<tr>';
        echo '<td>'.$i.'</td>';
        $m_factura=$prome_mov["Factura"];
        $fecha2=date("d-m-Y",strtotime($prome_mov["fecha"]));
        $m_fecha= $fecha2;
        echo '<td>'.$m_factura.'</td>';
        echo '<td>'.$m_fecha.'</td>';
        $ajus_id=$prome_mov['movimiento'];
        echo '<td>'.$prome_mov['movimiento'].'</td>';
        echo '<td>'.$prome_mov['codigo'].'</td>';
        echo '<td>'.$prome_mov['cantidad'].'</td>';
        echo '<td>'.$prome_mov['anterior'].'</td>';
        echo '<td>'.$prome_mov['actual'].'</td>';
        echo '</tr>';
        $i = $i + 1;
    }

    ?>

    </tbody>
    <tfoot>
    <tr>
        <th style="width:10px">#</th>
        <th>Factura</th>
        <th>Fecha</th>
        <th>Movimiento</th>
        <th>Código</th>
        <th>Cantidad</th>
        <th>Anterior</th>
        <th>Actual</th>
    </tr>
    </tfoot>
</table>
<?php

    if ($ajus_id=='Ajuste') {
        $ajussql="SELECT * from ajutras where IDMovimiento = '$Factura'";

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($ajussql);
        foreach ($ejecutar as $key => $arr_ajus) {

            echo '<H2>Comentario:'.$arr_ajus['Comentario'].'</H2>';
            $id_usuario=$arr_ajus['IDUsuario'];

            $maquina="SELECT * from acc_usuarios where id = $id_usuario";

            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($maquina);
            foreach ($ejecutar as $key => $arr_maq) {
                $nombre=$arr_maq['nombre'];
            }
            echo '<H3> Usuario: '.$id_usuario.' - '.$nombre.'</H3>';

    }


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



