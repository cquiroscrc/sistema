<?php
//session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$sql_Pedidos = "SELECT DISTINCT p.factura, p.Proveedor, e.nombre FROM Pedidos p, Proveedores e WHERE p.Proveedor = e.codigo";
?>
<style>
    .modal-full {
        min-width: 100%;
        margin: 0;
    }

    .modal-full .modal-content {
        /*min-height: 100vh;*/
    }

    .redondeado {
        border-radius: 5px;
    }
    .confondo {
        background-color: #def;
    }
</style>
<div class="content-wrapper">
      <section class="content-header">
        <h1>
          Administrar Pedidos
        </h1>
        <ol class="breadcrumb">
          <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
          <li class="active">Administrar Pedidos</li>
        </ol>
      </section>
      <section class="content">
        <div class="box">
             <div class="box-header with-border">
                 <div class="col-sm-4  pull-right">
                     <button onclick="Nuevo_Pedido()"  class="btn btn-warning" data-toggle ="modal"  ><i class="fa fa-pencil"></i> Nuevo</button>
                     <button onclick="Rep_Pedido()"  class="btn btn-info" data-toggle ="modal"  ><i class="ion ion-android-print"></i> Reporte</button>
                     <button onclick="Acc_precios()"  class="btn btn-danger" data-toggle ="modal"  ><i class="fa fa-money"></i> Precios</button>
                     <?php
                     if($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" ){
                         ?>
                         <button onclick="Enviar_Pedido()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-envelope"></i> Mail</button>
                         <button onclick="Aplicar_Pedido()" id="consulta" class="btn btn-success" data-toggle ="modal"  ><i class="fa fa-check"></i> Aplicar</button>
                         <?php
                     }
                     ?>
                     <span>&nbsp;&nbsp;</span>
                     <br>
                 </div>

                  <div class="col-sm-4">
                      <div class="box-body">

                            <select   id="seleccionarpedido" name="seleccionarpedido" required>
                            <option value="">SELECCIONAR PEDIDOS</option>
                            <?php
                            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Pedidos);
                            foreach ($ejecutar as $key => $value) {
                                echo '<option value="'.$value["factura"].'">'.$value["factura"].' * '.$value["nombre"].'</option>';
                            }
                             ?>
                              </select>
                            <button class="btn btn-primary" onclick="BuscarFactura()">Cargar</button>
                            <br>
                          <h4 style="display: inline;color:blue;">Pedido #: </h4><h4 id="codigofactura" style="display: inline;color:red"></h4>
                          <h4 style="display: inline;"> * </h4><h4 id="codigoproveedor" style="display: inline;color:red"></h4>
                          <div class="input-group mb-3">
                            <input type="text" id="hp" class="form-control"  onkeydown="EnterHp(event)" placeholder="Topran" autocomplete="off">
                              <br><br>
                            <button class="btn btn-secundary" onclick="BuscarHP()">Consultar</button>
                              <h5 style="display: inline;color:blue;">Codigo #: </h5><h4 onclick="CopiarHp()" id="codigoHpAnt" style="display: inline;color:red"></h4>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-4">
                        <div class="box-body">
                            <div class="input-group mb-3">
                                <input type="text" id="selector" class="form-control" placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
                                <hr>
                                <div style="font-size:large;color:#3989c6;">5 = Familia / 6 y Marca = Descripcion</div>
                                <button onclick="myFunctionCodigo()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>
                                Buscar Código!</button>
                            </div>
                        </div>
                  </div>
                 <div class="col-sm-4">
                     <!--            vacio-->
                 </div>
             <center>
                  <div id="resultado_compra" class="box-body">
                        <!-- AQUI VA LA CONSULTA -->
                  </div>
             </center>
        </div>
      </section>
</div>
<!--=====================================
MODAL NUEVO PEDIDO
======================================-->

<div class="modal fade" id="ModalNuevo" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Nuevo Pedido</h4>
            </div>
            <center>
                <div style="max-height:150px" class="modal-body">
                    <h3 style="color:red;" >Nuevo Pedido</h3>
                    <input type="text" id="nuevo_factura" >
                    <br><br>
                    <select id="nuevo_proveedor" name="nuevo_proveedor" required>
                        <option value="">SELECCIONAR PROVEEDOR</option>
                        <?php
                        $sql_proveedor="SELECT * FROM Proveedores order by codigo DESC";
                        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_proveedor);
                        foreach ($ejecutar as $key => $value) {
                            echo '<option value="'.$value["codigo"].'">'.$value["codigo"].' - '.$value["nombre"].'</option>';
                        }
                        ?>
                    </select>

                </div>
            </center>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                <button type="submit" class="btn btn-primary" onclick="Crear_Pedido()">Crear Pedido</button>

            </div>
        </div>
    </div>
