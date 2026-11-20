<?php

error_reporting(0);




function primer_mesgraf() {
      $month = date('m');
      $month = $month -1;
      $year = date('Y');
      $year = $year-1 ;
      return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
  }


$fechaInicial=primer_mesgraf();      
$fechaFinal=ultimo_mes();

$respuesta = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal);

$arrayFechas = array();
$arrayVentas = array();
$sumaPagosMes = array();
$promedio=0;
foreach ($respuesta as $key => $value) {

	#Capturamos sólo el año y el mes
	$fecha = substr($value["fechafact"],0,7);

	#Introducir las fechas en arrayFechas
	array_push($arrayFechas, $fecha);

	#Capturamos las ventas
	$arrayVentas = array($fecha => $value["total"]);
  $promedio=$promedio+$value["total"];
	#Sumamos los pagos que ocurrieron el mismo mes
	foreach ($arrayVentas as $key => $value) {
		
		$sumaPagosMes[$key] += $value;
	}

}


$noRepetirFechas = array_unique($arrayFechas);

$promedio=$promedio/14;

?>

<!--=====================================
GRÁFICO DE VENTAS
======================================-->


<div class="box box-solid bg-teal-gradient">
	
	<div class="box-header">
		
 		<i class="fa fa-th"></i>

  		<h3 class="box-title">Gráfico de Ventas Paticas / Promedio Mensual <?php echo number_format($promedio,2); ?></h3>
      <div class="box-tools pull-right">

       

    </div>

	</div>

	<div class="box-body border-radius-none nuevoGraficoVentas">

		<div class="chart" id="line-chart-ventas" style="height: 250px;"></div>

  </div>

</div>

<script>
	
 var line = new Morris.Line({
    element          : 'line-chart-ventas',
    resize           : true,
    data             : [

    <?php
    
    if($noRepetirFechas != null){

	    foreach($noRepetirFechas as $key){
        $dato=$sumaPagosMes[$key]-$promedio;
	    	echo "{ y: '".$key."', Ventas: ".$sumaPagosMes[$key].",Promedio: ".(round($dato,2))." },";

        

	    }
      $dato=$sumaPagosMes[$key]-$promedio;
	    echo "{y: '".$key."', Ventas: ".$sumaPagosMes[$key].",Promedio: ".(round($dato,2))." }";

    }else{

       echo "{ y: '0', Ventas: '0' }";

    }

    ?>

    ],
    xkey             : 'y',
    ykeys            : ['Ventas','Promedio'],
    labels           : ['Ventas','Promedio'],
    lineColors       : ['#efefef','#E65A26'],
    lineWidth        : 2,
    hideHover        : 'auto',
    gridTextColor    : '#fff',
    gridStrokeWidth  : 0.4,
    pointSize        : 4,
    pointStrokeColors: ['#efefef'],
    gridLineColor    : '#efefef',
    gridTextFamily   : 'Open Sans',
    preUnits         : '₵',
    gridTextSize     : 10
  });

</script>