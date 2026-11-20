
<?php 
?>
<script>

        /*=============================================
          CARGAR FECHA CON DATOS
          =============================================*/

        function Cargarbanco() {
             
            var cbanco = $("#seleccionarbacos").val();

            $('#listado_bancos').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/bancos/bancos_listado.php?cuenta='+cbanco)
                .done(function(response){
                    $('#listado_bancos').html(response);
                });


        }


        /*=============================================
          AGREGAR DEPOSITO
          =============================================*/
        function AgregarBanco() {
          $('#modalAgregarDeposito').modal();
        }
        /*=============================================
          BUSCAR MOVIMIENTO
          =============================================*/
        function BuscarMovimiento() {
            $('#ModalBuscar').modal();
        }
        /*=============================================
          MODIFICAR CUENTA
          =============================================*/
        function ModificarCuenta() {
            var cbanco = $("#seleccionarbacos").val();
            console.log(cbanco);

            $.ajax('vistas/modulos/bancos/bancos_cuentasB.php?cuenta='+cbanco)
                .done(function(response){

                    var resultadoajax = JSON.parse(response);
                     $("#editar_banco").val(resultadoajax[0]["Banco"].trim());
                     $("#editar_cuenta").val(resultadoajax[0]["Cuenta"].trim());
                     $("#editar_descripcion").val(resultadoajax[0]["Descripcion"].trim());
                     $("#editar_moneda").val(resultadoajax[0]["Moneda"].trim());
                     $('#modalModCuenta').modal();
                });
        }
        /*=============================================
          AGREGAR CUENTA
          =============================================*/
        function AgregarCuenta() {
            $('#modalAddCuenta').modal();
        }
        /*=============================================
          ACTUALILZAR CUENTA BANCARIA
          =============================================*/
        function ActualizarCuentaB(){
            var banco = $("#editar_banco").val();
            var cuenta = $("#editar_cuenta").val();
            var descripcion = $("#editar_descripcion").val();
            var moneda = $("#editar_moneda").val();

            var param = {
                banco:banco,
                cuenta: cuenta,
                descripcion:descripcion,
                moneda:moneda
            };

            $.ajax({
                data: param,
                url: "vistas/modulos/bancos/bancos_cuentasE.php",
                method: "post",
                success: function(data) {
                    console.log(data);

                    if (data=="exito") {

                        swal({
                            type: "success",
                            title: "La cuenta bancaria ha sido actualizada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            console.log(result);
                            $("#editar_banco").val("");
                            $("#editar_cuenta").val("");
                            $("#editar_descripcion").val("");
                            $("#editar_moneda").val("");
                            $('#modalModCuenta').modal('hide');
                            window.location.replace('bancos');
                        });


                    }

                }
            });


        }
        /*=============================================
          SALVAR CUENTA BANCARIA
          =============================================*/
        function AgregarCuentaB(){
            var banco = $("#nuevo_banco").val();
            var cuenta = $("#nuevo_cuentaB").val();
            var descripcion = $("#nuevo_descripcionB").val();
            var moneda = $("#nuevo_moneda").val();

            var param = {
                banco:banco,
                cuenta: cuenta,
                descripcion:descripcion,
                moneda:moneda
            };

            $.ajax({
                data: param,
                url: "vistas/modulos/bancos/bancos_cuentas.php",
                method: "post",
                success: function(data) {
                    console.log(data);
                    if (data=="existe") {
                        swal({
                            type: "error",
                            title: "La cuenta ya existe, no se pudo agregar",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){


                        });


                    }
                    if (data=="exito") {

                        swal({
                            type: "success",
                            title: "La cuenta bancaria ha sido agregado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            $("#nuevo_banco").val("");
                            $("#nuevo_cuentaB").val("");
                            $("#nuevo_descripcionB").val("");
                            $("#nuevo_moneda").val("");
                            $('#modalAddCuenta').modal('hide');
                            window.location.replace('bancos');

                        });


                    }

                }
            });


        }
        /*=============================================
          GUARDAR DEPOSITO
          =============================================*/
        function AgregarDeposito(){
          var tipo = $("select#tipo").val();
          var FechaDep = $("#nuevo_fecha").val();
          var CuentaDep = $("select#nuevo_cuenta").val();
          var Deposito = $("#nuevo_documento").val();
          var MontoDep = $("#nuevo_monto").val();
          var descripcion = $("#nuevo_descripcion").val();
          var Cuenta2 = $("select#nuevo_cuenta2").val();
          var MontoI = $("#nuevo_montoI").val();



          var param = {
              tipo:tipo,
              Date2: FechaDep,
              cuentas:CuentaDep,
              documento:Deposito,
              monto:MontoDep,
              descripcion:descripcion,
              cuenta2:Cuenta2,
              montoi:MontoI
          };

          $.ajax({
            data: param,
            url: "vistas/modulos/bancos/bancos_agregar.php",
            method: "post",
            success: function(data) {
                console.log(data);
                if (data=="existe") {
                  swal({
                      type: "error",
                      title: "El deposito ya existe, no se pudo agregar",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       

                      });
 

                }
                if (data=="exito") {

                  swal({
                      type: "success",
                      title: "El deposito ha sido agregado correctamente",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){

                      });
                    var dropDown = document.getElementById("tipo");
                    dropDown.selectedIndex = 0;
                    $("#nuevo_fecha").val("");
                    var dropDown = document.getElementById("nuevo_cuenta");
                    dropDown.selectedIndex = 0;
                    $("#nuevo_documento").val("");
                    $("#nuevo_monto").val("");
                    $("#nuevo_montoI").val("");
                    $("#nuevo_descripcion").val("");
                    var dropDown = document.getElementById("nuevo_cuenta2");
                    dropDown.selectedIndex = 0;
                    $('#modalAgregarDeposito').modal('hide');
                    Cargarbanco();
                }
                
            }
          });


        }
        /*=============================================
          ELIMINAR DEPOSITO
          =============================================*/
        function EliminaBancos(documento,fecha,cuenta){
            
            swal({
                  title: '¿Está seguro de borrar el deposito?',
                  text: "¡Si no lo está puede cancelar la acción!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Si, borrar deposito!'
                  }).then(function(result){
                    if (result.value) {

                        var param = {
                                    Date2: fecha,
                                    cuenta:cuenta,
                                    deposito:documento
                                  };

                      $.ajax({
                        data: param,
                        url: "vistas/modulos/caja/eliminar_depositos.php",
                        method: "post",
                        success: function(data) {
                          
                              console.log(data);
                              swal({
                                  type: "success",
                                  title: "El deposito ha sido eliminado correctamente",
                                  showConfirmButton: true,
                                  confirmButtonText: "Cerrar"
                                  }).then(function(result){
                                    Cargarbanco();
                                  });
                        } 
                      }); 

                    }
                });
       
              }
        /*=============================================
          HACER BUSQUEDA
          =============================================*/
        function HacerBusqueda(){
            var busqueda = $("#busqueda").val();
            var tipo = $("#tbusqueda").val();
            $("#busqueda").val("");
            $('#ModalBuscar').modal('hide');
            $("#modalBusqueda").modal();
            $('#resultadoM').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $.ajax('vistas/modulos/bancos/bancos_buscar.php?busqueda='+busqueda+'&tipo='+tipo)
                .done(function(response){
                    $('#resultadoM').html(response);
                });
        }

        /*=============================================
                  ENTER  DEPOSITO
          =============================================*/

        function uniKeyCodeT(event) {
          var key = event.keyCode;
          if (key==13) {
              $("#nuevo_fecha").focus();
          }
        }
        function uniKeyCodeF(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#nuevo_cuenta").focus();
          }
        }
        function uniKeyCodeC(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#nuevo_documento").focus();
          }
        }
        function uniKeyCodeD(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#nuevo_monto").focus();
          }
        }
        function uniKeyCodeM(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#nuevo_descripcion").focus();
          }
        }
        function uniKeyCodeDE(event) {
          var key = event.keyCode;
          if (key==13) {
            $("#nuevo_cuenta2").focus();
          }
        }
        function uniKeyCodeADDN(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevo_cuentaB").focus();
            }
        }
        function uniKeyCodeADDC(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevo_descripcionB").focus();
            }
        }
        function uniKeyCodeADDD(event) {
            var key = event.keyCode;
            if (key==13) {
                $("#nuevo_moneda").focus();
            }
        }

</script>








