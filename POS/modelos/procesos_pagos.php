
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
         CONSULTAS PENDIENTES
          =============================================*/
        function ConsultaPendientes(proveedor) {
          $('#listado_pendientes').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            var param = {
                        proveedor: proveedor
                      };

              $.ajax({
                  data: param,
                  url: "vistas/modulos/pagos/pagos_pendientes.php",
                  method: "post",
                  success: function(data) {
                     $('#listado_pendientes').html(data);
                  }
              });

        }

        /*=============================================
         CONSULTAS A PAGAR
          =============================================*/
        function ConsultaPagar() {
            $('#listado_pagar').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            var param = {
                        nada: ''
                      };

              $.ajax({
                  data: param,
                  url: "vistas/modulos/pagos/pagos_listado.php",
                  method: "post",
                  success: function(data) {
                     $('#listado_pagar').html(data);
                  }
              });


          
        }
        
        /*=============================================
         AGREGAR A PENDIENTES
          =============================================*/
        function AgregarPendiente(factura,proveedor,fecha) {
           
            var param = {
                        factura: factura,
                        proveedor: proveedor
                      };

              $.ajax({
                  data: param,
                  url: "vistas/modulos/pagos/pagos_incluir.php",
                  method: "post",
                  success: function(data) {
                    ConsultaPagar();
                    ConsultaPendientes(proveedor);
                  }
              });


          
        }

        /*=============================================
         AGREGAR A PENDIENTES
          =============================================*/
        function EliminarPendiente(factura,proveedor,fecha) {
           
            var param = {
                        factura: factura,
                        proveedor: proveedor
                      };

              $.ajax({
                  data: param,
                  url: "vistas/modulos/pagos/pagos_excluir.php",
                  method: "post",
                  success: function(data) {
                    ConsultaPagar();
                    ConsultaPendientes(proveedor);
                  }
              });


          
        }

        /*=============================================
         IMPRIMIR LISTADO
          =============================================*/
        function ImprimirListado() {
            window.open("extensiones/tcpdf/pdf/pagos.php", "_blank");
            
            
        }
        /*=============================================
         IMPRIMIR CHEQUE
          =============================================*/
        function ImprimirCheque(cheque) {
            if (cheque=='') {
              swal({
                      type: "error",
                      title: "No tiene numero de cheque asignado",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       

                      });

            }else{
              window.open("vistas/modulos/pagos/pagos_imprimir.php?cheque="+cheque, "_blank");  
            }
            
            
            
        }
        /*=============================================
         MODAL CHEQUE MANUAL
          =============================================*/
        function ChequeManual() {
            $('#modalChequeManual').modal();
        }
        /*=============================================
         MODAL PAGO
          =============================================*/
        function AgregarPago() {
            $('#modalAgregarPago').modal();
        }

        /*=============================================
         MODAL PAGO INDIVIDUAL
          =============================================*/
        function AgregarPagoIndividual(proveedor,nombre) {
            $('#proveedorIND').text(proveedor);
            $('#proveedorNOM').text(nombre);

            $('#modalAgregarPagoIndividual').modal();
        }

        /*=============================================
         AGREGAR PAGO A LOS CHEQUES
          =============================================*/
        function AgregarPagoCheque() {
           $('#modalAgregarPago').modal('hide');
          var cuentaBancaria= $('#cuenta_bancaria').val();
          var cuentaContable= $('#cuenta_contable').val();
          var fechaChk= $('#nuevo_fecha').val();
          var numeroChk= $('#nuevo_cheque').val();
          var param = {
                        cuentaBancaria: cuentaBancaria,
                        cuentaContable: cuentaContable,
                        fechaChk: fechaChk,
                        numeroChk: numeroChk
                      };

              $.ajax({
                  data: param,
                  url: "vistas/modulos/pagos/pagos_cheque.php",
                  method: "post",
                  success: function(data) {
                    console.log(data);
                    ConsultaPagar();
                  }
              });

            
        }
        /*=============================================
         IMPRIMIR INDIVIDUAL
          =============================================*/
        function ImprimirManual() {
           
          var cuentaBancaria= $('#cuenta_bancariaM').val();
          var cuentaContable= $('#cuenta_contableM').val();
          var fecha= $('#nuevo_fechaM').val();
          var cheque= $('#nuevo_chequeM').val();
          var nombre= $('#nuevo_nombre').val();
          var monto=$('#nuevo_monto').val();
          var detalle=$('#nuevo_detalle').val();
          window.open("vistas/modulos/pagos/pagos_imprimirM.php?cheque="+cheque+"&nombre="+nombre+"&monto="+monto+"&detalle="+detalle+"&fecha="+fecha+"&cuentabancaria="+cuentaBancaria+"&cuentacontable="+cuentaContable, "_blank");  
          
            
        }


        /*=============================================
         AGREGAR PAGO A LOS CHEQUES INDIVIDUAL
          =============================================*/
        function AgregarPagoChequeIndividual() {
           $('#modalChequeManual').modal('hide');
          var proveedor= $('#nuevo_nombre').text();
          var cuentaBancaria= $('#cuenta_bancariaM').val();
          var cuentaContable= $('#cuenta_contableM').val();
          var fechaChk= $('#nuevo_fechaM').val();
          var numeroChk= $('#nuevo_chequeM').val();
          var detalle= $('#nuevo_detalle').val();
          var monto= $('#nuevo_monto').val();
          var param = {
                        proveedor:proveedor,
                        cuentaBancaria: cuentaBancaria,
                        cuentaContable: cuentaContable,
                        fechaChk: fechaChk,
                        numeroChk: numeroChk,
                        detalle: detalle,
                        monto: monto
                      };

              $.ajax({
                  data: param,
                  url: "vistas/modulos/pagos/pagos_aplicarI.php",
                  method: "post",
                  success: function(data) {
                    console.log(data);
                    ConsultaPagar();
                  }
              });

            
        }
        /*=============================================
         APLICAR PAGO A LOS CHEQUES
          =============================================*/
        function AplicarPago() {
           swal({
                  title: '¿Está seguro de aplicar los Cheques?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, aplicar!'
                  }).then(function(result){
                    if (result.value) {
                            window.open("vistas/modulos/pagos/pagos_aplicar.php", "_blank");  
                            window.location.replace('pagos');
                                            
                                  }
                                });
            
        }
        /*=============================================
  BUSCAR FACTURA
  =============================================*/
        function BuscarFacturaC(){
            var factura = $("#compra_buscar").val();
            $("#agregar-compra").empty();
            var param = {
                factura:factura
            };

            $.ajax({
                data: param,
                url: "vistas/modulos/pagos/pagos_buscar.php",
                method: "post",
                success: function(data) {
                    //console.log(data);
                    var resultadoajax = JSON.parse(data);
                    var factura=resultadoajax.data[0][0]["NFac"];
                    var fecha=resultadoajax.data[0][0]["FechaFact"];
                    var total=resultadoajax.data[0][0]["Total"];
                    var ck = resultadoajax.data[0][0]["chkCancelacion"];
                    total= number_format(total,2,'.',',')
                    var proveedor=resultadoajax.data[0][0]["Proveedor"];
                    var estado=resultadoajax.data[0][0]["Estado"];
                    if (estado=='Pendiente') {
                        swal({
                            type: "error",
                            title: "¡La factura NO ha sido Cancelada!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                                $("#compra_buscar").val("");
                            }
                        })

                    }else{
                        var html= '<tr>\
                  <td>'+factura+'</td>\
                  <td>'+proveedor+'</td>\
                  <td>'+fecha+'</td>\
                  <td>'+total+'</td>\
                  <td>'+estado+'</td>\
                  <td>'+ck+'</td>\
                  </tr>';
                        $('#agregar-compra').append(html);
                    }
                }
            });

        }
        /*=============================================
         LIMPIAR SALDO
          =============================================*/
        function LimpiarSaldo() {
           swal({
                  title: '¿Está seguro de limpiar el saldo?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, limpiar!'
                  }).then(function(result){
                    if (result.value) {
                            window.open("vistas/modulos/pagos/pagos_limpiar.php", "_blank");  
                            window.location.replace('pagos');
                                            
                                  }
                                });
            
        }


        // < FIN PARTE DE CODIGO ****************
        

       

    </script>