</div>

<!-- FIN MODAL -->
<!--=====================================
MODAL CAMBIO DE PRODUCTO
======================================-->

<div class="modal fade" id="ModalCambio" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Cambiar Producto</h4>
            </div>
            <center>
                <div style="max-height:150px" class="modal-body">
                    <h3 style="color:red;" >Cambiar Producto</h3>
                    <input type="text" id="mod_codigo" >
                    <input type="text" id="mod_cantidad" >
                    <input type="text" id="mod_precio" >
                    <input type="text" id="mod_id" style="display: none;">

                </div>
            </center>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                <button type="submit" class="btn btn-primary" onclick="Acc_producto()">Actualizar Producto</button>

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

                <button type="submit" class="btn btn-primary" onclick="Acc_topran()">Actualizar Producto</button>

            </div>
        </div>
    </div>
</div>

<!-- FIN MODAL -->
<!--=====================================
MODAL CALCULO
======================================-->
<div class="modal fade" id="ModalCalculo" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Calculo de precios</h4>
            </div>
            <center>
                <div style="max-height:370px" class="modal-body">
                    <h3 style="color:red;" >Calculo de precios</h3>
                    Monto Facturas
                    <input class="form-control redondeado confondo" type="text" id="calculo_facturas" >
                    <br>
                    Tipo Cambio
                    <input class="form-control redondeado confondo" type="text" id="calculo_tc" >
                    <br>
                    Gastos Aduanales
                    <input class="form-control redondeado confondo" type="text" id="calculo_gastos" >
                    <br>
                    Impuesto I.V.A
                    <input class="form-control redondeado confondo" type="text" id="calculo_iva">

                </div>
            </center>
            <div class="modal-footer">

                Factor directo
                <input class="form-control redondeo confondo" type="text" id="calculo_factor">
                <hr>
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                <button type="submit" class="btn btn-warning" onclick="Cambio_Precios('sigue')">Continuar sin Actualizar</button>
                <button type="submit" class="btn btn-primary" onclick="Cambio_Precios('calcula')">Actualizar Precios</button>
            </div>
        </div>
    </div>
</div>

<!-- FIN MODAL -->
<!--=====================================
MODAL PRECIOS
======================================-->

<div class="modal fade" tabindex="-1" id="ModalPrecios" role="dialog">
    <div class="modal-dialog modal-full">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Cambiar Precio Pedido</h4>

            </div>
            <center>
                <div style="height: 600px" class="modal-body">
                    <h3>Cambiar Precios de pedidos</h3>
                    <h4 id="precio_factura" style="display:inline;color:blue"></h4><h4 style="display:inline;color:red"> - </h4>
                    <h4 id="precio_proveedor" style="display:inline;color:blue"></h4><h4 id="precio_nombre" style="display:inline;color:blue"></h4>
                    <div id="precio_resultado">

                    </div>

                </div>
            </center>
            <div style="height: 70px" class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                <button type="submit" class="btn btn-primary" onclick="precios_pedidos()">Actualizar Precios</button>
            </div>
        </div>
    </div>
</div>

<!-- FIN MODAL -->
<!--=====================================
MODAL BUSCAR CODIGO
======================================-->
<div id="modalBuscarCodigo" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" >
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
            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
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
 MODAL BUSCAR CODIGO
 ======================================-->
<div id="modalBuscarCodigo" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" >
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
            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
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

<script>
/*=============================================
  DOCUMENT READY
  =============================================*/
var TIPO='';
var PEDIDO=''
var CONSULTO=false;
$(document).ready(function(){
    // $('select#seleccionarpedido').on('change',function(){
    //       var valor = $(this).val();
    //       CONSULTO=false;
    //        $('#codigofactura').text(valor);
    // });
});
/*=============================================
  MONEY FORMAT (convierte a numero )
  =============================================*/

function money_format(number){

    var z = number.replace(',', '');
    number = parseFloat(z);
    return number;
}
/*=============================================
 ENTER BUSCAR CODIGO
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

/*=============================================
 MY FUNCTION CODIGO (Busca los codigos)
 =============================================*/
