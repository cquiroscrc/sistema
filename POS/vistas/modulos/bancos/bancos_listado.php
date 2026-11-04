<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['cuenta']) && !empty($_REQUEST['cuenta'])){
    $cuenta = $_REQUEST['cuenta'];
} 

?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<?php 


    $sql="SELECT TOP 450 b.Fecha, b.Cuenta, b.Documento, b.Descripcion, b.Credito, b.Debito, c.Banco 
    FROM tbl_Bancos b, tbl_Cuentas c WHERE b.Cuenta = c.Cuenta and b.Cuenta='$cuenta' ORDER BY Fecha DESC";

          echo'
          <table id="listado" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

              <thead>
                     <th style="width:10px">#</th>
                     <th>Fecha</th>
                     <th>Cuenta</th>
                     <th>Nombre</th>
                     <th>Documento</th>
                     <th>Descripción</th>
                     <th>Crédito</th>
                     <th>Débito</th>
                     <th>Acciones</th>
              </thead>
              <tbody>';
                
                $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                $i=1;

                foreach ($ejecutar as $key => $arr) {
                  $fecha=$arr["Fecha"];
                  $cuentab=$arr["Cuenta"];
                  $nombre=$arr["Banco"];
                  $documento=$arr["Documento"];
                  $descripcion=$arr["Descripcion"];
                  $credito = $arr["Credito"];
                  $debito = $arr["Debito"];

                  echo '<tr>
                  <td>'.$i.'</td>';
                  echo'<td>'.$fecha.'</td>';
                  echo'<td>'.$cuentab.'</td>';
                  echo'<td>'.$nombre.'</td>';
                  echo'<td>'.$documento.'</td>';
                  echo'<td>'.$descripcion.'</td>';
                  echo'<td style="color:blue">'.number_format($credito,2).'</td>';
                  echo'<td style="color:red">'.number_format($debito,2).'</td>';
                  echo"<td>
                  <div class='btn-group'>";
                  echo '<button class="btn btn-danger" onclick="EliminaBancos('."'".$documento."'".','."'".$fecha."'".','."'".$cuentab."'".')"><i class="fa fa-times"></i></button></div>';
                 echo'</tr>';
                  $i=$i+1;

                }

              echo'
              </tbody>
              <tfoot>
                  <tr>
                     <th style="width:10px">#</th>
                     <th>Fecha</th>
                     <th>Cuenta</th>
                     <th>Nombre</th>
                     <th>Documento</th>
                     <th>Descripción</th>
                     <th>Crédito</th>
                     <th>Débito</th>
                     <th>Acciones</th>
                  </tr>
              </tfoot>
          </table>
         <table border=1>';

 ?>



  <script>
    $(document).ready(function() {

      var table = $('#listado').DataTable({
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
  



