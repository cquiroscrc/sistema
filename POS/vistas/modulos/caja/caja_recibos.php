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

?>

    <table border = '1'>
    <tr>
        <th><button class='btn btn-success '  onclick='AgregarRecibos()'><i class='fa fa-plus'></i></button></th>
        <th><b># Recibo</b></th>
        <th><b>Cliente</b></th>
        <th><b>Monto</b></th>
        </tr>
       
    <?php 
          $sql_recibos="SELECT * FROM cxc WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and NFAC like'R-%' order by NFac";  
          $totalrecibos=0;
          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_recibos);
          foreach ($respuesta as $key => $arr_recibos) {
            $fact=$arr_recibos["NFac"];
            $cliente=$arr_recibos["IDCliente"];
            echo '<tr><td><button class="btn btn-danger btnEditarCliente" onclick="EliminarRecibo('."'".$cliente."'".','."'".$fact."'".','."'".$fecha1."'".')"><i class="fa fa-times"></button></td>';

            echo '<td>'.$fact.'</td> ';
            echo '<td>'.$cliente.'</td>';
            echo '<td>'.number_format($arr_recibos["Total"],2).'</td>';
            echo '</tr>';
            $totalrecibos=$totalrecibos+$arr_recibos["Total"];
            
          }

          echo '<td></td><td></td><td><b>Total Recibos</b></td><td id="Rep_Total_Recibos"><b>'.number_format($totalrecibos,2).'</b></td></tr>';
          echo "</table>";
         
     ?>





