<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");


     if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
      $fecha = $_REQUEST['fecha'];
      $fecha = date("m-d-Y", strtotime($fecha));
    }
$sql = "SELECT CODIGO, DESCRIPCION, existencia,costo, venta, ubicacion,DESC_ALT,potencia,
		substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
		WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
		WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
		Else CODIGO END As Filtro FROM inventario where not  EXISTS(select DISTINCT (codigo) from hgeneral where fecha > '$fecha' and hgeneral.codigo = inventario.codigo) AND existencia = 0 order by Filtro";
$totalcosto=0;
$totalpiezas=0;
 ?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<table id="codigos" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

        <thead>
               <th style="width:10px">#</th>
               <th>Código</th>
               <th>Descripción</th>
               <th>Existencia</th>
               <th>Costo</th>
               <th>Venta</th>
               <th>Fecha</th>
               <th>Ubicaciones</th>
               <th>Imagen</th>
        </thead>
        <tbody>
          
          <?php

          

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          $i=1;
          foreach ($ejecutar as $key => $arr) {
            $codigos=$arr["CODIGO"];
            $descripcion=$arr["DESCRIPCION"];
            $existencia=$arr["existencia"];
            $costo=$arr["costo"];
            $venta=$arr["venta"];
            $ubicacion=$arr["ubicacion"];
            $desc_tecnica=$arr["DESC_ALT"];
            $potencia=$arr['potencia'];
            $xpotencia=$potencia*100;
            $venta=$venta+$xpotencia;
            $totalcosto=$totalcosto+($costo*$existencia);
            $totalpiezas=$totalpiezas+$existencia;

            $sql5 = "SELECT codigo,max(fecha) as fecha FROM hgeneral where codigo like '".$codigos."' GROUP BY codigo";
            $ejecutar5 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql5);
            $ultima_fecha='01-01-1980';
            foreach ($ejecutar5 as $key => $arr5) {
                $ultima_fecha = date("d-m-Y", strtotime($arr5['fecha']));
            }


            echo '<tr>
            <td>'.$i.'</td>';
            echo'<td>'.$codigos.'</td>';
            echo'<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
            echo '<td style="color:blue;">'.number_format($existencia,2).'</td>';
            echo'<td>'.number_format($costo,2).'</td>';
            echo'<td>'.number_format($venta,2).'</td>';
            echo'<td>'.$ultima_fecha.'</td>';
            echo'<td>'.$ubicacion.'</td>';

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
              $existes = $_SERVER["DOCUMENT_ROOT"].'/subida_cat/'.$directorio.$foto.'.jpg';

              if (is_file($existes)) {
                  $archivo=$filename;
              } else {
                  $archivo=$path."inf.png";
              }

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
                <th>Existencia</th>
                <th>Costo</th>
                <th>Venta</th>
                <th>Fecha</th>
                <th>Ubicaciones</th>
                <th>Imagen</th>
            </tr>
        </tfoot>
    </table>
    <h4>Total Costo: <?php echo number_format($totalcosto,2); ?></h4>
    <h4>Total Piezas: <?php echo number_format($totalpiezas,2); ?></h4>
    <h4>Total Items: <?php echo number_format($i,2); ?></h4>

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

