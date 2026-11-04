<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
 require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
  $fecha_cierre = $_REQUEST['fecha'];
}  
$fecha1=$fecha_cierre;
$fecha2=$fecha_cierre;

$sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  order by nfac";  
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$vartotal=0; $vartotalC=0;
foreach ($respuesta as $key => $arr) {
  if ($arr["Tipo"]==0){
      $vartotal= $vartotal + $arr["total"];
  }else{
      $vartotalC= $vartotalC + $arr["total"];  
  }  
}

$sql_recibos="SELECT * FROM cxc WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and NFAC like'R-%' order by NFac";  

$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_recibos);
  $totalrecibos=0;
foreach ($respuesta as $key => $arr_recibos) {
  $totalrecibos=$totalrecibos+$arr_recibos["Total"];
}
$totalrecibos=$totalrecibos*-1;

$sql_egresos="SELECT e.Fecha, e.Documento, e.Tipo, e.Descripcion, e.Monto, t.Descripcion as egreso
  FROM tbl_Egresos e, tbl_TiposEgresos t
  WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'
  AND e.Tipo = t.Tipo order by e.Tipo,e.Documento";
  
  $e_descuentos=0;
  $e_repuestos=0;
  $e_papeleria=0;
  $e_combustible=0;
  $e_atencion=0;
  $e_encomiendas=0;
  $e_luz=0;
  $e_agua=0;
  $e_telefono=0;
  $e_otros=0;
  $e_alfaro=0;
  $totalegresos=0;

$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_egresos);
foreach ($respuesta as $key => $arr_egresos) {
  $tipoE=$arr_egresos["Tipo"];
  if ($tipoE==0) {
      $e_descuentos=$e_descuentos+ $arr_egresos['Monto'];
    }
    if ($tipoE==1) {
      $e_repuestos=$e_repuestos+ $arr_egresos['Monto'];
    }
    if ($tipoE==2) {
      $e_papeleria=$e_papeleria+ $arr_egresos['Monto'];
    }
    if ($tipoE==3) {
      $e_combustible=$e_combustible+ $arr_egresos['Monto'];
    }
    if ($tipoE==4) {
      $e_atencion=$e_atencion+ $arr_egresos['Monto'];
    }
    if ($tipoE==5) {
      $e_encomiendas=$e_encomiendas+ $arr_egresos['Monto'];
    }
    if ($tipoE==6) {
      $e_luz=$e_luz+ $arr_egresos['Monto'];
    }
    if ($tipoE==7) {
      $e_agua=$e_agua+ $arr_egresos['Monto'];
    }
    if ($tipoE==8) {
      $e_telefono=$e_telefono+ $arr_egresos['Monto'];
    }
    if ($tipoE==9) {
      $e_otros=$e_otros+ $arr_egresos['Monto'];
    }
    if ($tipoE==10) {
      $e_alfaro=$e_alfaro+ $arr_egresos['Monto'];
    }

    $totalegresos=$totalegresos+$arr_egresos["Monto"];
}

$totalingresos=$vartotal+$totalrecibos;
$sql_depositos="SELECT * FROM tbl_Bancos WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND Descripcion like 'Caja del %'  order by Cuenta, Documento";  

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_depositos);
      $totaldepositos=0;
  foreach ($respuesta as $key => $arr_depositos) {
      $totaldepositos=$totaldepositos+$arr_depositos["Credito"];
  }

?>
    <table border = '1'>
    <tr>
        <th><b>Contado</b></th>
        <th><b>Credito</b></th>
        <th><b>Recibos</b></th>
        <th><b>Descuentos</b></th>
        <th><b>Repuestos</b></th>
      </tr>
<?php 
  echo "<td>".number_format($vartotal,2)."</td> ";
  echo "<td>".number_format($vartotalC,2)."</td> ";
  echo "<td>".number_format($totalrecibos,2)."</td> ";
  echo "<td>".number_format($e_descuentos,2)."</td> ";
  echo "<td>".number_format($e_repuestos,2)."</td> ";
  

 ?>   <tr>
      <tr>
        <th><b>Papeleria</b></th>
        <th><b>Combustible</b></th>
        <th><b>Atencion</b></th>
        <th><b>Encomiendas</b></th>
        <th><b>Luz</b></th>
      </tr>
 <?php 
  echo "<td>".number_format($e_papeleria,2)."</td> ";
  echo "<td>".number_format($e_combustible,2)."</td> ";
  echo "<td>".number_format($e_atencion,2)."</td> ";
  echo "<td>".number_format($e_encomiendas,2)."</td> ";
  echo "<td>".number_format($e_luz,2)."</td> ";


  ?>
      <tr>
      <tr>
        
        <th><b>Agua</b></th>
        <th><b>Telefono</b></th>
        <th><b>Otros</b></th>
        <th><b>Cierres Tarjetas</b></th>
        <th><b>Tipo Cambio</b></th>
      </tr>
    <?php 
 
  
  //$data = file_get_contents("http://apis.gometa.org/tdc/tdc.json");
  $data=file_get_contents("https://api.hacienda.go.cr/indicadores/tc/dolar");
$tc = json_decode($data, true);
$compra= $tc['compra']['valor'];
$compra=$compra-3;
  
  echo "<td>".number_format($e_agua,2)."</td> ";
  echo "<td>".number_format($e_telefono,2)."</td> ";
  echo "<td>".number_format($e_otros,2)."</td> ";
  echo "<td>".number_format($e_alfaro,2)."</td> ";
  echo "<td style='color:#ff0000;' id='tccompra' ><b>" .number_format($compra,0)."</b></td> ";
  echo "</table>";
  

  $totalcaja=$totalingresos-$totalegresos;


  ?>
  <input type="hidden" id="Rep_Total_Total" value="<?php echo number_format($totalcaja,2); ?>">


</script>