<?php
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

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
      
      Consulta de documentos
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Consulta de documentos</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
  
        
       <div class="input-group mb-3">
         Número de Documento: <input type="text" placeholder="Documento" autocomplete="off" id="documento"  style="text-transform: uppercase;" name="documento" >&nbsp;&nbsp;
         &nbsp;
         <button class="btn btn-defaul" style="color:white;background: grey;" onclick="BusquedaDocumento('documento')"><i class="fa fa-repeat"></i> Cargar</button>&nbsp;
         &nbsp;
         Número de Recibo: <input type="text" placeholder="Recibo" autocomplete="off" id="recibo"  style="text-transform: uppercase;" name="recibo" >&nbsp;&nbsp;
         &nbsp;
         <button class="btn btn-defaul" style="color:white;background: grey;" onclick="BusquedaDocumento('recibo')"><i class="fa fa-repeat"></i> Cargar</button>&nbsp;
       </div>
          <center>
              <h4 style="color:blue;" >Búsqueda</h4>
            <h4 style="color:red;" id="busqueda"></h4>
          </center>
      <div class="box-body">
          <center>
          <div id="resultados">
              <!-- AQUI VA EL AJAX -->
          </div>
          </center>

      </div>

    </div>

  </section>

</div>

<!--=====================================
MODAL MOVIMIENTOS
======================================-->

<div id="modalBusqueda" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL MOVIMIENTOS
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Movimiento de Documento</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL MOVIMIENTOS
            ======================================-->
            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <div class="form-group">
                            <center>
                                <div id="resultadosD">
                                    <!-- AQUI VA EL AJAX -->
                                </div>
                            </center>
                        </div>
                    </div>
                </div>
                <!--=====================================
                PIE DEL MODAL MOVIMIENTOS
                ======================================-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ///**** SCRIPT -->
<script>
  
  $(document).ready(function(){


  });

  /*=============================================
         MY FUNCTION CODIGO SUCURSAL
         =============================================*/
  function BusquedaDocumento(tipo) {

      var documento = $('#documento').val();
      if(documento==''){
          documento = $('#recibo').val();
      }
      $('#documento').val("");
      $('#recibo').val("")
      //$('#modalUbicacionProducto').modal();
      $('#busqueda').text(documento);
      $('#resultados').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
      $.ajax('vistas/modulos/consultas/consulta_resultado.php?Factura='+documento+'&tipo='+tipo)
          .done(function(response){
              //console.log(response);
              $('#resultados').html(response);
          });

  }

  /*=============================================
   VER DOCUEMNTO
         =============================================*/
  function myVer(factura,fecha,cliente) {

      $('#modalBusqueda').modal();
      $('#resultadosD').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
      $.ajax('vistas/modulos/consultas/consulta_busqueda.php?Factura='+factura+'&fecha='+fecha+'&cliente='+cliente)
          .done(function(response){
              //console.log(response);
              $('#resultadosD').html(response);
          });

  }


</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Consulta';
</script>
