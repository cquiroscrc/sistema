<?php
date_default_timezone_set("America/Costa_Rica");
include "modelos/procesos_perdidas.php";

if($_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "mensajeria"){
  echo '<script>
    window.location = "inicio";
  </script>';
  return;
}

?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Ventas Perdidas
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Ventas Perdidas</li>
    </ol>
  </section>
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <div class="col-sm-5">
          <div class="input-group mb-3">
              <div class="input-group-prepend">
                  <span class="input-group-text" id="Articulo:">Buscar Código</span>
              </div>
              <input type="text" id="selector" class="form-control" placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
              <hr>
              <div style="font-size:large;color:#3989c6;">5 = Familia / 6 y Marca = Descripcion</div>
              <button onclick="myCodigoPerdido()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>
                  Buscar Código!
              </button>      
          </div>
        </div>
            <div class="box-tools pull-right">
                <?php
                if($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial") {
                ?>
                    <button onclick="myReportePerdidas()" id="consulta" class="btn btn-success" data-toggle ="modal"  ><i class="fa fa-file-excel-o"></i> Reporte!</button>
                <?php
                }
                ?>
            </div>
        </div>
      <center>
      <h3>Listado de Ventas perdidas</h3>
      <div id="sucursal_codigo" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>
    </div>
  </section>
</div> 
<!--=====================================
MODAL SUCURSAL
======================================-->

<div id="modalPerdida" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL PERDIDAS
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Consulta Codigo</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL PERDIDA
            ======================================-->
            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <div class="form-group">
                            <center>
                                <div id="resultado_sucursal">
                                <!-- AQUI VA EL AJAX -->
                                </div>   
                            </center>
                        </div>      
                    </div>
                </div>
                <!--=====================================
                PIE DEL MODAL PERDIDA
                ======================================-->
                <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--=====================================
MODAL MODIFICAR PERDIDA
======================================-->
<div id="modalModPerdida" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL SUCURSAL
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Modificar Porducto</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL SUCURSAL
            ======================================-->
            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <div class="form-group">
                            <center>
                                <div id="resultado_diferencias">
                                <!-- AQUI VA EL AJAX -->
                                </div>   
                            </center>
                        </div>      
                    </div>
                </div>
                <!--=====================================
                PIE DEL MODAL SUCURSAL
                ======================================-->
                <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--=============================================-->
<script>
    /*=============================================
     DOCUMENT READY
    =============================================*/
    $(document).ready(function(){
        // CLICK BUSCAR
        myListadoPerdidas();

        document.getElementById("consulta").onclick = function(){
          myCodigoPerdido();
        };
        // BORRA EL TEXTO DEL TXT DEL CODIGO
        $("#modalPerdida").on('hidden.bs.modal', function () {
            document.getElementById("selector").value = "";
        });
    });

    /*=============================================
     FIN DOCUMENT READY
    =============================================*/
     /*=============================================
          CLICK EN BOTON DE BUSCAR
          =============================================*/  
        document.getElementById('selector').addEventListener('keydown', 
        function (event) {
            if (event.which == 13 || event.keyCode == 13) {
                myCodigoPerdido();
                return false;
            }
        return true;
        }); 

</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ventas Perdidas';
</script>
