<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");
 ?>
<style>
  table.dataTable tr.odd { background-color:#3c8dbc; }
  table.dataTable tr.odd { color:white; }

  table.dataTable tr.even { background-color: white; }
</style>

  
<table  id="encomiendas" class="display" style="width:100%">
        <thead>
            <tr>
              <th>Fecha</th>
              <th>Documento</th>
              <th>Descripcion</th>
              <th>Monto</th>
            </tr>
        </thead>
        <tbody>
          
          <?php
              $tabla1= "fechas";
              $item = null;
              $valor = null;
              
              $fechas = ControladorUtilitaria::ctrMostrarUtilitaria($tabla1,$item, $valor);
              foreach ($fechas as $key => $value) {
                              
                 $fecha=$value["Fecha"];
              }
                
              $fecha2=$fecha;
              $fecha1=strtotime('-5 day', strtotime($fecha));
              $fecha1 = date('Y/m/d 00:00:00.000', $fecha1);
          
              $consulta = "SELECT * FROM tbl_Egresos WHERE Tipo=5 AND Fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY Fecha";
             
              $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

          foreach ($ejecutar as $key => $value) {
             $fechaE=strtotime($value["Fecha"]);
             $fechaE=date("d/m/Y" ,$fechaE) ;
            

            echo '<tr>
                    
                    <td style="text-align:left; color:black;">'.$fechaE.'</td>

                    <td style="text-align:right; color:red;"><b>'.$value["Documento"].'</b></td>

                    <td>'.$value["Descripcion"].'</td>

                    <td style="text-align:right;">'.number_format($value["Monto"],2).'</td>

                  </tr>';
          

          }

        ?>
   
        </tbody>
        <tfoot>
            <tr>
              <th>Fecha</th>
              <th>Documento</th>
              <th>Descripcion</th>
              <th>Monto</th>
            </tr>
        </tfoot>
    </table>
    
  <script>
    $(document).ready(function() {

      var table = $('#encomiendas').DataTable({
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
            }
       } 
      });


  //for table row
    
 
    } );

  </script>
  




