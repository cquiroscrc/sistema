
<?php 
?>
<script>

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
          BUSQUEDA DE CODIGO PERDIDO
          =============================================*/
        function myCodigoPerdido() {
          
          var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
          
              $("#modalPerdida").modal()
             $('#resultado_sucursal').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_perdidas.php?codigo='+dataToPass.codigo)
            .done(function(response){
              //console.log(response);
              $('#resultado_sucursal').html(response);
            });

        }

        /*=============================================
          LISTADO TOTAL
          =============================================*/
        function myListadoPerdidas() {
             $('#sucursal_codigo').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/resultado_perdidas.php')
            .done(function(response){
              //console.log(response);
              $('#sucursal_codigo').html(response);
            });

        }

        /*=============================================
          MY CODIGO REPORTE  *******************
          =============================================*/
        function myReportePerdidas() {
            window.open("controladores/perdidas.reporte.php");
        }

        
        /*=============================================
          AGREGA AL LISTADO
          =============================================*/
        function myPerdido(codigo,descripcion){
            $('#modalPerdida').modal('hide');
            swal({
                title: "Ventas Perdidas",
                text: "Digite la descripcion del producto "+codigo,
                input: 'text',
                type: "question",
                inputValue: descripcion,
            }).then(resultado => {
                if (resultado.value) {
                    let nuevo_descripcion = resultado.value;

                    var param = {
                        codigo: codigo,
                        descripcion:nuevo_descripcion
                    };

                    $.ajax({
                        data: param,
                        url: "ajax/insertar_perdidas.ajax.php",
                        method: "post",
                        success: function(data) {
                            console.log(data);
                            swal({
                                type: "success",
                                title: "El producto ha sido agregado correctamente",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                window.location.replace('perdidas');
                            });
                        }
                    });
                    // *****

                }
            });

             
        }

        /*=============================================
          MODIFICA EL CODIGO
          =============================================*/
        function myCambioPerdida(codigo,descripcion,adicional){

              swal({
                title: "Ventas Perdidas",
                text: "Corrija el codigo ingresado "+codigo,
                input: 'text',
                type: "question",
                inputValue: codigo,
              }).then(resultado => {
                  if (resultado.value) {
                      let nuevo_codigo = resultado.value;
                      swal({
                          title: "Ventas Perdidas",
                          text: "Corrija la descripción del codigo ingresado "+descripcion,
                          input: 'text',
                          type: "question",
                          inputValue: descripcion,
                      }).then(resultado => {
                          if (resultado.value) {
                              let nuevo_descripcion = resultado.value;
                              swal({
                                  title: "Ventas Perdidas",
                                  text: "Corrija la información adicional del codigo ingresado "+adicional,
                                  input: 'text',
                                  type: "question",
                                  inputValue: adicional,
                              }).then(resultado => {

                                      let nuevo_adicional = resultado.value;

                                      var param = {
                                          codigo: nuevo_codigo,
                                          descripcion:nuevo_descripcion,
                                          ubicacion:nuevo_adicional
                                      };

                                      $.ajax({
                                          data: param,
                                          url: "ajax/perdidas_modificar.ajax.php",
                                          method: "post",
                                          success: function(data) {
                                              console.log(data);
                                              swal({
                                                  type: "success",
                                                  title: "El producto ha sido actualizado correctamente",
                                                  showConfirmButton: true,
                                                  confirmButtonText: "Cerrar"
                                              }).then(function(result){
                                                  window.location.replace('perdidas');
                                              });
                                          }
                                      });
                                      // *****

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
                  title: '¿Está seguro de borrar el producto de ventas perdidas?',
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
                      url="ajax/perdidas_rebajar.ajax.php";
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
                              window.location.replace('perdidas');
                          });
                          
                      });        

                    }

              })
        }  

        // < FIN PARTE DE CODIGO ****************
    </script>








