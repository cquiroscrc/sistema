
<?php 
?>
<script>

		/*=============================================
          NUMBER FORMAT (convierte numero a moneda)
          =============================================*/
        var IVA=0;
        var PERMISO=2; 
        var PRUEBAS=true;
        var ValDesc =0;

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
          CARGAR INICIAL
          =============================================*/

        function CargarInicial(fecha) {
             
             // *** Animación
             $('#caja_recibos').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
             $('#caja_egresos').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
             $('#caja_depositos').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
             $('#caja_total').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
             $('#caja_resultado').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
             //  Fin de animación

            $.ajax('vistas/modulos/caja/caja_recibos.php?fecha='+fecha)
            .done(function(response){
              //console.log(response);
              $('#caja_recibos').html(response);
              $("#Enc_Recibos").text($("#Rep_Total_Recibos").text());
              $("#Res_Total_Recibos").text($("#Rep_Total_Recibos").text());
            });
             
            $.ajax('vistas/modulos/caja/caja_egresos.php?fecha='+fecha)
            .done(function(response){
              //console.log(response);
              $('#caja_egresos').html(response);
              $("#Enc_Egresos").text($("#Rep_Total_Egresos").text());
              $("#Res_Total_Egresos").text($("#Rep_Total_Egresos").text());
            });

            $.ajax('vistas/modulos/caja/caja_depositos.php?fecha='+fecha)
            .done(function(response){
              //console.log(response);
              $('#caja_depositos').html(response);
              $("#Enc_Depositos").text($("#Rep_Total_Depositos").text());
              $("#Res_Total_Depositos").text($("#Rep_Total_Depositos").text());
            });

            $.ajax('vistas/modulos/caja/caja_totales.php?fecha='+fecha)
            .done(function(response){
              //console.log(response);
              $('#caja_total').html(response);
              $("#Enc_Total").text($("#Rep_Total_Total").val());
              $("#Res_Total_Total").text($("#Rep_Total_Total").val());


            });

            $.ajax('vistas/modulos/caja/caja_resultado.php?fecha='+fecha)
            .done(function(response){
              //console.log(response);
              $('#caja_resultado').html(response);
              
            });


        }

        /*=============================================
          CARGAR FECHA CON DATOS
          =============================================*/

        function CargarFecha() {
             
             var fechaInicial = $("#fecha_cierre_caja").val();
             var dateString = moment(fechaInicial).format('MM/DD/Y');
             fecha=dateString;
             $("#FechaCierre").text(fecha);
            CargarInicial(fecha);
        }

         /*=============================================
          CAMBIAR FECHA EN SISTEMA
          =============================================*/

        function CambiarFecha() {
              var fechaInicial = $("#fecha_cambio_caja").val();
              var dateString = moment(fechaInicial).format('MM/DD/Y');
              fecha=dateString;

              swal({
                  title: '¿Está seguro de cambiar fecha del sistema?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, cambiar fecha!'
                  }).then(function(result){
                    if (result.value) {

                        var param = {
                                    fecha: fecha
                                  };

                      $.ajax({
                        data: param,
                        url: "vistas/modulos/caja/cambio_fecha.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "La fecha ha sido cambiada correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                   $('#FechaActualizada').text(fecha);
                                   ReCargarFecha(); 
                                  

                                  });
                        } 
                      }); 

                    }
                });
             
              $('#ModalFecha').modal('hide');
        }

         /*=============================================
          MODAL FECHA EN SISTEMA
          =============================================*/

        function CambioFecha() {
             $('#ModalFecha').modal();
        }
        /*=============================================
         MODAL HACER RECIBO
         =============================================*/
        function HacerRecibo() {
            $('#ModalHacerRecibo').modal();
        }
        /*=============================================
          HACER PROFORMA
          =============================================*/
        function ReciboPDF(){

            var codcliente = $('#recibo_cliente').val();
            var descripcion = $('#recibo_descripcion').val();
            var monto = $('#recibo_monto').val();
            var combo = document.getElementById("recibo_cliente");
            var nombre = combo.options[combo.selectedIndex].text;
            var fecha = $("#FechaCierre").text();

            if ($('#recibo_nombre').val()==''||$('#recibo_descripcion').val()==''||$('#recibo_monto').val()=='' ) {
                swal({
                    title: "Error al generar el recibo",
                    text: "¡No puede hacer el recibo con datos vacios vacia!",
                    type: "error",
                    confirmButtonText: "¡Cerrar!"
                });
            }else{
                if (codcliente=='0') {
                    swal({
                        title: "Facturacion",
                        text: "Digite el nombre para el recibo",
                        input: 'text',
                        type: "question",

                    }).then(resultado => {
                        if (resultado.value) {
                            let el_nombre = resultado.value;
                            nombre=el_nombre;
                            $('#recibo_descripcion').val('');
                            $('#recibo_monto').val('');
                            var dropDown = document.getElementById("recibo_cliente");
                            dropDown.selectedIndex = 0;

                            $('#ModalHacerRecibo').modal('hide');
                            setTimeout(() => { ReCargarFecha(); }, 2000);
                            window.open("extensiones/tcpdf/pdf/recibos.php?cliente=" + codcliente + "&descripcion=" + descripcion + "&monto=" + monto+"&nombre="+nombre+"&fecha=" + fecha, "_blank");

                        }
                    });


                }else {
                    $('#recibo_descripcion').val('');
                    $('#recibo_monto').val('');
                    var dropDown = document.getElementById("recibo_cliente");
                    dropDown.selectedIndex = 0;

                    $('#ModalHacerRecibo').modal('hide');
                    setTimeout(() => { ReCargarFecha(); }, 2000);
                    window.open("extensiones/tcpdf/pdf/recibos.php?cliente=" + codcliente + "&descripcion=" + descripcion + "&monto=" + monto+"&nombre="+nombre+"&fecha=" + fecha, "_blank");

                }

            }


        }

         /*=============================================
          RECARGAR FECHA CON DATOS
          =============================================*/

        function ReCargarFecha() {
             
             var fecha = $("#FechaCierre").text();
            CargarInicial(fecha);
        }
       
      /*=============================================
          AGREGAR DEPOSITOS
          =============================================*/
        function AgregarDeposito() {
          $('#modalDepositos').modal();

        } 

        /*=============================================
          AGREGAR EGRESOS
          =============================================*/
        function AgregarEgresos() {
          $('#modalEgreso').modal();

        } 
        /*=============================================
          AGREGAR DEPOSITOS
          =============================================*/
        function AgregarRecibos() {
          $('#modalRecibo').modal();

        } 
        /*=============================================
          GUARDAR DEPOSITO
          =============================================*/
        function GuardarDeposito(){
          var FechaDep = $("#FechaCierre").text();
          var MontoDep = $("#nuevoMontoDep").val();
          var CuentaDep = $("select#nuevoBanco").val();
          var Deposito = $("#nuevoDeposito").val();

          var param = {
                        Date2: FechaDep,
                        monto:MontoDep,
                        cuentas:CuentaDep,
                        documento:Deposito
                      };

          $.ajax({
            data: param,
            url: "vistas/modulos/caja/agregar_deposito.php",
            method: "post",
            success: function(data) {
                console.log(data);
                if (data=="existe") {
                  swal({
                      type: "error",
                      title: "El deposito ya existe, no se pudo agregar",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       

                      });
 

                }
                if (data=="exito") {

                  swal({
                      type: "success",
                      title: "El deposito ha sido agregado correctamente",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       $("#nuevoMontoDep").val("");
                       $("#nuevoDeposito").val(""); 
                       var dropDown = document.getElementById("nuevoBanco");
                       dropDown.selectedIndex = 0;
                       $('#modalDepositos').modal('hide'); 
                       ReCargarFecha();    
                      });

                }
                
            }
          });


        }
        /*=============================================
          ELIMINAR DEPOSITO
          =============================================*/
        function EliminarDeposito(documento,cuenta,fecha){
            
            swal({
                  title: '¿Está seguro de borrar el deposito?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, borrar deposito!'
                  }).then(function(result){
                    if (result.value) {

                        var param = {
                                    Date2: fecha,
                                    cuenta:cuenta,
                                    deposito:documento
                                  };

                      $.ajax({
                        data: param,
                        url: "vistas/modulos/caja/eliminar_depositos.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "El deposito ha sido eliminado correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                   
                                   ReCargarFecha();    
                                  });
                        } 
                      }); 

                    }
                });
       
              }

        /*=============================================
          GUARDAR EGRESO
          =============================================*/
        function GuardarEgreso(){
          var FechaEgre = $("#FechaCierre").text();
          var MontoEgre = $("#nuevoMonto").val();
          var TipoEgre = $("select#nuevoTipo").val();
          var Documento = $("#nuevoDocumento").val();
          var Descripcion = $("#nuevoDescripcion").val();
          var Empresa = $("#nuevoEmpresa").val();
          var Hora = $("#nuevoHora").val();
          var Lugar = $("#nuevoLugar").val();
          var Cliente = $("#nuevoCliente").val();
          var Subtotal =$("#nuevoSub").val();
          var IVA = $("#nuevoIVA").val();

          var param = {
                        egresos: TipoEgre,
                        documento:Documento,
                        Date2:FechaEgre,
                        monto:MontoEgre,
                        descripcion:Descripcion,
                        empresa:Empresa,
                        hora: Hora,
                        lugar: Lugar,
                        cliente: Cliente,
                        subtotal: Subtotal,
                        iva : IVA
                        
                      };

          $.ajax({
            data: param,
            url: "vistas/modulos/caja/agregar_egreso.php",
            method: "post",
            success: function(data) {
                console.log(data);
                if (data=="existe") {
                  swal({
                      type: "error",
                      title: "El egreso ya existe, no se pudo agregar",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       

                      });
 

                }
                if (data=="exito") {

                  swal({
                      type: "success",
                      title: "El egreso ha sido agregado correctamente",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       var dropDown = document.getElementById("nuevoTipo");
                       dropDown.selectedIndex = 0;
                       $("#nuevoMonto").val("");
                       $("#nuevoDescripcion").val(""); 
                       $("#nuevoDocumento").val("");
                       $("#nuevoEmpresa").val("");
                       $("#nuevoHora").val("");
                       $("#nuevoLugar").val("");
                       $("#nuevoCliente").val("");
                       $("#nuevoSub").val("");
                       $("#nuevoIVA").val("");
                       $('#modalEgreso').modal('hide'); 
                       ReCargarFecha();    
                      });

                }
                
            }
          });


        } 

          /*=============================================
          ELIMINAR EGRESO
          =============================================*/
        function EliminarEgreso(documento,tipo,fecha){
            
            swal({
                  title: '¿Está seguro de borrar el egreso?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, borrar Egreso!'
                  }).then(function(result){
                    if (result.value) {

                        var param = {
                                    Date2: fecha,
                                    tegreso:tipo,
                                    egreso:documento
                                  };

                      $.ajax({
                        data: param,
                        url: "vistas/modulos/caja/eliminar_egresos.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "El egreso ha sido eliminado correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                   
                                   ReCargarFecha();    
                                  });
                        } 
                      }); 

                    }
                });
       
              }

          /*=============================================
          GUARDAR RECIBO
          =============================================*/
        function GuardarRecibo(){
          var FechaRec = $("#FechaCierre").text();
          var MontoRec = $("#nuevoMontoRec").val();
          var cliente = $("select#nuevoClienteRec").val();
          var documento = $("#nuevoRecibo").val();

          var param = {
                        Date2: FechaRec,
                        monto:MontoRec,
                        recibos:cliente,
                        documento:documento
                      };

          $.ajax({
            data: param,
            url: "vistas/modulos/caja/agregar_recibo.php",
            method: "post",
            success: function(data) {
                console.log(data);
             
                if (data=="exito") {

                  swal({
                      type: "success",
                      title: "El recibo ha sido agregado correctamente",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       $("#nuevoMontoRec").val("");
                       $("#nuevoRecibo").val(""); 
                       var dropDown = document.getElementById("nuevoClienteRec");
                       dropDown.selectedIndex = 0;
                       $('#modalRecibo').modal('hide'); 
                       ReCargarFecha();    
                      });

                }
                
            }
          });


        }
        /*=============================================
          ELIMINAR RECIBO
          =============================================*/
        function EliminarRecibo(cliente,factura,fecha){
            
            swal({
                  title: '¿Está seguro de borrar el recibo?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, borrar recibo!'
                  }).then(function(result){
                    if (result.value) {

                        var param = {
                                    Date2: fecha,
                                    recibo:factura,
                                    cliente:cliente
                                  };

                      $.ajax({
                        data: param,
                        url: "vistas/modulos/caja/eliminar_recibos.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "El recibo ha sido eliminado correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                   
                                   ReCargarFecha();    
                                  });
                        } 
                      }); 

                    }
                });
       
              }
