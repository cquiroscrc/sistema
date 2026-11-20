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

<?php echo '<h3>'.$codigo.'</h3>'; ?>  
<table id="movimientos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">
        <thead>
            <tr>
              <th>Fecha</th>
              <th>Documento</th>
              <th>Tipo</th>
              <th>Cantidad</th>
              <th>Anterior</th>
              <th>Actual</th>
              
            </tr>
        </thead>
        <tbody>
          
          <?php

          $consulta="SELECT Factura, fecha, cantidad, movimiento, anterior, actual from hgeneral where codigo = '$codigo' 
              ORDER by fecha DESC";

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

          foreach ($ejecutar as $key => $value) {
              
            

            echo '<tr>
                    <td>'.$value["fecha"].'</td>
                    <td style="color:blue;">'.$value["Factura"].'</td>
                    <td>'.$value["movimiento"].'</td>';

                    if ($value["anterior"]>$value["actual"]) {
                      echo '<td style="color:red;"><b>'.$value["cantidad"].'</b></td>';
                    }else{
                      echo '<td style="color:green;"><b>'.$value["cantidad"].'</b></td>';
                    }
                    echo'<td>'.$value["anterior"].'</td>
                    <td>'.$value["actual"].'</td>
                    

                    

                  </tr>';
          

          }

        ?>
   
        </tbody>
        <tfoot>
            <tr>
              <th>Fecha</th>
              <th>Documento</th>
              <th>Tipo</th>
              <th>Cantidad</th>
              <th>Anterior</th>
              <th>Actual</th>
              
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
  




