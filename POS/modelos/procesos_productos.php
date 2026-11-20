
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
        

        // BOTONES >**********************
      
        /*=============================================
          MY FUNCTION CODIGO
          =============================================*/
        function myFunctionCodigoInventario() {
          
          var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
          

             $('#resultado_codigo').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_codigos.php?codigo='+dataToPass.codigo)
            .done(function(response){
              //console.log(response);
              $('#resultado_codigo').html(response);
            });

        }
        /*=============================================
          MY FUNCTION CODIGO
          =============================================*/
        function myFunctionCodigoInventario2() {
            var dataToPass2 = dispatchMask2.masked._value
            //alert(dataToPass2);
            swal({
                title: '¿Está seguro pasar '+ $("#paseorigen").text()+' al '+dataToPass2+' ?',
                text: "¡Si no lo está puede cancelar la acción!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, borrar producto!'
            }).then(function(result){
                if (result.value) {
                    var codigoorigen =  $("#paseorigen").text()
                    var codigodestino= dataToPass2;
                    var param = {
                        codigoorigen: codigoorigen,
                        codigodestino: codigodestino
                    };

                    url="vistas/modulos/productos_pase.php";
                    data=param;

                    myAjaxHH(url,data)
                        .done( function(data) {
                            console.log(data);
                            swal({
                                type: "success",
                                title: "El producto ha sido cambiado correctamente",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                console.log(result);
                                //window.location.replace('productos');
                            });

                        });

                }

            })
        }

        /*=============================================
          MY FUNCTION CODIGO
          =============================================*/
        function myAgregarCodigo() {
          
          var dataToPass = dispatchMask.masked._value;
          $("#nuevoCodigo").val(dataToPass);
          $("#nuevoUbicacion").val("ALTA");
          $('#modalAgregarProducto').modal('show');

        }

        /*=============================================
          IMAGEN ES TEMPORAL
          =============================================*/
        function myImagen(ruta) {

            var dataToPass = dispatchMask.masked._value;
            $("#nuevoCodigo").val(dataToPass);
            $("#nuevoUbicacion").val("ALTA");
            $('#modalAgregarProducto').modal('show');

        }

        /*=============================================
          MY FUNCTION CODIGO
          =============================================*/
        function myAgregarFoto() {
          $('#modalAgregarFoto').modal('show');
        }
        /*=============================================
          MY FUNCTION CODIGO
          =============================================*/
        function espacioblanco() {
            $('#ModalBlancos').modal('show');
        }
        /*=============================================
          MY CODIGO (Pregunta cantidad)
          =============================================*/
        function myCodigo(codigo){
                       
              var la_cantidad;
                           
              swal({
                title: "Facturacion",
                text: "Digite la cantidad a factuar",
                input: 'text',
                type: "question",
                
              }).then(resultado => {
                  if (resultado.value) {
                      let la_cantidad = resultado.value;
                      mylistadoHH(codigo,la_cantidad);
                  }
              });
               $('#modalBuscarCodigo').modal('hide');
             
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
                    var descripcion = resultadoajax.data[0][0]["DESCRIPCION"];
                    var precio = resultadoajax.data[0][0]["VENTA"];
                    var desc_tecnica = resultadoajax.data[0][0]["DESC_ALT"];
                    var sub = precio*cantidad;
                    var num_prefactura =$('#invoice-id').text()

                    var potencia=resultadoajax.data[0][0]['potencia'];
                    var xpotencia=potencia*100;
                    precio=money_format(precio)+xpotencia;
                    

                    var param = {

                        Factura: num_prefactura,
                        codigo: codigo,
                        cantidad:cantidad,
                        codcliente: $('#codigocliente').text(),
                        vendedor:$('#codigovendedor').text(),
                        precio:precio
                    };

                  	$.ajax({
                    	data: param,
                        url: "ajax/productos_prefactura.ajax.php",
                        method: "post",
                        success: function(data) {
                        	//console.log(data);
                          // AGREGA CODIGOS EN EL DOM
                        myRecargar(num_prefactura);
                    	}
                  	});

                  	 
            	}
          	});         
        }

         /*=============================================
          ELIMINAR CODIGO
          =============================================*/
        function myElimina(codigo,factura){
              var param = {
                      Factura: factura,
                      codigo: codigo,
                  };

              url="ajax/consulta_rebajar.ajax.php";
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
                      if (result.value) {
                        
                      }
                  });
                  myRecargar(factura);
              }); 
              // Recarga
        }  

        // < FIN PARTE DE CODIGO ****************

    </script>








