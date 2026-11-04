<?php 
date_default_timezone_set("America/Costa_Rica");
$num_prefactura="";
//include "modelos/procesos_factura.php"
 ?>
<style>
   
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

<!DOCTYPE html>

<html>
    <head>

    
    </head>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1 style="display:inline;">
     Compras
    </h1>
       
    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Compras</li>
    
    </ol>

  </section>
<section class="content">
<div id="invoice">

   
    <div class="invoice overflow-auto">
        <div style="min-width: 600px">
            <header>
                
            </header>
            <main>
                <div class="row contacts">
                    <div class="notices">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="col invoice-to">
                              <table> 
                                <tr>
                                  <td><h3 style="display:inline;color:#3989c6;">Proveedor:</h3></td>
                                  <td><input type="text" style='width :400px;margin: 2px' id="proveedor" onkeydown="proveedorKey(event)" list="list_proveedores"></td>
                                </tr>
                                <tr>
                                  <td><h3 style="display:inline;color:#3989c6;">Factura:</h3></td>    
                                  <td><input type="text"  align='right' style='width :200px;margin: 2px; text-transform: uppercase;' id="e_factura" autocomplete="off" onkeydown="facturaKey(event)"></td>
                                </tr>
                                <tr>
                                  <td><h4 style="display:inline;color:#3989c6;">Subtotal:</h4></td>
                                  <td><input type="text" style='width :200px;margin: 2px' id="e_subtotal" autocomplete="off" onkeydown="subtotalKey(event)"></td>
                                </tr>
                                <tr>
                                  <td><h4 style="display:inline;color:#3989c6;">Descuento:</h4></td>
                                  <td><input type="text" style='width :200px;margin: 2px' id="e_descuento" autocomplete="off" onkeydown="descuentoKey(event)"></td>
                                </tr>
                                <tr>
                                  <td><h4 style="display:inline;color:#3989c6;">Impuesto:</h4></td> 
                                  <td><input type="text" style='width :200px;margin: 2px' id="e_impuesto" autocomplete="off"  onkeydown="impuestoKey(event)"></td>
                                </tr>
                                <tr>
                                  <td><h4 style="display:inline;color:#3989c6;">Total:</h4></td>
                                  <td><input type="text" style='width :200px;margin: 2px' id="e_total" autocomplete="off"  onkeydown="totalKey(event)" ></td>
                                </tr>
                            </table>
                            <datalist id="list_proveedores">
                              <?php
                                    $sql="SELECT * FROM Proveedores ORDER BY nombre";
                                    
                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                    foreach ($ejecutar as $key => $arr) {
                                      $cod_proveedor=$arr['codigo'];
                                      $nombre_proveedor=$arr['nombre'];
                                      echo "<option value='".$cod_proveedor." - ".$nombre_proveedor."'>";    
                                    } 
                              ?>
                              </datalist>    
                            </div>
                        </div>
                        
                        <div class="col-sm-3">
                            <div class="col invoice-details">
                              <table>
                                
                                <tr></tr>
                                
                              </table>
                             
                        
                            </div>


                        </div>
                        <div class="col-sm-4 ">
                            <table>
                              <td>
                            <button style="width:120px;" onclick="myDevolucion()" id="callDevolucion" class="btn btn-warning"><i class="fa fa-undo"></i> Devolucion</button>                         
                            <button style="width:120px;" onclick="myCompra()" id="callFacturar" class="btn btn-success"><i class="fa fa-print"></i> Facturar</button>
                            
                            </td>
                            <tr></tr>
                                <tr><td>
                                  <button style="width:130px;" onclick="myManual()" id="callFacturar" class="btn btn-info"><i class="fa fa-print"></i> Factura Servcios</button>
                                </td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                            </table>
                        </div>
                        
                    </div>
                        
                    </div> 
                                
                  <!-- Consultar Codigo -->
                    <hr style="border:2px;" >
                    <hr style="border-color:#3989c6;">

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
                <table border="0" cellspacing="0" cellpadding="0" id="agregar-producto">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-left">CODIGO / DESCRIPCION</th>
                            <th class="text-right">COSTO</th>
                            <th class="text-right">PRECIO</th>
                            <th class="text-right">CANTIDAD</th>
                            <th class="text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody id="productos_list">
                        
                        
                        
                    </tbody>
                    <tfoot id="totales">
                        <tr>
                            <td></td>
                            <td colspan="2"></td>
                            <td colspan="2">SUBTOTAL</td>
                            <td id="fact_subtotal">0.00</td>
                        </tr>
                        <tr><td></td>
                            <td colspan="2"></td>
                            <td colspan="2">IMPUESTO</td>
                            <td id="fact_impuesto">0.00</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2"></td>
                            <td colspan="2">TOTAL</td>
                            <td id="fact_total">0.00</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="thanks"></div>
                
            </main>
            
        </div>
        
        <div></div>
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
MODAL VALORES
======================================-->

  <div class="modal fade" id="modalValores" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Valores de compra</h4>

        </div>
        <center>  
         <h4 id="codigo_compra"></h4>
         <h5 id="descripcion_compra"></h5>
         <input type="hidden" id="compra_compra_anterior">
         <br> 
         <table>
          <tr>
            <td>Cantidad: </td><td><input type="text" style='margin: 2px'  onkeydown="cantidadKey(event)" autocomplete="off" id="cantidad_compra"></td>
          </tr>
          <tr>
            <td>Precio Venta: </td><td><input type="text" style='margin: 2px' onkeydown="ventaKey(event)" autocomplete="off" id="venta_compra"></td>
          </tr>
          <tr>
            <td>Precio Compra: </td><td><input type="text" style='margin: 2px' autocomplete="off" id="compra_compra"></td>
          </tr>
        
         </table>

      </center>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-success" onclick="mylistadoHH()">Guardar</button>

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
MODAL CAMBIO DE FECHA
======================================-->

  <div class="modal fade" id="ModalFecha" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Cambiar Fecha</h4>

        </div>
        <center>  
        <div style="max-height:120px" class="modal-body">
          <h3 style="color:red;" >Seleccione la Fecha de la factura</h3>
          <input type="date" id="fecha_cambio" name="fecha"  min="2018-01-01" >
        </div>
      </center>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary" onclick="CambiarFechaCompras()">Cambiar fecha</button>

        </div>
      </div>   
    </div>
  </div>
  
