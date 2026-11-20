<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

  if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $factura = $_REQUEST['consecutivo'];
      
    }

 ?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>
<h3>Ajuste # <?php echo $factura ?></h3>
<table id="codigos_lis" class="table table-bordered table-striped dt-responsive " width="100%">

        <thead>
               
               <th>Factura</th>
               <th>Fecha</th>
               <th>Movimientos</th>
               <th>Codigo</th>
               <th>Cantidad</th>
               <th>Anterior</th>
               <th>actual</th> 
        </thead>
        <tbody>
          
          <?php 
                  
                  $consulta="SELECT * from hgeneral where Factura = '$factura' 
                  ORDER by FECHA DESC";
                  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);
                  foreach ($respuesta as $key => $prome_mov) {
                     $m_factura=$prome_mov["Factura"];
                     $fecha2=date("d-m-Y",strtotime($prome_mov["fecha"]));
                     $m_fecha= $fecha2;
                     echo '<td>'.$m_factura.'</td>';
                     echo '<td>'.$m_fecha.'</td>';
                     $ajus_id=$prome_mov['movimiento'];
                     echo '<td>'.$prome_mov['movimiento'].'</td>';
                     echo '<td>'.$prome_mov['codigo'].'</td>';
                     echo '<td>'.$prome_mov['cantidad'].'</td>';
                     echo '<td>'.$prome_mov['anterior'].'</td>';
                     echo '<td>'.$prome_mov['actual'].'</td>';
                     echo '</tr>';
                  }
          
           ?>
   
        </tbody>
        <tfoot>
            <tr>
              
               <th>Factura</th>
               <th>Fecha</th>
               <th>Movimientos</th>
               <th>Codigo</th>
               <th>Cantidad</th>
               <th>Anterior</th>
               <th>actual</th> 
            </tr>
        </tfoot>
    </table>
    
  <?php 
    $consulta="SELECT * from ajutras where IDMovimiento = '$factura'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);
    foreach ($respuesta as $key => $arr_ajus) {
    
      echo '<H2>Comentario:'.$arr_ajus['Comentario'].'</H2>';
      $id_usuario=$arr_ajus['IDUsuario'];

       $maquina="SELECT * from acc_usuarios where id = $id_usuario";
      $respuestam = ControladorUtilitaria::ctrMostrarUtilitariaVar($maquina);
      foreach ($respuestam as $key => $arr_maq) {
          $nombre=$arr_maq['nombre'];
        }
      echo '<H3> Usuario: '.$id_usuario.' - '.$nombre.'</H3>';
    }  

   ?>

  <script>
    $(document).ready(function() {

      var table = $('#codigos_lis').DataTable({
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
  



