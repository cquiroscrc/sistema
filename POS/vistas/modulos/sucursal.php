<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_sucursal.php";

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
      
      Administrar productos en Sucursal
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Administrar productos en sucursal</li>
    
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
              <button onclick="myFunctionCodigoSucursal()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>        
                  Buscar Código!
              </button>      
          </div>
        </div>
        
            <div class="box-tools pull-right">
              <button onclick="myReporteSucursal()" id="consulta" class="btn btn-success" data-toggle ="modal"  ><i class="fa fa-file-excel-o"></i> Reporte!</button>
              <button onclick="myFunctionDiferenciasSucursal()" id="consulta" class="btn btn-warning" data-toggle ="modal"  ><i class="fa fa-search-plus"></i> Diferencias!</button>
            </div>
        </div>   
      
      <center>
      
      <h3>Listado de Codigo en Sucursal</h3>
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

<div id="modalSucursalProducto" class="modal fade" tabindex="-1" role="dialog">
  
    <div class="modal-dialog modal-lg" >

        <div class="modal-content">

          

            <!--=====================================
            CABEZA DEL MODAL SUCURSAL
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

              <button type="button" class="close" data-dismiss="modal">&times;</button>

              <h4 class="modal-title">Consulta de Codigo</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL SUCURSAL
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
                PIE DEL MODAL SUCURSAL
                ======================================-->

                <div class="modal-footer">

                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                </div>

            </div>
        </div>
    </div>
</div>
<!--=====================================
MODAL SUCURSAL
======================================-->

<div id="modalSucursalDiferencias" class="modal fade" tabindex="-1" role="dialog">
  
    <div class="modal-dialog modal-lg" >

        <div class="modal-content">

          

            <!--=====================================
            CABEZA DEL MODAL SUCURSAL
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

              <button type="button" class="close" data-dismiss="modal">&times;</button>

              <h4 class="modal-title">Consulta diferencias Sucursal</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL SUCURSAL
            ======================================-->

            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">  

                    <div class="box-body">

                    
                    
                        <div class="form-group">
                            <button onclick="myReportediferencias()" id="consulta" class="btn btn-success" data-toggle ="modal"  ><i class="ion ion-ios-list-outline"></i>Reporte!</button>
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



<script>



    /*=============================================
     DOCUMENT READY
    =============================================*/
    $(document).ready(function(){
        // CLICK BUSCAR
        myCodigoSucursal();

        document.getElementById("consulta").onclick = function(){
          myFunctionCodigoSucursal();
        };
        
        
        // BORRA EL TEXTO DEL TXT DEL CODIGO
        $("#modalSucursalProducto").on('hidden.bs.modal', function () {
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
          //console.log(document.getElementById("selector").value);
          
            if (event.which == 13 || event.keyCode == 13) {
                myFunctionCodigoSucursal();
                return false;
            }
        return true;
          
        }); 

       


</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Sucursal';
</script>