/*=============================================
  BUSCAR FACTURA
  =============================================*/
  function BuscarFactura(){
    var factura = $("#anular_buscar").val();
    $("#agregar-factura").empty();
    var param = {
                  factura:factura
                };

    $.ajax({
      data: param,
      url: "vistas/modulos/caja/caja_buscar.php",
      method: "post",
      success: function(data) {
          //console.log(data); 
          var resultadoajax = JSON.parse(data);
          var factura=resultadoajax.data[0][0]["nfac"];    
          var nombre=resultadoajax.data[0][0]["Nombre"];     
          var total=resultadoajax.data[0][0]["total"]; 
          total= number_format(total,2,'.',',')   
          var vendedor=resultadoajax.data[0][0]["vendedor"];
          var estado=resultadoajax.data[0][0]["estado"];        
          if (estado=='ANULADA   ') {
            swal({
                      type: "error",
                      title: "¡La factura ya fie anulada!",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                      if (result.value) {
                        $("#anular_buscar").val("");
                      }
                    })

          }else{
            var html= '<tr>\
                  <td id="fac_anular">'+factura+'</td>\
                  <td>'+vendedor+'</td>\
                  <td>'+nombre+'</td>\
                  <td>'+total+'</td>\
                  <td>'+estado+'</td>\
                  </tr>';
                  $('#agregar-factura').append(html);
          }
      } 
    }); 

  }

