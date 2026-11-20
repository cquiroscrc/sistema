
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
          CARGA PRODUCTOS
          =============================================*/
        function myWoo() {
            
             $('#ingresados').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/woocommerce/ingresados.php')
            .done(function(response){
              //console.log(response);
              $('#ingresados').html(response);
            });

        }

        /*=============================================
          MY FUNCTION CODIGO
          =============================================*/
        function myFunctionCodigo() {
             var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
             $('#resultados').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
             
             if ($("#selector").val()!="") {
              $.ajax('vistas/modulos/woocommerce/busqueda.php?codigo='+dataToPass.codigo)
              .done(function(response){
                $("#selector").val("");
                 $('#resultados').html(response);
              });
             }
        }
         
         /*=============================================
          AGERGAR WOO
          =============================================*/
        function AgregarWoo(codigo,descripcion,precio,boton) {
            $("#"+boton+".btn").hide();
            
              var param = {
                              codigo: codigo,
                              descripcion: descripcion,
                              precio: precio
                            };
                $.ajax({
                  data: param,
                  url: "vistas/modulos/woocommerce/agregar.php",
                  method: "post",
                  success: function(data) {
                        console.log(data);
                        myWoo();
                  } 
                }); 

          }
       
       /*=============================================
          BORRAR CODIGO
          =============================================*/
        function EliminarWoo(codigo) {


          swal({
                  title: '¿Está seguro de borrar el producto del Woocommerce?',
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
                                    codigo: codigo                                    
                                  };

                      $.ajax({
                        data: param,
                        url: "vistas/modulos/woocommerce/eliminar.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "El código fue eliminado correctamente correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                      myWoo();
                                  });
                        }
                         
                      }); 

                    }
                });
             
        } 
       
        /*=============================================
         REPORTE EXCEL
          =============================================*/

        function ExpWoo() {
            window.open("vistas/modulos/woocommerce/reporte.php");         
        }
        function ExpWooAct() {
            window.open("vistas/modulos/woocommerce/actualiza.php");
        }

    </script>








