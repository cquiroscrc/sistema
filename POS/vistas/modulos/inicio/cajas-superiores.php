<?php



$item = null;
$valor = null;
$orden = "id";

$ventas = ControladorVentas::ctrSumaTotalVentas();


// Funciones para Inicio y Fin de mes
function primer_mes() {
      $month = date('m');
      $year = date('Y');
      return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
  }

function ultimo_mes() { 
      $month = date('m');
      $year = date('Y');
      $day = date("d", mktime(0,0,0, $month+1, 0, $year));
 
      return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
      };

$inicio=primer_mes();      
 
$fin=ultimo_mes();


// Funcion Metas

$valor1="SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                 .$inicio." 00:00:00.000' AND fechafact <= '".$fin." 23:59:00.000' AND impuesto <> 0";
             
$totalMetas = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor1);

foreach ($totalMetas as $key => $value) {

  $metas = $value["venta"];
} 

        $tabla1= "fechas";
        $item = null;
        $valor = null;

        $fechas = ControladorUtilitaria::ctrMostrarUtilitaria($tabla1,$item, $valor);
    foreach ($fechas as $key => $value) {
                      
         $fecha=$value["Fecha"];
        }
        
        $fecha=strtotime($fecha);

        $fecha = date("m/d/Y", $fecha);

// Meta diaria

$valor3="SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                 .$fecha." 00:00:00.000' AND fechafact <= '".$fecha." 23:59:00.000' AND impuesto <> 0";
                    

$metadiaria = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor3);

foreach ($metadiaria as $key => $value) {

  $metahoy = $value["venta"];
}

// Total Mensual de Ventas

$valor2="SELECT SUM(total) as total FROM hfacturas WHERE fechafact BETWEEN '$inicio 00:00:00.000' AND '$fin 23:59:59.000'";             

$totalVentas = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor2);

foreach ($totalVentas as $key => $value) {

  $ventasMensual = $value["total"];
}  


// Funcion total de Inventario

$ValorInventario= "SELECT SUM(COSTO*EXISTENCIA) AS TOTAL FROM INVENTARIO WHERE ubicacion NOT LIKE '9%'";

$totalInventario = ControladorUtilitaria::ctrMostrarUtilitariaVar($ValorInventario);
foreach ($totalInventario as $key => $value) {
 
  $Inventario = $value["TOTAL"];
}  

?>



<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-aqua">
    
    <div class="inner">
      
      <h4><?php echo '&cent;'. number_format($ventas["total"],2); ?></h4>

      <p>Ventas</p>
    
    </div>
    
    <div class="icon">
     
      <i class="ion ion-cash"></i>
    
    </div>
    
    <a href="ventas" class="small-box-footer">
      
      Más info <i class="fa fa-arrow-circle-right"></i>
    
    </a>

  </div>

</div>

<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-green">
    
    <div class="inner">
    
      
      <h4><?php echo '&cent;'. number_format($metas,2); ?></h4>
      
      
      <p>Metas de hoy <?php echo '&cent;'. number_format($metahoy,2); ?></p>
    
    </div>
    
    <div class="icon">
    
      <i class="ion ion-clipboard"></i>
    
    </div>
    
    <a href="metas" class="small-box-footer">
      
      Mes anterior <i class="fa fa-arrow-circle-right"></i>
    
    </a>

  </div>

</div>

<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-yellow">
    
    <div class="inner">
    
      <h4><?php echo '&cent;'.number_format($ventasMensual,2); ?></h4>

      <p>Total de ventas del Mes</p>
  
    </div>
    
    <div class="icon">
    
      <i class="ion ion-android-calendar"></i>
    
    </div>
    
    <a href="reportes" class="small-box-footer">

      Más info <i class="fa fa-arrow-circle-right"></i>

    </a>

  </div>

</div>

<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-red">
  
    <div class="inner">
    
      <h4><?php echo '&cent;'.number_format($Inventario,2); ?></h4>

      <p>Total Inventario</p>
    
    </div>
    
    <div class="icon">
      
      <i class="ion ion-ios-cart"></i>
    
    </div>
    
    <a href="extensiones/tcpdf/pdf/Total_inventario.php" target="_blank" class="small-box-footer">
      
      Más info <i class="fa fa-arrow-circle-right"></i>
    
    </a>

  </div>

</div>