function myFunctionCodigo() {

    var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
    $("#modalBuscarCodigo").modal()
    $('#result').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    $.ajax('vistas/modulos/pedidos/consulta_productos.php?codigo='+dataToPass.codigo)
        .done(function(response){
            $('#result').html(response);
        });
}
/*=============================================
MY CODIGO  (Agrega cantidad y precio)
=============================================*/

function myCodigo(codigo){

    var la_cantidad;
    var el_monto;
    swal({
        title: "Pedidos",
        text: "Digite la cantidad del pedido",
        input: 'text',
        type: "question",

    }).then(resultado => {
        if (resultado.value) {
            let la_cantidad = resultado.value;

            swal({
                title: "Pedidos",
                text: "Digite el monto $ del pedido",
                input: 'text',
                type: "info",

            }).then(resultado => {
                if (resultado.value) {
                    let el_monto = resultado.value;
                    mylistadoHH(codigo,la_cantidad,el_monto);
                }
            });
        }
    });
    $('#modalBuscarCodigo').modal('hide');

}
/*=============================================
MY LISTADO HH (Agrega productos a BD)
=============================================*/
function mylistadoHH(codigo,cantidad,monto){
    var factura= $('#sel_pedido').text();
    var proveedor= $('#sel_proveedor').text();
    var param = {
            factura: factura,
            proveedor: proveedor,
            codigo: codigo,
            cantidad:cantidad,
            monto:monto
        };
        //console.log(param);
        $.ajax({
            data: param,
            url: "vistas/modulos/pedidos/pedidos_agregar.php",
            method: "post",
            success: function(data) {
                //console.log(data);
                // AGREGA CODIGOS EN EL DOM
                CargarFactura()
            }
        });
}

/*=============================================
  MY AJAX HH (ejecuta AJAX)
  =============================================*/
function myAjaxHH(url,data){
    var ajaxhh=$.ajax({
        "method":"POST",
        "url": url,
        "data": data
    });
    return ajaxhh;
    console.log(ajaxhh);
}
/*=============================================
  BUSCAR FACTURA
  =============================================*/
function BuscarFactura(){

    var seleccion = $("#seleccionarpedido").val()


    if (seleccion==''){

    }else{

        $("#codigofactura").text(seleccion)
        CargarFactura();
    }

}
/*=============================================
  CARGAR FACTURA
  =============================================*/
function CargarFactura(){
    TIPO='pedido';
    CONSULTO=true;
    var factura= $('#codigofactura').text();
    var dropDown = document.getElementById("seleccionarpedido");
    dropDown.selectedIndex = 0;

   $('#resultado_compra').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    var param = {
                  Factura: factura,
                  tipo: TIPO
                  };
              url="vistas/modulos/pedidos/pedidos_pedidos.php";
              data="";
              data=param;
              myAjaxHH(url,data)
              .done( function(data) {
                $('#resultado_compra').html(data);
              });
    $("#selector").val("");
    // Para que no se cierre el DOM
    document.body.style.paddingRight="0px";
    $("#selector").focus();
}
/*=============================================
ELIMINA CODIGOS DEL PEDIDO
=============================================*/
function El_pedidos(codigo){
    swal({
        title: '¿Está seguro de eliminar el codigo ?',
        text: "¡Si no lo está puede cancelar la acción!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, borrar producto!'
    }).then(function(result){
        if (result.value) {
            var param = {
                idcodigo: codigo,
            };
            $.ajax({
                data: param,
                url: "vistas/modulos/pedidos/pedidos_eliminar.php",
                method: "post",
                success: function(data) {
                    CargarFactura()
                }
            });
        }

    })

}
/*=============================================
LLAMA MODAL DE CAMBIO DE PRECIOS
=============================================*/
function Cambio_Precios(tipo){

    var facturas = $("#calculo_facturas").val();
    var tc = $("#calculo_tc").val();
    var gastos = $("#calculo_gastos").val();
    var iva= $("#calculo_iva").val();
    var factor= $("#calculo_factor").val();

    $("#ModalCalculo").modal('hide');
    $("#calculo_facturas").val("");
    $("#calculo_tc").val("");
    $("#calculo_gastos").val("");
    $("#calculo_iva").val("");
    $("#calculo_factor").val("");

    var factores=0;
    if (facturas==''){
        factores=factor;
        factores=parseInt(factores);
    }else{
        factores=(facturas*tc)+(gastos-iva);
        factores=factores/facturas;
        factores=parseInt(factores);
    }
    if (tipo=='siga'){
        factores=0;
    }
    var factura= $('#sel_pedido').text();
    var proveedor = $("#sel_proveedor").text();
    $("#precio_factura").text(factura);
    $("#precio_proveedor").text(proveedor);
    $('#precio_resultado').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    var param = {
        Factura: factura,
        factor: factores,
        tipo: TIPO
    };
    url="vistas/modulos/pedidos/pedidos_precios.php";
    data="";
    data=param;
    myAjaxHH(url,data)
        .done( function(data) {
            $('#precio_resultado').html(data);
        });
    $("#ModalPrecios").modal();
}
/*=============================================
LLAMA MODAL DE CAMBIO DE PRODUCTOS
=============================================*/
function Acc_precios(){
    if (CONSULTO==false){

    }else{
        $("#ModalCalculo").modal();
    }

}
/*=============================================
LLAMA EL PEDIDO CUANDO SE CIERRA
=============================================*/
$("#ModalPrecios").on('hidden.bs.modal', function () {
    CargarFactura();
});
/*=============================================
  RECORRER TABLA PRECIOS
  =============================================*/
