<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_diarias.php";

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "mensajeria"){


  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Ventas diarias
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Ventas diarias
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-5">
          <div class="input-group mb-3">
              <div class="input-group-prepend">
                  <span class="input-group-text" id="Articulo:">Buscar Código</span>
              </div>
              Fecha Inicio: <input type="date" id="fecha_diarias" name="fecha"  min="2018-01-01" >
              Fecha Fin: <input type="date" id="fecha_diarias_Fin" name="fecha"  min="2018-01-01" >
              <hr>
              
              <button onclick="myFunctionCodigoDiarias()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>        
                  Buscar Reporte!
              </button> 
              
         </div>
        </div>
        <div class="col-sm-3">
          <div class="input-group mb-3">
              <div class="input-group-prepend">
                  <span class="input-group-text" id="Articulo:">Generar Reporte</span>
              </div>
              &nbsp;
              <hr>
              
              <button onclick="myFunctionPDF()" id="consulta" class="btn btn-danger" data-toggle ="modal"  ><i class="fa fa-file-pdf-o"></i>        
                  Generar Reporte!
              </button> 
              
         </div>

        </div>
         
      </div>
      <center>
      
      <h3>Listado de ventas diarias</h3>
      <div id="diarias_codigo" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>
    </div>

  </section>

</div> 





 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ventas Diarias';
</script>
