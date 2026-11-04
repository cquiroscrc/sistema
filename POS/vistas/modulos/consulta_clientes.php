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


  
<table id="example" class="display" style="width:100%">
        <thead>
            <tr>
              <th>Codigo</th>
              <th>Nombre</th>
              <th>Cedula</th>
              <th>Email</th>
              <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
          
          <?php

          

          $consulta = "SELECT codigo,nombre,cedula,correo,telefono FROM Clientes WHERE comentario <> '**DESACTIVADO**' ORDER BY codigo";

          $clientes = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

          foreach ($clientes as $key => $value) {
            

            echo '<tr>
                    <td>
                      <div class="btn-group">
                            
                          <button class="btn btn-success">'.$value["codigo"].'</button>
                      </div>  
                    </td>

                    <td>'.$value["nombre"].'</td>

                    <td>'.$value["cedula"].'</td>

                    <td>'.$value["correo"].'</td>

                    <td>'.$value["telefono"].'</td>

                  </tr>';
          

            }

        ?>
   
        </tbody>
        <tfoot>
            <tr>
                <th>Codigo</th>
               <th>Nombre</th>
               <th>Cedula</th>
               <th>Email</th>
               <th>Teléfono</th>
            </tr>
        </tfoot>
    </table>
  <script>
    $(document).ready(function() {
      var table = $('#example').DataTable({
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
       
        $('#example tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            // data[x] 0 boton y codigo, 1 nombre, 2 cedula
            //alert( 'You clicked on '+data[2]+'\'s row' );
            //console.log(data);
            myCliente(data[2],data[1],data[3],data[0]);
            
        } );
    } );

  </script>
  




