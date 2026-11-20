<?php
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
include "modelos/procesos_woocommerce.php";

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){

  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}


?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Listado de WooCommerce
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Woocommerce</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
  
        
        <div class="input-group mb-3">
         
          <input type="text" id="selector"  placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;"> &nbsp;
          <button onclick="myFunctionCodigo()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i> Buscar Código!     </button>&nbsp; 

          <button class="btn btn-success" onclick="ExpWoo()"><i class="fa fa-file-excel-o"></i> Exportar   </button>&nbsp;&nbsp;
          <button class="btn btn-warning" onclick="ExpWooAct()"><i class="fa fa-file-excel-o"></i> Actualizar</button>  &nbsp;
         </div>    
      <div class="box-body">
          <center>
          <div id="resultados">
              <!-- AQUI VA EL AJAX -->
          </div>
          <hr>
          <h4>Productos ingresados</h4>
          <div id="ingresados">
            
          </div>
          </center>

      </div>

    </div>

  </section>

</div>



<!-- ///**** SCRIPT -->
<script>
  
  $(document).ready(function(){
    myWoo();



  });

  document.getElementById('selector').addEventListener('keydown', 
        function (event) {
          //console.log(document.getElementById("selector").value);
          
            if (event.which == 13 || event.keyCode == 13) {
                myFunctionCodigo();
                return false;
            }
        return true;
          
        });



</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Woocommerce';
</script>
