<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['mensajero']) && !empty($_REQUEST['mensajero'])){
    $id_mensajero = $_REQUEST['mensajero'];      
} 

?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<?php 

  if ($id_mensajero=='3') {
    $sql="SELECT * FROM mandados where aplicado is null  ORDER BY fecha DESC";
    $mandado=0;
    $sqlmensa = "SELECT * FROM mensajeros";
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlmensa);
    foreach ($ejecutar as $key => $arrmensa) {
        $mandado=$arrmensa['mandado'];
        $id_mensajero2=$arrmensa['id'];
        $nombre=$arrmensa['nombre'];
        $sql="SELECT * FROM mandados where aplicado is null AND motorizado='$id_mensajero2' ORDER BY fecha DESC";
        
             echo '<h3 style="color:red;"><b>'.$nombre.'</b></h3><br>'; 

             echo' 
              <table id="listado'.$id_mensajero2.'" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

                      <thead>
                             <th style="width:10px">#</th>
                             <th>Fecha</th>
                             <th>Factura</th>
                             <th>Lugar</th>
                             <th>Monto</th>
                             <th>Cantidad</th>
                             <th>Envia</th>
                             <th>Acciones</th>
                             
                      </thead>
                      <tbody>';
                        
                        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                        $i=1;
                        $mandados=0;
                        $unidades=0;
                        $subtotal=0;
                        foreach ($ejecutar as $key => $arr) {
                          $fecha=$arr["fecha"];
                          $factura=$arr["factura"];
                          $lugar=$arr["lugar"];
                          $monto=$arr["monto"];
                          $cantidad=$arr["cantidad"];
                          $envia = $arr["envia"];
                          $mensajero=$arr["motorizado"];
                          $mandados=$mandados+$arr['cantidad'];
                          $subtotal=$subtotal+($arr['cantidad']-$mandado);
                          if ($cantidad==0) {
                            //no suma
                          }else{
                            $unidades++;      
                          }
                          
                          

                          echo '<tr>
                          <td>'.$i.'</td>';
                          echo'<td>'.$fecha.'</td>';
                          echo'<td>'.$factura.'</td>';
                          echo'<td>'.$lugar.'</td>';
                          echo'<td>'.$monto.'</td>';

                          if ($cantidad==0) {
                            echo '<td style="color:red;"><b>'.number_format($cantidad,2).'</b></td>';
                          }else{
                          echo '<td style="color:blue;"><b>'.number_format($cantidad,2).'</b></td>';
                          }
                          echo'<td>'.$envia.'</td>';
                          
                          
                          echo"<td>
                          <div class='btn-group'>";
                          echo '<button class="btn btn-danger" onclick="myElimina('."'".$factura."'".','."'".$fecha."'".','."'".$mensajero."'".')"><i class="fa fa-times"></i></button></div>'; 

                          
                          echo'</tr>';
                          $i=$i+1;

                        }

                      echo'
                      </tbody>
                      <tfoot>
                          <tr>
                             <th style="width:10px">#</th>
                             <th>Fecha</th>
                             <th>Factura</th>
                             <th>Lugar</th>
                             <th>Monto</th>
                             <th>Cantidad</th>
                             <th>Envia</th>
                             <th>Acciones</th>
                          </tr>
                      </tfoot>
                  </table>
                 <table border=1>';
                   echo '<h3 style="color:blue;"><b>Mandados = '.number_format($mandados,2).' Unidades = '.number_format($unidades,2).' Total = '.number_format($subtotal,2).'</b></h3>';
                   echo'</table>';

    }

  }else{
    $sql="SELECT * FROM mandados where aplicado is null AND motorizado='$id_mensajero' ORDER BY fecha DESC"; 
    $sqlmensa = "SELECT * FROM mensajeros WHERE id='$id_mensajero'";
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlmensa);
    foreach ($ejecutar as $key => $arrmensa) {
      $mandado=$arrmensa['mandado'];
    }    
  

          echo'
          <table id="listado1" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

              <thead>
                     <th style="width:10px">#</th>
                     <th>Fecha</th>
                     <th>Factura</th>
                     <th>Lugar</th>
                     <th>Monto</th>
                     <th>Cantidad</th>
                     <th>Envia</th>
                     <th>Acciones</th>
                     
              </thead>
              <tbody>';
                
                $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                $i=1;
                $mandados=0;
                $unidades=0;
                $subtotal=0;
                foreach ($ejecutar as $key => $arr) {
                  $fecha=$arr["fecha"];
                  $factura=$arr["factura"];
                  $lugar=$arr["lugar"];
                  $monto=$arr["monto"];
                  $cantidad=$arr["cantidad"];
                  $envia = $arr["envia"];
                  $mensajero=$arr["motorizado"];
                  $mandados=$mandados+$arr['cantidad'];
                  $subtotal=$subtotal+($arr['cantidad']-$mandado);
                  if ($cantidad==0) {
                    //no suma
                  }else{
                    $unidades++;      
                  }
                  
                  

                  echo '<tr>
                  <td>'.$i.'</td>';
                  echo'<td>'.$fecha.'</td>';
                  echo'<td>'.$factura.'</td>';
                  echo'<td>'.$lugar.'</td>';
                  echo'<td>'.$monto.'</td>';

                  if ($cantidad==0) {
                    echo '<td style="color:red;"><b>'.number_format($cantidad,2).'</b></td>';
                  }else{
                  echo '<td style="color:blue;"><b>'.number_format($cantidad,2).'</b></td>';
                  }
                  echo'<td>'.$envia.'</td>';
                  
                  
                  echo"<td>
                  <div class='btn-group'>";
                  echo '<button class="btn btn-danger" onclick="myElimina('."'".$factura."'".','."'".$fecha."'".','."'".$mensajero."'".')"><i class="fa fa-times"></i></button></div>'; 

                  
                  echo'</tr>';
                  $i=$i+1;

                }

              echo'
              </tbody>
              <tfoot>
                  <tr>
                     <th style="width:10px">#</th>
                     <th>Fecha</th>
                     <th>Factura</th>
                     <th>Lugar</th>
                     <th>Monto</th>
                     <th>Cantidad</th>
                     <th>Envia</th>
                     <th>Acciones</th>
                  </tr>
              </tfoot>
          </table>
         <table border=1>';
           echo '<h3 style="color:blue;"><b>Mandados = '.number_format($mandados,2).' Unidades = '.number_format($unidades,2).' Total = '.number_format($subtotal,2).'</b></h3>';
           echo'</table> ';

  }   


 ?>



  <script>
    $(document).ready(function() {

      var table = $('#listado2').DataTable({
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
      var table = $('#listado1').DataTable({
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

      var table = $('#listado3').DataTable({
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
  



