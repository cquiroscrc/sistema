<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_ajustes.php";

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "mensajeria"){


  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

       $consecutivo2=0;
       $sql="SELECT * FROM Consecutivos WHERE tipo= '05'";    
       $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        foreach ($respuesta as $key => $value) {
              $consecutivo=$value['numero'];
        }

        $sql="SELECT * FROM ajutras WHERE Estado=0";    
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        foreach ($respuesta as $key => $value) {
              $consecutivo2=$value['IDMovimiento'];
        }
            
        if ($consecutivo2<>0) {
          $consecutivo=$consecutivo2;
        }


?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Ajustes
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Ajustes</li>
    
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
              <button onclick="myFunctionCodigoAjuste()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>        
                  Buscar Código!
              </button> 
         </div>
        </div> 
        <div class="col-sm-5">     
         <div class="input-group mb-3">
          <?php 
            if($_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "especial"  ){

            }else{  
              echo'
              <div class="input-group-prepend">
                  <span class="input-group-text">Razón del Ajuste</span>
              </div>
              <input type="text" class="form-control" id="razon" placeholder="Razón del ajuste" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
              <hr>
              <div style="font-size:large;color:#3989c6;">&nbsp;</div>
              <button onclick="myFunctionAjusteAplicar()" id="consulta" class="btn btn-success" data-toggle ="modal"  ><i class="fa fa-check"></i>        
                  Aplicar Ajuste
              </button>'; 
            }
           ?>
         </div>
        </div>
        <div class="col-sm-2">
          <div class="input-group mb-3">
              <div class="input-group-prepend">
                  <span class="input-group-text">Buscar Ajuste</span>
              </div>
              <input type="text" class="form-control" id="busqueAjuste" placeholder="Busqueda de ajuste" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
              <hr>
              <div style="font-size:large;color:#3989c6;">&nbsp;</div>
              <button onclick="myFunctionBuscarAplicar()" id="consulta" class="btn btn-warning" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>        
                  Buscar Ajuste
              </button> 
              
         </div>
         
        </div>
         
      </div>
      <center>
      
      <h3 style="display:inline;" >Ajuste # </h3><h3 style="color:red; display:inline;" id="numeroAjuste"><?php echo $consecutivo; ?></h3><h3 style="display:inline;">-<?php echo $_SESSION["id"]; ?></h3>
      <div id="ajuste_codigo" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>
    </div>

  </section>

</div> 
<!--=====================================
MODAL AJUSTE
======================================-->

<div id="modalAjusteProducto" class="modal fade" tabindex="-1" role="dialog">
  
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
                                <div id="resultado_ajuste">  
                                
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
MODAL AJUSTE MOSTRAR
======================================-->

<div id="modalAjusteHecho" class="modal fade" tabindex="-1" role="dialog">
  
    <div class="modal-dialog modal-lg" >

        <div class="modal-content">

            <!--=====================================
            CABEZA DEL MODAL SUCURSAL
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

              <button type="button" class="close" data-dismiss="modal">&times;</button>

              <h4 class="modal-title">Consulta de Ajuste</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL SUCURSAL
            ======================================-->

            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">  

                    <div class="box-body">

                    
                        <div class="form-group">
                            <center>
                                <div id="busqueda_ajuste">  
                                
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
        myCodigoAjuste();

        document.getElementById("consulta").onclick = function(){
          myFunctionCodigoAjuste();
        };
        
        
        // BORRA EL TEXTO DEL TXT DEL CODIGO
        $("#modalAjusteProducto").on('hidden.bs.modal', function () {
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
                myFunctionCodigoAjuste();
                return false;
            }
        return true;
          
        }); 

       


</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ajustes';
</script>
