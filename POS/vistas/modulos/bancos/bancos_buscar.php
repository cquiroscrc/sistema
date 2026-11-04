<?php
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['busqueda']) && !empty($_REQUEST['busqueda'])){
  $busqueda = $_REQUEST['busqueda'];
} 
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
  $tipo = $_REQUEST['tipo'];
}

if ($tipo=='descripcion'){
    $sql="SELECT b.Fecha,b.Cuenta,b.Documento,b.Descripcion,b.Credito,b.Debito, c.Banco FROM tbl_Bancos b, tbl_Cuentas c WHERE 
    b.Descripcion like '%$busqueda%' AND b.Cuenta= c.Cuenta ORDER BY b.Fecha,b.Descripcion";

}
if ($tipo=='documento'){
    $sql="SELECT b.Fecha,b.Cuenta,b.Documento,b.Descripcion,b.Credito,b.Debito, c.Banco FROM tbl_Bancos b, tbl_Cuentas c WHERE 
    b.Documento like '%$busqueda%' AND b.Cuenta= c.Cuenta ORDER BY b.Fecha,b.Descripcion";
}
if ($tipo=='monto'){
    $sql="SELECT b.Fecha,b.Cuenta,b.Documento,b.Descripcion,b.Credito,b.Debito, c.Banco FROM tbl_Bancos b, tbl_Cuentas c WHERE
    b.Credito=$busqueda or b.Debito = $busqueda  AND b.Cuenta= c.Cuenta ORDER BY b.Fecha,b.Descripcion";
}

?>

<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<?php echo '<h3>'.$busqueda.'</h3>'; ?>
<table id="movimientos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">
        <thead>
            <tr>
              <th>Fecha</th>
              <th>Cuenta</th>
              <th>Nombre</th>
              <th>Documento</th>
              <th>Descripcion</th>
              <th>Credito</th>
              <th>Debito</th>
            </tr>
        </thead>
        <tbody>

          <?php

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

          foreach ($ejecutar as $key => $arr) {
            $fecha_sis=strtotime($arr['Fecha']);
            echo '<tr>
                    <td>'.date("d-m-Y", $fecha_sis).'</td>
                    <td>'.$arr["Cuenta"].'</td>
                    <td>'.$arr["Banco"].'</td>';
                    echo '<td>'.$arr["Documento"].'</td>';
                    echo '<td>'.$arr["Descripcion"].'</td>';
                    if ($arr['Credito']>0) {
                        echo "<td style='color:blue'>".number_format($arr['Credito'],2)."</td>";
                    }else{
                        echo "<td>".number_format($arr['Credito'],2)."</td>";
                    }
                    if ($arr['Debito']>0) {
                        echo "<td style='color:red'>".number_format($arr['Debito'],2)."</td>";
                    }else{
                        echo "<td>".number_format($arr['Debito'],2)."</td>";
                    }
                    echo '</tr>';
          }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Fecha</th>
                <th>Cuenta</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Descripcion</th>
                <th>Credito</th>
                <th>Debito</th>
            </tr>
        </tfoot>
    </table>

  <script>
    $(document).ready(function() {

      var table = $('#movimientos').DataTable({
        "deferRender": true,
        "retrieve": true,
        "processing": true,
        "lengthMenu": [[5,10,20, -1],[5,10,50,"Mostrar Todo"]],
        "language": {

            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
            "sFirst":    "Primero",
            "sLast":     "Último",
            "sNext":     "Siguiente",
            "sPrevious": "Anterior"
            },
            "oAria": {
              "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
          "buttons": {
              "copy": "Copiar",
              "colvis": "Visibilidad"
          }
       }
      });


  //for table row


    } );

  </script>