/*=============================================
  ANULAR
  =============================================*/
  function AnularFactura(){
    var factura = $("#fac_anular").text();
      // $valid_session = isset($_SESSION['id']) ? $_SESSION['id'] === session_id() : FALSE;
      // if (!$valid_session) {
      //     header('Location: inicio');
      //     exit();
      // }
    
    
    swal({
        title: '¿Está seguro de anular la venta?',
        text: "¡Si no lo está puede cancelar la accíón!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Anular venta!'
      }).then(function(result){
        if (result.value) {
          
            window.open("vistas/modulos/anular_proceso.php?consecutivo="+factura, "_blank");
            $("#anular_buscar").val("");
            $("#agregar-factura").empty();

        }

  })
  }
        /*=============================================
        LIBERAR
        =============================================*/
        function LiberarFactura(){

            swal({
                title: '¿Está seguro de liberar la venta?',
                text: "¡Si no lo está puede cancelar la accíón!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Liberar!'
            }).then(function(result){
                if (result.value) {
                    window.open("vistas/modulos/liberar_proceso.php", "_blank");
                    window.location.reload()

                }


            })
        }

/*=============================================
          ENTER  DEPOSITO
  =============================================*/

function anularKey(event) {
  var key = event.keyCode;
  if (key==13) {
    BuscarFactura();    
  }
}
function uniKeyCodeDep(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoMontoDep").focus(); 
    
  } 
}
function uniKeyCodeMD(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoBanco").focus(); 
    
  } 
}
/*=============================================
          ENTER  EGRESO
  =============================================*/

