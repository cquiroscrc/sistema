<?php
require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");
session_start();

$cod = "XX";
if (isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])) {
    $cod = $_REQUEST['codigo'];
    $familia = 0;
}

if (substr($cod, 0, 5) == '1___ ') {
    $codigo = $cod;
} else {
    $cant = stripos($cod, "_", 0);
    if ($cant <> 0) {
        $codigo = substr($cod, 0, $cant);
    } else {
        $codigo = $cod;
    }
}
$codigo = trim($codigo);
$codigobusq = '';

if ($familia == 1) {
    $codigo = '1___ ' . $codigo;

}

//// *************** Incluye recibir ******************
$suge = 0;
if ($codigo != "") {
    $reglon = 1;

    $codigo = strtoupper($codigo);
    //// *************** Incluye la consulta ******************

    $where = "";
    if (substr($codigo, 0, 1) == 1) { // vw
        $where = "codigo like '$codigo%'";
        $sql = "SELECT * FROM inventario WHERE $where 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";
    } elseif (substr($codigo, 0, 1) == 0) { // vw
        $where = "codigo like '$codigo%'";
        $sql = "SELECT * FROM inventario WHERE $where 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";
    } elseif (substr($codigo, 0, 1) == 2) {
        $where = "codigo like '$codigo%'";
        $sql = "SELECT * FROM inventario WHERE $where 
          order by codigo";
    } elseif (substr($codigo, 0, 1) == 3) {
        $where = "codigo like '$codigo%'";
        $sql = "SELECT * FROM inventario WHERE $where 
          order by codigo";
    } elseif (substr($codigo, 0, 1) == 4) {
        $where = "codigo like '$codigo%'";
        $sql = "SELECT * FROM inventario WHERE $where 
          order by codigo";
    } elseif (substr($codigo, 0, 1) == 5) {
        $fam = 1;
        $des = substr($codigo, 1, strlen($codigo));
        $codigo = $fam . '___ ' . $des;

        $where = "codigo like '$codigo%'";
        $sql = "SELECT * FROM inventario WHERE $where 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";
    } elseif (substr($codigo, 0, 1) == 9) {
        $where = "codigo like '$codigo%'";
        $sql = "SELECT * FROM inventario WHERE $where 
          order by codigo";
    } elseif (substr($codigo, 0, 1) == 6) {
        $fam = substr($codigo, 1, 1);
        $des = substr($codigo, 2, strlen($codigo));
        $where = "codigo like '$fam%' 
           AND descripcion like '%$des%' OR DESC_ALT like '%$des%' AND codigo like '$fam%'";
        $sql = "SELECT * FROM inventario WHERE $where order by descripcion";
    }


}

