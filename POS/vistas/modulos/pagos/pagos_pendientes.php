<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
 require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
  $proveedor = $_REQUEST['proveedor'];
}  

?>


    <table border = '1'>
    <tr>
        
       
        <th><b>Fecha</b></th>
        <th><b>Factura</b></th>
        <th><b>Subtotal</b></th>
        <th><b>Impuesto</b></th>
        <th><b>Descuento</b></th>
        <th><b>Total</b></th>
        <th><b>Saldo</b></th>
        <th><b>Agregar</b></th>
        </tr>
        
    <?php 
  $sql="SELECT * FROM cxp WHERE Proveedor='$proveedor' and Estado='Pendiente' order by FechaFact,NFac";  
  $vartotal=0;
  $varsaldo=0;
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  foreach ($respuesta as $key => $value) {
    $fecha=$value['FechaFact'];
    $factura=$value['NFac'];
    $subtotal=$value['SubTotal'];
    $impuesto=$value['Impuesto'];
    $descuento=$value['Descuento'];
    $total=$value['Total'];
    $cheque=$value['chkCancelacion'];
    
    
    
    echo '<td>'.$fecha.'</td> ';
    echo '<td>'.$factura.'</td>';
    echo '<td>'.number_format($subtotal,2).'</td>';
    echo '<td>'.number_format($impuesto,2).'</td>';
    echo '<td>'.number_format($descuento,2).'</td>';
    echo '<td>'.number_format($total,2).'</td>';
    $vartotal=$vartotal+$total;
    if ($cheque==0) {
     $varsaldo=$varsaldo+$total;
    }
    echo "<td style='color:blue'>".number_format($varsaldo,2)."</td>";
    $esta='no';
    $sql_tmp="SELECT * FROM tmpCxP WHERE proveedor='$proveedor' and nfac='$factura'";  

    $respuesta1 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tmp);
    foreach ($respuesta1 as $key => $value) {
      $esta='si';
    }

    if ($esta=='si') {
      echo '<td><button class="btn btn-danger btnEditarCliente" onclick="EliminarPendiente('."'".$factura."'".','."'".$proveedor."'".','."'".$fecha."'".')"><i class="fa fa-times"></button></td>';
    }else{
      echo '<td><button class="btn btn-success btnEditarCliente" onclick="AgregarPendiente('."'".$factura."'".','."'".$proveedor."'".','."'".$fecha."'".')"><i class="fa fa-plus"></button></td>'; 
    }

    echo '</tr>';
    
  }
  
    echo "</table>";
         
     ?>





