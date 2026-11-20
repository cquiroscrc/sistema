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
      return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
  }

function ultimo_mes1() { 
      $month = date('m');
      $year = date('Y');
      $day = date("d", mktime(0,0,0, $month+1, 0, $year));
 
      return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
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
            
          echo '<h3 class="box-title">Meta del Mes '.$mes_nombre.' es : '.number_format($total_meta,2).'</h3>';  
          ///////


   ?>
      <div class="box-tools pull-right">

      <button type="button" class="btn btn-box-tool" data-widget="collapse">

        <i class="fa fa-minus"></i>

      </button>

      <button type="button" class="btn btn-box-tool" data-widget="remove">

        <i class="fa fa-times"></i>

      </button>

    </div>

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
            


          $sqlT_vendedores="SELECT v.nombre,v.codigo , SUM(h.subtotal)- SUM(h.descuento) AS  venta FROM hfacturas h INNER JOIN Vendedores v on FORMAT(cast( h.vendedor as numeric),'###') = FORMAT(cast( v.codigo as numeric),'###')WHERE h.fechafact BETWEEN '$inicio 00:00:00.000' AND  '$fin 23:59:59.000'GROUP BY v.codigo, v.nombre ORDER BY venta DESC";
            //echo $sqlT_vendedores1;
            
            $TT_vendedores= ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlT_vendedores); 
            

            foreach ($TT_vendedores as $key => $valor) {
            
               echo ' <li><i class="fa fa-circle-o text-'.$colores[$i].'"></i> '.$valor["codigo"]." - ".$valor["nombre"]." = &cent;".number_format($valor["venta"],2).'</li>'; 
               $i=$i+1; 
            }
         // }  

         
       ?>

		  	 	</ul>
          <br>
		    </div>
        <div>
        
        <h4>  Meta piezas Daihatsu (250)</h4>
        <br>
        </div>
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
              $respuesta_list=ControladorUtilitaria::ctrMostrarUtilitariaVar($vendedores_list);
              
              foreach ($respuesta_list as $key => $valor) {
                $nom_vend=$valor["nombre"];
                $cod_vend=$valor["codigo"];


               /* $consulta="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '$inicio 00:00:00.000' AND h.fecha <= '$fin 23:59:00.000' AND h.vendedor like'%".(int)$cod_vend."' and (h.CODIGO like '2%TW'  or h.CODIGO like '2%EU' or h.CODIGO like '2%GR' or h.CODIGO like '2%ZL' or h.CODIGO like '2%JP') and i.MARCA = 'DH' and movimiento='Venta' and h.codigo=i.CODIGO"; 
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
               
                foreach ($respuesta as $key => $value) {
                  if ($value["cantidad"]<>0) {
                     
                    echo ' <li><i class="fa fa-circle-o text-'.$colores[$j].'"></i> '.$nom_vend.' - '.$value["cantidad"].'</li>';
                    $j=$j+1;
                    
                  }
                  
                }  
              
              }
             ?>
          
          </ul>

        </div>

		</div>

    </div>

    <div class="box-footer no-padding">
    	
		<ul class="nav nav-pills nav-stacked">

			 <?php
           echo '<h4 class="box-title">Productos mas vendidos con precio mayor 1500 en el mes de '.$mes_nombre.'</h4>'; 

            $sql_productos= "SELECT top 10 h.codigo as CODIGO,i.DESCRIPCION,i.VENTA,SUM(h.cantidad) as veces FROM hgeneral h INNER JOIN inventario i on h.codigo = i.codigo WHERE h.movimiento = 'VENTA' AND h.fecha BETWEEN '$inicio 00:00:00.000' AND  '$fin 23:59:59.000' AND precio > 1000 GROUP BY h.codigo,i.CODIGO,i.DESCRIPCION,i.VENTA ORDER BY veces DESC";

            $productos= ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_productos);
            
            $descripcion="";
            $veces=0;
            $venta=0;
            
            for($i = 0; $i <10; $i++){
              
              include "controladores/fotos.php";
             echo '<li>
             
              <a>
              <img src="'.$filename.'" class="img-thumbnail" width="60px"   >
              '.$productos[$i]["DESCRIPCION"].'

              <span class="pull-right text-'.$colores[$i].'">Cantidad:   
              '.$productos[$i]["veces"].' - Precio: &cent;'.number_format($productos[$i]["VENTA"],2).' - Total: &cent;'.number_format($productos[$i]["veces"]*$productos[$i]["VENTA"],2).'
              </span>
              
              </a>

               </li>';
               echo '<hr style="border:2px;" >
                    <hr style="border-color:#3989c6;">';
            }
            
            echo '<h4 class="box-title">Productos mas vendidos con de la tienda en el mes de '.$mes_nombre.'</h4>'; 

            $sql_productos= "SELECT top 10 h.codigo as CODIGO,i.DESCRIPCION,i.VENTA,SUM(h.cantidad) as veces FROM hgeneral h INNER JOIN inventario i on h.codigo = i.codigo WHERE h.movimiento = 'VENTA' AND h.fecha BETWEEN '$inicio 00:00:00.000' AND  '$fin 23:59:59.000' AND h.codigo like'9%ZL' GROUP BY h.codigo,i.CODIGO,i.DESCRIPCION,i.VENTA ORDER BY veces DESC";

            $productos= ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_productos);
            
            $descripcion="";
            $veces=0;
            $venta=0;
            
            for($i = 0; $i <10; $i++){
              
              include "controladores/fotos.php";
             echo '<li>
             
              <a>
              <img src="'.$filename.'" class="img-thumbnail" width="60px"   >
              '.$productos[$i]["DESCRIPCION"].'

              <span class="pull-right text-'.$colores[$i].'">Cantidad:   
              '.$productos[$i]["veces"].' - Precio: &cent;'.number_format($productos[$i]["VENTA"],2).' - Total: &cent;'.number_format($productos[$i]["veces"]*$productos[$i]["VENTA"],2).'
              </span>
              
              </a>

               </li>';
               echo '<hr style="border:2px;" >
                    <hr style="border-color:#3989c6;">';
            }

           

          	

			?>


		</ul>

    </div>

</div>

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


                $consulta="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '$inicio 00:00:00.000' AND h.fecha <= '$fin 23:59:00.000' AND h.vendedor like'%".(int)$cod_vend."' and h.CODIGO like '2%TW' and i.MARCA = 'DH' and movimiento='Venta' and h.codigo=i.CODIGO"; 

                
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

</script>