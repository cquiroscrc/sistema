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
        
        <th><button class='btn btn-success ' onclick='AgregarDeposito()' ><i class='fa fa-plus'></i></button></td>
        <th><b># Depósito</b></th>
        <th><b>Cuenta</b></th>
        <th><b>Descripción</b></th>
        <th><b>Monto</b></th>
        <th><b>Check</b></th>
        </tr>
        
    <?php 
  $sql_depositos="SELECT * FROM tbl_Bancos WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND Descripcion like 'Caja del %'  order by Cuenta, Documento";  
  $totaldepositos=0;        
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_depositos);
  foreach ($respuesta as $key => $arr_depositos) {
    $ddocumento=$arr_depositos["Documento"];
    $cuenta=$arr_depositos["Cuenta"];

    
    echo '<tr><td><button class="btn btn-danger btnEditarCliente" onclick="EliminarDeposito('."'".$ddocumento."'".','."'".$cuenta."'".','."'".$fecha1."'".')"><i class="fa fa-times"></button></td>';
    
    echo '<td>'.$ddocumento.'</td> ';
    echo '<td>'.$cuenta.'</td>';
    echo '<td>'.$arr_depositos["Descripcion"].'</td>';
    echo '<td>'.number_format($arr_depositos["Credito"],2).'</td>';
    echo '<td><center><input type="checkbox"></center></td>';
    echo '</tr>';
    $totaldepositos=$totaldepositos+$arr_depositos["Credito"];
  }
  echo '<td></td><td></td><td><b>Total Depositos</b></td><td id="Rep_Total_Depositos"><b>'.number_format($totaldepositos,2).'</b></td><td></td><td></td></tr>';
          echo "</table>";
         
     ?>





