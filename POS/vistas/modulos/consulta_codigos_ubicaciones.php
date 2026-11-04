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
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==0 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==2) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==3) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==4) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==5) {
            $fam=1;
            $des=substr($codigo,1,strlen($codigo));
            $codigo=$fam.'___ '.$des;
             
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";        
      }
      elseif (substr($codigo,0,1)==9) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }elseif (substr($codigo,0,1)==6) {
            $fam=substr($codigo,1,1);
            $des=substr($codigo,2,strlen($codigo));
           $sql="SELECT * FROM inventario WHERE codigo like '$fam%' 
           AND descripcion like '%$des%' OR DESC_ALT like '%$des%' AND codigo like '$fam%' order by descripcion";    
      }


  
}

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
               <th>Stock</th>
               <th>Ubicación</th>
               <th>Acciones</th>
               <th>Imagen</th> 
        </thead>
        <tbody>
          
          <?php

          

          $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          $i=1;
          foreach ($ejecutar as $key => $arr) {
            $codigos=$arr["CODIGO"];
            $descripcion=$arr["DESCRIPCION"];
            $existencia=$arr["EXISTENCIA"];
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
              $boton = str_replace(" ","_",$codigos);
            ?>  
            <td><button onclick="myAgregarCodigoUbicacion('<?php echo $codigos; ?>')"  class="btn btn-success btn-add"><i class="ion ion-archive"></i></button>
            
            <?php 
            echo"</td>";  
            echo'<td><a class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';
            
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
               <th>Imagen</th>
            </tr>
        </tfoot>
    </table>
    
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


  
<script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });

        $(".btn-add").click(function () {
              $(this).prop('disabled',true);
             
            });

    </script>



