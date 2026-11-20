
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
          BUSCAR FECHA
          =============================================*/
        function myFunctionCodigoDiarias(){

            var fechaInicial = $("#fecha_diarias").val();
            var fechaFinal = $("#fecha_diarias_Fin").val();
            var dateString = moment(fechaInicial).format('M/D/Y');
            fechaInicial=dateString;
            var dateString = moment(fechaFinal).format('M/D/Y');
            fechaFinal=dateString;

            $('#diarias_codigo').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_diarias.php?fecha='+fechaInicial+'&fechafin='+fechaFinal)
            .done(function(response){
              //console.log(response);
              $('#diarias_codigo').html(response);
            });
            
        }
      
      /*=============================================
          GENERAR PDF
          =============================================*/
        function myFunctionPDF(){

            var fechaInicial = $("#fecha_diarias").val();
            var fechaFinal = $("#fecha_diarias_Fin").val();
            var dateString = moment(fechaInicial).format('M/D/Y');
            fechaInicial=dateString;
            var dateString = moment(fechaFinal).format('M/D/Y');
            fechaFinal=dateString;
            
            window.open("extensiones/tcpdf/pdf/diarios.php?fecha="+fechaInicial+'&fechafin='+fechaFinal, "_blank");
            
        }
       

       

    </script>








