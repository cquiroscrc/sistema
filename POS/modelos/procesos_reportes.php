
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
         CARGAR FECHA
          =============================================*/
        function cargarfecha() {
            var fechaInicial = $("#fecha_inicio_reporte").val();
            var dateString = moment(fechaInicial).format('M/D/Y');
            $("#f_inicio").text(dateString);

            var fechaFinal = $("#fecha_fin_reporte").val();
            var dateString = moment(fechaFinal).format('M/D/Y');
            $("#f_fin").text(dateString);
        }
        /*=============================================
         FUNCIONES DE TIPO
          =============================================*/
        function repventas() {
            $("#tipo_rep").text("VENTAS");
        }
        function repcompras() {
            $("#tipo_rep").text("COMPRAS");
        }
        function repcontable() {
            $("#tipo_rep").text("CONTABLE");
        }
        function repfinanciero() {
            $("#tipo_rep").text("FINANCIERO");
            $("#modalFinanciero").modal();
        }
        function repbancos() {
            $("#tipo_rep").text("BANCOS");
        }
        function repegresos() {
            $("#tipo_rep").text("EGRESOS");
        }
        function repd151() {
            $("#tipo_rep").text("D151");
        }
        function repCabys() {
            $("#tipo_rep").text("Cabys");
        }
        function conteo() {
            $("#tipo_rep").text("Conteo");
        }
        function garantia() {
            $("#tipo_rep").text("Garantias");
        }
        function vendido() {
            $("#tipo_rep").text("Vendido");
        }
        function woocommerce() {
            $("#tipo_rep").text("Woocommerce");
        }
        //FIN TIPO REPORTES
        /*=============================================
         EXCEL
          =============================================*/
        function rep_excel() {
            var Rtipo= $("#tipo_rep").text();
            var Rini= $("#f_inicio").text();
            var Rfin= $("#f_fin").text();

            if (Rtipo=='' || Rini=='' || Rfin=='' ) {
                swal({
                    type: "error",
                    title: "Las fechas o el tipo de reporte no pueden estar vacios",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result){

                });

            }else{
                if (Rtipo=='BANCOS'){
                  
                    window.open("vistas/modulos/reportes/reporte_bancos.php?fini="+Rini+"&ffin="+Rfin+"&tipor="+Rtipo);
                    
                }else{
                    if (Rtipo=='ConteoE' || Rtipo=='ConteoS' || Rtipo=='Garantias'|| Rtipo=='VendidoE'|| Rtipo=='VendidoS' || Rtipo=='Woocommerce' ){
                        swal({
                            type: "error",
                            title: "Este reporte no tiene version en Excel",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){

                        });

                    }else{
                        
                        window.open("vistas/modulos/reportes/reporte_excel.php?fini="+Rini+"&ffin="+Rfin+"&tipor="+Rtipo);
                    }
                }

            }
        }
        /*=============================================
         EXCEL
          =============================================*/
        /*=============================================
         PDF
          =============================================*/
        function rep_pdf() {
            var Rtipo = $("#tipo_rep").text();
            var Rini = $("#f_inicio").text();
            var Rfin = $("#f_fin").text();

            if (Rtipo == '' || Rini == '' || Rfin == '') {
                swal({
                    type: "error",
                    title: "Las fechas o el tipo de reporte no pueden estar vacios",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function (result) {

                });

            } else {
                if (Rtipo == 'CONTABLE') {
                    swal({
                        type: "error",
                        title: "El reporte Contable no tiene version en PDF",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function (result) {

                    });
                }
                if (Rtipo == 'FINANCIERO') {
                    swal({
                        type: "error",
                        title: "El reporte Fianciero no tiene version en PDF",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function (result) {

                    });
                }
                if (Rtipo == 'BANCOS') {
                    swal({
                        type: "error",
                        title: "El reporte de Bancos no tiene version en PDF",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function (result) {

                    });
                }
                if (Rtipo == 'EGRESOS') {
                    swal({
                        type: "error",
                        title: "El reporte de Egresos no tiene version en PDF",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function (result) {

                    });
                }
                if (Rtipo == 'D151') {
                    swal({
                        type: "error",
                        title: "El reporte de D-151 no tiene version en PDF",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function (result) {

                    });
                }
                if (Rtipo == 'Cabys') {
                    window.open("vistas/modulos/reportes/reporte_pdf.php?tipor=" + Rtipo);
                }
                if (Rtipo == 'VENTAS') {
                    window.open("vistas/modulos/reportes/reporte_pdf.php?fini=" + Rini + "&ffin=" + Rfin + "&tipor=" + Rtipo);
                }
                if (Rtipo == 'COMPRAS') {
                    window.open("vistas/modulos/reportes/reporte_pdf.php?fini=" + Rini + "&ffin=" + Rfin + "&tipor=" + Rtipo);
                }
                if (Rtipo == 'Conteo' ||  Rtipo == 'Garantias' || Rtipo == 'Vendido'|| Rtipo == 'Woocommerce') {
                    window.open("vistas/modulos/reportes/reporte_pdf.php?fini=" + Rini + "&ffin=" + Rfin + "&tipor=" + Rtipo);
                }
            }
        }
        /*=============================================
         PDF
          =============================================*/
        function GuardarFinanciero() {
            swal({
                title: '¿Está seguro de agregar Cierre Financiero',
                text: "¡Si no lo está puede cancelar la acción!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, hacer cierre!'
            }).then(function(result){
                if (result.value) {
                    var Rtipo= $("#tipo_rep").text();
                    var Rini= $("#f_inicio").text();
                    var Rfin= $("#f_fin").text();
                    var Costo= $("#nuevoCosto").val();
                    var Agua= $("#nuevoAgua").val();
                    var Luz= $("#nuevoLuz").val();
                    var Tel= $("#nuevoTel").val();
                    var CCSS= $("#nuevoCCSS").val();
                    var Tri= $("#nuevoTRI").val();
                    var TC= $("#nuevoTC").val();
                    var param = {
                        Date1: Rini,
                        Date2: Rfin,
                        costof:Costo,
                        agua:Agua,
                        luz:Luz,
                        telefono:Tel,
                        ccss:CCSS,
                        tributacion:Tri,
                        tc:TC
                    };

                    $.ajax({
                        data: param,
                        url: "vistas/modulos/reportes/reporte_financiero.php",
                        method: "post",
                        success: function(data) {
                            console.log(data);
                            rep_excel();
                            $('#modalFinanciero').modal('hide');
                            $("#nuevoCosto").val('');
                            $("#nuevoAgua").val('');
                            $("#nuevoLuz").val('');
                            $("#nuevoTel").val('');
                            $("#nuevoCCSS").val('');
                            $("#nuevoTRI").val('');
                            $("#nuevoTC").val('');

                        }
                    });

                }
            });

        }
        /*=============================================
        ENTER  FINANCIERO
        =============================================*/
        function uniKeyCodeCF(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevoAgua").focus();
            }
        }
        function uniKeyCodeAG(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevoLuz").focus();
            }
        }
        function uniKeyCodeLZ(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevoTel").focus();
            }
        }
        function uniKeyCodeTL(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevoCCSS").focus();
            }
        }
        function uniKeyCodeCCSS(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevoTRI").focus();
            }
        }
        function uniKeyCodeTRI(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevoTC").focus();
            }
        }
        // < FIN PARTE DE CODIGO ****************


    </script>








