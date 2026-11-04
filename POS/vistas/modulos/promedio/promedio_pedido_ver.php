<?php
session_start();
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['pedido']) && !empty($_REQUEST['pedido'])){
          $pedido = $_REQUEST['pedido'];
          
       }


?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>
<h1>Ver Pedido <?php echo $pedido; ?></h1>
<table id="tabla_rev" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">
   <thead>
    <tr>
        <th>Item</th>
        <th>Codigo</th>
        <th>Descripcion</th>
        <th>Cantidad</th>
        <th>Foto</th>
        <th>Eliminar</th>

    </tr>
  </thead>
  <tbody>
<?php 
        
          $sql_pedido="SELECT * FROM hacer_pedido WHERE pedido='$pedido'";
          
          $fila=1;
          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pedido);
          foreach ($ejecutar as $key => $row) {
          
                  $p_pedido=$row['pedido'];
                  $p_codigo=$row['codigo'];
                  $p_cantidad=$row['cantidad'];
                  $p_id=$row['id'];
                  $descripcion='CODIGO NUEVO';

                  $sql="SELECT * FROM inventario WHERE codigo like '$p_codigo%' order by codigo";    
                  $resultados = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                  foreach ($resultados as $key => $arr) {
                    
                    $descripcion=$arr["DESCRIPCION"];
                  }
                  echo '<td>'.$fila.'</td>';
                  echo '<td>'.$p_codigo.'</td>';
                  echo '<td>'.$descripcion.'</td>';
                  echo '<td>'.number_format($p_cantidad,2).'</td>';
                  $path='../subida_cat/';


                    if (substr($p_codigo,0,1)==1) {
                      $directorio='1/';
                    }
                    if (substr($p_codigo,0,1)==2) {
                      $directorio='2/';
                    }
                    if (substr($p_codigo,0,1)==3) {
                      $directorio='3/';
                    }
                    if (substr($p_codigo,0,1)==9) {
                      $directorio='9/';
                    }
                    if (substr($p_codigo,0,1)==0) {
                      $directorio='0/';
                    }
                    $codigos1=rtrim($p_codigo);
                    if (substr($p_codigo,-2)=='EU') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                       
                    } 
                    if (substr($p_codigo,-2)=='GR') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                    }
                    if (substr($p_codigo,-2)=='TW') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                       
                    }
                    if (substr($p_codigo,-2)=='JP') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                    }
                    if (substr($p_codigo,-2)=='ZL') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                    }

                    $foto=substr ( $codigos1 , 1 );
                    $foto=rtrim($foto);

                    if (substr($foto,0,1)==' ') {
                          //echo 'FOTOS';
                          $foto=substr($foto, 2, strlen($foto));
                        }
                        
                    $filename = $path.$directorio.$foto.".jpg";
                    $archivo=$filename;

                      $existes = $_SERVER["DOCUMENT_ROOT"].'/subida_cat/'.$directorio.$foto.'.jpg';

                      if (is_file($existes)) {
                          $archivo=$filename;
                      } else {
                          $archivo=$path."inf.png";
                      }

                    echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$p_codigo.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';

                    ?>
                  
                      <td><button class='btn btn-danger ' onclick='EliminarCodigo("<?php echo $p_codigo.'","'.$p_pedido.'","'.$p_id;?>")' ><i class='glyphicon glyphicon-remove'></i></button></td>
                    <?php 
                  
                 

                  echo '</tr>';  
                  $fila++;
          }
       echo '</tbody>';         
    ?>
    <tfoot>
            <tr>
                <th>Item</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Cantidad</th>
                <th>Foto</th>
                <th>Eliminar</th> 
            </tr>
        </tfoot>
    </table>
   <script>
     $(document).ready(function() {

      var table = $('#tabla_rev').DataTable({
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

   </script> 
   <script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>
