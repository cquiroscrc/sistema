<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");
?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<?php
          echo'
          <table id="listado" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

              <thead>
                     <th style="width:10px">#</th>
                     <th>Planilla</th>
                     <th>Fecha</th>
                     <th>Total</th>
                     <th>Acciones</th>
                     
              </thead>
              <tbody>';
                $sql="SELECT DISTINCT id_planilla,fecha, sum(total) as total FROM rh_planilla 
                    GROUP BY id_planilla,fecha ORDER BY fecha desc";

                $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                $i=1;

                foreach ($ejecutar as $key => $arr) {
                  $fecha=$arr["fecha"];
                  $planilla=$arr["id_planilla"];
                  $total=$arr["total"];

                  echo '<tr>
                  <td>'.$i.'</td>';
                  echo'<td>'.$planilla.'</td>';
                  echo'<td>'.$fecha.'</td>';
                  echo'<td>'.number_format($total,2).'</td>';
                  echo"<td>
                  <div >";
                  echo '<button class="btn btn-success" onclick="myCarga('."'".$planilla."'".','."'".$fecha."'".','."'".$total."'".')"><i class="fa fa-edit"></i></button>&nbsp;';
                  echo '<button class="btn btn-primary" onclick="myReporte('."'".$planilla."'".','."'".$fecha."'".','."'".$total."'".')"><i class="fa fa-file-pdf-o"></i></button>&nbsp;';
                  echo '<button class="btn btn-danger" onclick="myBorrar('."'".$planilla."'".','."'".$fecha."'".','."'".$total."'".')"><i class="fa fa-calendar-times-o"></i></button>';
                  echo'</div></tr>';
                  echo'</tr>';
                  $i=$i+1;
                }

              echo'
              </tbody>
              <tfoot>
                  <tr>
                     <th style="width:10px">#</th>
                     <th>Planilla</th>
                     <th>Fecha</th>
                     <th>Total</th>
                     <th>Acciones</th>
                  </tr>
              </tfoot>
          </table>';
 ?>



  <script>
    $(document).ready(function() {

      var table = $('#listado').DataTable({
        "deferRender": true,
        "retrieve": true,
        "processing": true,
        "lengthMenu": [[10,20, -1],[10,50,"Mostrar Todo"]],
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
            }
       } 
      });


  //for table row
    
 
    } );

  </script>
  



