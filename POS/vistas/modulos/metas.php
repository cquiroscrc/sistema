<?php

$item = null;
$valor = null;
$orden = "ventas";

$productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

$colores = array("red","green","yellow","aqua","fuchsia","orange","blue","olive","teal","maroon");

$totalVentas = ControladorProductos::ctrMostrarSumaVentas();

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


?>

<!--=====================================
PRODUCTOS MÁS VENDIDOS
======================================-->

<div class="box box-warning">
	
	<div class="box-header with-border">
          <?php
              $total_meta=0;    /// Sacar la meta
              $fecha_metas = strtotime ($inicio ) ;
              $ano = date('Y',$fecha_metas);
              $mes = date('m',$fecha_metas);

              setlocale(LC_TIME, 'es_ES');

              $fecha = DateTime::createFromFormat('!m', $mes);
              $mes_nombre = strftime("%B", $fecha->getTimestamp()); // marzo

              $sql_meta="SELECT * FROM tbl_metas WHERE mes =".$mes." AND ano=".$ano."";
              $res_meta=ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_meta);
              //var_dump($res_meta);
              foreach ($res_meta as $key => $value) {
                  $total_meta=$value["meta"];
              }

              $sql_desc="SELECT * FROM tbl_Utiles WHERE IDUtilidad =2";
              $res_desc=ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_desc);
              foreach ($res_desc as $key => $valdesc) {
                  $t_descuentos=$valdesc["Valor"];
                  if ($t_descuentos==1) {
                      $t_descuento='SI';
                  }else{
                      $t_descuento='No';
                  }
              }
              echo '<center>';
              echo '<h3 class="box-title">Meta del Mes '.$mes_nombre.' es : '.number_format($total_meta,2).'</h3>';
              echo '</center>';///////
           ?>
    </div>

	<div class="box-body">
      	<div class="row">
	        <div class="col-md-5">
                <div class="chart-responsive">
	            	<canvas id="pieChart" height="130"></canvas>
	          	</div>
	        </div>
            <div class="col-md-7">
		  	 	<ul class="chart-legend clearfix">
                    <?php
                         //Cargar Metas Vendedores
                        $i=0;
                        $total=0;
                        $sqlT_vendedores="SELECT v.nombre,v.codigo , SUM(h.subtotal)- SUM(h.descuento) AS  venta FROM hfacturas h INNER JOIN Vendedores v on FORMAT(cast( h.vendedor as numeric),'###') = FORMAT(cast( v.codigo as numeric),'###')WHERE h.fechafact BETWEEN '$inicio 00:00:00.000' AND  '$fin 23:59:59.000'GROUP BY v.codigo, v.nombre ORDER BY venta DESC";
                            //echo $sqlT_vendedores1;
                        $TT_vendedores= ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlT_vendedores);
                        foreach ($TT_vendedores as $key => $valor) {
                           echo ' <li><i class="fa fa-circle-o text-'.$colores[$i].'"></i> '.$valor["codigo"]." - ".$valor["nombre"]." = &cent;".number_format($valor["venta"],2).'</li>';
                           $total=$total+$valor["venta"];
                           $i=$i+1;
                        }
                        echo "<h3>Total de metas: ".number_format($total,2)."</h3>";
                        echo '<input type="hidden" id="total_meta" name="total_meta" value="'.$total.'" >';
                        echo '<input type="hidden" id="meta_mes" name="meta_mes" value="'.$total_meta.'" >';

                   ?>
		  	 	</ul>
                <br>
		    </div>
            <div class="col-md-5">
            </div>
        </div>
            <div class="row">
                <center>
                <h4>  Meta piezas Daihatsu (250)</h4>
                </center>
                <br>
