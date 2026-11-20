<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_productos.php";

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){


  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Administrar productos
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar productos</li>
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
              <button onclick="myAgregarCodigo()" style="margin-left: 10px" class="btn btn-primary" >
                  Agregar producto
              </button>
              <button onclick="myAgregarFoto()" style="margin-left: 10px" class="btn btn-primary" >
                Agregar Foto
              </button>    
         </div>
          <div class="col-6 col-sm-4">
              <br>
                  <button onclick="espacioblanco()" class="btn btn-info" data-toggle ="modal"  ><i class="ion ion-ios-redo"></i>   Espacios en Blanco</button>

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
MODAL FOTOS
======================================-->
<div id="modalAgregarFoto" class="modal fade" tabindex="-1" role="dialog">
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
                                <div id="resultadoFotos">
                                <form class="form-signin" method="post" enctype="multipart/form-data">
                                  <table align="center" border="0" cellspacing="0">
                                    <center><h1><span class="glyphicon glyphicon-camera" aria-hidden="true"></span>&nbsp;Cargar Multiple Archivos</h1>
                                    </center>                                      
                                    <div class="col-sm-16">
                                      <input type="file"  id="archivo[]" name="archivo[]" multiple="">
                                      <br>
                                      <select class="form-control" id="tipo" name="tipo" required>
                                      <optgroup >
                                        <?php
                                          $sql = "SELECT * FROM Marcas";
                                          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                          foreach ($respuesta as $key => $value) {
                                            echo '<option value="'.$value["numero"].'">'.$value["descripcion"].'</option>';
                                          }  
                                        echo '</optgroup>'
                                      ?>
                                      </select>
                                    </div>
                                    <br>
                                  <button type="submit" class="btn btn-primary">Cargar</button>
                                  </table> 
                                </form>
                                </div>   
                            </center>
                        </div>      
                    </div>
                </div>
                <?php
                $crearFoto = new ControladorUtilitaria();
                $crearFoto -> ctrCrearFoto();
                 ?>
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
MODAL AGREGAR PRODUCTO
======================================-->
<div id="modalAgregarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar producto</h4>
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
                <input type="text" class="form-control input-lg" id="nuevoCodigo" name="nuevoCodigo" placeholder="Ingresar código" readonly required>
              </div>
            </div>
            <!-- ENTRADA PARA LA DESCRIPCIÓN -->
             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoDescripcion" placeholder="Ingresar descripción" required>
              </div>
            </div>
            <!-- ENTRADA PARA LA DESCRIPCIÓN TECNICA-->
             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-android-settings"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoTecnica" placeholder="Ingresar descripción Técnica" required>
              </div>
            </div>
             <!-- ENTRADA PARA PRECIO COMPRA -->
             <div class="form-group row">
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="ion ion-cash"></i></span>
                    <input type="number" class="form-control input-lg" id="nuevoPrecioCompra" name="nuevoPrecioCompra" step="any" min="0" placeholder="Precio de costo" required>
                  </div>
                </div>
                <!-- ENTRADA PARA PRECIO VENTA -->
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="ion ion-android-cart"></i></span>
                    <input type="number" class="form-control input-lg" id="nuevoPrecioVenta" name="nuevoPrecioVenta" step="any" min="0" placeholder="Precio de venta" required>
                  </div>
                </div>
            </div>
            <!-- ENTRADA PARA UBICACION -->
             <div class="form-group row">
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="ion ion-android-pin"></i></span>
                    <input type="text" class="form-control input-lg" id="nuevoUbicacion" name="nuevoUbicacion" placeholder="Ubicacion" required>
                  </div>
                </div>
                <!-- ENTRADA PARA POTENCIA -->
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="ion ion-speedometer"></i></span>
                    <input type="number" class="form-control input-lg" id="nuevoPotencia" name="nuevoPotencia" placeholder="Potencia" required>
                  </div>
                </div>
            </div>
            <!-- ENTRADA PARA SELECCIONAR PROVEEDOR -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-earth"></i></span>
                <select class="form-control input-lg" id="nuevoProveedor" name="nuevoProveedor" required>
                  <option value="">Selecionar Proveedor</option>
                  <?php
                  $item = null;
                  $valor = null;
                  $categorias = ControladorProveedores::ctrMostrarProveedores($item, $valor);
                  foreach ($categorias as $key => $value) {
                    echo '<option value="'.$value["codigo"].'">'.$value["nombre"].'</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            <!-- ENTRADA PARA SELECCIONAR MARCA -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-model-s"></i></span>
                <select class="form-control input-lg" id="nuevoMarca" name="nuevoMarca" required>
                  <option value="">Selecionar Marca</option>
                  <?php
                    $tabla= "Marcas";
                    $item = null;
                    $valor = null;
                    $marca = ControladorUtilitaria::ctrMostrarUtilitaria($tabla,$item, $valor);
                    foreach ($marca as $key => $value) {
                      echo '<option value="'.$value["codigo"].'">'.$value["descripcion"].'</option>';
                    }
                 ?>
                </select>
              </div>
            </div>
          </div>
        </div>
        <!--=====================================
        PIE DEL MODAL
        ======================================-->
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar producto</button>
        </div>
      </form>
        <?php
          $crearProducto = new ControladorProductos();
          $crearProducto -> ctrCrearProducto();
        ?>
    </div>
  </div>

</div>
<!--=====================================
MODAL BLANCOS
======================================-->
<div class="modal fade" id="ModalBlancos" role="dialog">
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header" style="background:#3c8dbc; color:white">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Espacios en Blanco</h4>
        </div>
        <center>
            <div  class="modal-body">
                <h3 style="color:red;" >Listado de Codigos con espacios en blanco</h3>
                <?php
                $sql = "SELECT CODIGO, DESCRIPCION, Existencia, ubicacion,
                        substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
                        WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
                        WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
                        Else CODIGO END As Filtro FROM Inventario WHERE CODIGO LIKE '% ' order by Filtro
                        ";
                echo '<center><table border =1>
		               
		                <tr class="encabezadoRegistro">
		                    <th>Codigo</th>
		                    <th>Descripcion</th>
		                    <th>Existencia</th>
		                    <th>Ubicacion</th>
		                    <th>Eliminar</th>';
                echo '</tr>';
                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    echo '<tr>';
                    echo '<td>'.$arr["CODIGO"].'</td><td>'.$arr["DESCRIPCION"].'</td><td>'.$arr["Existencia"].'</td><td>'.$arr["ubicacion"].'</td>';
                    echo "<td><a class ='btn btn-danger btnEditarCliente' href='#' onclick='if(confirm(\"Desea Arreglar el codigo?\")) 
      				        location.href=\"vistas/modulos/espacios_eliminar.php?codigo=".$arr["CODIGO"]."\";
      				        '><i class=\"fa fa-times\"></a></td>";
                }
                echo '</table></center>';
                ?>
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
            </div>
            <!-- ENTRADA PARA CABYS-->
             <div class="form-group row">
             <div class="col-xs-6">
              <div class="input-group">
                <span class="input-group-addon"><i class="ion ion-android-settings"></i></span>
                <input type="number" class="form-control input-lg" placeholder="CABYS" id="editarCabys" name="editarCabys" min="0" required>
              </div>
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
    document.title = 'Sistema / Productos';
</script> 
