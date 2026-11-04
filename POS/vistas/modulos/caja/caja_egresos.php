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
        <th><button class='btn btn-success '  onclick='AgregarEgresos()'><i class='fa fa-plus'></i></button></th>
        <th><b># Egreso</b></th>
        <th><b>Tipo</b></th>
        <th><b>Descripción Egreso</b></th>
        <th><b>Descripción</b></th>
        <th><b>Monto</b></th>
        <th><b>Check</b></th>
        </tr>
        
    <?php 
          $sql_egresos="SELECT e.Fecha, e.Documento, e.Tipo, e.Descripcion, e.Monto, t.Descripcion as egreso FROM tbl_Egresos e, tbl_TiposEgresos t WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND e.Tipo = t.Tipo order by e.Tipo,e.Documento";
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

            $documento=$arr_egresos["Documento"];
            $tipoE=$arr_egresos["Tipo"];
            echo '<tr><td><button class="btn btn-danger btnEditarCliente" onclick="EliminarEgreso('."'".$documento."'".','."'".$tipoE."'".','."'".$fecha1."'".')"><i class="fa fa-times"></button></td>';
    
            echo '<td>'.$documento.'</td> ';
            echo '<td>'.$tipoE.'</td>';
            echo '<td>'.$arr_egresos["egreso"].'</td>';
            echo '<td>'.$arr_egresos["Descripcion"].'</td>';
            echo '<td>'.number_format($arr_egresos["Monto"],2).'</td>';
            echo '<td><center><input type="checkbox"></center></td>';
            echo '</tr>';
            //**** totales de engresos
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
          echo '<td></td><td><b>Total Egresos</b></td><td></td><td id="Rep_Total_Egresos"><b>'.number_format($totalegresos,2).'</b></td><td></td><td></td><td></td></tr>';
          echo "</table>";
         
