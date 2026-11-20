
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
        var motorizado='';

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
          CARGA MANDADOS
          =============================================*/
        function myMandado(id_mensajero) {
            motorizado=id_mensajero;
             $('#listado_mandados').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/resultado_mandados.php?mensajero='+id_mensajero)
            .done(function(response){
              //console.log(response);
              $('#listado_mandados').html(response);
            });

        }
        /*=============================================
          AGREGAR MANDADOS MODAL
          =============================================*/
        function myAgregarMandado() {
            if (motorizado=='3') {

            }else{
              document.getElementById("nuevo_motorizado").value = motorizado; 
              $("#nuevo_motorizado").prop('disabled',true);     
            }
            
            $("#modalAgregarMandado").modal()
        }
        /*=============================================
         IMPRIMIR MANDADOS
          =============================================*/
        function myImprimirMandado(datos) {
          const { value: fruit } = swal({
              title: 'Selecione un mensajero',
              input: 'select',
              inputOptions: {
                1: 'Alberto Villa',
                2: 'Roberto Lainez',
                3: 'Javier Roldán'
              
              },
              inputPlaceholder: 'Seleccionar un mensajero',
              showCancelButton: true,
              inputValidator: (value) => {
                  var param = {
                            motorizado: value                                           
                        };

                    url="vistas/modulos/mandados_imprime.php";
                    data="";
                    data=param;
                    
                    myAjaxHH(url,data)
                    .done( function(data) {
                      console.log(data);
                    });

              }
            });

                
            
        }

        /*=============================================
         IMPRIMIR MANDADOS
          =============================================*/
        function myAplicarMandado(datos) {
          swal({
                  title: '¿Está seguro de aplicar los mandados?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, aplicar!'
                  }).then(function(result){
                    if (result.value) {

                                  const { value: fruit } = swal({
                                      title: 'Selecione un mensajero',
                                      input: 'select',
                                      inputOptions: {
                                        1: 'Alberto Villa',
                                        2: 'Roberto Lainez',
                                        3: 'Javier Roldán'
                                    
                                      },
                                      inputPlaceholder: 'Seleccionar un mensajero',
                                      showCancelButton: true,
                                      inputValidator: (value) => {
                                          var param = {
                                                    motorizado: value                                           
                                                };

                                            url="vistas/modulos/mandados_aplica.php";
                                            data="";
                                            data=param;
                                            
                                            myAjaxHH(url,data)
                                            .done( function(data) {
                                              console.log(data);

                                               swal({
                                                  type: "success",
                                                  title: "Los mandados han sido aplicado correctamente",
                                                  showConfirmButton: true,
                                                  confirmButtonText: "Cerrar"
                                                  }).then(function(result){
                                                      if (result.value) {
                                                        window.location.replace('mensajeria');      
                                                      }
                                                }) 

                                            });

                                      }
                                    });
                                  }
                                });
                
            
        }
        /*=============================================
          ELIMINAR MANDADOS
          =============================================*/
        function myElimina(factura,fecha,mensajero) {
            
          swal({
                  title: '¿Está seguro de borrar el mandado?',
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
                                    factura: factura,
                                    fecha: fecha,
                                    motorizado: mensajero                                           
                                };

                            url="ajax/eliminar_mandado.ajax.php";
                            data="";
                            data=param;
                            
                            myAjaxHH(url,data)
                            .done( function(data) {
                                console.log(data);
                                swal({
                                  type: "success",
                                  title: "El mandado ha sido eliminado correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                      if (result.value) {
                                        $("#modalAgregarMandado").modal('hide');  
                                        window.location.replace('mensajeria');      
                                      }
                                })
                                                            
                            });
                    }
                });               
           
        }
        /*=============================================
          AGREGAR MANDADOS BD
          =============================================*/
        function AgregarMandado() {
             var mensajero = $('#nuevo_motorizado').val();             
             var envia=$('#nuevo_envia').val();
             var factura=$('#nuevo_factura').val();
             var monto=$('#nuevo_monto').val();
             var fecha=$('#nuevo_fecha').val();   
             var dateString = moment(fecha).format('Y-MM-DD');
             fecha=dateString;
             var lugar=$('#nuevo_lugar').val();
             var cantidad=$('#nuevo_cantidad').val();
             

             if (envia=='' || factura=='' || monto=='' || fecha=='' || fecha=='' || lugar=='' || cantidad=='' || motorizado=='' ) {
                swal({
                  title: "Error en el mandado",
                  text: "¡Hay campos vacios!",
                  type: "error",
                  confirmButtonText: "¡Cerrar!"
                });

            }else{ 

              var param = {
                          envia: envia,
                          factura: factura,
                          monto: monto,
                          fecha: fecha,
                          lugar: lugar,
                          cantidad: cantidad,
                          motorizado: mensajero                                           
                      };

                  url="ajax/insertar_mandado.ajax.php";
                  data="";
                  data=param;
                  
                  myAjaxHH(url,data)
                  .done( function(data) {
                      console.log(data);
                      swal({
                        type: "success",
                        title: "La cantidad ha sido cambiado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                              $("#modalAgregarMandado").modal('hide');  
                              window.location.replace('mensajeria');      
                            }
                      })
                                                  
                  });

              
            }

            
        }        
      
        

       

    </script>








