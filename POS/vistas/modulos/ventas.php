<?php
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){

  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}
if(isset($_GET["fechaInicial"])){
  $ini = $_GET["fechaInicial"];
  $fin = $_GET["fechaFinal"];
}else{
  $ini="";
  $fin="";
}

$xml = ControladorVentas::ctrDescargarXML();

if($xml){

  rename($_GET["xml"].".xml", "xml/".$_GET["xml"].".xml");

  echo '<a class="btn btn-block btn-success abrirXML" archivo="xml/'.$_GET["xml"].'.xml" href="ventas">Se ha creado correctamente el archivo XML <span class="fa fa-times pull-right"></span></a>';

}

?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Administrar ventas
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Administrar ventas</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
  
        
        
         Fecha Inicio: <input type="date" id="fecha_inicio_reporte" name="fecha"  min="2018-01-01" >
         
         Fecha Fin: <input type="date" id="fecha_fin_reporte" name="fecha" min="2018-01-01" >
         
         
         
          <button class="btn btn-primary" onclick="reeviarFecha()">Cargar</button>
          
         <h4><?php echo $ini."--".$fin; ?></h4>      
      <div class="box-body">
        
       <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
         
        <thead>
         
         <tr>
           
           <th style="width:10px">#</th>
           <th>Código factura</th>
           <th>Cliente</th>
           <th>Nombre</th>
           <th>Vendedor</th>
           <th>Subtotal</th>
           <th>Descuento</th>
           <th>Impuesto</th>  
           <th>Total</th> 
           <th>Fecha</th>
           <th>Acciones</th>

         </tr> 

        </thead>

        <tbody>

        <?php

            if(isset($_GET["fechaInicial"])){

              $fechaInicial = $_GET["fechaInicial"];
              $fechaFinal = $_GET["fechaFinal"];
              
            }else{

              $tabla1= "fechas";
                $item = null;
                $valor = null;

                $fechas = ControladorUtilitaria::ctrMostrarUtilitaria($tabla1,$item, $valor);
            foreach ($fechas as $key => $value) {
                              
                 $fecha=$value["Fecha"];
                }
                
                $fecha=strtotime($fecha);

                $fecha = date("m/d/Y", $fecha);

          
            $fechaInicial = $fecha." 00:00:00.000";
            $fechaFinal = $fecha ." 23:59:00.000";

            }

            

          

          $respuesta = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal);
          
          foreach ($respuesta as $key => $value) {

           
           echo '<tr>

                  <td>'.($key+1).'</td>';
                  
                  if ($value["total"]<0) {
                    echo '<td style="color:red;"><b>'.$value["nfac"].'</b></td>';
                  }else{
                    echo '<td style="color:blue;"><b>'.$value["nfac"].'</b></td>';  
                  }
                 
                  echo'

                  <td>'.$value["proveedor"].'</td>

                  <td>'.$value["Nombre"].'</td>

                  <td>'.$value["vendedor"].'</td>

                  <td>'.number_format($value["subtotal"],2).'</td>

                  <td>'.number_format($value["descuento"],2).'</td>

                  <td>'.number_format($value["impuesto"],2).'</td>';

                  if ($value["total"]<0) {
                    echo'<td style="color:red;"><b>'.number_format($value["total"],2).'</b></td>';
                  }else{
                    if ($value["estado"]=="APLICADA  ") {
                      
                      echo'<td style="color:blue;"><b>'.number_format($value["total"],2).'</b></td>';  
                    
                    }else{
                    
                      echo'<td style="color:red;"><b>'.number_format($value["total"],2).'</b></td>';  
                    
                    }
                    
                  }  
                  echo '<td>'.$value["fechafact"].'</td>  

                  <td>

                    <div class="btn-group">

                      <a class="btn btn-success" href="index.php?ruta=ventas&xml='.$value["nfac"].'">xml</a>';
                     
                      echo '<a class="btn btn-warning" href="vistas/modulos/pdf.php?factura='.$value["nfac"].'" target="_blank"><i class="fa fa-file-pdf-o"></i></a>'; 

                      echo '<button class="btn btn-info btnImprimirFactura" codigoVenta="'.$value["nfac"].'">

                        <i class="fa fa-print"></i>

                      </button>';

                      echo '<button class="btn btn-facebook btnReenviarVenta" idVenta="'.$value["nfac"].'"><i class="fa fa-mail-forward"></i></button>';
                      echo '<button class="btn btn-danger btnEliminarVenta" idVenta="'.$value["nfac"].'"><i class="fa fa-times"></i></button>';


                    

                    echo '</div>  

                  </td>

                </tr>';
            }

        ?>
               
        </tbody>

       </table>

       <?php

      $eliminarVenta = new ControladorVentas();
      $eliminarVenta -> ctrEliminarVenta();

      ?>
       

      </div>

    </div>

  </section>

</div>

<script>
  
  $(document).ready(function(){

         
   


  });

</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Reporte Ventas';
</script>
