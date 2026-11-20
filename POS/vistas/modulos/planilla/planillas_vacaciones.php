<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");

$year=date("Y");

echo "<h2 style='color:red'>Vacaciones del año ".$year."</h2>";
?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<?php

          echo'
          <table id="listadohacerplanilla" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

              <thead>
                     <th style="width:10px">Id</th>
                     <th>Nombre</th>
                     <th>Fecha</th>
                     <th>Cantidad</th>
                     <th>Total</th>
                     <th>Acciones</th>
                    
              </thead>
              <tbody>';
                $nombre="";
                $sql = "SELECT v.id_empleado, v.fecha, v.cantidad, e.nombre, e.apellido from 
                vacaciones v, rh_empleados e WHERE v.id_empleado= e.id_empleado and year(v.fecha)=$year order by v.id_empleado, v.fecha";
                $total=0;
                $empleado='';
                $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

                foreach ($ejecutar as $key => $arr) {

                  $id_empleado=$arr["id_empleado"];
                    if ($empleado=='') {
                        $empleado=$id_empleado;
                    }else{

                        if ($empleado <> $id_empleado) {
                            echo '<tr>';
                            echo '<th>'.$empleado.'</th><th>'.$nombre.'</th><th></th><th></th><th>'.$total.'</th>';
                            echo "<th>";
                            echo '<button class="btn btn-success" onclick="AddVacaciones('."'".$empleado."'".','."'".$nombre."'".','."'".$total."'".')"><i class="fa fa-edit"></i></button>&nbsp;';
                            echo "</th>";
                            $total=0;
                            $empleado=$id_empleado;
                        }
                    }
                    $nombre = $arr['nombre'].' '.$arr['apellido'];
                    $fecha = $arr['fecha'];
                    $cantidad = $arr['cantidad'];
                    $total=$total+$arr['cantidad'];
                    echo '<tr>';
                    echo '<td>'.$id_empleado.'</td><td>'.$nombre.'</td><td>'.$fecha.'</td><td>'.$cantidad.'</td><td>'.$total.'</td>';
                    echo"<td>
                    <div >";
                        echo '<button class="btn btn-danger" onclick="EleVacaciones('."'".$fecha."'".','."'".$id_empleado."'".','."'".$cantidad."'".')"><i class="fa fa-crosshairs"></i></button>';
                    echo'</div></tr>';

                }
                echo '<tr>';
                echo '<th>'.$empleado.'</th><th>'.$nombre.'</th><th></th><th></th><th>'.$total.'</th>';
                echo "<th>";
                echo '<button class="btn btn-success" onclick="AddVacaciones('."'".$empleado."'".','."'".$nombre."'".','."'".$total."'".')"><i class="fa fa-edit"></i></button>&nbsp;';
                echo "</th>";

              echo'
              </tbody>
              <tfoot>
                  <tr>
                     <th style="width:10px">Id</th>
                     <th>Nombre</th>
                     <th>Fecha</th>
                     <th>Cantidad</th>
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
        "lengthMenu": [[-1],["Mostrar Todo"]],
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
  



