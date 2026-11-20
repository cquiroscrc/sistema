
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
          MY FUNCTION CODIGO SUCURSAL
          =============================================*/
        function myFunctionCodigoUbicacion() {
          
          var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
          
             $('#modalUbicacionProducto').modal(); 
             $('#resultadoion').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_codigos_ubicaciones.php?codigo='+dataToPass.codigo)
            .done(function(response){
              //console.log(response);
              $('#resultadoion').html(response);
            });

        }
        
        /*=============================================
          MY UBICACION
          =============================================*/
        function myUbicacion() {
         
            var ubi = $("#ubica").val();
            $('#resultado_ubicacion').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_ubicacion.php?codigo='+ubi)
            .done(function(response){
              //console.log(response);
              $('#resultado_ubicacion').html(response);
            });

        }
       
        
        /*=============================================
          MY AGREGAR UBICACION
          =============================================*/
        function myAgregarCodigoUbicacion(codigo){
               var ubica = $('#ubica').val(); 
               if (ubica=='') {
                  swal({
                      type: "error",
                      title: "La ubicacion no puede estar vacio",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                          console.log(result);
                          
                      });

               }else{       
                   
                    var param = {
                            codigo: codigo,
                            ubicacion: ubica
                        };

                      url="ajax/agregar_ubicacion.ajax.php";
                      data="";
                      data=param;

                      myAjaxHH(url,data)
                      .done( function(data) {
                        //console.log(data);
                        swal({
                          type: "success",
                          title: "El producto ha sido agregado a la ubicacion correctamente",
                          showConfirmButton: true,
                          confirmButtonText: "Cerrar"
                          }).then(function(result){
                            
                              //window.location.replace('ubicaciones');
                          });
                          
                      });  
              }
        }

            
           
         /*=============================================
          ELIMINAR CODIGO
          =============================================*/
        function myVaciar(){
              var ubica = $('#ubica').val(); 
              if (ubica=='') {
                  swal({
                      type: "error",
                      title: "La ubicacion no puede estar vacio",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                          console.log(result);
                          
                      });

               }else{       

                swal({
                    title: '¿Está seguro de vaciar la ubicación?',
                    text: "¡Si no lo está puede cancelar la acción!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Si, vaciar productos!'
                    }).then(function(result){
                      if (result.value) {
                          
                          var param = {
                              ubicacion: ubica
                          };

                        url="ajax/ubicacion_rebajar.ajax.php";
                        data="";
                        data=param;

                        myAjaxHH(url,data)
                        .done( function(data) {

                          var parent = $(this).parent().remove();
                          swal({
                            type: "success",
                            title: "La ubicación ha sido vaciada",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                            }).then(function(result){
                                console.log(result);
                                //window.location.replace('ubicacion');
                            });
                            
                        });        

                      }
                  })
                }
        }  

        // < FIN PARTE DE CODIGO ****************
        

       

    </script>








