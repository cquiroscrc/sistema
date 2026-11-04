<?php
date_default_timezone_set("America/Costa_Rica");
$num_prefactura="";
include "modelos/procesos_factura.php"
?>
<style>
    body {
        overflow-x: hidden;
    }
    .redondeado {
        border-radius: 5px;
    }
    .confondo {
        background-color: #def;
    }
    .sinborde {
        border: 0;
    }
    .sinbordefondo {
        background-color: #eee;
        border: 0;
    }
    .outlinenone {
        outline: none;
        background-color: #dfe;
        border: 0;
    }
    .redondeadonorelieve {
        border-radius: 5px;
        border: 1px solid #39c;
    }


</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1 style="display:inline;">
            Facturación **//**
        </h1>
        <?php
        $num_prefactura = $_SESSION["usuario"]."-".rand(10,99);
        echo '<h3 style="display:inline;" class="invoice-id" id="invoice-id" name="invoiceid">'.$num_prefactura.'</h3>';
        ?>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Facturación</li>
        </ol>
    </section>
    <section class="content">
        <div id="invoice">
            <div class="invoice overflow-auto">
                <div style="min-width: 600px">
                    <div class="row contacts">
                        <div class="row container">
                            <div class="col-6 col-sm-4">
                                <div class="col invoice-to">
                                    <button onclick="myFunctionCliente()" id="consultacliente" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search"></i>    Buscar Cliente!
                                    </button>
                                    <button onclick="myAgregarCliente()" id="crearcliente" class="btn btn-warning" data-toggle ="modal"  ><i class="fa fa-address-card"></i>    Crear Cliente!
                                    </button>
                                    <div class="text-gray-light">FACTURAR A:</div>
                                    <h4 id="nombrecliente" class="to">CLIENTE DE CONTADO</h4>
                                    <div id="cedulacliente" class="address">111111111</div>
                                    <div id="codigocliente" class="address">0</div>
                                    <div id="correocliente" style="color:blue;" class="email">facturaelectronica@lospaticas.com</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <i style="font-size:large;color:#3989c6;" class="fa fa-users">VENDEDOR
                                    <select   id="seleccionarVendedor" name="seleccionarVendedor" required>
                                        <option value="">SELECCIONAR VENDEDOR</option>
                                        <?php
                                        $valor = "SELECT * FROM Vendedores ORDER BY codigo";
                                        $ventas = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);
                                        foreach ($ventas as $key => $value) {
                                            echo '<option value="'.$value["codigo"].'">'.$value["nombre"].'</option>';
                                        }
                                        ?>
                                    </select></i>
                                <br><br>
                                <div id="codigovendedor" style="display:none;"></div>
                            </div>
                            <div class="col-6 col-sm-4 ">
                                <button style="width:125px;" onclick="myBorrar()" id="callBorrar" class="btn btn-danger"><i class="fa fa-trash"></i> Borrar</button>
                                <button style="width:125px;" onclick="myDevolucion()" id="callDevolucion" class="btn btn-warning"><i class="fa fa-undo"></i> Devolucion</button><br>
                                <button style="width:125px;" onclick="myEncomienda()" id="callEncomiendas" class="btn btn-info"><i class="fa fa-bus"></i> Encomiendas</button>
                                <button style="width:125px;" onclick="myTelefonos()" id="callTelefonos" class="btn btn-default"><i class="fa fa-phone"></i> Teléfonos</button><br>
                                <button style="width:125px;background:yellow;" onclick="OrdendeCompra()" id="callProforma" class="btn btn-default"><i class="fa fa-truck"></i> Orden Compra
                                </button>
                                <button style="width:125px;" onclick="hacerProforma()" id="callProforma" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Proforma PDF
                                </button><br>
                                <button style="width:125px;" onclick="PrepararFactura()" id="callFacturar" class="btn btn-success"><i class="fa fa-print"></i> Facturar</button>
                            </div>
                        </div>
                        <!-- Consultar Codigo -->
                        <hr style="border:2px;" >
                        <hr style="border-color:#3989c6;">
                        <div class="col-6 col-sm-4">
                            <div class="notices">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="Articulo:">Buscar Código</span>
                                    </div>
                                    <input type="text" id="selector" class="form-control" placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
                                    <hr>
                                    <div style="font-size:large;color:#3989c6;">5 = Familia / 6 y Marca = Descripcion</div>
                                    <button onclick="myFunctionCodigo()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>
                                        Buscar Código!
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--ya-->
                    <div class="col-12">
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap" id="agregar-producto">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>CODIGO / DESCRIPCION</th>
                                    <th>PRECIO</th>
                                    <th>CANTIDAD</th>
                                    <th>TOTAL</th>
                                    <th style="visibility:hidden"></th>
                                </tr>
                                </thead>
                                <tbody id="productos_list">
                                </tbody>
