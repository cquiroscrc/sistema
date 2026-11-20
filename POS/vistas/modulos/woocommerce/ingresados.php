<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");


?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<table id="listado" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

<thead>
       <th style="width:10px">#</th>
       <th>Código</th>
       <th>Descripción</th>
       <th>Existencia</th>
       <th>Accion</th>
</thead>
<tbody>

<?php 

  
    $sql="SELECT i.CODIGO,i.DESCRIPCION,i.EXISTENCIA,w.Codigo as cod2, w.Descripcion as desc2,
    substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1)
    WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))Else i.CODIGO END As Filtro FROM inventario i, woocommerce w WHERE i.CODIGO = w.Codigo ORDER BY Filtro";
    $i=1;
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($ejecutar as $key => $value) {
        $codigo=$value['CODIGO'];
        $descripcion=$value['desc2'];
        $existencia=$value['EXISTENCIA'];
        
        echo '<tr>
        <td>'.$i.'</td>
        <td>'.$codigo.'</td>';
        echo'<td>'.$descripcion.'</td>';
        echo'<td>'.$existencia.'</td>';
        echo"<td>
        <div class='btn-group'>";
        echo '<button class="btn btn-danger" onclick="EliminarWoo('."'".$codigo."'".')"><i class="fa fa-times"></i></button></div>'; 
        echo'</tr>';
        $i=$i+1;

    }

  
 ?>
 </tbody>
    <tfoot>
        <tr>
           <th style="width:10px">#</th>
           <th>Código</th>
           <th>Descripción</th>
           <th>Existencia</th>
           <th>Accion</th>
        </tr>
    </tfoot>
</table>


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

        table.on('draw', function () {
            $('[data-fancybox]', this).fancybox({
                toolbar: false,
                smallBtn: true,
                iframe: {
                    preload: false,
                    css : {
                        width: '600px'
                    }
                },

            });

        })
    
 
    } );

  </script>
  



