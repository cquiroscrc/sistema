
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
        function myFunctionCodigoAjuste() {
          
          var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
          
              $("#modalAjusteProducto").modal()
             $('#resultado_ajuste').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_ajuste.php?codigo='+dataToPass.codigo)
            .done(function(response){
              //console.log(response);
              $('#resultado_ajuste').html(response);
            });

        }
        /*=============================================
          MY CODIGO SUCURSAL
          =============================================*/

        function myCodigoAjuste() {
             $('#ajuste_codigo').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');

             var consecutivo = $('#numeroAjuste').text();
            $.ajax('vistas/modulos/resultado_ajuste.php?consecutivo='+consecutivo)
            .done(function(response){
              //console.log(response);
              $('#ajuste_codigo').html(response);
            });

        }

        
        
        /*=============================================
          MY CODIGO (Pregunta cantidad)
          =============================================*/
        function myCodigoMas(codigo){
                       
              var la_cantidad;
              var tipo=1;             
              swal({
                title: "Ajuste",
                text: "Digite la cantidad para agregar a inventario",
                input: 'text',
                type: "question",
                
              }).then(resultado => {
                  if (resultado.value) {
                      let la_cantidad = resultado.value;
                      mylistadoHH(codigo,la_cantidad,tipo);
                  }
              });
               $('#modalAjusteProducto').modal('hide');
             
        }
        /*=============================================
          MY BUSCAR AJUSTE
          =============================================*/
        function myFunctionBuscarAplicar(){
                var dataToPass = $('#busqueAjuste').val();
              
                $("#modalAjusteHecho").modal()
                $('#busqueda_ajuste').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
                $.ajax('vistas/modulos/resultado_busqueda_ajuste.php?consecutivo='+dataToPass)
                .done(function(response){
                  //console.log(response);
                  $('#busqueda_ajuste').html(response);
                });
              
              
             
        }
        /*=============================================
          MY CODIGO (Pregunta cantidad)
          =============================================*/
        function myCodigoMenos(codigo){
                       
              var la_cantidad;
              var tipo=2;                          
              swal({
                title: "Ajuste",
                text: "Digite la cantidad para rebajar a inventario",
                input: 'text',
                type: "question",
                
              }).then(resultado => {
                  if (resultado.value) {
                      let la_cantidad = resultado.value;
                      mylistadoHH(codigo,la_cantidad,tipo);
                  }
              });
               $('#modalAjusteProducto').modal('hide');
             
        }
        /*=============================================
          MY LISTADO HH
          =============================================*/
        function mylistadoHH(codigo,cantidad,tipo){
            
            url="ajax/consulta_productos.ajax.php?codigo="+codigo;
            data="";
            data=codigo;
            myAjaxHH(url,data)
            .done( function(data) {
            
            	var resultadoajax = JSON.parse(data);
            	var existencia = resultadoajax.data[0][0]['EXISTENCIA'];
            
	            // VERIFICA SI LA CANTIDAD ES MAYOR
	            if (Number(existencia)< Number(cantidad) && tipo==2) {

	              swal({
	                title: "Error al facturar",
	                text: "¡La cantidad facturada es mayor a la existencia",
	                type: "error",
	                confirmButtonText: "¡Cerrar!"
	              });

	              
	            }else{  

                    // INGRESAR A BD EL CODIGO
                  var ajuste =  $('#numeroAjuste').text();
                  var param = {
                        codigo: codigo,
                        cantidad:cantidad,
                        ajuste: ajuste,
                        tipo:tipo
                    };

                  	$.ajax({
                    	data: param,
                        url: "ajax/productos_ajuste.ajax.php",
                        method: "post",
                        success: function(data) {
                          console.log(data);
                          swal({
                                type: "success",
                                title: "El producto ha sido agregardo al ajuste correctamente",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                                }).then(function(result){
                                  window.location.replace('ajustes');    
                                });
                            

                          //window.location.replace('sucursal');
                    	}
                  	});

                  	 
            	}
          	});         
        }
        /*=============================================
          MY FUNCTION APLICAR 
          =============================================*/
        function myFunctionAjusteAplicar(){

              var ajuste =  $('#numeroAjuste').text();
              var razon =  $('#razon').val();
              var param = {
                            razon: razon,
                            ajuste: ajuste
                        };

                      url="ajax/ajuste_aplicar.ajax.php";
                      data="";
                      data=param;

                      myAjaxHH(url,data)
                      .done( function(data) {
                          console.log(data);
                          swal({
                              type: "success",
                              title: "El producto ha sido aplicado correctamente",
                              showConfirmButton: true,
                              confirmButtonText: "Cerrar"
                              }).then(function(result){
                                  
                                  window.location.replace('ajustes');
                              });
                              
                          });

        }
         /*=============================================
          ELIMINAR CODIGO myFunctionAjusteAplicar()
          =============================================*/
        function myElimina(codigo,factura){

              swal({
                  title: '¿Está seguro de borrar el producto del ajuste?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, borrar producto!'
                  }).then(function(result){
                    if (result.value) {
                        var ajuste =  $('#numeroAjuste').text();
                        var param = {
                            codigo: codigo,
                            ajuste: ajuste
                        };

                      url="ajax/ajuste_rebajar.ajax.php";
                      data="";
                      data=param;

                      myAjaxHH(url,data)
                      .done( function(data) {

                        var parent = $(this).parent().remove();
                        swal({
                          type: "success",
                          title: "El producto ha sido borrado correctamente",
                          showConfirmButton: true,
                          confirmButtonText: "Cerrar"
                          }).then(function(result){
                              console.log(result);
                              window.location.replace('ajustes');
                          });
                          
                      });        

                    }

              })
        }  

        // < FIN PARTE DE CODIGO ****************
        

       

    </script>








