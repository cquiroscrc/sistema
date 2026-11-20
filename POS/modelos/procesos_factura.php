
<?php 
?>
<script>

		/*=============================================
          NUMBER FORMAT (convierte numero a moneda)
          =============================================*/
        var IVA=0;
        var PERMISO=2; 
        var PRUEBAS=false;
        var ValDesc =0;
        var EntroOrden=false;
        var EXONERADO=false;
        var NUMERODOCUMENTOEXO="";
        var IDENTIFICACIONEXO="";
        var FECHAEMISIONEXO="";
        var FECHAVENCIMIENTOEXO="";
        var DOCUMENTOCODIGOEXO="";
        var DOCUMENTODESCRIPCIONEXO="";
        var NOMBREINSTITUCIONEXO="";
        var EXOPORCENTAJE=0;
        var EXOMONTO=0;
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
          CONSULTA EL IVA Y SI HAY PERMISO DE DESCUENTO ESPECIAL
          =============================================*/
        function ConsultaIVA(){
          var url ="ajax/consulta_impuesto.ajax.php";
          var data='';
          
          myAjaxHH(url,data)
              .done( function(data) {
                IVA=money_format(data);
                IVA=IVA/100;
              });
          var url ="ajax/consulta_descuento.ajax.php";
          var data='';
          
          myAjaxHH(url,data)
              .done( function(data) {
                PERMISO=money_format(data);
              });

          // 2 = NO HAY PERMISO  // 1 = SI HAY PERMISO
        }

        // BOTONES >**********************
          
        /*=============================================
        MY BORRAR LIMPIAR PREFACTURA
        =============================================*/
        function myBorrar(){
            var fac_act = $('#invoice-id').text();    
            url="ajax/consulta_borrar.ajax.php?codigo="+fac_act;
            data="";
              //data=fac_act;
            myAjaxHH(url,data)
            	.done( function(data) {
                	                  
              	});
            setTimeout(
            	 function() 
               	{
                	swal({
                  	type: "success",
                  	title: "La Prefactura ha sido borrado correctamente",
                  	showConfirmButton: true,
                  	confirmButtonText: "Cerrar"
                  	}).then(function(result){
                    	if (result.value) {
                    	}
                	}); 
                 	window.location.replace('factura');
                }, 1000); 
        }
        
        /*=============================================
        MYDEVOLUCION
        =============================================*/
        function myDevolucion(){

            swal({
                title:"Devolucion de Productos",
                text: "Digite el numero de factura",
                input: 'text',
                type: "question",
                
              }).then(resultado => {
                  if (resultado.value) {
                      let factura = resultado.value;
                      //console.log(factura)
                      $("#modalDevolucion").modal()
                       $('#resultadoDev').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
                      $.ajax('vistas/modulos/consulta_devolucion.php?codigo='+factura)
                      .done(function(response){
                        //console.log(response);
                        $('#resultadoDev').html(response);
                      });
              }
            });
            
        }

        /*=============================================
          SELECCIONAR DEVOLUCION
          =============================================*/
        function sel_dev(codigo){
          //AGREGA CODIGOS A DEVOLUCION
              console.log(codigo);
              dev_codigo= $(this).attr("value");
              console.log(dev_codigo);
           
        }
        /*=============================================
          DEVOLUCION APLICAR
          =============================================*/
        function DevolucionAplicar(){
          
              consecutivo=$('#consecutivoFac').text();
              //console.log(consecutivo);
              $('#modalDevolucion').modal('hide');
              window.open("vistas/modulos/devolucion_aplicar.php?codigo="+consecutivo, "_blank");
              
        }

        /*=============================================
          CONSULTAR ENCOMIENDA
          =============================================*/
        function myEncomienda(){
              
            $("#modalBuscarEncomiendas").modal()
             $('#resultadoE').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_encomiendas.php')
            .done(function(response){
              //console.log(response);
              $('#resultadoE').html(response);
            });
        }

        /*=============================================
          CONSULTAR TELEFONOS
          =============================================*/
        function myTelefonos(){
              
            $("#modalBuscarTelefonos").modal()
             $('#resultadot').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/consulta_telefonos.php')
            .done(function(response){
              //console.log(response);
              $('#resultadot').html(response);
            });
        }
        /*=============================================
          AGREGAR TELEFONOS
          =============================================*/
        function InsertarTelefono(){
          tdescripcion = $('#tdescripcion').val();
          ttelefono = $('#ttelefono').val();
          tcorreo = $('#tcorreo').val();
          var param = {
                      Numero: ttelefono,
                      Descripcion: tdescripcion,
                      Correo: tcorreo
                  };

              url="ajax/insertar_telefonos.ajax.php";
              data=param;
              //console.log(data);
              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
                swal({
                  type: "success",
                  title: "El telefono ha sido agregado correctamente",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar"
                  }).then(function(result){
                      if (result.value) {
                        $('#modalBuscarTelefonos').modal('hide');
                      }
                  });
                   
              });  
        }

          	
      /*=============================================
          ORDEN DE COMPRA
          =============================================*/
      function OrdendeCompra(){
            EntroOrden=false;
            $('#modalOrdendeCompra').modal('show');
            $('#listOC').empty();
            $('#nombreEmpresa').val("");
            var consecutivo="orden-" + Math.floor(Math.random() * 99);
            $('#consecutivoOC').text(consecutivo);
                      
      }
      /*=============================================
          INSERTAR A ORDEN
          =============================================*/
      function InsertaraOrden(){


            var descripcionOrden =$('#descripcionO').val();
            var cantidadOrden =$('#cantidadO').val();
            var empresaOrden =$('#nombreEmpresa').val();
            var consecutivoOrden =$('#consecutivoOC').text();
            
            if (nombreEmpresa=='' || descripcionOrden=='' || cantidadOrden=='' ) {
                swal({
                  title: "Error en la orden de compra",
                  text: "¡Hay campos vacios!",
                  type: "error",
                  confirmButtonText: "¡Cerrar!"
                });

            }else{

                var param = {
                          empresa: empresaOrden,
                          cantidad: cantidadOrden,
                          descripcion: descripcionOrden,
                          consecutivo: consecutivoOrden                                            
                      };

                url="ajax/insertar_orden.ajax.php";
                data="";
                data=param;
                
                myAjaxHH(url,data)
                .done( function(data) {
                console.log(data);  
                $('#descripcionO').val("");
                $('#cantidadO').val("");
                //console.log(dataOrden);
                EntroOrden=true;
                var html= '<tr>\
                      <td class="text-left">'+cantidadOrden+'</td>\
                      <td class="text-justify">'+descripcionOrden.toUpperCase()+'</td>\
                      </tr>';
                $('#listOC').append(html);
            
                                  
        });
       }         
      }        

      /*=============================================
          HACER ORDEN DE COMPRA Imprime
          =============================================*/   
      function hacerOrden(){
          if (EntroOrden==true) {
            var nOC = $('#consecutivoOC').text();
            var Nempresa =$('#nombreEmpresa').val()
            $('#listOC').empty();
            $('#nombreEmpresa').val("");
            $('#modalOrdendeCompra').modal('hide');
            window.open("extensiones/tcpdf/pdf/orden.php?orden="+nOC+"&empresa="+Nempresa, "_blank");
          }
      }
      /*=============================================
          HACER PROFORMA
          =============================================*/
      function hacerProforma(){
            
          if ($('#fact_subtotal').text()=='0.00') {
            swal({
                  title: "Error al facturar",
                  text: "¡No puede hacer una proforma vacia!",
                  type: "error",
                  confirmButtonText: "¡Cerrar!"
                });
          }else{
            var codcliente= $('#codigocliente').text();
            
              if (codcliente=='0') {
                swal({
                  title: "Facturacion",
                  text: "Digite el nombre para la proforma",
                  input: 'text',
                  type: "question",
                  
                }).then(resultado => {
                    if (resultado.value) {
                        let el_nombre = resultado.value;
                        var codprefactura =$('#invoice-id').text();
                        $('#modalOrdendeCompra').modal('hide');
                        window.open("extensiones/tcpdf/pdf/proforma.php?prefactura="+codprefactura+"&nombre="+el_nombre , "_blank");
                        
                    }
                }); 


              }else{
                var el_nombre = $('#nombrecliente').text();
                var codprefactura =$('#invoice-id').text();
                $('#modalOrdendeCompra').modal('hide');
                window.open("extensiones/tcpdf/pdf/proforma.php?prefactura="+codprefactura+"&nombre="+el_nombre , "_blank");
              }
            
        }
      }
      
      /*=============================================
          PREPARAR FACTURA
          =============================================*/
      function PrepararFactura(){

          if ($('#fact_subtotal').text()=='0.00') {
            swal({
                  title: "Error al facturar",
                  text: "¡No puede hacer una factura vacia!",
                  type: "error",
                  confirmButtonText: "¡Cerrar!"
                });
          }else{

            $('#nombreCliente').val($('#nombrecliente').text())
            if ($('#nombrecliente').text()=='CLIENTE DE CONTADO') {
              $("#nombreCliente").removeAttr("readonly");
            }
            if (EXONERADO==true) {
              divExo.style.display='block';  
              divExo2.style.display='block'; 
              var subexo=money_format($('#fact_subtotal').text());
              EXOMONTO= subexo*EXOPORCENTAJE; 
              var EXOMONTO2=number_format(EXOMONTO,2,'.',',')
              $('#FacturaExoneracion').val(EXOMONTO2);
              var parcialTotal=money_format($('#fact_total').text());
              parcialTotal=parcialTotal-EXOMONTO;
              parcialTotal=number_format(parcialTotal,2,'.',',')
              $('#Facturatotal').val(parcialTotal);
              $('#InstitucionExoneracion').val(NOMBREINSTITUCIONEXO);
            }else{
              divExo.style.display='none';   
              divExo2.style.display='none';   
              $('#Facturatotal').val($('#fact_total').text());
            }
            

            $('#Facturasubtotal').val($('#fact_subtotal').text());
            $('#Facturadescuento').val("0.00");
            $('#Facturaimpuesto').val($('#fact_impuesto').text());
            $('#modalFacturar').modal('show');

          }
      }

       /*=============================================
          MY TECNICA
          =============================================*/
      function myTecnica(codigo){
            var param = {
                      codigo:codigo
                  };

              url="ajax/consulta_productos.ajax.php";
              data=param;
            
              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
                var resultadoajax = JSON.parse(data);
                 var cod_tecnica=resultadoajax.data[0][0]["CODIGO"];
                 var des_tecnica=resultadoajax.data[0][0]["DESCRIPCION"];
                 var tecnica_tecnica =resultadoajax.data[0][0]["DESC_ALT"];
                 var id_tecnica = resultadoajax.data[0][0]["Carro"];
                 $("#id_tecnica").text(id_tecnica);
                 $("#codigo_tecnica").text(cod_tecnica);
                 $("#descripcion_tecnica").text(des_tecnica);
                 $("#text_tecnica").text(tecnica_tecnica);
                 $('#modalBuscarCodigo').modal('hide');
                 $('#modalTecnica').modal();
                
              });

      }

      /*=============================================
          GUARDAR TECNICA
          =============================================*/
      function GuardarTecnica(){
            var codigo_tec=$("#codigo_tecnica").text();
            var text_tecnica =$("#text_tecnica").val();
            text_tecnica = text_tecnica.toUpperCase();
            var usuario =$("#usuario_id").text();
            var param = {
                      codigo:codigo_tec,
                      tecnica: text_tecnica,
                      usuario:usuario
                  };

              url="ajax/tecnica_productos.ajax.php";
              data=param;
            
              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
                  if (data=="exito") {
                    swal({
                    type: "success",
                    title: "La descripcion Técnica ha sido actualizada correctamente",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                          $('#modalTecnica').modal('hide');
                        }
                    });  
                  }
                
              });
      }


      /*=============================================
          RECALCULA DESCUENTO FACTURA DOBLE CLICK
          =============================================*/
      function FacRecalcularOriginal(){ // Doble click
        var valdesc=money_format($('#Facturadescuento').val());
        var sub = $('#fact_subtotal').text();
        sub= sub.replace(",","");
        console.log (sub);
        sub = money_format(sub);
        console.log (sub);
        var codclien = $('#codigocliente').text();
        var cantdesc=ValDesc/100;
        var valdesc2=sub*cantdesc;
        var descRecal=0;         
        descRecal=sub*cantdesc;
        var impRacal=(sub-descRecal)*IVA;
        if (EXONERADO==true) {
            EXOMONTO=(sub-descRecal)*EXOPORCENTAJE;
            totRecal=sub-descRecal+impRacal-EXOMONTO;
            var EXOMONTO2=number_format(EXOMONTO,2,'.',',')
             $('#FacturaExoneracion').val(EXOMONTO2);
          }else{
            var totRecal=sub-descRecal+impRacal;
        }
       
        $('#Facturadescuento').val(number_format(descRecal,2,'.',','));
        $('#Facturaimpuesto').val(number_format(impRacal,2,'.',','));
        $('#Facturatotal').val(number_format(totRecal,2,'.',','));
      } // Doble Click
      /*=============================================
          RECALCULA DESCUENTO FACTURA ENTER y TEXTO
          =============================================*/
      function FacRecalcular(){
        var valdesc=money_format($('#Facturadescuento').val());
        // Debe ser valor numerico
        if( isNaN(valdesc) ) {
          swal({
                  title: "Error al facturar",
                  text: "¡El descuento debe ser numeros!",
                  type: "error",
                  confirmButtonText: "¡Cerrar!"
                });
        }else{  // Si es numerico
          var sub = $('#fact_subtotal').text();
          sub = money_format(sub);
          
          var cantdesc=ValDesc/100;
          var valdesc2=sub*cantdesc;
          valdesc2=valdesc2.toFixed(2)
          var descRecal=0;  
          var errordesc="no";
          

            if (PERMISO==1) {
              descRecal=valdesc;
              errordesc="no";  
            }
            
            if (PERMISO==2) {
                if (valdesc>valdesc2){
                  errordesc="si";   
                }else{
                  errordesc="no";
                  descRecal=valdesc;
                }
              
            }
          
            if (descRecal>=sub) { 
              errordesc="si";
            }

            if (errordesc=="si") {
              swal({
                      title: "Error al facturar",
                      text: "¡El descuento NO debe ser mayor al permitido!",
                      type: "error",
                      confirmButtonText: "¡Cerrar!"
                    });
            descRecal=0;  
            }


          }// Comprovacion numerio
          var impRacal=(sub-descRecal)*IVA;
          
          if (EXONERADO==true) {
            EXOMONTO=(sub-descRecal)*EXOPORCENTAJE;
            totRecal=sub-descRecal+impRacal-EXOMONTO;
            var EXOMONTO2=number_format(EXOMONTO,2,'.',',')
             $('#FacturaExoneracion').val(EXOMONTO2);
          }else{
            var totRecal=sub-descRecal+impRacal;  
          }      
          
          
         
          $('#Facturadescuento').val(number_format(descRecal,2,'.',','));
          $('#Facturaimpuesto').val(number_format(impRacal,2,'.',','));
          $('#Facturatotal').val(number_format(totRecal,2,'.',','));

      }  
      /*=============================================
          IMPRIMIR FACTURA
          =============================================*/ 
       function imprimirFacturaEPC(){
          if ($(codigovendedor).text()=='000') {
            swal({
                  title: "Error al facturar",
                  text: "¡No puede facturar con vendedor vacio!",
                  type: "error",
                  confirmButtonText: "¡Cerrar!"
                });

          }else{
              
              if (EXONERADO==true) {
                  if ($("#InstitucionExoneracion").val()=='') {
                    swal({
                      title: "Error al facturar",
                      text: "¡No puede facturar con la institucion vacia!",
                      type: "error",
                      confirmButtonText: "¡Cerrar!"
                    });
                    return;
                  }else{
                    NOMBREINSTITUCIONEXO=$("#InstitucionExoneracion").val();
                  }
              }
              
              var nfac =$('#invoice-id').text();
              var subtotal = $('#fact_subtotal').text();
              subtotal= subtotal.replace(",","");
              subtotal = money_format(subtotal);
              var impuesto = $('#Facturaimpuesto').val();
              impuesto = money_format(impuesto);
              var descuento = $('#Facturadescuento').val();
              descuento = money_format(descuento);
              var total = $('#Facturatotal').val();
              total= total.replace(",","");
              total = money_format(total);
              var proveedor = $('#codigocliente').text();
              var vendedor = $('#codigovendedor').text();
              var Nombre = $('#nombreCliente').val();
              Nombre = Nombre.toUpperCase();
              var Adicional = $('#nombreAdicional').val();
              Adicional = Adicional.toUpperCase();
              var medio_pago = $('#t_pago').val();
              var param = {
                      nfac: nfac,
                      subtotal: subtotal,
                      impuesto: impuesto,
                      descuento: descuento,                                            
                      total: total,
                      proveedor: proveedor,
                      vendedor: vendedor,
                      Nombre: Nombre,
                      Adicional: Adicional,
                      Exoneracion: EXONERADO,
                      numerodocumento:NUMERODOCUMENTOEXO,
                      identificacion: IDENTIFICACIONEXO,
                      fechaemision:FECHAEMISIONEXO,
                      fechavencimiento:FECHAVENCIMIENTOEXO,
                      documentocodigo:DOCUMENTOCODIGOEXO,
                      documentodescripcion:DOCUMENTODESCRIPCIONEXO,
                      nombreinstitucion:NOMBREINSTITUCIONEXO,
                      porcentaje:EXOPORCENTAJE,
                      montoexoneracion:EXOMONTO,
                      t_pago:medio_pago

                  };

              url="ajax/insertar_prefactura.ajax.php";
              data="";
              data=param;
            
              myAjaxHH(url,data)
              .done( function(data) {
                console.log('Insertar ='+data);
              
                window.open("vistas/modulos/factura_aplicar.php?codigo="+nfac, "_blank");
                
                window.location.replace('factura');
                sleep(2);
              });

             $('#modalFacturar').modal('hide');   
            
          }
     }   
         // <FIN BOTONES ****************************
        

      /*=============================================
          MY PREFACTURA (Recarga si hay Prefacturas existentes)
          =============================================*/
      function myPrefactura(codigo){
          $('#modalPrefactura').modal('hide');
          $('#invoice-id').text(codigo);
          myRecargar(codigo);          
      }



      /*=============================================
          RECARGAR TODOS LOS DATOS * (SE UTILIZA PARA TODO)
          =============================================*/
      function myRecargar(prefactura){
          url="ajax/consulta_recargar.ajax.php?codigo="+prefactura;
	        data="";
	        data=prefactura;
	        myAjaxHH(url,data)
	        .done( function(datos) {
		        var resultadoajax = JSON.parse(datos);
		        
		        // Limpia lista y totales
		        subtotalact=0; descuentoact=0; impuestoact=0; totalact=0;

		        // Limpia la tabla
		        $('#productos_list').empty();

		        $('#fact_subtotal').text(subtotalact.toFixed(2));
		        $('#fact_descuento').text(descuentoact.toFixed(2));
		        $('#fact_impuesto').text(impuestoact.toFixed(2));
		        $('#fact_total').text(totalact.toFixed(2));

		        var codcliente=resultadoajax.data[0][0]["codcliente"];
            	var vendedor=resultadoajax.data[0][0]["vendedor"];

	            for (var i = resultadoajax.data[0].length - 1; i >= 0; i--) {
	                var codigo = resultadoajax.data[0][i]['codigo'];
	                var descripcion = resultadoajax.data[0][i]['DESCRIPCION'];
	                var desc_tecnica = resultadoajax.data[0][i]['DESC_ALT'];
	                var cantidad = resultadoajax.data[0][i]['cantidad'];
	                var precio = resultadoajax.data[0][i]['precio'];
	                var sub = resultadoajax.data[0][i]['cantidad']*resultadoajax.data[0][i]['precio'];
	                
                  subtotalact=subtotalact+sub;
	                descuentoact = 0;
                      
	                impuestoact = (subtotalact-descuentoact)*IVA;
	                totalact = subtotalact-descuentoact+impuestoact;
	                precio=number_format(precio,2,'.',',');
	                sub=number_format(sub,2,'.',',');
	                
	               // AGREGA CODIGOS EN EL DOM
	               var html= '<tr>\
	                            <td class="no" value="'+codigo+'"><a title="Eliminar '+codigo+'" style="color:white;"><i class="ion ion-android-cancel"></a></td>\
	                            <td class="text-left"><h3>\
	                              <a data-toggle="tooltip" title="'+desc_tecnica+'">\
	                              '+descripcion+'</a>\
	                              </h3>'+codigo+'</td>\
	                            <td class="unit" value="'+codigo+'"><a title="Cambiar precio '+codigo+'">'+precio+'</td>\
	                            <td class="qty" value="'+codigo+'"><a title="Cambiar cantidad '+codigo+'">'+cantidad+'</td>\
	                            <td class="total">'+sub+'</td>\
	                        </tr>';
	                $('#agregar-producto').append(html);

	            }
	            
	            //Agrega Totales
	            subtotalact=number_format(subtotalact,2,'.',',');
	            descuentoact=number_format(descuentoact,2,'.',',');
	            impuestoact=number_format(impuestoact,2,'.',',');
	            totalact=number_format(totalact,2,'.',',');
	            $('#fact_subtotal').text(subtotalact);
	            $('#fact_descuento').text(descuentoact);
	            $('#fact_impuesto').text(impuestoact);
	            $('#fact_total').text(totalact);

	            
	            //RECARGA CLIENTE
	            url="ajax/consulta_precarga_cliente.ajax.php?codigo="+codcliente;
	            data="";
	            data=codcliente;
	            myAjaxHH(url,data)
	            .done( function(data) {
		            var resultadoajax = JSON.parse(data);
		            var nombre = resultadoajax.data[0][0]["nombre"];
		            var cedula = resultadoajax.data[0][0]["cedula"];
		            var codigocliente = resultadoajax.data[0][0]["codigo"];
		            var correo = resultadoajax.data[0][0]["correo"];
                ValDesc= resultadoajax.data[0][0]["descuento"];
	                $('#nombrecliente').text(nombre);
	                $('#cedulacliente').text(cedula);
	                $('#codigocliente').text(codigocliente);
	                $('#correocliente').text(correo);

		        });
		        
		        // RECARGA VENDEDOR
		        url="ajax/consulta_precarga_vendedores.ajax.php?codigo="+vendedor;
		        data="";
		        data=vendedor;
		        myAjaxHH(url,data)
		        .done( function(data) {
		            var resultadoajax = JSON.parse(data);
		            var nombre = resultadoajax.data[0][0]["nombre"];
		            var codigoven = resultadoajax.data[0][0]["codigo"];
		            document.getElementById("seleccionarVendedor").value = codigoven;  
		            $('#codigovendedor').text(codigoven);
		        });


	        });
	      // Para que no se cierre el DOM
          document.body.style.paddingRight="0px";
          $( "#selector" ).focus();
      }
        // PARTE DE CLIENTE >****************

        /*=============================================
          MY FUNTION CLIENTE
          =============================================*/
      function myFunctionCliente() {
          
          $("#modalBuscarCliente").modal()
          // Hace el loading
          $('#result1').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
          $.ajax('vistas/modulos/consulta_clientes.php')
          .done(function(response){
            $('#result1').html(response);
          });
      }

        /*=============================================
          MY CLIENTE
          =============================================*/
      function myCliente(cedula,nombre,correo,cliente){
              
              var z = cliente.search(">0<");
              if (z==-1){
                var n = cliente.search('success">');
                var res = cliente.substr(n+9,5);
                var s = res.search('</');
                
                if(s==3){
                  var codigocliente=res.substr(0, 3);
                  
                }else if (s==-1) {
                
                  var t = res.search('<');
                
                  if(t==4){
                
                    var codigocliente=res.substr(0, 4);  
                
                  }else {
                
                  var codigocliente=res;
                
                  }
                }
              }else{
                var codigocliente=0;
              }
              
              $('#nombrecliente').text(nombre);
              $('#cedulacliente').text(cedula);
              $('#codigocliente').text(codigocliente);
              $('#correocliente').text(correo);
              // Esconde el modal
              $('#modalBuscarCliente').modal('hide');
              var codigo =$('#invoice-id').text()
              var codigovendedor = $('#codigovendedor').text();
              var param = {
                      Factura: codigo,
                      codcliente: codigocliente,
                      vendedor: codigovendedor,
                      
                  };

              url="ajax/actualizar_cliente.ajax.php";
              data="";
              data=param;
              
              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
                ValDesc=data;
              });
              
      }

        /*=============================================
          MY FUNTION  CREAR CLIENTE
          =============================================*/
        function myAgregarCliente() {
          $("#modalBuscarAgregarCliente").modal()
        }
        /*=============================================
          VALIDAR CORREO
          =============================================*/
        function validateMail(idMail)
          {
            //Creamos un objeto 
            object=document.getElementById(idMail);
            valueForm=object.value;
           
            // Patron para el correo
            var patron=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
            if(valueForm.search(patron)==0)
            {
              //Mail correcto
              object.style.color="#000";
              return;
            }
            //Mail incorrecto
            object.style.color="#ff0000";
          }

        /*=============================================
          AGREGAR CLIENTE
          =============================================*/
        function InsertarCliente(){
          nombreCli = $('#nombreCli').val();
          cedulaCli = $('#cedulaCli').val();
          telefonoCli = $('#telefonoCli').val();
          celularCli = $('#celularCli').val();
          correoCli = $('#correoCli').val();
          tipoCli = $('#tipoCli').val();
          actividad =$('#actividadCli').val();

          var param = {
                      nombre: nombreCli,
                      cedula: cedulaCli,
                      telefono: telefonoCli,
                      celular: celularCli,
                      correo : correoCli,
                      tipo: tipoCli,
                      actividad : actividad
                  };

              url="ajax/insertar_clientes.ajax.php";
              data="";
              data=param;
              //console.log(data);
              myAjaxHH(url,data)
              .done( function(data) {
                //console.log(data);
                if (data==1) {
                  swal({
                  type: "success",
                  title: "El cliente ha sido agregado correctamente",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar"
                  }).then(function(result){
                      if (result.value) {
                         nombreCli = $('#nombreCli').val();
                        $('#cedulaCli').val("");
                        $('#telefonoCli').val("");
                        $('#celularCli').val("");
                        $('#correoCli').val("");
                        $('#tipoCli').val("");
                        $('#modalBuscarAgregarCliente').modal('hide');
                      }
                  });
                }else{
                  swal({
                    type: "warning",
                    title: data,
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                         
                        }
                    });
                }
                   
              });  
        }
        // < FIN PARTE DE CLIENTE ****************

        // PARTE DE CODIGO >****************

        /*=============================================
          MY FUNCTION CODIGO
          =============================================*/
        function myFunctionCodigo() {
          
          var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
          $("#modalBuscarCodigo").modal()
          $('#result').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
          //$.ajax('vistas/modulos/consulta_productos.php?codigo='+dataToPass.codigo)
          $.ajax('vistas/modulos/factura_productos.php?codigo='+dataToPass.codigo)
          .done(function(response){
            $('#result').html(response);
          });
        }

        /*=============================================
          ACTUALIZA PRECIO
          =============================================*/        
        function myFunctionActualizaPrecio(codigo,factura,precio) {

            var param = {

                Factura: factura,
                codigo: codigo,
                precio:precio
            };

            $.ajax({
              data: param,
                url: "ajax/actualiza_prefactura.ajax.php",
                method: "post",
                success: function(data) {
                  console.log(data);

                  if (data=='OK') {
                    swal({
                    type: "success",
                    title: "El precio ha sido cambiado correctamente",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                          myRecargar(factura);
                        }
                      })
                  }else{
                    swal({
                      type: "error",
                      title: "¡El precio no puede ser menor que al precio anterior",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                      if (result.value) {
                        myRecargar(factura);
                      }
                    })
                  }
                  
              }
            });
  
          
          
        }

        /*=============================================
          ACTUALIZA CANTIDAD
          =============================================*/        
        function myFunctionActualizaCantidad(codigo,factura,cantidad) {

            var param = {

                Factura: factura,
                codigo: codigo,
                cantidad:cantidad
            };

            $.ajax({
              data: param,
                url: "ajax/actualiza_prefactura_cant.ajax.php",
                method: "post",
                success: function(data) {
                  console.log(data);

                  if (data=='OK') {
                    swal({
                    type: "success",
                    title: "La cantidad ha sido cambiado correctamente",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                          myRecargar(factura);
                        }
                      })
                  }else{
                    swal({
                      type: "error",
                      title: "¡La cantidad no puede ser mayor que el inventario",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                      if (result.value) {
                        myRecargar(factura);
                      }
                    })
                  }
                  
              }
            });
  
          
          
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
          EXONERACIONES BUSCAR
          =============================================*/
        function BuscarExo(){
          swal({
                title: "Facturacion",
                text: "Digite el porcentaje de le exoneración (Maximo 13)",
                input: 'number',
                type: "question",
                
              }).then(resultado => {
                  if (resultado.value) {
                    EXOPORCENTAJE = resultado.value; 
                    EXOPORCENTAJE=EXOPORCENTAJE/100;

                    swal({
                  title: "Facturacion",
                  text: "Digite el numero de la exoneración",
                  input: 'text',
                  type: "info",



                }).then(resultado => {
                    if (resultado.value) {
                        let exo = resultado.value;
                        var param = {
                                documento: exo
                            };

                        url="ajax/consulta_exoneraciones.ajax.php";
                        data=param;

                        myAjaxHH(url,data)
                        .done( function(data) {
                          //console.log(data);
                          if (data=="ERROR") {
                            // tipo de exo
                              NUMERODOCUMENTOEXO=exo;
                              const { value: fruit } = swal({
                                title: 'Tipo de exoneracion',
                                input: 'select',
                                inputOptions: {
                                  1: 'Compras autorizadas',
                                  2: 'Ventas exentas a diplomáticos',
                                  3: 'Autorizado por Ley especial',
                                  4: 'Exenciones Dirección General de Hacienda',
                                  5: 'Transitorio V ',
                                  6: 'Transitorio IX',
                                  7: 'Transitorio XVII',
                                  99:'Otros '
                                },
                                inputPlaceholder: 'Seleccionar un tipo de exoneración',
                                showCancelButton: true,
                                inputValidator: (value) => {
                                    if (value==1) {
                                      var CODEXO='01';
                                      DOCUMENTODESCRIPCIONEXO='Compras autorizadas';
                                    }
                                    if (value==2) {
                                      var CODEXO='02';
                                      DOCUMENTODESCRIPCIONEXO='Ventas exentas a diplomáticos';
                                    }
                                    if (value==3) {
                                      var CODEXO='03';
                                      DOCUMENTODESCRIPCIONEXO='Autorizado por Ley especial'; 
                                    }
                                    if (value==4) {
                                      var CODEXO='04';
                                      DOCUMENTODESCRIPCIONEXO='Exenciones Dirección General de Hacienda';
                                    }
                                    if (value==5) {
                                      var CODEXO='05';
                                      DOCUMENTODESCRIPCIONEXO='Transitorio V';
                                    }
                                    if (value==6) {
                                      var CODEXO='06';
                                      DOCUMENTODESCRIPCIONEXO='Transitorio IX';
                                    }
                                    if (value==7) {
                                      var CODEXO='07';
                                      DOCUMENTODESCRIPCIONEXO='Transitorio XVII';
                                    }
                                    if (value==99) {
                                      var CODEXO='99';
                                      DOCUMENTODESCRIPCIONEXO='Otros';
                                    }

                                    EXONERADO=true;
                                    DOCUMENTOCODIGOEXO=CODEXO;
                                    IDENTIFICACIONEXO=$("#cedulacliente").text();
                                    FECHAEMISIONEXO=$('#FechaActualizada').text();
                                    FECHAVENCIMIENTOEXO=$('#FechaActualizada').text();
                                    
                                    $("#datosEXO").text('Porcentaje: '+EXOPORCENTAJE+' Numero Documento: '+NUMERODOCUMENTOEXO+' Tipo Documento: '+DOCUMENTOCODIGOEXO+' Cedula: '+IDENTIFICACIONEXO+' Nombre: '+NOMBREINSTITUCIONEXO);
                                        }
                            });



                            // fin tipo
                          }else{
                              var resultadoajax = JSON.parse(data);
                              console.log(resultadoajax);
                              EXONERADO=true;
                              NUMERODOCUMENTOEXO=resultadoajax["numeroDocumento"];
                              IDENTIFICACIONEXO=resultadoajax["identificacion"];
                              FECHAEMISIONEXO=resultadoajax["fechaEmision"];
                              FECHAVENCIMIENTOEXO=resultadoajax["fechaVencimiento"];
                              DOCUMENTOCODIGOEXO=resultadoajax["tipoDocumento"]["codigo"];
                              DOCUMENTODESCRIPCIONEXO=resultadoajax["tipoDocumento"]["descripcion"];
                              NOMBREINSTITUCIONEXO=resultadoajax["nombreInstitucion"];
                              $("#datosEXO").text('Porcentaje: '+EXOPORCENTAJE+' Numero Documento: '+NUMERODOCUMENTOEXO+' Tipo Documento: '+DOCUMENTOCODIGOEXO+' Cedula: '+IDENTIFICACIONEXO+' Nombre: '+NOMBREINSTITUCIONEXO);
                          }
                          
                      })
                            
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








