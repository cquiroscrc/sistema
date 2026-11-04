<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

  if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];
      
    }

 ?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<table id="codigos_lis" class="table table-bordered table-striped dt-responsive " width="100%">

        <thead>
               <th style="width:10px">#</th>
               <th>Código</th>
               <th>Descripción</th>
               <th>Cantidad</th>
               <th>Costo</th>
               <th>Total</th>
               <th>Tipo</th>
               <th>Acciones</th>
               <th>Imagen</th> 
        </thead>
        <tbody>
          
          <?php

          $sql="SELECT d.IDMovimiento,d.IDTipoMovimiento,d.Codigo,d.Cantidad,d.Costo,d.IDBodegaO,d.IDBodegaD,d.ID,i.DESCRIPCION FROM detabodega d, inventario i WHERE IDMovimiento='".$consecutivo."' AND d.Codigo = I.CODIGO  ORDER BY ID";
        

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          $i=1;
          foreach ($ejecutar as $key => $arr) {
            $codigos=rtrim($arr["Codigo"]);
            $descripcion=$arr["DESCRIPCION"];
            $cantidad=$arr["Cantidad"];
            $costo=$arr["Costo"];
            $id=$arr["ID"];
            $tipo=$arr["IDTipoMovimiento"];

            echo '<tr>
            <td>'.$id.'</td>';
            echo'<td>'.$codigos.'</td>';
            echo'<td>'.$descripcion.'</td>';
            echo '<td style="color:blue;"><b>'.number_format($cantidad,2).'</b></td>';
            echo'<td>'.number_format($costo,2).'</td>';
            echo '<td style="color:orange"><b>'.number_format(($costo*$cantidad),2).'</b></td>';
            if ($tipo==1) {
                $tipodes='Ajuste Entrada';
                echo '<td style="color:green">'.$tipodes.'</td>';
                
            }else{
                $tipodes='Ajuste Salida';
                echo '<td style="color:red">'.$tipodes.'</td>';
            }

            // Fotos
            $path='../../subida_cat/';

              if (substr($codigos,0,1)==1) {
                $directorio='1/';
              }
              if (substr($codigos,0,1)==2) {
                $directorio='2/';
              }
              if (substr($codigos,0,1)==3) {
                $directorio='3/';
              }
              if (substr($codigos,0,1)==4) {
                $directorio='4/';
              }
              if (substr($codigos,0,1)==9) {
                $directorio='9/';
              }
              if (substr($codigos,0,1)==0) {
                $directorio='0/';
              }
              $codigos1=rtrim($codigos);
              if (substr($codigos,-2)=='EU') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
                 
              } 
              if (substr($codigos,-2)=='GR') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
              }
              if (substr($codigos,-2)=='TW') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
                 
              }
              if (substr($codigos,-2)=='JP') {
                 $codigos1=substr($codigos,0,-2);
                 $codigos1=rtrim($codigos1);
              }
              if (substr($codigos,-2)=='ZL') {
                 $codigos1=substr($codigos,0,-2);
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

            echo"<td>
            <div class='btn-group'>";
            echo '<button class="btn btn-danger" onclick="myElimina('."'".$codigos."'".')"><i class="fa fa-times"></i></button></div>'; 

            echo"</td>";
            echo'<td><a data-fancybox  data-type="iframe" class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'"></a></td>';
            
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
               <th>Stock</th>
               <th>Ubicación</th>
               <th>Bodega</th>
               <th>Ubicación 2</th>
               <th>Acciones</th>
               <th>Imagen</th>
            </tr>
        </tfoot>
    </table>
    
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



