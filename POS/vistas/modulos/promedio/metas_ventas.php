<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");


     if(isset($_REQUEST['inicio']) && !empty($_REQUEST['inicio'])){
      $inicio = $_REQUEST['inicio'];
      $inicio = date("m-d-Y", strtotime($inicio));
    }

    if(isset($_REQUEST['fin']) && !empty($_REQUEST['fin'])){
        $final = $_REQUEST['fin'];
        $final = date("m-d-Y", strtotime($final));
    }

$sql="SELECT v.nombre,v.codigo , SUM(h.subtotal)- SUM(h.descuento) AS  venta 
FROM hfacturas h INNER JOIN Vendedores v on FORMAT(cast( h.vendedor as numeric),'###') = 
FORMAT(cast( v.codigo as numeric),'###')WHERE h.fechafact BETWEEN '$inicio 00:00:00.000' AND  
'$final 23:59:59.000'GROUP BY v.codigo, v.nombre ORDER BY venta DESC";

 ?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<table id="codigos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

        <thead>
               <th>Código</th>
               <th>Nombre</th>
               <th>Subtotal</th>
        </thead>
        <tbody>
          
          <?php

          

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          $totalventa=0;
          foreach ($ejecutar as $key => $arr) {
            $codigo=$arr["codigo"];
            $nombre=$arr["nombre"];
            $venta=$arr["venta"];
            $totalventa=$totalventa+$venta;
            echo '<tr>';
            echo'<td>'.$codigo.'</td>';
            echo'<td>'.$nombre.'</td>';
            echo '<td style="color:blue;">'.number_format($venta,2).'</td>';
            echo'</tr>';
          }

        ?>
   
        </tbody>

    </table>
    <h4 style="color: blue;"> Del: <?php  echo $inicio.' al: '.$final;?></h4>
    <h4>Total Ventas: <?php echo number_format($totalventa,2); ?></h4>
  <script>
    $(document).ready(function() {

      var table = $('#codigos').DataTable({
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

  //for table row
    
 
    } );

  </script>

<script type="text/javascript">
       $(document).ready(function() {
           $(".fancybox").fancybox();

       });
</script>