<!--            </div>-->
            <div class="col-md-5">
                <div class="chart-responsive">
                  <canvas id="pieChart2" height="130"></canvas>
                </div>
            </div>
            <div class="col-md-7">
                <ul class="chart-legend clearfix">
                    <?php
                      $vendedores_list="SELECT * FROM vendedores ORDER BY codigo";
                       $j=0;
                       $totaldh=0;
                      $respuesta_list=ControladorUtilitaria::ctrMostrarUtilitariaVar($vendedores_list);

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
                          if ($value["cantidad"]<>0) {
                            echo ' <li><i class="fa fa-circle-o text-'.$colores[$j].'"></i> '.$nom_vend.' - '.$value["cantidad"].'</li>';
                              $totaldh=$totaldh+$value["cantidad"];
                            $j=$j+1;
                          }
                        }
                      }
                    echo "<h3>Total de piezas: ".number_format($totaldh,2)."</h3>";
                    ?>
                </ul>
            </div>
            <div class="col-md-5">
            </div>
		</div>
        <hr>
        <div class="row">
        <div class="col-md-4">
            <div>
                <center>
                    <h4>  Agregar Metas</h4>
                </center>
                <br>
            </div>
            <form class="form-signin" action="vistas/modulos/metas_aplicar.php" method="POST">
                <center>
                <label for="mes">Mes: </label>
                <input type=text" id="mes" name="mes" required placeholder="Mes" autofocus>
                <br><br>
                <label for="ano">Año: </label>
                <input type="text" id="ano" name="ano" required placeholder="Año" autofocus>
                <br><br>
                <label for="meta">Meta: </label>
                <input type="text" id="meta" name="meta" required placeholder="Meta" autofocus>
                <br><br>
                <button class="btn-primary" type="submit">Agregar</button>
                </center>
            </form>
        </div>
<!--  Ventas en rango de fecha-->
        <div class="col-md-4">
            <div>
                <center>
                    <h4>  Reporte de Vendedores x Fecha</h4>

                <br>
                <label for="inicio">Del: </label>
                <input id="inicio" name="inicio" type="date">
                <label for="final"> al: </label>
                <input id="final" name="final" type="date">
                <button class="btn-primary" onclick="Rep_ventas()">Buscar</button>
                </center>
                <br>
                <br>
                <hr>
                <center><h4>Autorizar Descuento</h4>
                <h4><?php echo $t_descuento ?></h4>

                <select style="width: 100px" id="descuentoESP" name="descuento">
                    <optgroup >
                        <option value="2">NO</option>;
                        <option value="1">SI</option>;
                    </optgroup>
                </select>
                <button class="btn-primary" onclick="ActualizarDescuento()">Actualizar</button>
                </center>
            </div>

            <center>

        </div>
<!--        Vendedores-->
        <div class="col-md-4">
            <div>
                <center>
                    <h4>  Mantenimiento de Vendedores</h4>
                </center>
                <br>
            </div>
            <center>
                <select   id="seleccionarVendedor" name="seleccionarVendedor" required>
                    <option value="">SELECCIONAR VENDEDOR</option>
                    <?php
                    $valor = "SELECT * FROM Vendedores ORDER BY codigo";
                    $ventas = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);
                    foreach ($ventas as $key => $value) {
                        echo '<option value="'.$value["codigo"].'">'.$value["codigo"].' - '.$value["nombre"].'</option>';
                    }
                    ?>
                </select>
                <br><br>
                <button class="btn-primary" onclick="ModAddVendedor()" >Agregar</button>&nbsp;&nbsp;<button class="btn-warning" onclick="ModVendedor()">Modificar</button>
                <hr>
                <?php
                if ($_SESSION["perfil"] == "total") {
                    echo '<button class="btn-primary" onclick="comisiones()" >Comisiones</button>';
                }
                ?>
            </center>
        </div>
        </div>
    </div>
