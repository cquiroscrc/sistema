<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['planilla']) && !empty($_REQUEST['planilla'])){
    $planilla = $_REQUEST['planilla'];
}
if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
    $fecha = $_REQUEST['fecha'];
}
echo "<h2 style='color:red'>Planilla ".$planilla." de la fecha : ".$fecha."</h2>";
?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<?php

          echo'
          <table id="listadohacerplanilla" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

              <thead>
                     <th style="width:10px">#</th>
                     <th>Nombre</th>
                     <th>Salario</th>
                     <th>Reajuste</th>
                     <th>Extras</th>
                     <th>Comisión</th>
                     <th>Mandados</th>
                     <th>Otros Ingresos</th>
                     <th>CCSS</th>
                     <th>Retencion</th>
                     <th>Vales</th>
                     <th>Compras</th>
                     <th>Otros Egresos</th>
                     <th>Total</th>
                     <th>Acciones</th>
                     
              </thead>
              <tbody>';
                $sql = "SELECT * FROM rh_planilla WHERE id_planilla ='".$planilla."' and fecha = '".$fecha."' 
                ORDER BY id_empleado";

                $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                $i=1;

                foreach ($ejecutar as $key => $arr) {
                  $id=$arr["id_empleado"];

                  $sql2 = "SELECT nombre, apellido, id_empleado FROM rh_empleados WHERE id_empleado ='".$id."'";

                  $ejecutar2 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql2);

                  foreach ($ejecutar2 as $key => $value) {
                      $nombre = $value["nombre"] . ' ' . $value["apellido"];
                  }

                  $base=$arr["base"];
                  $reajuste=$arr["reajuste"];
                  $extras=$arr["extras"];
                  $vales=$arr["vales"];
                  $compras=$arr["compras"];
                  $oegresos=$arr["otros_egresos"];
                  $comision=$arr["comision"];
                  $mandados=$arr["mandados"];
                  $ccss=$arr["ccss"];
                  $oingresos=$arr["otros_ingresos"];
                  $retencion=$arr["retencion"];
                  $total=$arr["total"];

                  echo '<tr>
                  <td>'.$id.'</td>';
                  echo'<td>'.$nombre.'</td>';
                  echo'<td>'.number_format($base,2).'</td>';
                  echo'<td>'.number_format($reajuste,2).'</td>';
                  echo'<td>'.number_format($extras,2).'</td>';
                  echo'<td>'.number_format($comision,2).'</td>';
                  echo'<td>'.number_format($mandados,2).'</td>';
                  echo'<td>'.number_format($oingresos,2).'</td>';
                  echo'<td style="color:red">'.number_format($ccss,2).'</td>';
                  echo'<td style="color:red">'.number_format($retencion,2).'</td>';
                  echo'<td style="color:red">'.number_format($vales,2).'</td>';
                  echo'<td style="color:red">'.number_format($compras,2).'</td>';
                  echo'<td style="color:red">'.number_format($oegresos,2).'</td>';
                  echo'<td style="color:blue"><b>'.number_format($total,2).'</b></td>';
                  echo"<td>
                  <div >";
                    echo '<button class="btn btn-success" onclick="myModificar('."'".$planilla."'".','."'".$fecha."'".','."'".$id."'".')"><i class="fa fa-edit"></i></button>&nbsp;';
                    echo '<button class="btn btn-danger" onclick="myRetencion('."'".$planilla."'".','."'".$fecha."'".','."'".$id."'".')"><i class="fa fa-crosshairs"></i></button>';
                  echo'</div></tr>';
                  $i=$i+1;
                }

              echo'
              </tbody>
              <tfoot>
                  <tr>
                     <th style="width:10px">#</th>
                     <th>Nombre</th>
                     <th>Salario</th>
                     <th>Reajuste</th>
                     <th>Extras</th>
                     <th>Comisión</th>
                     <th>Mandados</th>
                     <th>Otros Ingresos</th>
                     <th>CCSS</th>
                     <th>Retencion</th>
                     <th>Vales</th>
                     <th>Compras</th>
                     <th>Otros Egresos</th>
                     <th>Total</th>
                     <th>Acciones</th>
                  </tr>
              </tfoot>
          </table>';
 ?>


  <script>
    $(document).ready(function() {

      var table = $('#listadohacerplanilla').DataTable({
        "deferRender": true,
        "retrieve": true,
        "processing": true,
        "lengthMenu": [[15,20, -1],[15,50,"Mostrar Todo"]],
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
  