function precios_pedidos(){
    if (CONSULTO==false) {

    }else{

        swal({
            title: '¿Está seguro de actualizar los precios?',
            text: "¡Si no lo está puede cancelar la acción!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, actualizar precios!'
        }).then(function(result){
            if (result.value) {
                var linea="";
                var nuevo_precio="";
                var table = document.getElementById('codigos_precio'),
                    rows = table.getElementsByTagName('tr'),
                    i, j, cells, customerId;

                for (i = 0, j = rows.length; i < j; ++i) {
                    cells = rows[i].getElementsByTagName('td');
                    if (!cells.length) {
                        continue;
                    }
                    linea= cells[1].innerHTML;
                    nuevo_precio = cells[9].firstChild.value;
                    var nuevos_precio= money_format(nuevo_precio);
                    var factura = $('#sel_pedido').text();
                    var proveedor = $("#sel_proveedor").text();
                    var param = {
                        Factura: factura,
                        Codigo:linea,
                        precio:nuevos_precio,
                        proveedor:proveedor
                    };

                    url="vistas/modulos/pedidos/pedidos_cambio.php";
                    data="";
                    data=param;
                    myAjaxHH(url,data)
                        .done( function(data) {
                            console.log(data);
                            $("#ModalPrecios").modal('hide');
                        });
                }

            }
        })
    }
}
/*=============================================
LLAMA MODAL DE CAMBIO DE PRODUCTOS
=============================================*/
function Mod_pedidos(id,codigo,cantidad,monto){
    $("#mod_codigo").val(codigo);
    $("#mod_cantidad").val(cantidad);
    $("#mod_precio").val(monto);
    $("#mod_id").val(id);
    $("#ModalCambio").modal();
}
/*=============================================
ACTUALIZA PRODUCTO EN BD
=============================================*/
function Acc_producto(){
    var id_codigo = $("#mod_id").val();
    var codigo =$("#mod_codigo").val();
    var cantidad = $("#mod_cantidad").val();
    var precio =$("#mod_precio").val();

    var param = {
        idcodigo: id_codigo,
        codigo :codigo,
        cantidad: cantidad,
        monto:precio
    };
    $.ajax({
        data: param,
        url: "vistas/modulos/pedidos/pedidos_modificar.php",
        method: "post",
        success: function(data) {
            console.log(data);
            $("#mod_codigo").val("");
            $("#mod_cantidad").val("");
            $("#mod_precio").val("");
            $("#mod_id").val("");
            $("#ModalCambio").modal('hide');
            CargarFactura()

        }
    });
}
/*=============================================
ACTUALIZA TOPRAN
=============================================*/
function Acc_topran(){
    var codigo =$("#mod_codigo_tai").val();
    var topran = $("#mod_codigo_topran").val();
    console.log(topran);
    var param = {
        codigo :codigo,
        topran: topran
    };
    $.ajax({
        data: param,
        url: "vistas/modulos/pedidos/pedidos_modificar_topran.php",
        method: "post",
        success: function(data) {
            console.log(data);
            $("#mod_codigo_tai").val("");
            $("#mod_codigo_topran").val("");
            $("#Modaltopran").modal('hide');
            // CargarFactura()

        }
    });
}
/*=============================================
REPORTE DE PEDIDO
=============================================*/
function Rep_Pedido(){
    if (CONSULTO==false){

    }else{
        var factura= $('#codigofactura').text();
        window.open("extensiones/tcpdf/pdf/pedidos.php?factura="+factura, "_blank");
    }

}
/*=============================================
ENVIAR PEDIDO
=============================================*/
function Enviar_Pedido(){
    if (CONSULTO==false){

    }else{

        swal({
            title: "Pedidos",
            text: "Cuando está pronosticado su llegada",
            input: 'text',
            type: "question",

        }).then(resultado => {
            if (resultado.value) {
                let fecha = resultado.value;
                var factura= $('#codigofactura').text();
                var proveedor = $('#codigoproveedor').text();
                var param = {
                    factura: factura,
                    proveedor :proveedor,
                    fecha: fecha
                };
                $.ajax({
                    data: param,
                    url: "vistas/modulos/pedidos/pedidos_enviar.php",
                    method: "post",
                    success: function(data) {
                        console.log(data);
                        var factura= $('#codigofactura').text();
                        window.open("extensiones/tcpdf/pdf/pedidos.php?factura="+factura, "_blank");

                    }
                });
            }
        });
    }

}

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
LLAMA MODAL DE TOPRAN
=============================================*/
function BuscarHP(){

    codigo=$("#hp").val();
    var noValido = /\s/;
    var hayespacio = noValido.test(codigo);
    console.log(hayespacio);
    if (hayespacio == true){
        console.log("hay");

    }else{
        console.log("no hay");
        var tam = codigo.length;
        var nueva = codigo.substr(0,3);
        var ultima = codigo.substr(3,tam);
        var nuevo = nueva+" "+ultima;
        console.log(nuevo);
        codigo=nuevo;
    }

    var param = {
        codigo: codigo
    };

    $('#codigoHpAnt').html("");
    $.ajax({
        data: param,
        url: "vistas/modulos/pedidos/pedidos_vw.php",
        method: "post",
        success: function(data) {
            //console.log(data);
            swal({
                type: "warning",
                title: "El codigo VW es "+ data,
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                //console.log(result);
                $("#hp").val("");
                $('#codigoHpAnt').html(data);
                $("#selector").focus()
            });
      //      $("#mod_codigo_topran").val(data);

        }
    });
   //$("#Modaltopran").modal();
}


