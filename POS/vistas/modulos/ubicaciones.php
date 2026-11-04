<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_ubicacion.php";

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "mensajeria"){


  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Administrar Ubicaciones
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Administrar Ubicaciones</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        <div class="col-sm-4">
      </div>
        <div class="col-sm-4">
          
          <select class="form-control" id="ubicacion" name="ubicacion">
              <optgroup >
                <option value="">Seleccione la Ubicación</option>
          <?php 
            $sql="SELECT DISTINCT(ubicacion) FROM inventario order by ubicacion";  
            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
            foreach ($ejecutar as $key => $arr) {  
                echo "<option value='".$arr['ubicacion']."'>".$arr['ubicacion']."</option>";
                $conta++;
              }

           ?>
            </optgroup>
          </select>
          <p></p>
          <input type="text" id="ubica" class="form-control" placeholder="Ubicación" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
          <p></p>
          <button onclick="myUbicacion()" id="consulta" class="btn btn-warning" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>        
                    Cargar Ubicación
                </button>

          <button onclick="myVaciar()" id="vaciar" class="btn btn-danger" data-toggle ="modal"  ><i class="ion ion-share"></i> Vaciar </button>
        </div>
        
            <div class="box-tools pull-right">
              
              <div class="input-group mb-3">
                <input type="text" id="selector" class="form-control" placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
                <hr>
                <div style="font-size:large;color:#3989c6;">5 = Familia / 6 y Marca = Descripcion</div>
                <button onclick="myFunctionCodigoUbicacion()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>        
                    Buscar Código!
                </button>      
              </div>
            </div>
        </div>   
      
      <center>
      <h3>Listado de Codigo en Sucursal</h3>
      <div id="resultado_ubicacion" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>
    </div>

  </section>

</div> 
<!--=====================================
MODAL SUCURSAL
======================================-->

<div id="modalUbicacionProducto" class="modal fade" tabindex="-1" role="dialog">
  
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
                                <div id="resultadoion">  
                                
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
        // CAMBIA VALOR DE LA UBICACION
        $('select#ubicacion').on('change',function(){
          var valor = $(this).val();
          $('#ubica').val(valor);
             
        });
    });

    

   
    /*=============================================
     FIN DOCUMENT READY
    =============================================*/
       // BORRA EL TEXTO DEL TXT DEL CODIGO
        $("#modalUbicacionProducto").on('hidden.bs.modal', function () {
            document.getElementById("selector").value = "";
        });
     /*=============================================
          CLICK EN BOTON DE BUSCAR
          =============================================*/  
        document.getElementById('selector').addEventListener('keydown', 
        function (event) {
          //console.log(document.getElementById("selector").value);
          
            if (event.which == 13 || event.keyCode == 13) {
                myFunctionCodigoUbicacion();
                return false;
            }
        return true;
          
        }); 

       


</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ubicaciones';
</script>
