<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_bodegas.php";

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
      Consulta productos
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Consulta productos</li>
    </ol>
  </section>
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
          <div class="input-group mb-3">
              <div class="input-group-prepend">
                  <span class="input-group-text" id="Articulo:">Buscar Código</span>
              </div>
              <input type="text" id="selector" class="form-control" placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
              <hr>
              <div style="font-size:large;color:#3989c6;">5 = Familia / 6 y Marca = Descripcion</div>
              <button onclick="myFunctionCodigoInventario()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>        
                  Buscar Código!
              </button> 

         </div>

      </div>
      <center>
      <div id="resultado_codigo" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>
    </div>

  </section>

</div>

<!--=====================================
MODAL MOVIMIENTOS
======================================-->
<div id="modalMovimientoProducto" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL MOVIMIENTOS
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Movimiento de Codigo</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL MOVIMIENTOS
            ======================================-->
            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <div class="form-group">
                            <center>
                                <div id="resultadoM">
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


<!--=====================================
MODAL PASE
======================================-->
<div class="modal fade" id="ModalPase" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Pase de código</h4>
            </div>
            <center>
                <div  style="max-height:250px" class="modal-body">
                    <h3 style="color:red;" >Pase de codigo</h3>
                    <h4 style="color:blue;" id="paseorigen"  ></h4>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="Articulo:">Buscar Código</span>
                        </div>
                        <input type="text" id="selector2" class="form-control" placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
                        <hr>

                        <div style="font-size:large;color:#3989c6;">5 = Familia / 6 y Marca = Descripcion</div>
                        <button onclick="myFunctionCodigoInventario2()" id="consulta2" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>
                            Buscar Código!
                        </button>

                    </div>

                </div>
            </center>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>
<!-- FIN MODAL -->
<!--=====================================
MODAL TOPRAN
======================================-->

<div class="modal fade" id="Modaltopran" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Equivalencias Topran</h4>
            </div>
            <center>
                <div style="max-height:150px" class="modal-body">
                    <h3 style="color:red;" >Equivalencias Topran</h3>
                    <input type="text" id="mod_codigo_tai" >
                    <input type="text" id="mod_codigo_topran" autocomplete="off">
                </div>
            </center>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                

            </div>
        </div>
    </div>
</div>

<!-- FIN MODAL -->

<!--=====================================
MODAL EDITAR PRODUCTO
======================================-->
<div id="modalEditarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar producto</h4>
        </div>
        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          <div class="box-body">
            <!-- ENTRADA PARA EL CÓDIGO -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-ios-barcode-outline"></i></span>
                <input type="text" class="form-control input-lg" id="editarCodigo" name="editarCodigo" readonly required>
              </div>
            </div>
            <!-- ENTRADA PARA LA DESCRIPCIÓN -->
             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span>
                <input type="text" class="form-control input-lg" id="editarDescripcion" name="editarDescripcion" required>
              </div>
            </div>
            <!-- ENTRADA PARA LA DESCRIPCIÓN TECNICA-->
             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-android-settings"></i></span>
                <input type="text" class="form-control input-lg" id="editarTecnica" name="editarTecnica" required>
              </div>
            </div>
             <!-- ENTRADA PARA STOCK **VALIDAR USUARIO****-->
             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-android-unlock"></i></span>
                <?php 
                  if($_SESSION["perfil"] == "total"){
                    echo '<input type="number" class="form-control input-lg" id="editarStock" name="editarStock" min="0" required>';
                  }else{
                    echo '<input type="number" class="form-control input-lg" id="editarStock" name="editarStock" min="0" readonly required>';
                  }  
                 ?>
              </div>
            </div>
             <!-- ENTRADA PARA PRECIO COMPRA -->
             <div class="form-group row">
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="ion ion-cash"></i></span>
                    <input type="number" class="form-control input-lg" id="editarPrecioCompra" name="editarPrecioCompra" step="any" min="0" required>
                  </div>
                </div>
                <!-- ENTRADA PARA PRECIO VENTA -->
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="ion ion-android-cart"></i></span>
                    <input type="number" class="form-control input-lg" id="editarPrecioVenta" name="editarPrecioVenta" step="any" min="0"  required>
                  </div>
                </div>
              </div>
             <!-- ENTRADA PARA MARCA -->
             <div class="form-group row">
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="ion ion-model-s"></i></span>
                    <input type="text" class="form-control input-lg" id="editarMarca" name="editarMarca" step="any"  required>
                  </div>
                </div>
                <!-- ENTRADA PARA PROVEEDOR -->
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="ion ion-earth"></i></span>
                    <input type="text" class="form-control input-lg" id="editarProveedor" name="editarProveedor" required>
                  </div>
                </div>
              </div>
                <!-- ENTRADA PARA UBICACION-->
            <div class="form-group row">
             <div class="col-xs-6">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-android-pin"></i></span>
                <input type="text" class="form-control input-lg" id="editarUbicacion" name="editarUbicacion" min="0" required>
              </div>
            </div>
            <!-- ENTRADA PARA POTENCIA-->
             <div class="col-xs-6">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-speedometer"></i></span>
                <input type="number" class="form-control input-lg" id="editarPotencia" name="editarPotencia" min="0" required>
              </div>
            </div>
            <!-- FIN ENTRADAS  -->
        <!--=====================================
        PIE DEL MODAL
        ======================================-->
        <hr>
        <div class="modal-footer">
          <p style="color:#d2cece;""  name="id_tecnicas" id="id_tecnicas">Usuario</p>
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
        <?php
          $editarProducto = new ControladorProductos();
          $editarProducto -> ctrEditarProducto();
        ?>
    </div>
  </div>

</div>

<?php
  $eliminarProducto = new ControladorProductos();
  $eliminarProducto -> ctrEliminarProducto();
?>


<script>

    /*=============================================
     DOCUMENT READY
    =============================================*/
    $(document).ready(function(){
        // CLICK BUSCAR
        
        document.getElementById("consulta").onclick = function(){
          myFunctionCodigoInventario();
        };

        
        // BORRA EL TEXTO DEL TXT DEL CODIGO
        $("#modalBuscarCodigo").on('hidden.bs.modal', function () {
            document.getElementById("selector").value = "";
        });

       
        
    });
    /*=============================================
LLAMA MODAL DE TOPRAN
=============================================*/
    function topran(codigo){

        $("#mod_codigo_tai").val(codigo);
        var param = {
            codigo: codigo
        };
        var topran="";
        $.ajax({
            data: param,
            url: "vistas/modulos/pedidos/pedidos_topran.php",
            method: "post",
            success: function(data) {
                // console.log(data);
                $("#mod_codigo_topran").val(data);

            }
        });
        $("#Modaltopran").modal();
    }

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
                myFunctionCodigoInventario();
                return false;
            }
        return true;
          
        });
        document.getElementById('selector2').addEventListener('keydown',
        function (event) {
            //console.log(document.getElementById("selector").value);

            if (event.which == 13 || event.keyCode == 13) {
                myFunctionCodigoInventario2();
                return false;
            }
            return true;

        });
</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Productos Bodega';
</script> 
