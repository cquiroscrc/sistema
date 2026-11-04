<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");


  $cod="XX";
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $cod = $_REQUEST['codigo'];
      $familia=0;
    }
    
    if (substr($cod,0,5)=='1___ ') {
      $codigo=$cod;
    }
    else{
      $cant=stripos($cod,"_",0);
      if ($cant<>0) {
        $codigo= substr($cod,0,$cant);  
      }else{
        $codigo=$cod;
      }
    }
    $codigo= trim($codigo);
    $codigobusq = '';
    
    if ($familia==1) {
      $codigo='1___ '.$codigo;

    }
    
    //// *************** Incluye recibir ******************
    $suge=0;
    if ($codigo != ""){
      $reglon=1;
      
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************

      if (substr($codigo,0,1)==1 ) { // vw
          $sql="SELECT e.codigo,e.cantidad,e.ubicacion ,i.Descripcion , i.DESC_ALT From tbl_exi_suc e, inventario i WHERE e.codigo like '$codigo%' and e.codigo = i.CODIGO
          order by substring(e.codigo,1,1),substring(e.codigo,5,4), 
          substring(e.codigo,9,4),substring(e.codigo,1,4),substring(e.codigo,13,12)";
      }
      elseif (substr($codigo,0,1)==0 ) { // vw
          $sql="SELECT e.codigo,e.cantidad,e.ubicacion ,i.Descripcion , i.DESC_ALT From tbl_exi_suc e, inventario i WHERE e.codigo like '$codigo%' and e.codigo = i.CODIGO
          order by substring(e.codigo,1,1),substring(e.codigo,5,4), 
          substring(e.codigo,9,4),substring(e.codigo,1,4),substring(e.codigo,13,12)";
      }
      elseif (substr($codigo,0,1)==2) {
           $sql="SELECT e.codigo,e.cantidad,e.ubicacion ,i.Descripcion , i.DESC_ALT From tbl_exi_suc e, inventario i WHERE e.codigo like '$codigo%' and e.codigo = i.CODIGO
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==3) {
           $sql="ELECT * FROM e.codigo,e.cantidad,e.ubicacion ,i.Descripcion , i.DESC_ALT From tbl_exi_suc e, inventario i WHERE e.codigo like '$codigo%' and e.codigo = i.CODIGO 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==4) {
           $sql="SELECT e.codigo,e.cantidad,e.ubicacion ,i.Descripcion , i.DESC_ALT From tbl_exi_suc e, inventario i WHERE e.codigo like '$codigo%' and e.codigo = i.CODIGO
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==5) {
            $fam=1;
            $des=substr($codigo,1,strlen($codigo));
            $codigo=$fam.'___ '.$des;

           $sql="SELECT e.codigo,e.cantidad,e.ubicacion ,i.Descripcion , i.DESC_ALT From tbl_exi_suc e, inventario i WHERE e.codigo like '$codigo%' AND e.codigo = i.CODIGO 
          order by substring(e.codigo,1,1),substring(e.codigo,5,4), 
          substring(e.codigo,9,4),substring(e.codigo,1,4),substring(e.codigo,13,12)";
      }
      elseif (substr($codigo,0,1)==9) {
           $sql="SELECT e.codigo,e.cantidad,e.ubicacion ,i.Descripcion , i.DESC_ALT From tbl_exi_suc e, inventario i WHERE e.codigo like '$codigo%' AND e.codigo = i.CODIGO
          order by codigo";    
      }elseif (substr($codigo,0,1)==6) {
            $fam=substr($codigo,1,1);
            $des=substr($codigo,2,strlen($codigo));
          $sql="SELECT e.codigo,e.cantidad,e.ubicacion ,i.Descripcion , i.DESC_ALT From tbl_exi_suc e, inventario i WHERE e.codigo like '$codigo%' and e.codigo = i.CODIGO
          order by codigo";
      }


  
}
//echo $sql;
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
               <th>Stock</th>
               <th>Ubicación</th>
               <th>Acciones</th>
        </thead>
        <tbody>
          
          <?php

          

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          $i=1;
          foreach ($ejecutar as $key => $arr) {
            $codigos=$arr["codigo"];
            $descripcion=$arr["Descripcion"];
            $existencia=$arr["cantidad"];
            $ubicacion=$arr["ubicacion"];
            $desc_tecnica=$arr["DESC_ALT"];

            

            echo '<tr>
            <td>'.$i.'</td>';
            echo'<td>'.$codigos.'</td>';
            echo'<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
            if ($existencia==0) {
              echo '<td style="color:red;"><b>'.number_format($existencia,2).'</b></td>';
            }else{
            echo '<td style="color:blue;"><b>'.number_format($existencia,2).'</b></td>';
            }
            

            echo'<td>'.$ubicacion.'</td>';
           
            echo '<td><button class="btn btn-success" id="add_codigo" onclick="myCodigoMas('."'".$codigos."'".')"><i class="ion ion-android-add-circle"></i></button>';
            echo '<button class="btn btn-danger" id="rest_codigo" onclick="myCodigoMenos('."'".$codigos."'".')"><i class="ion ion-android-remove-circle"></i></button></td>';  
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
               <th>Acciones</th>
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
  