<!--    Mantenimiento-->
    <hr>

    <div class="row">
        <div class="col-md-4">
            <div>
                <center>
                    <h4>  Utilidades</h4>
                </center>
                <br>
            </div>
            <form class="form-signin" action="vistas/modulos/metas_aplicar.php" method="POST">
                <center>
                    <label for="mes">Compañia: </label>
                    <input type=text" id="mes" name="mes" required placeholder="Mes" autofocus>
                    <br><br>
                    <label for="ano">Cedula: </label>
                    <input type="text" id="ano" name="ano" required placeholder="Año" autofocus>
                    <br><br>
                    <label for="meta">Meses analizar: </label>
                    <input type="text" id="meta" name="meta" required placeholder="Meta" autofocus>
                    <br><br>
                    <button class="btn-primary" type="submit">Agregar</button>
                </center>
            </form>

        </div>
        <div class="col-md-4">
            <div>
                <center>
                    <h4>  Consecutivos</h4>
                </center>
                <br>
            </div>
            <form class="form-signin" action="vistas/modulos/metas_aplicar.php" method="POST">
                <center>
                    <label for="mes">Factura: </label>
                    <input type=text" id="mes" name="mes" required placeholder="Mes" autofocus>
                    <br><br>
                    <label for="ano">Recibos: </label>
                    <input type="text" id="ano" name="ano" required placeholder="Año" autofocus>
                    <br><br>
                    <label for="meta">Ajustes: </label>
                    <input type="text" id="meta" name="meta" required placeholder="Meta" autofocus>
                    <br><br>
                    <button class="btn-primary" type="submit">Agregar</button>
                </center>
            </form>

        </div>
    </div>
    <!--=====================================
    MODAL MODIFICAR VENDEDOR
    ======================================-->
    <div class="modal fade" id="ModalModVendedor" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modificar Vendedor</h4>
                </div>
                <center>
                    <div style="max-height:120px" class="modal-body">
                        <h3 style="color:red;" >Modificar Vendedor</h3>
                        <input type="text" id="codigo_vendedor" name="codigo_vendedor" >
                        <input type="text" id="nombre_vendedor" name="nombre_vendedor" >
                    </div>
                </center>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary" onclick="AccVendedor()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN MODAL -->
    <!--=====================================
      MODAL AGREGAR VENDEDOR
      ======================================-->
    <div class="modal fade" id="ModalAddVendedor" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Agregar Vendedor</h4>
                </div>
                <center>
                    <div style="max-height:120px" class="modal-body">
                        <h3 style="color:red;" >Agregar Vendedor</h3>
                        <input type="text" id="Addcodigo_vendedor" name="Addcodigo_vendedor" >
                        <input type="text" id="Addnombre_vendedor" name="Addnombre_vendedor" >
                    </div>
                </center>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary" onclick="AddVendedor()">Agregar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN MODAL -->
    <!--=====================================
          MODAL REPORTE DE METAS
          ======================================-->
    <div class="modal fade" id="ModalVentas" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Ventas por vendedor</h4>
                </div>
                <center>
                    <div  class="modal-body">
                        <div id="ventas_resultado">

                        </div>
                    </div>
                </center>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN MODAL -->
    <!--=====================================
          MODAL Comisiones
          ======================================-->
    <div class="modal fade" id="ModalComisiones" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Comisiones</h4>
                </div>
                <center>
                    <div  class="modal-body">
                        <div id="comisiones_resultado">

                        </div>
                    </div>
                </center>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN MODAL -->
<script>
	

  // -------------
  // - PIE CHART -
  // -------------
  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
  var pieChart       = new Chart(pieChartCanvas);
  var PieData        = [

      <?php
            $M_vendedores= ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlT_vendedores);
            $i=0; 
            foreach ($M_vendedores as $key => $value2) {
               echo "{
                value    : ".$value2["venta"].",
                color    : '".$colores[$i]."',
                highlight: '".$colores[$i]."',
                label    : '".$value2["nombre"]."'
              },";
              $i=$i+1;
            }
        ?>
  ];

  var pieOptions     = {
    // Boolean - Whether we should show a stroke on each segment
    segmentShowStroke    : true,
    // String - The colour of each segment stroke
    segmentStrokeColor   : '#fff',
    // Number - The width of each segment stroke
    segmentStrokeWidth   : 1,
    // Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    // Number - Amount of animation steps
    animationSteps       : 100,
    // String - Animation easing effect
    animationEasing      : 'easeOutBounce',
    // Boolean - Whether we animate the rotation of the Doughnut
    animateRotate        : true,
    // Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale         : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive           : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio  : false,
    // String - A legend template
    legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
    // String - A tooltip template
    tooltipTemplate      : '<%=value %> <%=label%>'
  };
  // Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  // -----------------
  // - END PIE CHART -
  // -----------------

  var pieChartCanvas2 = $('#pieChart2').get(0).getContext('2d');
  var pieChart2       = new Chart(pieChartCanvas2);
  var PieData2        = [
  
  <?php
        $vendedores_list="SELECT * FROM vendedores ORDER BY codigo";
           $i=0;
          $respuesta_list=ControladorUtilitaria::ctrMostrarUtilitariaVar($vendedores_list);
          foreach ($respuesta_list as $key => $valor1) {
            $nom_vend=$valor1["nombre"];
            $cod_vend=$valor1["codigo"];
            /*$consulta="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '$inicio 00:00:00.000' AND h.fecha <= '$fin 23:59:00.000' AND h.vendedor like'%".(int)$cod_vend."' and h.CODIGO like '2%TW' and i.MARCA = 'DH' and h.codigo=i.CODIGO and movimiento='Venta' or movimiento = ' DEVOLUCION'";
            */
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

            foreach ($respuesta as $key => $value3) {
              if ($value3["cantidad"]<>0) {
                echo "{
                      value    : ".$value3["cantidad"].",
                      color    : '".$colores[$i]."',
                      highlight: '".$colores[$i]."',
                      label    : '".$nom_vend."'
                    },";
                    $i=$i+1;
              }
            }
          }
   ?>
   ];
  var pieOptions2     = {
    // Boolean - Whether we should show a stroke on each segment
    segmentShowStroke    : true,
    // String - The colour of each segment stroke
    segmentStrokeColor   : '#fff',
    // Number - The width of each segment stroke
    segmentStrokeWidth   : 1,
    // Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    // Number - Amount of animation steps
    animationSteps       : 100,
    // String - Animation easing effect
    animationEasing      : 'easeOutBounce',
    // Boolean - Whether we animate the rotation of the Doughnut
    animateRotate        : true,
    // Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale         : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive           : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio  : false,
    // String - A legend template
    legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
    // String - A tooltip template
    tooltipTemplate      : '<%=value %> <%=label%>'
  };
  // Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart2.Doughnut(PieData2, pieOptions2);
  // -----------------
  // - END PIE CHART -
  // -----------------
  /*=============================================
    MY AJAX HH (ejecuta AJAX)
    =============================================*/
  function myAjaxHH(url,data){
      var ajaxhh=$.ajax({
          "method":"POST",
          "url": url,
          "data": data
      });
      return ajaxhh;
      console.log(ajaxhh);
  }
