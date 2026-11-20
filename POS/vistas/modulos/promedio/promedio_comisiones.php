<?php

 require("../../../modelos/conexion.php");
 require("../../../controladores/utilitaria.controlador.php");
 require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['metames']) && !empty($_REQUEST['metames'])){
          $metames = $_REQUEST['metames'];
    } 
    if(isset($_REQUEST['totalmeta']) && !empty($_REQUEST['totalmeta'])){
          $totalmeta = $_REQUEST['totalmeta'];
          
    }
// Funciones para Inicio y Fin de mes
function primer_mes1() {
    $month = date('m');
    $year = date('Y');
    return date('Y-m-d', mktime(0,0,0, $month-1, 1, $year));
}

function ultimo_mes1() {
    $month = date('m');
    $year = date('Y');
    $day = date("d", mktime(0,0,0, $month, 0, $year));

    return date('Y-m-d', mktime(0,0,0, $month-1, $day, $year));
};

$inicio=primer_mes1();

$fin=ultimo_mes1();

    $llegar=35000;  //Aqui se cambia el monto inicial
    $porcentaje=2.5; // Aqui se cambia el porcentaje.
    $xmillon=15000;
    $piezas=250;
    $pagopiezas=25000;
    $x50=50000;
    $primer=20000;
    $segundo=10000;
    $promotor=0.13;
    $total_final=0;
    echo '<table border="1"><tr>';
    echo '<h3>Meta mes = '.number_format($metames,2).' / Total Meta = '.number_format($totalmeta,2).'</h3>';
    $sqlT_vendedores="SELECT v.nombre,v.codigo , SUM(h.subtotal)- SUM(h.descuento) AS  venta FROM hfacturas h INNER JOIN Vendedores v on FORMAT(cast( h.vendedor as numeric),'###') = FORMAT(cast( v.codigo as numeric),'###')WHERE h.fechafact BETWEEN '$inicio 00:00:00.000' AND  '$fin 23:59:59.000'GROUP BY v.codigo, v.nombre ORDER BY venta DESC";
    //echo $sqlT_vendedores1;
    $TT_vendedores= ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlT_vendedores);
    foreach ($TT_vendedores as $key => $valor) {
        $comision=0;
        $diferencia=0;
        $vendedor = $valor["codigo"];
        $meta = $valor["venta"];
        $nombre = $valor["nombre"];
        echo '<th>'.$vendedor.'</th><th>'.$nombre.'</th><th>'.number_format($meta,2).'</th>';

        if ($meta > $metames){

            $comision=$comision+$llegar;
            $diferencia=$meta-$metames;
            $por=$diferencia*$porcentaje/100;
            $comision=$comision+$por;

            $total_final=$total_final+$comision;

            echo '<th>'.number_format($comision,2).'</th>';

        }
        echo '</tr>';
    }
    echo '</table>';
    echo'<h3>Piezas Daihatsu = '.$piezas.'</h3>';
    $vendedores_list="SELECT * FROM vendedores ORDER BY codigo";
    $respuesta_list=ControladorUtilitaria::ctrMostrarUtilitariaVar($vendedores_list);
    echo '<table border="1"><tr>';
    foreach ($respuesta_list as $key => $valor) {
        $nom_vend=$valor["nombre"];
        $cod_vend=$valor["codigo"];

        $consulta="SELECT SUM(
                          CASE 
                            WHEN h.movimiento = 'Venta' THEN cantidad
                            WHEN h.movimiento = 'DEVOLUCION' THEN -cantidad 
                          ELSE 0
                        END
                  ) AS  cantidad 
                  FROM hgeneral h, inventario i 
                  WHERE h.fecha >='$inicio 00:00:00.000'
                  AND h.fecha <= '$fin 23:59:00.000' AND h.vendedor like'%".(int)$cod_vend."'
                  and (h.CODIGO like '2%TW'  or h.CODIGO like '2%EU' or h.CODIGO like '2%GR' or h.CODIGO like '2%ZL' 
                  or h.CODIGO like '2%JP') and i.MARCA = 'DH' and h.codigo=i.CODIGO 
                  AND movimiento IN ('Venta', 'DEVOLUCION')";
        $respuesta= ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

        foreach ($respuesta as $key => $value) {
            $comipiezas=0;
            if ($value["cantidad"]<>0) {
                $piezasv=$value["cantidad"];
                echo '<th>'.$cod_vend.'</th><th>'.$nom_vend.'</th><th>'.number_format($piezasv,2).'</th>';

                if ($piezasv>$piezas){
                    $comipiezas=$comipiezas+$pagopiezas;
                    $sobrepasa=$piezasv-$piezas;
                    $adicional=intdiv($sobrepasa,50);
                    $pagoadicional=$adicional*$x50;
                    $comipiezas=$comipiezas+$pagoadicional;

                    $total_final=$total_final+$comipiezas;

                    echo '<th>'.number_format($comipiezas,2).'</th>';
                }

                echo '</tr>';
            }

        }
    }

    echo '</table>';
    echo '<h4>Primer lugar = '.number_format($primer,2). ' / Segundo = '.number_format($segundo,2).'</h4>';
    echo '<h4>Promotor = '.number_format($totalmeta*$promotor/100,2).'</h4>';
    $total_final=$total_final+($totalmeta*$promotor/100);
    echo '<h3>Total Comisiones = '.number_format($total_final,2).' / '.number_format(($total_final+40000)/3,2).'</h3>';


?>
