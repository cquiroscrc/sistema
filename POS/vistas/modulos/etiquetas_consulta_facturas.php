<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura = $_REQUEST['Factura'];
    }   
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];
    }   
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
    }   
        

 ?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<table id="codigos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

        <thead>
               <th style="width:10px">#</th>
               <th>ID</th>
               <th>Código</th>
               <th>Descripción</th>
               <th>Cantidad</th>
               <th>Detalle</th>
               <th>Etiquetas</th>
               
               
        </thead>
        <tbody>
          
          <?php





$sqlpedido="SELECT p.ID, p.Codigo,i.DESCRIPCION,p.Proveedor,p.Factura,p.Cantidad,p.Etiquetas,p.Fecha,i.Ubicacion,p.Caja, substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1)WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))Else p.codigo END As Filtro FROM Pedidos as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE Factura='$Factura' ORDER BY Filtro";

$sqlcompra = "SELECT p.Codigo,i.CODIGO as COD,i.DESCRIPCION,p.Proveedor,p.Factura,p.Cantidad,p.Etiquetas,p.Fecha,i.Ubicacion,p.Caja,substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1) WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 ))) WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))Else p.codigo END As Filtro FROM hmcompras as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE p.Factura='$Factura' ORDER BY Filtro";



          
          if ($tipo=='compra') {
            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcompra);
          }else{
            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlpedido);

          }
          //print_r($ejecutar);
          $i=1;
          $totetiquetas=0;
          foreach ($ejecutar as $key => $arr) {

            $codigos=$arr["Codigo"];
            $descripcion=$arr["DESCRIPCION"];
            $proveedor=$arr["Proveedor"];
            $factura=$arr["Factura"];
            $cantidad=$arr["Cantidad"];
            $fecha=$arr["Fecha"];
            $ubicacion=$arr["Ubicacion"];
            $date_now = date('dmy');
            $detalle= $proveedor.'-'.$factura.'-'.$date_now.'-'.number_format($cantidad,0).'-'.$ubicacion;  
            $etiquetas=$arr["Etiquetas"];
              if ($tipo=='compra') {
                  $idproducto=$i;
              }else{
                  $idproducto=$arr["ID"];
              }

            $totetiquetas=$totetiquetas+$etiquetas;
            echo '<tr>
            <td>'.$i.'</td>
            <td>'.$idproducto.'</td>';
            echo'<td>'.$codigos.'</td>';
            echo'<td>'.$descripcion.'</td>';
            echo'<td>'.number_format($cantidad,2).'</td>';
            echo'<td>'.$detalle.'</td>';
            if (is_null($etiquetas) || $etiquetas=="") {
              echo'<td><input class="imp" type="number" value="'.number_format($cantidad,0).'"></td>'; 
            }else{
              echo'<td><input class="imp" type="number" value="'.number_format($etiquetas,0).'"></td>';
            }
            
            echo'</tr>';
            $i=$i+1;

          }

        ?>
   
        </tbody>
        <tfoot>
            <tr>
               <th style="width:10px">#</th>
                <th>ID</th>
               <th>Código</th>
               <th>Descripción</th>
               <th>Cantidad</th>
               <th>Detalle</th>
               <th>Etiquetas</th>
               
            </tr>
        </tfoot>
    </table>
<h4 style="color:red;">Total etiquetas: <?php echo $totetiquetas;   ?></h4>
  <script>
    $(document).ready(function() {

      var table = $('#codigos').DataTable({
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
  