/*=============================================
COPIAR HP
=============================================*/
function EnterHp(event) {
    var key = event.keyCode;
    if (key==13) {
       BuscarHP();

    }
}

function CopiarHp() {
    var aux = $('#codigoHpAnt').html();
    console.log (aux);
    $('#selector').val(aux);
    $("#selector").focus()

}
/*=============================================
APLICAR PEDIDO
=============================================*/
function Aplicar_Pedido(){
    if (CONSULTO==false){

    }else{
        swal({
            title: '¿Está seguro de aplicar el pedido?',
            text: "¡Si no lo está puede cancelar la acción!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, aplicar pedido!'
        }).then(function(result) {
            if (result.value) {
                var factura= $('#codigofactura').text();
                var proveedor = $('#codigoproveedor').text();
                var total = $('#totalpedido').text();
                total=money_format(total);
                var param = {
                    factura: factura,
                    proveedor :proveedor,
                    total:total
                };
                $.ajax({
                    data: param,
                    url: "vistas/modulos/pedidos/pedidos_aplicar.php",
                    method: "post",
                    success: function(data) {
                        console.log(data);
                        swal({
                            type: "success",
                            title: "El pedido ha sido aplicado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            console.log(result);
                            window.location.replace('pedidos');
                        });

                    }
                });
            }
        });
    }

}
/*=============================================
NUEVO PEDIDO
=============================================*/
function Nuevo_Pedido(){
    $("#ModalNuevo").modal();
}

function Crear_Pedido(){
    $("#codigofactura").text($("#nuevo_factura").val())
    factura=$("#nuevo_factura").val();
    proveedor=$("#nuevo_proveedor").val();



    $('#resultado_compra').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    var param = {
        Factura: factura,
        proveedor:proveedor,
        tipo: TIPO
    };
    url="vistas/modulos/pedidos/pedidos_pedidos.php";
    data="";
    data=param;
    myAjaxHH(url,data)
        .done( function(data) {
            $('#resultado_compra').html(data);
        });
    $("#ModalNuevo").modal('hide');
}

/*=============================================
ENTER EN PRECIOS
=============================================*/
function EnterTabla(event) {
    var key = event.keyCode;
    var $focused = $(':focus');
    if (key == 13 || key == 40) {

        $focused.closest('tr').nextAll().eq(0).find('.imp').focus();
        $focused.closest('tr').nextAll().eq(0).find('.imp').select();
    }
    if (key==38){
        $focused.closest('tr').prevAll().eq(0).find('.imp').focus();
    }
}

</script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pedidos';
</script>