<?php
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");
$proveedor=0;
if (isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])) {
    $Factura = $_REQUEST['Factura'];
    $factura = $Factura;
}

if (isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])) {
    $proveedor = $_REQUEST['proveedor'];
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

<table id="codigos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

    <thead>
    <th style="width:10px">#</th>
    <th>Código</th>
    <th>Descripción</th>
    <th>Cantidad</th>
    <th>Precio</th>
    <th>Factor</th>
    <th>Sugerido</th>
    <th>Anterior</th>
    <th>Total</th>
    <th>Accion</th>
    <th>Imagen</th>


    </thead>
    <tbody>

    <?php
    $sqlpedido = "SELECT * FROM Pedidos WHERE Factura='$Factura' order by ID DESC";

    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlpedido);
    $total_ped = 0;
    $i = 1;
    foreach ($ejecutar as $key => $arr) {
        $codigos = $arr["Codigo"];
        $descripcion="********** CODIGO NUEVO ***********";
        $sql_inventario = "SELECT DESCRIPCION, VENTA FROM inventario WHERE CODIGO='$codigos'";
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_inventario);
        foreach ($respuesta as $key => $value) {
            $descripcion = $value["DESCRIPCION"];
            $precioanterior = $value["VENTA"];
        }
        $id_codigos = $arr["ID"];
        $proveedor = $arr["Proveedor"];
        $factura = $arr["Factura"];
        $cantidad = $arr["Cantidad"];
        $factor = $arr["factor"];
        $precio = $arr["Precio"];
        $sugerido = $arr["Sugerido"];
        //$precioanterior = $arr["PrecioAnterior"];
        $total = $cantidad * $precio;

        echo '<tr>';
        echo "<td>" . $i . "</td>";
        echo '<td>' . $codigos . '</td>';
        echo '<td>' . $descripcion . '</td>';
        echo '<td style="color:blue"><b>' . number_format($cantidad, 2) . '</b></td>';
        echo '<td>' . number_format($precio, 2) . '</td>';
        echo '<td><b>' . number_format($factor, 2) . '</b></td>';
        echo '<td style="color:red"><b>' . number_format($sugerido, 2) . '</b></td>';
        echo '<td>' . number_format($precioanterior, 2) . '</td>';
        echo '<td style="color:green"><b>' . number_format($total, 2) . '</b></td>';
        $total_ped = $total_ped + $total;

        echo '<td><button class="btn btn-success"  onclick="Mod_pedidos('."'".$id_codigos."'".','."'".$codigos."'".','."'".$cantidad."'".','."'".$precio."'".')"><i class="fa fa-pencil"></i></button>';
        echo '<button class="btn btn-secundary" onclick="topran('."'".$codigos."'".')"><i class="fa fa-exchange"></i></button></div>';
        echo "<button class='btn btn-danger' onclick='El_pedidos(".$id_codigos.")'><i class='fa fa-times'></i></button></div>";


        echo "</td>";


        // ingresar imagen************
        $path = '../../subida_cat/';

        if (substr($codigos, 0, 1) == 1) {
            $directorio = '1/';
        }
        if (substr($codigos, 0, 1) == 2) {
            $directorio = '2/';
        }
        if (substr($codigos, 0, 1) == 3) {
            $directorio = '3/';
        }
        if (substr($codigos, 0, 1) == 4) {
            $directorio = '4/';
        }
        if (substr($codigos, 0, 1) == 9) {
            $directorio = '9/';
        }
        if (substr($codigos, 0, 1) == 0) {
            $directorio = '0/';
        }
        $codigos1 = rtrim($codigos);
        if (substr($codigos, -2) == 'EU') {
            $codigos1 = substr($codigos, 0, -2);
            $codigos1 = rtrim($codigos1);

        }
        if (substr($codigos, -2) == 'GR') {
            $codigos1 = substr($codigos, 0, -2);
            $codigos1 = rtrim($codigos1);
        }
        if (substr($codigos, -2) == 'TW') {
            $codigos1 = substr($codigos, 0, -2);
            $codigos1 = rtrim($codigos1);

        }
        if (substr($codigos, -2) == 'JP') {
            $codigos1 = substr($codigos, 0, -2);
            $codigos1 = rtrim($codigos1);
        }
        if (substr($codigos, -2) == 'ZL') {
            $codigos1 = substr($codigos, 0, -2);
            $codigos1 = rtrim($codigos1);
        }

        $foto = substr($codigos1, 1);
        $foto = rtrim($foto);

        if (substr($foto, 0, 1) == ' ') {
            //echo 'FOTOS';
            $foto = substr($foto, 2, strlen($foto));
        }

        $filename = $path . $directorio . $foto . ".jpg";
        $archivo = $filename;

        $existes = $_SERVER["DOCUMENT_ROOT"].'/subida_cat/'.$directorio.$foto.'.jpg';

        if (is_file($existes)) {
            $archivo=$filename;
        } else {
            $archivo=$path."inf.png";
        }

        echo '<td><a class="fancybox" href="' . $archivo . '" title="' . $codigos . '  -- ' . $descripcion . '"><img width="75"  height="75" src= "' . $archivo . '""></a></td>';

        echo '</tr>';
        $i = $i + 1;

    }

    ?>

    </tbody>
    <tfoot>
    <tr>
        <th style="width:10px">#</th>
        <th>Código</th>
        <th>Descripción</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Factor</th>
        <th>Sugerido</th>
        <th>Anterior</th>
        <th>Total</th>
        <th>Accion</th>
        <th>Imagen</th>

    </tr>
    </tfoot>
</table>
<div>
    <h3 style="display: inline">Total pedido:</h3>
    <h3 id="totalpedido" style="display: inline"><?php echo number_format($total_ped, 2); ?></h3>
    <p id="sel_pedido" style="display: none;"><?php echo $factura; ?></p>
    <p id="sel_proveedor" style="display: none;"><?php echo $proveedor; ?></p>
</div>
<script>
    $(document).ready(function () {
        $("#codigoproveedor").text($("#sel_proveedor").text())
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
<script type="text/javascript">
    $(document).ready(function () {
        $(".fancybox").fancybox();
    });
</script>


