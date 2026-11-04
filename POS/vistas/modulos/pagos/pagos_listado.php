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
        <th><b>Proveedor</b></th>
        <th><b>Subtotal</b></th>
        <th><b>Total</b></th>
        <th><b>Saldo</b></th>
        <th><b>Agregar</b></th>
        </tr>
        
    <?php 
  $sql="SELECT * FROM tmpCxP order by proveedor,nfac,fechaFact";  
    $saldo_parcial=0;
    $saldo=0;
    $proveedor='';
    $cheque='';
    $nombre='';
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  foreach ($respuesta as $key => $arr) {
      
      if ($proveedor=='') {
        $proveedor=$arr['proveedor'];
      }

      if ($proveedor!=$arr['proveedor']) {
        $sql_pro="SELECT nombre FROM Proveedores WHERE codigo='$proveedor'";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pro);
        foreach ($ejecutar as $key => $value) {
          $nombre=$value['nombre'];
        }
        echo "<tr> ";
        echo "<th>".$nombre."</th> ";
        echo "<th>".number_format($saldo_parcial,2)."</th>";
        echo "<th>Cheque</th>";
        echo '<th ondblclick="AgregarPagoIndividual('."'".$proveedor."'".','."'".$nombre."'".')" >'.$cheque.'</th>';
        echo "<th>Proveedor</th>";  
        echo "<th>".$proveedor."</th>";
        echo '<th><button class="btn btn-success btnEditarCliente" onclick="ImprimirCheque('."'".$cheque."'".')"><i class=" fa fa-check-square-o"></button></th>';
        echo "</tr> ";  
        $saldo_parcial=0;
        $proveedor=$arr['proveedor'];
      }

      echo "<td>".$arr["fechaFact"]."</td> ";
      echo "<td>".$arr["nfac"]."</td>";
      echo "<td>".$arr["proveedor"]."</td>";
      $cheque=$arr['chkAsociado'];
      $proveedor=$arr['proveedor'];
      $factura=$arr['nfac'];
      echo "<td>".number_format($arr["subtotal"],2)."</td>";
      echo "<td>".number_format($arr["total"],2)."</td>";
      //    if ($arr["chkCancelacion"]==0) {
      //      $varsaldo= $varsaldo + $arr["Total"];  
      //    }
      $saldo_parcial=$saldo_parcial+$arr['total'];
      $saldo=$saldo+$arr['total'];
      $fecha='';
      echo "<td style='color:blue'>".number_format($saldo_parcial,2)."</td>";
      echo '<td><button class="btn btn-danger btnEditarCliente" onclick="EliminarPendiente('."'".$factura."'".','."'".$proveedor."'".','."'".$fecha."'".')"><i class="fa fa-times"></button></td>';
      echo "</tr> ";
    }
      $sql_pro="SELECT nombre FROM Proveedores WHERE codigo='$proveedor'";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pro);
        foreach ($ejecutar as $key => $value) {
          $nombre=$value['nombre'];
        }
      echo "<tr> ";
      echo "<th>".$nombre."</th> ";
      echo "<th>".number_format($saldo_parcial,2)."</th>";
      echo "<th>Cheque</th>";
      echo '<th ondblclick="AgregarPagoIndividual('."'".$proveedor."'".','."'".$nombre."'".')" >'.$cheque.'</th>';
      echo "<th>Proveedor</th>";
      echo "<th>".$proveedor."</th>";  
      echo '<th><button class="btn btn-success btnEditarCliente" onclick="ImprimirCheque('."'".$cheque."'".')"><i class=" fa fa-check-square-o"></button></th>';
      echo "</tr> ";  
      echo "<td>Total</td> ";
      echo "<td></td>";
      echo "<td>".number_format($saldo,2)."</td>";
      echo "<td></td>";
      echo "<td></td>";
      echo "<td></td>";  
      echo "<td></td>";
      echo "</tr> ";  
    
  
  
    echo "</table>";
         
     ?>





