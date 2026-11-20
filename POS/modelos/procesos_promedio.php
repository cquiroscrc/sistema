
<?php 
?>
<script>

		/*=============================================
          NUMBER FORMAT (convierte numero a moneda)
          =============================================*/
     
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
          MY FUNCTION CODIGO
          =============================================*/
        function myFunctionCodigo() {

          var proveedor = $("select#seleccionarProveedor").val();
          
          if ($("#nuevo_pedido").text()=='' && $("#nombre_pedido").val()=="") {
            
              swal({
                      type: "error",
                      title: "El pedido no puede estar vacio",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       
                });

            }else{
              if ($("#nuevo_pedido").text()=="" && $("nombre_pedido").val()!="") {
                var nuevo = $("#nombre_pedido").val()
                nuevo = nuevo.toUpperCase();
                var valor=nuevo+'-'+proveedor;
                $("#nuevo_pedido").text(valor);

              }
          
              if ($("#selector").val()!="") {
                var dataToPass = {codigo:dispatchMask.masked.currentMask._value};  
              }
              
              var myPedido= $("#nuevo_pedido").text();

             $('#resultados').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
             
             if ($("#selector").val()!="") {
              $.ajax('vistas/modulos/promedio/promedio_productos.php?pedido='+myPedido+'&codigo='+dataToPass.codigo)
              .done(function(response){
                $("#selector").val("");
                 $('#resultados').html(response);
              });
             }else{
              $.ajax('vistas/modulos/promedio/promedio_productos.php?proveedor='+proveedor+'&pedido='+myPedido)
              .done(function(response){ 
                 $('#resultados').html(response);
              });
             }
          }
        }
          /*=============================================
          SELECCIONAR PEDIDO
          =============================================*/
          $('select#seleccionarPedido').on('change',function(){
              var valor = $(this).val();
              $("#nuevo_pedido").text(valor);
              

          });

         /*=============================================
          MY FUNCTION CODIGO
          =============================================*/
        function AgregarCodigo(codigo,id) {
            var proveedor = $("select#seleccionarProveedor").val();
            var pedido =$("#nuevo_pedido").text();
           
            if ($("select#selector").val()=="") {
                // El porveedor esta vacio
                
            }else{
              if ($("#nuevo_pedido").text()=="") {
                swal({
                      type: "error",
                      title: "El pedido no puede estar vacio",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       
                });
                
              }else{
                 //*** AQUI AGREGA A PEDIDO 
                 var cantidad = $("#"+id).val();
                 var pedido = $("#nuevo_pedido").text();

                 if (cantidad !='') {
                    var param = {
                                    codigo: codigo,
                                    proveedor:proveedor,
                                    pedido:pedido,
                                    cantidad:cantidad
                                  };
                      $.ajax({
                        data: param,
                        url: "vistas/modulos/promedio/promedio_pedido.php",
                        method: "post",
                        success: function(data) {
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "El producto fue agregado",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                   

                                  });
                        } 
                      }); 

                 }else{
                  alert("vacio");
                 }
                 // ** FIN AGREGAR
              }
            }
          }
        /*=============================================
          REVISION MOVIMIENTOS
          =============================================*/
        function MovimientosCodigo(codigo,id) {
            $('#modalMovimientoProducto').modal();
            $('#resultadoM').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_movimientos.php?codigo=' + codigo)
                .done(function (response) {
                    //console.log(response);
                    $('#resultadoM').html(response);
                });
        }
        /*=============================================
         CAMBIAR MESES
          =============================================*/
        function Meses_Estudio() {
                $("#ModalMeses").modal();
        }
        /*=============================================
          CAMBIA MESES
          =============================================*/
        function CambiarMeses() {
            var meses= $("#estudio").val();

                $.ajax('vistas/modulos/promedio/promedio_meses.php?mes='+meses)
                    .done(function(response){
                        console.log(response);
                        swal({
                            type: "success",
                            title: "El estudio de meses ha cambiado a "+meses+ " meses",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            $('#ModalMeses').modal('hide');
                        });
                    });
        }

        /*=============================================
          REVISION PEDIDO
          =============================================*/
        function Rev_pedido() {
          var pedido= $("#nuevo_pedido").text();
          if (pedido=='') {
            swal({
                      type: "error",
                      title: "El pedido no puede estar vacio",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       
                });

          }else{
          $("#modalRevisar").modal();
          
           $('#rev_pedido').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
           $.ajax('vistas/modulos/promedio/promedio_pedido_ver.php?pedido='+pedido)
            .done(function(response){
              //console.log(response);
              $('#rev_pedido').html(response);
              
            });
          }  

        }
       /*=============================================
          BORRAR CODIGO
          =============================================*/
        function EliminarCodigo(codigo,pedido,id) {


          swal({
                  title: '¿Está seguro de borrar el producto del pedido?',
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
                                    pedido: pedido,
                                    id:id
                                  };

                      $.ajax({
                        data: param,
                        url: "vistas/modulos/promedio/promedio_eliminar.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "El código fue eliminado correctamente correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                    $('#rev_pedido').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
                                       $.ajax('vistas/modulos/promedio/promedio_pedido_ver.php?pedido='+pedido)
                                        .done(function(response){
                                          //console.log(response);
                                          $('#rev_pedido').html(response);
                                          
                                        });
                                   
                                  

                                  });
                        } 
                      }); 

                    }
                });
             
              

        } 
       
        /*=============================================
         REPORTE EXCEL
          =============================================*/

        function Exp_pedido() {
            var pedido= $("#nuevo_pedido").text();
            if (pedido=='') {
            swal({
                      type: "error",
                      title: "El pedido no puede estar vacio",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       
                });

          }else{
            window.open("vistas/modulos/promedio/promedio_reporte.php?pedidos="+pedido); 
          }            
        }

        /*=============================================
         LIMPIAR PEDIDO
          =============================================*/

        function Limpiar_pedido() {
            var pedido= $("#nuevo_pedido").text();
            if (pedido=='') {
            swal({
                      type: "error",
                      title: "El pedido no puede estar vacio",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       
                });

          }else{

            swal({
                  title: '¿Está seguro de borrar todo el pedido?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, borrar pedido!'
                  }).then(function(result){
                    if (result.value) {

                        var param = {
                                    
                                    pedidos: pedido
                                  };

                      $.ajax({
                        data: param,
                        url: "vistas/modulos/promedio/promedio_limpiar.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "El pedido fue eliminado correctamente correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                     window.location.replace('promedio'); 
                                   
                                  });
                        } 
                      }); 

                    }
                });
                       
        }
      }

    </script>