<!--                                <tr style="display:none;">-->
                                <tr id="acomodo" style="visibility:hidden">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td >123456789012345678901234567890123456789012</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.row -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap" id="agregar-producto">
                            <thead>
                            <tr>
                            </tr>
                            </thead>
                        </table>
                        <!--                    <hr style="border-color:#3989c6;">-->
                    </div>
                    <!--ya-->
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-responsive table-striped" id="totales">
                                <tr>
                                    <td style="width:40px" class="no">SUBTOTAL</td>
                                    <td style="width:100px" id="fact_subtotal">0.00</td>
                                    <td style="visibility:hidden"></td>
                                </tr>
                                <tr>
                                    <td class="no">IMPUESTO</td>
                                    <td id="fact_impuesto">0.00</td>
                                    <td style="visibility:hidden"></td>
                                </tr>
                                <tr>
                                    <th class="no">TOTAL</th>
                                    <b>
                                        <th  id="fact_total">0.00</th>
                                        <td style="visibility:hidden"></td>
                                    </b>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                    if($_SESSION["perfil"] =="total"|| $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "especial" ){
                        ?>
                        <button onclick="BuscarExo()">Exoneraciones</button>
                        <p id="datosEXO"></p>
                    <?php } ?>
                </div>
                <div>
                </div>
            </div>
    </section>

    <!--=====================================
    MODAL DESCRIPCION TECNIA
    ======================================-->

    <div class="modal fade" id="modalTecnica" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Descripcion Técnica</h4>
                </div>
                <center>
                    <h4 id="codigo_tecnica"></h4>
                    <h5 id="descripcion_tecnica"></h5>
                    <br>
                    <textarea name="text_tecnica" id="text_tecnica" rows="3" cols="80" ></textarea>
                </center>
                <div class="modal-footer">
                    <p style="color:#d2cece;"" id="id_tecnica"></p>
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-success" onclick="GuardarTecnica()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- FIN MODAL -->
    <!--=====================================
    MODAL FACTURAR CODIGO
    ======================================-->

    <div id="modalFacturar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
                <!--=====================================
                CABEZA DEL MODAL
                ======================================-->
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Facturar</h4>
                </div>
                <!--=====================================
                CUERPO DEL MODAL
                ======================================-->
                <div  class="modal-body">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="box-body">
                            <!-- ENTRADA PARA EL NOMBRE -->
                            <div class="form-group">
                                <!-- <button class="btn btn-default">Exonerado</button> -->
                                <center>
                                    <div id="resultfact">
                                        <form class="form-horizontal" onsubmit="return false" >
                                            <div class="form-group">
                                                <label class="control-label col-sm-2" for="nombreCliente">Nombre:</label>
                                                <div class="col-sm-10">
                                                    <input style="font-size: 22px" type="text" class="form-control redondeado confondo" id="nombreCliente" autocomplete="off" placeholder="Ingrese el nombre del cliente" readonly name="nombreCliente">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2" for="nombreAdicional">Adicional:</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control redondeado confondo" id="nombreAdicional" autocomplete="off" placeholder="Ingrese algun dato adicional" name="nombreAdicional">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2" for="Facturasubtotal">Subtotal:</label>
                                                <div class="col-sm-10">
                                                    <input style="font-size: 22px" type="text" class="form-control redondeado confondo" id="Facturasubtotal" autocomplete="off" readonly name="Facturasubtotal">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2" for="Facturadescuento">Descuento:</label>
                                                <div class="col-sm-10">
                                                    <input style="font-size: 22px" ondblclick="FacRecalcularOriginal()" onblur="FacRecalcular()" type="text" class="form-control redondeado confondo" id="Facturadescuento" autocomplete="off"  name="Facturadescuento">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2" for="Facturaimpuesto">Impuesto:</label>
                                                <div class="col-sm-10">
                                                    <input style="font-size: 22px" type="text" class="form-control redondeado confondo" id="Facturaimpuesto" autocomplete="off" readonly name="Facturaimpuesto">
                                                </div>
                                            </div>
                                            <div id="divExo" class="form-group" style="display:none">
                                                <label class="control-label col-sm-2" for="FacturaExoneracion">Exoneracion:</label>
                                                <div class="col-sm-10">
                                                    <input style="font-size: 22px" type="text" class="form-control redondeado confondo" id="FacturaExoneracion" autocomplete="off" readonly name="FacturaExoneracion">
                                                </div>
                                            </div>
                                            <div id="divExo2" class="form-group" style="display:none">
                                                <label class="control-label col-sm-2" for="InstitucionExoneracion">Institucion:</label>
                                                <div class="col-sm-10">
                                                    <input style="font-size: 22px" type="text" class="form-control redondeado confondo" id="InstitucionExoneracion"   name="InstitucionExoneracion">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2" for="Facturatotal">Total:</label>
                                                <div class="col-sm-10">
                                                    <input style="font-size: 24px; color:red;" type="text" class="form-control redondeado confondo" id="Facturatotal" autocomplete="off" readonly name="Facturatotal">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2" for="Pago">Tipo de pago:</label>
                                                <div class="col-sm-10">
                                                    <select style="font-size: 18px; color:green;  "class="form-control redondeado confondo" name="t_pago" id="t_pago">
                                                        <option value="01" selected="selected">1- Efectivo</option>
                                                        <option value="02">2- Tarjeta</option>
                                                        <option value="04">4- Transferencia</option>
                                                        <option value="06">6- Sinpe MOVIL</option>
                                                    </select>
                                                    
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                    <!--=====================================
                    PIE DEL MODAL
                    ======================================-->
                    <div class="modal-footer">
                        <button onclick="imprimirFacturaEPC()" id="callBorrar" class="btn btn-success"><i class="fa fa-save"></i> Facturar</button>
                        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====================================
    MODAL BUSCAR CODIGO
    ======================================-->
    <div style="width:100%" id="modalBuscarCodigo" class="modal fade" tabindex="-1" role="dialog">
        <div class="">
            <div class="modal-content">
                <!--=====================================
                CABEZA DEL MODAL
                ======================================-->
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Resultado Busqueda Código</h4>
                </div>
                <!--=====================================
                CUERPO DEL MODAL
                ======================================-->
                <div style="width:100%; height: 100%" class="modal-body">
                    <div class="" role="document">
                        <div class="box-body">
                            <div class="form-group">
                                <center>
                                    <div id="result">
                                        <!-- AQUI VA EL AJAX -->
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                    <!--=====================================
                    PIE DEL MODAL
                    ======================================-->
                    <div class="modal-footer">
                        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====================================
    MODAL BUSCAR CLIENTE
    ======================================-->
    <div id="modalBuscarCliente" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
                <!--=====================================
                CABEZA DEL MODAL CLIENTE
                ======================================-->
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Resultado Busqueda Cliente</h4>
                </div>
                <!--=====================================
                CUERPO DEL MODAL CLIENTE
                ======================================-->
                <div class="modal-body">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="box-body">
                            <!-- ENTRADA PARA EL NOMBRE -->
                            <div class="form-group">
                                <center>
                                    <div id="result1">
                                        <!-- AQUI VA EL AJAX -->
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                    <!--=====================================
                    PIE DEL MODAL CLIENTE
                    ======================================-->
                    <div class="modal-footer">
                        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====================================
    MODAL TELEFONOS
    ======================================-->

    <div id="modalBuscarTelefonos" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
                <!--=====================================
                CABEZA DEL MODAL TELEFONOS
                ======================================-->
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Resultado Busqueda Teléfonos</h4>
                </div>
                <!--=====================================
                CUERPO DEL MODAL TELEFONOS
                ======================================-->
                <div class="modal-body">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="box-body">
                            <!-- ENTRADA PARA EL NOMBRE -->
                            <div class="form-group">
                                <center>
                                    <div id="resultadot">
                                        <!-- AQUI VA EL AJAX -->
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                    <!--=====================================
                    PIE DEL MODAL TELEFONO
                    ======================================-->
                    <div class="modal-footer">
                        <button onclick="InsertarTelefono()" id="callBorrar" class="btn btn-success"><i class="fa fa-save"></i> Agregar Telefono</button>
                        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====================================
    MODAL AGREGAR CLIENTE
    ======================================-->
    <div id="modalBuscarAgregarCliente" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
                <!--=====================================
                CABEZA DEL MODAL AGREGAR CLIENTE
                ======================================-->
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Ingresar Cliente</h4>
                </div>
                <!--=====================================
                CUERPO DEL MODAL AGREGAR CLIENTE
                ======================================-->
                <div class="modal-body">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="box-body">
                            <div id="resultadoCli">
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="nombreCli">Nombre:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control redondeado confondo" id="nombreCli" placeholder="Ingrese el nombre" name="nombreCli">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="cedulaCli">Cédula:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control redondeado confondo" id="cedulaCli" placeholder="Ingrese Cédula" name="cedulaCli">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="telefonoCli">Telefono:</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control redondeado confondo" id="telefonoCli" placeholder="Ingrese Teléfono" name="telefonoCli">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="celularCli">Celular:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control redondeado confondo" id="celularCli" placeholder="Ingresar Celular" name="celularCli">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="correoCli">Correo:</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control redondeado confondo" id="correoCli" placeholder="Ingresar Correo" name="correoCli" onKeyUp="javascript:validateMail('correoCli')">
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <label class="control-label col-sm-2" for="actividadCli">Actividad Comercial:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control redondeado confondo" id="actividadCli" placeholder="Actividad Comercial" name="actividadCli">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="tipoCli">Tipo Cédula:</label>
                                        <div class="col-sm-10">
                                            <select class="form-control redondeado confondo" id="tipoCli" name="tipoCli">
                                                <optgroup >
                                                    <option value="01">01- Cédula Física</option>
                                                    <option value="02">02- Cédula Jurídica</option>
                                                    <option value="03">03- DIMEX</option>
                                                    <option value="04">04- NITE</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--=====================================
                    PIE DEL MODAL AGREGAR CLIENTE
                    ======================================-->
                    <div class="modal-footer">
                        <button onclick="InsertarCliente()" id="callBorrar" class="btn btn-success"><i class="fa fa-save"></i> Agregar Cliente</button>
                        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====================================
    MODAL ORDEN DE COMPRA
    ======================================-->

    <div id="modalOrdendeCompra" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
                <!--=====================================
                CABEZA DEL MODAL ORDEN DE COMPRA
                ======================================-->
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Orden de Compra</h4>
                    <div id="consecutivoOC"></div>
                </div>

                <!--=====================================
                CUERPO DEL MODAL ORDEN DE COMPRA
                ======================================-->

                <div class="modal-body">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="box-body">
                            <div id="resultadoOrden">
                                <form class="form-horizontal" onsubmit="return false" >
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="nombreEmpresa">Nombre Empresa:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control redondeado confondo" id="nombreEmpresa" autocomplete="off" placeholder="Ingrese el nombre de la emprensa" name="nombreEmpresa" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="cantidadO">Cantidad:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control redondeado confondo" id="cantidadO" autocomplete="off" placeholder="Ingrese la cantidad del producto" name="cantidadO" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="descripcionO">Descripcion:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control redondeado confondo" id="descripcionO" autocomplete="off" placeholder="Ingrese la descripción del producto" name="descripcionO" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                    <button onclick="InsertaraOrden()" id="insertarO" class="btn btn-success"><i class="fa fa-save"></i> Agregar Producto</button>
                                </form>
                            </div>
                            <hr>
                            <div>
                                <table border = 1>
                                    <thead id="ProOC">
                                    <td style="width:90px;"><b>Cantidad</b></td>
                                    <td style="width:500px;"><b>Descripcion</b></td>
                                    </thead>
                                    <tbody id="listOC">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--=====================================
                    PIE DEL MODAL ORDEN DE COMPRA
                    ======================================-->
                    <div class="modal-footer">
                        <button onclick="hacerOrden()" id="callBorrar" class="btn btn-success"><i class="fa fa-print"></i> Imprimir Orden</button>
                        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====================================
    MODAL DEVOLUCION
    ======================================-->

    <div id="modalDevolucion" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
                <!--=====================================
                CABEZA DEL MODAL DEVOLUCION
                ======================================-->
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Devolución de producto</h4>
                </div>
                <!--=====================================
                CUERPO DEL MODAL DEVOLUCION
                ======================================-->
                <div class="modal-body">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="box-body">
                            <center>
                                <div id="resultadoDev">
                                </div>
                            </center>
                        </div>
                    </div>
                    <!--=====================================
                    PIE DEL MODAL DEVOLUCION
                    ======================================-->
                    <div class="modal-footer">
                        <button onclick="DevolucionAplicar()" id="callBorrar" class="btn btn-success"><i class="fa fa-save"></i> Aplicar</button>
                        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====================================
    MODAL ENCOMIENDAS
    ======================================-->
    <div id="modalBuscarEncomiendas" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
                <!--=====================================
                CABEZA DEL MODAL ENCOMIENDAS
                ======================================-->
                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Resultado Busqueda Encomiendas</h4>
                </div>
                <!--=====================================
                CUERPO DEL MODAL ENCOMIENDAS
                ======================================-->
                <div class="modal-body">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="box-body">
                            <div class="form-group">
                                <center>
                                    <div id="resultadoE">
                                        <!-- AQUI VA EL AJAX -->
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                    <!--=====================================
                    PIE DEL MODAL ENCOMIENDAS
                    ======================================-->
                    <div class="modal-footer">
                        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====================================
    MODAL PREFACTURA
    ======================================-->

    <!-- Modal -->
    <div class="modal fade" id="modalPrefactura" role="dialog">
        <div class="modal-dialog ">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Prefacturas salvadas</h3>
                </div>
                <div class="modal-body">

                    <p><i class="ion ion-ios-paper"></i> Seleccione la prefactura que desee continuar</p>
                    <center>
                        <div class="input-group">

                            <table border="1" id="example" class="display" cellspacing="0" cellpadding="2">

                                <tr>
                                    <th style="text-align:center;" width="50"> Prefactura </th>
                                    <th style="text-align:center;" width="50"> Vendedor </th>
                                    <th style="text-align:center;" width="200"> Nombre Vendedor </th>
                                    <th style="text-align:center;" width="50"> Cliente </th>
                                    <th style="text-align:center;" width="200"> Nombre Cliente </th>
                                    <th style="text-align:center;" width="150">Fecha</th>
                                </tr>

                                <?php
                                // Revisa si existe alguna prefactura y la muestra
                                $busqueda= $_SESSION["usuario"];
                                $valor = "SELECT DISTINCT h.Factura , h.fecha, h.codcliente, h.vendedor,c.nombre as nomcliente, v.nombre as nomvendedor FROM hpregeneral h, clientes c, Vendedores v  WHERE h.codcliente = c.codigo AND h.vendedor = v.codigo AND h.Factura like '$busqueda%' ORDER BY fecha";

                                $prefactura = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);
                                $entro=0;
                                foreach ($prefactura as $key => $value) {

                                    echo '<tr>';

                                    echo'  <td>
                                              <button class="btn btn-success" id="sel_prefactura" onclick="myPrefactura('."'".$value["Factura"]."'".')">'.$value["Factura"].'</button> 
                                            </td>';

                                    echo'<td style="text-align:center;">'.$value["vendedor"].'</td>
                                            <td style="text-align:center;">'.$value["nomvendedor"].'</td>
                                            <td style="text-align:center;">'.$value["codcliente"].'</td>
                                            <td style="text-align:center;">'.$value["nomcliente"].'</td>
                                            <td style="text-align:right;">'.$value["fecha"].'</td>

                                          </tr>';
                                    $entro=1;
                                }
                                ?>
                            </table>
                        </div>
                    </center>
                </div>
                <div class="modal-footer">
                    <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                </div>
            </div>

        </div>
    </div>
    <!--=====================================
            FIN DEL MODAL PREFACTURA
    ======================================-->
