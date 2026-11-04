<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");
 
 if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
      $fecha = $_REQUEST['fecha'];
    }
if(isset($_REQUEST['fechafin']) && !empty($_REQUEST['fechafin'])){
    $fechafin = $_REQUEST['fechafin'];
}

  $sql="SELECT h.fecha, h.codigo, h.cantidad, h.movimiento, i.DESCRIPCION,i.ubicacion, i.existencia,
        substring(h.CODIGO,1,1),substring(h.CODIGO,6,3 ),substring(h.CODIGO,10,15),Case substring(h.CODIGO, 1, 1)
        WHEN '0' then substring(h.CODIGO,1,1)+substring(h.CODIGO,5,4 )+substring(h.CODIGO,9,4 )+' '+substring(h.CODIGO,1,4 )+' '+ltrim(rtrim(substring(h.CODIGO,13,12 )))
        WHEN '1' then substring(h.CODIGO,1,1)+substring(h.CODIGO,5,4 )+substring(h.CODIGO,9,4 )+' '+substring(h.CODIGO,1,4 )+' '+ltrim(rtrim(substring(h.CODIGO,13,12 )))      
        Else h.CODIGO END As Filtro from HGENERAL h, inventario i where fecha > '".$fecha." 00:00:00.000'  
        and fecha < '".$fechafin ." 23:59:59.999'  AND h.codigo= i.CODIGO AND h.CODIGO <> 'SP' AND h.codigo <> '9SM' AND h.codigo <>'9ENCOMIENDA' AND h.codigo <>'9TM'order by Filtro";
 ?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<table id="codigos_suc" class="table  table-bordered table-striped codigos_suc" style="width:100%">

        <thead>
               <th style="width:10px">#</th>
               <th>Código</th>
               <th>Descripción</th>
               <th>Cantidad</th>
               <th>Movimiento</th>
               <th>Ubicación</th>
               <th>Existencia</th>
        </thead>
        <tbody>
          
          <?php

          

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          $i=1;
          foreach ($ejecutar as $key => $arr) {
            $codigos=$arr["codigo"];
            $descripcion=$arr["DESCRIPCION"];
            $cantidad=$arr["cantidad"];
            $movimiento=$arr["movimiento"];
            $ubicacion=$arr["ubicacion"];
            $existencia=$arr["existencia"];
              
            

            echo '<tr>
            <td>'.$i.'</td>';
            echo'<td>'.$codigos.'</td>';
            echo'<td>'.$descripcion.'</td>';
            echo'<td>'.$cantidad.'</td>';
            echo'<td>'.$movimiento.'</td>';
            echo'<td>'.$ubicacion.'</td>';

            if ($existencia==0) {
              echo '<td style="color:red;"><b>'.number_format($existencia,2).'</b></td>';
            }else{
            echo '<td style="color:blue;"><b>'.number_format($existencia,2).'</b></td>';
            }
            
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
               <th>Cantidad</th>
               <th>Movimiento</th>
               <th>Ubicación</th>
               <th>Existencia</th>
            </tr>
        </tfoot>
    </table>
    
  <script>
    $(document).ready(function() {

      var table = $('#codigos_suc').DataTable({
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
  

