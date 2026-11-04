<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");


    $sql = "SELECT i.CODIGO, i.DESCRIPCION, i.EXISTENCIA, i.ubicacion as ubicacion,e.ubicacion as ubica2, e.cantidad, e.ubicacion as sucubi,
    substring(i.CODIGO,1,1),substring(i.CODIGO,6,3 ),substring(i.CODIGO,10,15),Case substring(i.CODIGO, 1, 1)
    WHEN '0' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))
    WHEN '1' then substring(i.CODIGO,1,1)+substring(i.CODIGO,5,4 )+substring(i.CODIGO,9,4 )+' '+substring(i.CODIGO,1,4 )+' '+ltrim(rtrim(substring(i.CODIGO,13,12 )))      
    Else i.CODIGO END As Filtro FROM inventario i, tbl_exi_suc e WHERE i.CODIGO = e.codigo AND e.cantidad >= i.EXISTENCIA order by Filtro
    ";

 ?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<table id="codigos_dif" class="table  table-bordered table-striped codigos_dif" style="width:100%">

        <thead>
               
               <th>Codigo</th>
               <th>Descripcion</th>
               <th>Cantidad</th>
               <th>Existencia</th>
               <th>Ubicacion</th>
               <th>Ubicacion 2</th>
        </thead>
        <tbody>
          
          <?php

          

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          
          foreach ($ejecutar as $key => $arr) {
            
            echo '<tr>';
            echo '<td>'.$arr["CODIGO"].'</td><td>'.$arr["DESCRIPCION"].'</td><td>'.$arr["cantidad"].'</td><td>'.$arr["EXISTENCIA"].'</td><td>'.$arr["ubicacion"].'</td><td>'.$arr["ubica2"].'</td></tr>';
            
          }

        ?>
   
        </tbody>
        <tfoot>
            <tr>
               
               <th>Codigo</th>
               <th>Descripcion</th>
               <th>Cantidad</th>
               <th>Existencia</th>
               <th>Ubicacion</th>
               <th>Ubicacion 2</th>
            </tr>
        </tfoot>
    </table>
    
  <script>
    $(document).ready(function() {

      var table = $('#codigos_dif').DataTable({
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
  