</html>

<?php
if ($entro==1){

    ?>
    <script>
        $("#modalPrefactura").modal("show");
    </script>
    <?php
}
?>


<script>

    /*=============================================
     DOCUMENT READY
    =============================================*/
    $(document).ready(function(){



        if ($(window).width() > 960) {
            acomodo.style.display='none';
        }
        // CLICK BUSCAR
        ConsultaIVA();
        document.getElementById("consulta").onclick = function(){
            myFunctionCodigo();
        };

        // CAMBIA VALOR DEL VENDEDOR
        $('select#seleccionarVendedor').on('change',function(){
            var valor = $(this).val();
            $('#codigovendedor').text(valor);
            var codigocliente= $('#codigocliente').text();
            var codigo =$('#invoice-id').text()
            var codigovendedor = $('#codigovendedor').text();
            var param = {
                Factura: codigo,
                codcliente: codigocliente,
                vendedor: codigovendedor,

            };

            url="ajax/actualizar_cliente.ajax.php";
            data="";
            data=param;

            myAjaxHH(url,data)
                .done( function(data) {
                    console.log(data);
                });
        });

        // BORRA EL TEXTO DEL TXT DEL CODIGO
        $("#modalBuscarCodigo").on('hidden.bs.modal', function () {
            document.getElementById("selector").value = "";
        });

        //ELIMINA CODIGOS
        $('#productos_list').on('click','.no', function(){
            eliminar_codigo= $(this).attr("value");
            var fac_act = $('#invoice-id').text();
            myElimina(eliminar_codigo,fac_act);
        });
        //CAMBIO DE PRECIOS CODIGOS
        $('#productos_list').on('click','.unit', function(){

            var nuevo_precio;
            var codigo_precio=$(this).attr("value");
            var fac_act = $('#invoice-id').text();
            swal({
                title: "Facturacion",
                text: "Digite el precio a facturar para "+codigo_precio,
                input: 'text',
                type: "question",

            }).then(resultado => {
                if (resultado.value) {
                    let nuevo_precio=resultado.value;
                    myFunctionActualizaPrecio(codigo_precio,fac_act,nuevo_precio);
                }
            });

        });

        //CAMBIO DE PRECIOS CODIGOS
        $('#productos_list').on('click','.qty', function(){

            var nuevo_cantidad;
            var codigo_cantidad=$(this).attr("value");
            var fac_act = $('#invoice-id').text();
            swal({
                title: "Facturacion",
                text: "Digite la cantidad a facturar para "+codigo_cantidad,
                input: 'text',
                type: "question",

            }).then(resultado => {
                if (resultado.value) {
                    let nuevo_cantidad=resultado.value;
                    myFunctionActualizaCantidad(codigo_cantidad,fac_act,nuevo_cantidad);
                }
            });

        });

        $('resultadoOrden#Facturadescuento').on('change',function(){
            FacRecalcular();

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
                myFunctionCodigo();
                return false;
            }
            return true;

        });

    document.getElementById('Facturadescuento').addEventListener('keydown',
        function (event) {
            //console.log(document.getElementById("selector").value);

            if (event.which == 13 || event.keyCode == 13) {
                FacRecalcular();
                return false;
            }
            return true;

        });


</script>



<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Facturacion';
</script>