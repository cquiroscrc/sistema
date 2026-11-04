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

  
<table  id="telefonos" class="display" style="width:100%">
        <thead>
            <tr>
              <th>Descripcion</th>
              <th>Telefono</th>
              <th>Email</th>
            </tr>
        </thead>
        <tbody>
          
          <?php

          $consulta = "SELECT * FROM tbl_TEL_Numeros ORDER BY Descripcion";

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

          foreach ($ejecutar as $key => $value) {
              
            

            echo '<tr>

                    <td>'.$value["Descripcion"].'</td>

                    <td>'.$value["Numero"].'</td>

                    <td style="text-align:right;">'.$value["Correo"].'</td>

                  </tr>';
          

          }

        ?>
   
        </tbody>
        <tfoot>
            <tr>
              <th>Descripcion</th>
              <th>Telefono</th>
              <th>Email</th>
            </tr>
        </tfoot>
    </table>
    <div>
        <br>
        <form  role="form" method="post">
        <label for="fname">Descripcion:</label>
        <input type="text" id="tdescripcion" name="tdescripcion" required>
        <label for="lname">Telefono:</label>
        <input type="text" id="ttelefono" name="ttelefono" required><br>
        <label align="left" for="lname">Email:</label>
        <input type="text" id="tcorreo" name="tcorreo">
        
        
        </form> 
    </div>
  <script>
    $(document).ready(function() {

      var table = $('#telefonos').DataTable({
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
  




