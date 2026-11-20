
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
        function myFunctionCodigoSucursal() {
          
          var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
          
              $("#modalSucursalProducto").modal()
             $('#resultado_sucursal').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_sucursal.php?codigo='+dataToPass.codigo)
            .done(function(response){
              //console.log(response);
              $('#resultado_sucursal').html(response);
            });

        }
        /*=============================================
          MY FUNCTION DIFERENCIAS SUCURSAL
          =============================================*/
        function myFunctionDiferenciasSucursal() {
              $("#modalSucursalDiferencias").modal()
             $('#resultado_diferencias').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/diferencia_sucursal.php')
            .done(function(response){
              //console.log(response);
              $('#resultado_diferencias').html(response);
            });

        }
        /*=============================================
          MY CODIGO SUCURSAL
          =============================================*/

        function myCodigoSucursal() {
             $('#sucursal_codigo').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/resultado_sucursal.php')
            .done(function(response){
              //console.log(response);
              $('#sucursal_codigo').html(response);
            });

        }

        /*=============================================
          MY CODIGO REPORTE
          =============================================*/

        function myReporteSucursal() {
            window.open("controladores/sucursal.reporte.php");             
        }
        /*=============================================
          MY CODIGO REPORTE DIFERENCIAS
          =============================================*/

        function myReportediferencias() {
            window.open("controladores/sucursal.diferencias.php");             
        }
        
        /*=============================================
          MY CODIGO (Pregunta cantidad)
          =============================================*/
        function myCodigo(codigo){
                       
              var la_cantidad;
                           
              swal({
                title: "Sucursal",
                text: "Digite la cantidad para agregar a sucursal",
                input: 'text',
                type: "question",
                
              }).then(resultado => {
                  if (resultado.value) {
                      let la_cantidad = resultado.value;
                      mylistadoHH(codigo,la_cantidad);
                  }
              });
               $('#modalSucursalProducto').modal('hide');
             
        }

        /*=============================================
          MY CODIGO (Pregunta cantidad)
          =============================================*/
        function myUbicacion(codigo){
                       
              var la_ubicacion;             
              swal({
                title: "Sucursal",
                text: "Digite la ubicacion en sucursal",
                input: 'text',
                type: "question",         
              }).then(resultado => {
                  if (resultado.value) {
                      let la_ubicacion = resultado.value;
                      // Aqui va el ajax para actualizar

                      var param = {
                        codigo: codigo,
                        ubicacion:la_ubicacion
                      };

                      $.ajax({
                        data: param,
                          url: "ajax/sucursal_ubicacion.ajax.php",
                          method: "post",
                          success: function(data) {
                            console.log(data);
                            swal({
                                type: "success",
                                title: "El producto ha sido actualizado correctamente",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                                }).then(function(result){
                                  console.log(result);
                                  window.location.replace('sucursal');    
                                });
                                

                            }
                      });
                  }        
             });
        }

        /*=============================================
          MY LISTADO HH
          =============================================*/
        function mylistadoHH(codigo,cantidad){
          
            url="ajax/consulta_productos.ajax.php?codigo="+codigo;
            data="";
            data=codigo;
            myAjaxHH(url,data)
            .done( function(data) {
            
            	var resultadoajax = JSON.parse(data);
            	var existencia = resultadoajax.data[0][0]['EXISTENCIA'];
            
	            // VERIFICA SI LA CANTIDAD ES MAYOR
	            if (Number(existencia)< Number(cantidad)) {

	              swal({
	                title: "Error al facturar",
	                text: "¡La cantidad facturada es mayor a la existencia",
	                type: "error",
	                confirmButtonText: "¡Cerrar!"
	              });

	              
	            }else{  

                    // INGRESAR A BD EL CODIGO
                  var param = {
                        codigo: codigo,
                        cantidad:cantidad
                    };

                  	$.ajax({
                    	data: param,
                        url: "ajax/productos_sucursal.ajax.php",
                        method: "post",
                        success: function(data) {
                          console.log(data);
                          swal({
                                type: "success",
                                title: "El producto ha sido agregardo a sucursal correctamente",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                                }).then(function(result){
                                  console.log(result);
                                  window.location.replace('sucursal');    
                                });
                            

                          //window.location.replace('sucursal');
                    	}
                  	});

                  	 
            	}
          	});         
        }

         /*=============================================
          ELIMINAR CODIGO
          =============================================*/
        function myElimina(codigo,factura){

              swal({
                  title: '¿Está seguro de borrar el producto de sucursal?',
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
                            codigo: codigo,
                        };

                      url="ajax/sucursal_rebajar.ajax.php";
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
                              window.location.replace('sucursal');
                          });
                          
                      });        

                    }

              })
        }  

        // < FIN PARTE DE CODIGO ****************
        

       

    </script>