<!-- FIN MODAL -->



</html>



 
<script>
    IVA=0;
    /*=============================================
     DOCUMENT READY
    =============================================*/
    $(document).ready(function(){
     // CLICK BUSCAR
        ConsultaIVA();
        document.getElementById("consulta").onclick = function(){
          myFunctionCodigo();
        };
        
        
        
        // BORRA EL TEXTO DEL TXT DEL CODIGO
        $("#modalBuscarCodigo").on('hidden.bs.modal', function () {
            document.getElementById("selector").value = "";
        });

        //ELIMINA CODIGOS
        $('#productos_list').on('click','.no', function(){
          eliminar_codigo= $(this).attr("value");
          var Factura =$("#e_factura").val();
          var proveedor=$("#proveedor").val();
          var n = proveedor.search(" ");
          var proveedor = proveedor.slice(0, n);
          myElimina(eliminar_codigo,Factura,proveedor);
        });
        //CAMBIO DE PRECIOS CODIGOS
        

        $('resultadoOrden#Facturadescuento').on('change',function(){
         FacRecalcular();
          
        });
        $('#modalValores').on('shown.bs.modal', function () {
          $('#cantidad_compra').focus();
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
         function number_format(number, decimals, dec_point, thousands_point) {
            if (number == null || !isFinite(number)) {
                throw new TypeError("number is not valid");
            }
            if (!decimals) {
                var len = number.toString().split('.').length;
                decimals = len > 1 ? len : 0;
            }
            if (!dec_point) {
                dec_point = '.';
            }
            if (!thousands_point) {
                thousands_point = ',';
            }
            number = parseFloat(number).toFixed(decimals);
            number = number.replace(".", dec_point);
            var splitNum = number.split(dec_point);
            splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
            number = splitNum.join(dec_point);
            return number;
        }
        /*=============================================
          MONEY FORMAT (convierte a numero )
          =============================================*/

        function money_format(number){
          
          var z = number.replace(',', '');
          number = parseFloat(z);
          return number;
        }

         /*=============================================
          CONSULTA EL IVA Y SI HAY PERMISO DE DESCUENTO ESPECIAL
          =============================================*/
        function ConsultaIVA(){
          var url ="ajax/consulta_impuesto.ajax.php";
          var data='';
          
          myAjaxHH(url,data)
              .done( function(data) {
                IVA=money_format(data);
                IVA=IVA/100;
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
          MY FUNCTION CODIGO
          =============================================*/
        function myFunctionCodigo() {
          
          var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
          $("#modalBuscarCodigo").modal()
          $('#result').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
          $.ajax('vistas/modulos/consulta_productos.compras.php?codigo='+dataToPass.codigo)
          .done(function(response){
            $('#result').html(response);
          });
        }

        

        /*=============================================
          MY CODIGO (Pregunta y costos)
          =============================================*/
        function myCodigo(codigo){

            url="ajax/consulta_productos.ajax.php?codigo="+codigo;
            data="";
            data=codigo;
            myAjaxHH(url,data)
            .done( function(data) {
            
              var resultadoajax = JSON.parse(data);
              var descripcion = resultadoajax.data[0][0]["DESCRIPCION"];
              var precio = resultadoajax.data[0][0]["VENTA"];
              var costo = resultadoajax.data[0][0]["COSTO"];
              $("#codigo_compra").text(codigo);
              $("#descripcion_compra").text(descripcion);
              $("#venta_compra").val(precio);
              $("#modalBuscarCodigo").modal("hide");
              $("#compra_compra_anterior").val(costo);
              $("#modalValores").modal();
              
            });           
        }


        /*=============================================
          MY LISTADO HH
          =============================================*/
        function mylistadoHH(){

              var codigo = $("#codigo_compra").text();
              var descripcion = $("#descripcion_compra").text();
              var venta = $("#venta_compra").val();
              var costo_anterior = $("#compra_compra_anterior").val();
              var costo = $("#compra_compra").val();
              var cantidad = $("#cantidad_compra").val();
              var Factura =$("#e_factura").val();
              var proveedor=$("#proveedor").val();
              var n = proveedor.search(" ");
              var proveedor = proveedor.slice(0, n);
              $("#compra_compra").val("");
              $("#cantidad_compra").val("");
              $("#venta_compra").val("");
              var subtotal= $("#e_subtotal").val();
              var descuento= $("#e_descuento").val();

          
              var param = {

                  codigo: codigo,
                  cantidad:cantidad,
                  costo:costo,
                  costoa:costo_anterior,
                  precio:venta,
                  Factura: Factura,
                  proveedor:proveedor,
                  subtotal:subtotal,
                  descuento:descuento
                  
              };

              $.ajax({
                data: param,
                  url: "ajax/productos_compra.ajax.php",   /// ASI DE METE EL CODIGO
                  method: "post",
                  success: function(data) {
                   
                    console.log(data);
                    $("#modalValores").modal('hide');
                  myRecargar(Factura);
                  }
              });

            
        }
        /*=============================================
          ELIMINAR CODIGO
          =============================================*/
        function myElimina(codigo,factura,proveedor){
              var param = {
                      Factura: factura,
                      codigo: codigo,
                      proveedor:proveedor
                  };

              url="ajax/consulta_rebajar.compra.php";
              data="";
              data=param;

              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
                var parent = $(this).parent().remove();
                swal({
                  type: "success",
                  title: "El producto ha sido borrado correctamente",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar"
                  }).then(function(result){
                      if (result.value) {
                        
                      }
                  });
                  myRecargar(factura);
              }); 
              // Recarga
              
              
              
        }  


        /*=============================================
          RECARGAR TODOS LOS DATOS * (SE UTILIZA PARA TODO)
          =============================================*/
          function myRecargar(compra){
              url="ajax/consulta_recargar.compra.php?codigo="+compra;
              data="";
              data=compra;
              myAjaxHH(url,data)
              .done( function(datos) {
                var resultadoajax = JSON.parse(datos);
                
                // Limpia lista y totales
                subtotalact=0; descuentoact=0; impuestoact=0; totalact=0;

                // Limpia la tabla
                $('#productos_list').empty();

                $('#fact_subtotal').text(subtotalact.toFixed(2));
                $('#fact_descuento').text(descuentoact.toFixed(2));
                $('#fact_impuesto').text(impuestoact.toFixed(2));
                $('#fact_total').text(totalact.toFixed(2));


                  for (var i = resultadoajax.data[0].length - 1; i >= 0; i--) {
                      var codigo = resultadoajax.data[0][i]['Codigo'];
                      var descripcion = resultadoajax.data[0][i]['DESCRIPCION'];
                      var desc_tecnica = resultadoajax.data[0][i]['DESC_ALT'];
                      var cantidad = resultadoajax.data[0][i]['Cantidad'];
                      var precio = resultadoajax.data[0][i]['Precio'];
                      var costo = resultadoajax.data[0][i]['PrecioSugerido'];
                      var sub = resultadoajax.data[0][i]['Cantidad']*resultadoajax.data[0][i]['PrecioSugerido'];
                      
                      subtotalact=subtotalact+sub;
                      descuentoact = 0;
                          
                      impuestoact = (subtotalact-descuentoact)*IVA;
                      totalact = subtotalact-descuentoact+impuestoact;
                      precio=number_format(precio,2,'.',',');
                      sub=number_format(sub,2,'.',',');
                      
                     // AGREGA CODIGOS EN EL DOM
                     var html= '<tr>\
                      <td class="no" value="'+codigo+'"><a title="Eliminar '+codigo+'" style="color:white;"><i class="ion ion-android-cancel"></a></td>\
                      <td class="text-left"><h3>\
                      <a data-toggle="tooltip" title="'+desc_tecnica+'">\
                      '+descripcion+'</a>\
                      </h3>'+codigo+'</td>\
                      <td class="unit" value="'+codigo+'"><a title="Cambiar precio '+codigo+'">'+costo+'</td>\
                      <td class="unit" value="'+codigo+'"><a title="Cambiar precio '+codigo+'">'+precio+'</td>\
                      <td class="qty" value="'+codigo+'"><a title="Cambiar cantidad '+codigo+'">'+cantidad+'</td>\
                      <td class="total">'+sub+'</td>\
                      </tr>';
                      $('#agregar-producto').append(html);

                  }
                  
                  //Agrega Totales
                  subtotalact=number_format(subtotalact,2,'.',',');
                  descuentoact=number_format(descuentoact,2,'.',',');
                  impuestoact=number_format(impuestoact,2,'.',',');
                  totalact=number_format(totalact,2,'.',',');
                  $('#fact_subtotal').text(subtotalact);
                  $('#fact_descuento').text(descuentoact);
                  $('#fact_impuesto').text(impuestoact);
                  $('#fact_total').text(totalact);

                  
                
               

              });
              $( "#selector" ).focus();
          }

          /*=============================================
          APLICAR COMPRA
          =============================================*/
        function myCompra(){
          swal({
                  title: '¿Está seguro de aplicar la compra?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Aplicar',
                  confirmButtonText: 'Si, aplicar compra!'
                  }).then(function(result){
                    if (result.value) {

                        var consecutivo =$("#e_factura").val();
                        var proveedor=$("#proveedor").val();
                        var n = proveedor.search(" ");
                        var proveedor = proveedor.slice(0, n);
                        var subtotal=$("#e_subtotal").val();
                        var descuento=$("#e_descuento").val();
                        var impuesto=$("#e_impuesto").val();
                        var total=$("#e_total").val();


                        var param = {
                                    consecutivo: consecutivo,
                                    proveedor: proveedor,
                                    subtotal : subtotal,
                                    descuento: descuento,
                                    impuesto: impuesto,
                                    total:total
                                  };

                      $.ajax({
                        data: param,
                        url: "ajax/aplicar_compra.ajax.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "La compra fue agregada correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                    window.location.replace('compras');

                                  });
                                } 
                      }); 

                    }
                });

        }  
        /*=============================================
          APLICAR DEVOLUCION
          =============================================*/
        function myDevolucion(){
          swal({
                  title: '¿Está seguro de aplicar la DEVOLUCION compra?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Aplicar',
                  confirmButtonText: 'Si, aplicar devolución!'
                  }).then(function(result){
                    if (result.value) {

                        var consecutivo =$("#e_factura").val();
                        var proveedor=$("#proveedor").val();
                        var n = proveedor.search(" ");
                        var proveedor = proveedor.slice(0, n);
                        var subtotal=$("#e_subtotal").val();
                        var descuento=$("#e_descuento").val();
                        var impuesto=$("#e_impuesto").val();
                        var total=$("#e_total").val();


                        var param = {
                                    consecutivo: consecutivo,
                                    proveedor: proveedor,
                                    subtotal : subtotal,
                                    descuento: descuento,
                                    impuesto: impuesto,
                                    total:total
                                  };

                      $.ajax({
                        data: param,
                        url: "ajax/aplicar_dev_compra.ajax.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "La devolución fue agregada correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                    window.location.replace('compras');

                                  });
                                } 
                      }); 

                    }
                });

        } 
         /*=============================================
          AGREGAR MANUAL
          =============================================*/
          function myManual(){
              $("#ModalFecha").modal()  
          } 
        /*=============================================
          AGERAGAR MANUAL
          =============================================*/
        function CambiarFechaCompras(){
                 //Aplico el encabezado
  
                  swal({
                        title: '¿Está seguro de aplicar la compra?',
                        text: "¡Si no lo está puede cancelar la acción!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'Aplicar',
                        confirmButtonText: 'Si, aplicar compra!'
                        }).then(function(result){
                          if (result.value) {

                              var consecutivo =$("#e_factura").val();
                              var proveedor=$("#proveedor").val();
                              var n = proveedor.search(" ");
                              var proveedor = proveedor.slice(0, n);
                              var subtotal=$("#e_subtotal").val();
                              var descuento=$("#e_descuento").val();
                              var impuesto=$("#e_impuesto").val();
                              var total=$("#e_total").val();
                              var fecha=$("#fecha_cambio").val();


                              var param = {
                                          consecutivo: consecutivo,
                                          proveedor: proveedor,
                                          subtotal : subtotal,
                                          descuento: descuento,
                                          impuesto: impuesto,
                                          fecha:fecha,
                                          total:total
                                        };

                            $.ajax({
                              data: param,
                              url: "ajax/aplicar_compraM.ajax.php",
                              method: "post",
                              success: function(data) {
                                
                                    console.log(data);
                                    swal({
                                        type: "success",
                                        title: "La compra fue agregada correctamente",
                                        showConfirmButton: true,
                                        confirmButtonText: "Cerrar"
                                        }).then(function(result){
                                          window.location.replace('compras');


                                        });
                                      } 
                            }); 

                          }
                      });
      
      }
        /*=============================================
          MY TECNICA
          =============================================*/
      function myTecnica(codigo){
            var param = {
                      codigo:codigo
                  };

              url="ajax/consulta_productos.ajax.php";
              data=param;
            
              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
                var resultadoajax = JSON.parse(data);
                 var cod_tecnica=resultadoajax.data[0][0]["CODIGO"];
                 var des_tecnica=resultadoajax.data[0][0]["DESCRIPCION"];
                 var tecnica_tecnica =resultadoajax.data[0][0]["DESC_ALT"];
                 var id_tecnica = resultadoajax.data[0][0]["Carro"];
                 $("#id_tecnica").text(id_tecnica);
                 $("#codigo_tecnica").text(cod_tecnica);
                 $("#descripcion_tecnica").text(des_tecnica);
                 $("#text_tecnica").text(tecnica_tecnica);
                 $('#modalBuscarCodigo').modal('hide');
                 $('#modalTecnica').modal();
                
              });

      }

      /*=============================================
          GUARDAR TECNICA
          =============================================*/
      function GuardarTecnica(){
            var codigo_tec=$("#codigo_tecnica").text();
            var text_tecnica =$("#text_tecnica").val();
            text_tecnica = text_tecnica.toUpperCase();
            var usuario =$("#usuario_id").text();
            var param = {
                      codigo:codigo_tec,
                      tecnica: text_tecnica,
                      usuario:usuario
                  };

              url="ajax/tecnica_productos.ajax.php";
              data=param;
            
              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
                  if (data=="exito") {
                    swal({
                    type: "success",
                    title: "La descripcion Técnica ha sido actualizada correctamente",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                          $('#modalTecnica').modal('hide');
                        }
                    });  
                  }
                
              });
      }

        /*=============================================
          ENTER ENCABEZADOS
          =============================================*/

        function proveedorKey(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#e_factura").focus();      
          }
        }

        function facturaKey(event) {

          var key = event.keyCode;
          if (key==13) {
            var Factura = $("#e_factura").val();
            Factura= Factura.toUpperCase();
            $("#e_factura").val(Factura);
            var proveedor= $("#proveedor").val();
            var n = proveedor.search(" ");
            var proveedor = proveedor.slice(0, n);
            var param = {
                      Factura: Factura,
                      proveedor:proveedor
                  };

              url="ajax/consulta_compra.ajax.php";
              data="";
              data=param;

              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
                if (data=='ENTRO') {
                  swal({
                  type: "error",
                  title: "La factura ya existe",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar"
                  }).then(function(result){
                      if (result.value) {
                        
                      }
                  });

                }
                if (data=="TERMINAR") {
                  var factura = $("#e_factura").val();
                  myRecargar(factura);
                  $("#e_subtotal").focus(); 

                }
                if (data!="ENTRO" || data!="TERMINAR") {
                  $("#e_subtotal").focus();       
                }
                  
                
                
              }); 
            
            
          }
        } 

         
        function subtotalKey(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#e_descuento").focus(); 
            
          }
        }
        function descuentoKey(event) {
          var key = event.keyCode;
          if (key==13) {
            var calculo = $("#e_subtotal").val()-$("#e_descuento").val();
            var impuesto = calculo * IVA;
            var total = calculo + impuesto;
            $("#e_impuesto").val(impuesto);
            $("#e_total").val(total);
            $("#e_impuesto").focus();   
          }
        }
        function impuestoKey(event) {
          var key = event.keyCode;
          if (key==13) {
            var calculo = $("#e_subtotal").val()-$("#e_descuento").val();
            var impuesto = $("#e_impuesto").val();
            var total = parseFloat(calculo) + parseFloat(impuesto);
            $("#e_total").val(total);
            $("#e_total").focus();   
          }
        }
        function totalKey(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#selector").focus(); 
            
          }
        } 
         function cantidadKey(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#venta_compra").focus(); 
            
          }
        }   
         function ventaKey(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#compra_compra").focus(); 
            
          }
        }

</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Compras';
</script>