function ModVendedor() {
    var vendedor= $("#seleccionarVendedor").val();

    var combo = document.getElementById("seleccionarVendedor");
    var nombre = combo.options[combo.selectedIndex].text;
    $("#codigo_vendedor").val(vendedor);
    $("#nombre_vendedor").val(nombre);
    $("#ModalModVendedor").modal();
}
function ModAddVendedor() {
  $("#ModalAddVendedor").modal();
}
function comisiones(){
      var metames = $("#meta_mes").val();
      var totalmeta= $("#total_meta").val();

      var param ={
          metames : metames,
          totalmeta : totalmeta
      };
        url="vistas/modulos/promedio/promedio_comisiones.php";
        data="";
        data=param;
        myAjaxHH(url,data)
            .done( function(data) {
                $('#comisiones_resultado').html(data);
                //console.log(data);
            });
        $("#ModalComisiones").modal();



}
function ActualizarDescuento() {
    var descuento = $("#descuentoESP").val();
    var param = {
        descuento: descuento
    };

    $.ajax({
        data: param,
        url: "vistas/modulos/descuento_mod.php",
        method: "post",
        success: function(data) {
            // Mensaje listo
            swal({
                type: "success",
                title: "El cambio fue realizado",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                if (result.value) {
                    window.location.replace('metas');
                }
            })


        }
    });

}
function Rep_ventas() {
    var inicio = $("#inicio").val();
    var final = $("#final").val();

    $('#ventas_resultado').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    var param = {
        inicio: inicio,
        fin: final
    };
    url="vistas/modulos/promedio/metas_ventas.php";
    data="";
    data=param;
    myAjaxHH(url,data)
        .done( function(data) {
            $('#ventas_resultado').html(data);
        });
    $("#ModalVentas").modal();

}

function AccVendedor(){
    var codigo = $("#codigo_vendedor").val();
    var nombre = $("#nombre_vendedor").val();

    var param = {
        codigo: codigo,
        nombre: nombre
    };

    $.ajax({
        data: param,
        url: "vistas/modulos/vendedores_mod.php",
        method: "post",
        success: function(data) {
            // Mensaje listo
            swal({
                type: "success",
                title: "El cambio fue realizado",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                if (result.value) {
                    window.location.replace('metas');
                }
            })


        }
    });

}

function AddVendedor(){
    var codigo = $("#Addcodigo_vendedor").val();
    var nombre = $("#Addnombre_vendedor").val();

    var param = {
        codigo: codigo,
        nombre: nombre
    };

    $.ajax({
        data: param,
        url: "vistas/modulos/vendedores_add.php",
        method: "post",
        success: function(data) {
            // Mensaje listo
            swal({
                type: "success",
                title: "El vendedor fue agregado correctamente",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                if (result.value) {
                    window.location.replace('metas');
                }
            })
        }
    });

}

</script>

    <script type="text/javascript" language="JavaScript">
        document.title = 'Sistema / Metas';
    </script>