function uniKeyCodeTE(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoDocumento").focus(); 
    
  } 
}
function uniKeyCodeDE(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoMonto").focus(); 
    
  } 
}
function uniKeyCodeME(event) {
  var key = event.keyCode;
  if (key==13) {

         $("#nuevoSub").focus();
      }  

}
function uniKeyCodeST(event) {
    var key = event.keyCode;
    if (key==13) {
            $("#nuevoIVA").focus();
        }
}
function uniKeyCodeIVA(event) {
    var key = event.keyCode;
    if (key==13) {
        if( $('#nuevoDescripcion').is(':visible') ) {
            $("#nuevoDescripcion").focus();
        }
        else {
            $("#nuevoEmpresa").focus();
        }
    }
}
function uniKeyCodeEE(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoHora").focus(); 
    
  } 
}
function uniKeyCodeHE(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoLugar").focus(); 
    
  } 
}
function uniKeyCodeLE(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoCliente").focus(); 
    
  } 
}

/*=============================================
          ENTER  RECIBOS
  =============================================*/

function uniKeyCodeRe(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoMontoRec").focus(); 
    
  } 
}
function uniKeyCodeCR(event) {
  var key = event.keyCode;
  if (key==13) {
    $("#nuevoRecibo").focus(); 
    
  } 
}


/*=============================================
          MOSTRAR CAMPOS EN EGRESOS
  =============================================*/

function ShowSelected(){

  const empresa = document.getElementById('gEmpresa');
  const hora = document.getElementById('gHora');
  const lugar = document.getElementById('gLugar');
  const cliente = document.getElementById('gCliente');
  const descripcion = document.getElementById('gDescripcion');

  var cod = document.getElementById("nuevoTipo").value;
  
  if (cod==5) {
    
      empresa.style.display='block'; 
      hora.style.display='block'; 
      lugar.style.display='block'; 
      cliente.style.display='block'; 
      descripcion.style.display='none';
  }else{
      empresa.style.display='none'; 
      hora.style.display='none'; 
      lugar.style.display='none'; 
      cliente.style.display='none'; 
      descripcion.style.display='block';

  }

}

/*=============================================
  HACER REPORTE FACTURAS
  =============================================*/   
      function ReporteFacturas(){
            var fecha = $("#FechaCierre").text();
            window.open("extensiones/tcpdf/pdf/Reporte_Facturas.php?fecha="+fecha, "_blank");     
      }
/*=======================================*/
/*=============================================
  HACER REPORTE CIERRE
  =============================================*/   
      function ReporteCierre(){
            var fecha = $("#FechaCierre").text();
            window.open("extensiones/tcpdf/pdf/Reporte_Cierre.php?fecha="+fecha, "_blank");     
      }
/*=======================================*/
    </script>








