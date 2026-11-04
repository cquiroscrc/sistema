<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];
          
        } 
 ?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<?php echo '<h3>Saldo del cliente '.$codigo.'</h3>'; ?>
<table id="movimientos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">
        <thead>
            <tr>
              <th>Fecha</th>
              <th>Saldo</th>
              <th>Factura</th>
              <th>Total</th>
              <th>Subtotal</th>
              <th>Impuesto</th>
              <th>Descuento</th>
            </tr>
        </thead>
        <tbody>
          
          <?php
          $sql="SELECT * FROM cxc WHERE IDCliente='$codigo'  order by Fecha,Nfac";

          $varsaldo =0;
          $Valtotal =0;
          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

          foreach ($ejecutar as $key => $value) {
              $Valtotal=$value["Total"];
              if (substr($value["NFac"],0,1)=="R") {
                  $Valtotal=$Valtotal*-1;
              }
              if (substr($value["NFac"],0,1)=="D") {
                  if($Valtotal>0){
                  $Valtotal=$Valtotal*-1;}
              }

              $varsaldo= $varsaldo + $Valtotal;
            echo '<tr>
                    <td>'.$value["Fecha"]. '</td>
                    <td style="color:#fc050d;">' .number_format($varsaldo,2). '</td>
                    <td style="color:#0000ff;">' .$value["NFac"].'</td>';
                    echo '<td> '.number_format($Valtotal,2). '</td>';
                    echo '<td>'.number_format($value["SubTotal"],2).'</td>';
                    echo'<td>'.number_format($value["Impuesto"],2).'</td>
                    <td>'.number_format($value["Descuento"],2).'</td>';



                    echo'</tr>';
          }

        ?>
   
        </tbody>
        <tfoot>
            <tr>
                <th>Fecha</th>
                <th>Saldo</th>
                <th>Factura</th>
                <th>Total</th>
                <th>Subtotal</th>
                <th>Impuesto</th>
                <th>Descuento</th>
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
  