?>
<style>
    table.dataTable tr.odd {
        background-color: #D6D6D6;
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
    <th>Stock</th>
    <th>Promedio</th>
    <th>Transito</th>
    <th>Bodega</th>
    <th>Venta</th>
    <th>Costo</th>
    <th>$</th>
    <th>Proveedor</th>
    <th>Ubicación</th>
    <th>Cabys</th>
    <th>Acciones</th>
    <th>Imagen</th>
    </thead>
    <tbody>

    <?php


    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    if (!is_array($ejecutar)) {
        $ejecutar = [];
    }

    $mapaPromedio = [];
    $mapaPedido = [];
    $mapaPedidoDetalle = [];
    $mapaSucursal = [];
    $mapaProveedor = [];
    $mapaCabys = [];

    if (!empty($where) && count($ejecutar) > 0) {
        $subCodigos = "SELECT codigo FROM inventario WHERE $where";

        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar(
            "SELECT codigo, (SUM(cantidad)/6) as promedio from hgeneral where codigo in ($subCodigos)
                AND movimiento not in('Ajuste') AND
                substring(factura,1,2)='F-' AND
                FECHA >= Dateadd(mm,-6,Getdate()) AND
                FECHA <= GETDATE()
                GROUP BY codigo"
        );
        if (is_array($respuesta)) {
            foreach ($respuesta as $valor) {
                $mapaPromedio[rtrim($valor["codigo"])] = $valor["promedio"];
            }
        }

        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar(
            "SELECT codigo, SUM(cantidad) as pedido from pedidos where codigo in ($subCodigos) GROUP BY codigo"
        );
        if (is_array($respuesta)) {
            foreach ($respuesta as $valor) {
                $mapaPedido[rtrim($valor["codigo"])] = $valor["pedido"];
            }
        }

        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar(
            "SELECT p.codigo, p.Factura, p.Cantidad, t.shortname from pedidos p, Proveedores t where p.Proveedor=t.codigo and p.codigo in ($subCodigos)"
        );
        if (is_array($respuesta)) {
            foreach ($respuesta as $arr_ped) {
                $clavePedido = rtrim($arr_ped["codigo"]);
                if (!isset($mapaPedidoDetalle[$clavePedido])) {
                    $mapaPedidoDetalle[$clavePedido] = "";
                }
                $mapaPedidoDetalle[$clavePedido] .= trim($arr_ped['shortname']) . " / " . $arr_ped['Factura'] . " / " . $arr_ped['Cantidad'] . " *-* ";
            }
        }

        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar(
            "SELECT codigo, cantidad FROM tbl_exi_suc WHERE codigo in ($subCodigos)"
        );
        if (is_array($respuesta)) {
            foreach ($respuesta as $arr_suc) {
                $mapaSucursal[rtrim($arr_suc["codigo"])] = $arr_suc["cantidad"];
            }
        }

        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar(
            "SELECT codigo, shortname FROM Proveedores WHERE codigo in (SELECT proveedor FROM inventario WHERE $where)"
        );
        if (is_array($respuesta)) {
            foreach ($respuesta as $valor) {
                $mapaProveedor[rtrim($valor["codigo"])] = $valor["shortname"];
            }
        }

        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar(
            "SELECT Codigo, Descripcion FROM DE_CABYS WHERE Codigo in (SELECT CODIGO_CABYS FROM inventario WHERE $where)"
        );
        if (is_array($respuesta)) {
            foreach ($respuesta as $valor) {
                $mapaCabys[rtrim($valor["Codigo"])] = $valor["Descripcion"];
            }
        }
    }

    $i = 1;
    foreach ($ejecutar as $key => $arr) {
        $codigos = $arr["CODIGO"];
        $claveCodigo = rtrim($codigos);
        $descripcion = $arr["DESCRIPCION"];
        $existencia = $arr["EXISTENCIA"];
        $costo = $arr["COSTO"];
        $venta = $arr["VENTA"];
        $costodol = $arr["costodol"];
        $proveedor = $arr["proveedor"];
        $ubicacion = $arr["ubicacion"];
        $desc_tecnica = $arr["DESC_ALT"];
        $potencia = $arr['potencia'];
        $cabys= $arr['CODIGO_CABYS'];
        $xpotencia = $potencia * 100;
        $venta = $venta + $xpotencia;


        echo '<tr>
            <td>' . $i . '</td>';
        echo '<td>' . $codigos . '</td>';
        echo '<td><a name="' . $codigos . '" id="a"></a><a style="color:black" data-toggle="tooltip" title="' . $desc_tecnica . '">' . $descripcion . '</a></td>';
        if ($existencia == 0) {
            echo '<td style="color:red;"><b>' . number_format($existencia, 2) . '</b></td>';
        } else {
            echo '<td style="color:blue;"><b>' . number_format($existencia, 2) . '</b></td>';
        }
        // Promedio de los ultimos 6 meses. Se calcula una sola vez para todo el resultado.
        $promedio = array_key_exists($claveCodigo, $mapaPromedio) ? $mapaPromedio[$claveCodigo] : 0;
        echo '<td style="color:maroon;"><b>' . number_format($promedio, 2) . '</b></td>';
        $pedidos = array_key_exists($claveCodigo, $mapaPedido) ? $mapaPedido[$claveCodigo] : 0;
        $ped_totales = isset($mapaPedidoDetalle[$claveCodigo]) ? $mapaPedidoDetalle[$claveCodigo] : "";

        echo '<td><a name="' . $codigos . '" id="a"></a><a style="color:green;" data-toggle="tooltip" title="' . $ped_totales . '"><b>' . number_format($pedidos, 2) . '</b></a></td>';
        $sucursal = isset($mapaSucursal[$claveCodigo]) ? $mapaSucursal[$claveCodigo] : 0;
        echo '<td style="color:orange"><b>' . $sucursal . '</b></td>';
        echo '<td>' . $venta . '</td>';
        echo '<td>' . $costo . '</td>';
        echo '<td>' . $costodol . '</td>';
        $claveProveedor = rtrim((string) $proveedor);
        $nombre_prove = array_key_exists($claveProveedor, $mapaProveedor) ? $mapaProveedor[$claveProveedor] : "Local";
        $claveCabys = rtrim((string) $cabys);
        $nombre_cabys = array_key_exists($claveCabys, $mapaCabys) ? $mapaCabys[$claveCabys] : "N/A";


        echo '<td>' . $nombre_prove . '</td>';
        echo '<td>' . $ubicacion . '</td>';
        echo '<td><a style="color:black" data-toggle="tooltip" title="' . $nombre_cabys . '">' . $cabys . '</a></td>';
        // Fotos
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

        echo "<td>";
        if ($_SESSION["perfil"] == "especial"){

        }else{
            echo "    
            <div class='btn-group'><button class='btn btn-warning btnEditarProducto' idProducto='" . $codigos . "' data-toggle='modal' data-target='#modalEditarProducto'><i class='fa fa-pencil'></i></button>";

            echo "<button class='btn btn-danger btnEliminarProducto' idProducto='" . $codigos . "' existencia='" . $existencia . "'><i class='fa fa-times'></i></button></div>";


        }

        echo "<div><button class='btn btn-success btnMovimientoProducto' idProducto='" . $codigos . "' data-toggle='modal' data-target='#modalMovimientoProducto'><i class='ion ion-refresh'></i></button>";
        if ($_SESSION["perfil"] == "especial"){

        }else {
            echo "<button class='btn btn-info btnPaseProducto' idProducto='" . $codigos . "' data-toggle='modal' data-target='#modalPase'><i class='fa fa-arrows-h'></i></button></div>";
        }
        //echo '<button class="btn btn-success" id="add_codigo" onclick="myImagen('."'".$archivo."'".')"><i class="ion ion-android-add-circle"></i></button>';
        echo '<a href="vistas/modulos/descarga.php?archivo='.$filename.'" target="_blank" class="btn btn-buynow">Descargar</a>';

        echo "</td>";
        list($width, $height, $type, $attr) = getimagesize('../'.$archivo);

        echo'<td><a data-fancybox  data-type="iframe" class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'"></a>';
        echo '<br>';
        echo $width.' X '.$height;
        echo '</td>';
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
        <th>Stock</th>
        <th>Promedio</th>
        <th>Transito</th>
        <th>Bodega</th>
        <th>Venta</th>
        <th>Costo</th>
        <th>$</th>
        <th>Proveedor</th>
        <th>Ubicación</th>
        <th>Cabys</th>
        <th>Acciones</th>
        <th>Imagen</th>
    </tr>
    </tfoot>
</table>
<?php
// Llama la generacion de PDF
    echo '<a href="../consulta_simple_imprime.php?codigo='.$codigo.'" target="_blank"><img width="48"  height="48" src= "../img/pdf.png">Generar PDF</a> ';

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

        table.on('draw', function () {
            $('[data-fancybox]', this).fancybox({
                toolbar: false,
                smallBtn: true,
                iframe: {
                    preload: false,
                    css : {
                        width: '600px'
                    }
                },

            });

        })
        //for table row


    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $(".fancybox").fancybox();
    });
</script>



