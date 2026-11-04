//Esta variable es para guardar el xmlFirmado y luedo poder descargarlo
var descargaXMLFirmado = "";
var descargaXMLClave = "";
 
/**********************************/
/*Check if is LogedIn         */
/**********************************/
function checkUserLogin() {
    Api_checkLogin(function (data) {
        console.log(data);
    },
            function (data) {
                sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                iGoTo("./user_login.html");
            });
}


function Front_debug(d) {
    console.log("[FrontEnd] >> " + d);
}

/************************************/
/*Inicio de Funciones del admin*/
/************************************/


// I will redirect somewhere
function iGoTo(goTo) {
    $("body").hide();
    window.location.href = goTo;
}
 

/*
 *This function get all your receptor by your userName and your sessionkey  
 */
function Api_getVouchers(selectID) {
    var client_id;

    var user = [];
    user.w = 'admin';
    user.r = 'company_get_env';
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idMasterUser = localStorage.getItem('idMasterUser');
    API_PostRequest(user,
            function (data) {
                if (typeof data.resp == 'object') {
                    client_id = data.resp[0].env;
                    var user = [];
                    user.w = 'admin';
                    user.r = 'get_vouchers';
                    user.iam = localStorage.getItem('userName');
                    user.sessionKey = localStorage.getItem('sessionKey');
                    user.idMasterUser = localStorage.getItem('idMasterUser');
                    user.env = client_id;
                    $("#" + selectID).empty();
                    API_PostRequest(user,
                            function (data) {
                                if (typeof data.resp == 'object') {
                                    for (i = 0; i < data.resp.length; i++) {
                                        $("#" + selectID).append('<tr><td>' + data.resp[i].consecutivo + '</td><td>' + data.resp[i].clave + '</td><td>' + data.resp[i].tipoDocumento + '</td><td>' + data.resp[i].fechaCreacion + '</td><td><button value="' + data.resp[i].idComprobante + '" onclick="addRow($(this).val());"class="btn btn-warning btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">edit</i></button><button value="' + data.resp[i].idComprobante + '" onclick="" class="btn btn-danger btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">delete</i></button></td></tr>');
                                    }
                                } else {
                                    console.log("Error recuperando el ambiente");
                                }
                                $('#data-table').dataTable();
                            },
                            function (data) {
                                sweetAlert({title: "Ocurrio un error cargando los tiquetes", type: "error"});
                            });


                } else {
                    console.log("Error cambiando el ambiente de facturacion de hacienda");
                }
            },
            function (data) {
                sweetAlert({title: "Ocurrio un error cargando el ambiente de facturacion de hacienda", type: "error"});
            });


}

function Api_get_inventory(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'get_inventory';
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idMasterUser = localStorage.getItem("idMasterUser");
    user.sucursal = localStorage.getItem("sucursal");
    $("#" + selectID).empty();
    API_PostRequest(user,
            function (data) {
                if (typeof data.resp == 'object') {
                    for (i = 0; i < data.resp.length; i++) {
                        $("#" + selectID).append('<tr><td>' + data.resp[i].codigoBarras + '</td><td>' + data.resp[i].nombre + '</td><td>' + data.resp[i].unidadMedida + '</td><td>' + data.resp[i].precioVenta + '</td><td>' + data.resp[i].disponible + '</td><td><button value="' + data.resp[i].idProducto + '" onclick="" class="btn btn-warning btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">edit</i></button><button value="' + data.resp[i].idReceptor + '" onclick="" class="btn btn-danger btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">delete</i></button></td></tr>');
                    }
                } else {
                    console.log("Error recuperando los clientes");
                }
                $('#data-table').dataTable();
            },
            function (data) {
                sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
            });
}

function Api_deleteReciver(idReceptor) {
    swal({
        title: "Desea eliminar el cliente?",
        text: "Si elimina el cliente no lo podra recuperar nunca mas!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Si, estoy seguro!',
        cancelButtonText: "No, Cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function (isConfirm) {

        if (isConfirm) {
            deleteClient(idReceptor);
        } else {
            swal("Cancelled", "Your imaginary file is safe :)", "error");
        }
    });
}


function deleteClient(idReceptor) {
    var user = [];
    user.w = 'admin';
    user.r = 'delete_reciver';
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idReceptor = idReceptor;
    API_PostRequest(user,
            function (data) {
                if (data.resp == '1') {
                    sweetAlert({title: "Cliente Eliminado", type: "success"});
                    Api_getAllreceptor("tableBody");
                } else {
                    console.log("Error eliminando el cliente");
                }
            },
            function (data) {
                sweetAlert({title: "Ocurrio un error eliminando el cliente", type: "error"});
            });
}







function rowSetValue(row, value) {
    var idRow = ($(row).closest('tr')[0].rowIndex);
    $('#tabla tr:eq(' + idRow + ') td :input:enabled:visible:first').val(value);
}


function Api_getProduct(row) {
    var user = [];
    user.w = 'admin';
    user.r = 'getProductByCode';
    user.codigo = $("#code" + row).val();
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idMasterUser = localStorage.getItem('idMasterUser');
    user.sucursal = localStorage.getItem('sucursal');
    API_PostRequest(user,
            function (data) {
                if (typeof data.resp == 'object') {
                    for (i = 0; i < data.resp.length; i++) {
                        $("#unidad" + row).val(data.resp[i].unidadMedida);
                        $("#precio" + row).val(parseInt(data.resp[i].precioVenta));
                        $("#iv" + row).val(data.resp[i].cantidadImpuesto);
                        $("#idIV" + row).val(data.resp[i].codigo);
                        $("#code" + row).val(data.resp[i].descripcion);
                        Api_formUpdate();

                    }
                } else {
                    console.log("Error recuperando los clientes");
                }
            },
            function (data) {
                sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
            });

}





function Api_add_voucher(clave, consecutivo, estado, xmlEnviadoBase64, tipoDocumento, respuestaMHBase64, idReceptor, env) {

    var user = [];
    user.w = 'admin';
    user.r = 'companny_add_voucher';
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idMasterUser = localStorage.getItem('idMasterUser');
    user.clave = clave;
    user.consecutivo = consecutivo;
    user.consecutivo = consecutivo;
    user.estado = estado;
    user.xmlEnviadoBase64 = xmlEnviadoBase64;
    user.tipoDocumento = tipoDocumento;
    user.respuestaMHBase64 = respuestaMHBase64;
    user.idReceptor = idReceptor;
    user.env = env;
    API_PostRequest(user,
            function (data) {
                if (data.resp == '1') {
                    console.log("Se guardo el voucher");
                } else {
                    console.log("Error guardando voucher");
                }
            },
            function (data) {
                sweetAlert({title: "Ocurrio un error guardando el voucher", type: "error"});
            });
}







function padDigits(number, digits) {
    return Array(Math.max(digits - String(number).length + 1, 0)).join(0) + number;
}




function Api_registro_call() {

    var userData = [];
    userData.fullName = $("#name").val() + " " + $("#lastName").val();
    userData.userName = $("#userName").val();
    userData.userEmail = $("#email").val();
    userData.about = $("#companyName").val();
    userData.pwd = $("#password").val();
    userData.userCountry = $("#country").val();
    Api_registerUser(userData,
            function (data) {
                sweetAlert({title: "Bienvenido!", text: "Bienvenido al sistema\n" + userData.userName, type: "success"});
                iGoTo("general.html");
            },
            function (data) {
                sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
            });
}

function Api_companny_registro_call() {

    var userData = [];
    userData.fullName = $("#name").val() + " " + $("#lastName").val();
    userData.userName = $("#userName").val();
    userData.userEmail = $("#email").val();
    userData.about = "";
    userData.pwd = $("#password").val();
    userData.userCountry = $("#country").val();
    userData.idMasterUser = localStorage.getItem('idMasterUser');
    userData.settings = $("#idTerminal").val();
    Api_companny_registerUser(userData,
            function (data) {
                var userData = [];
                var user = [];
                user.w = 'admin';
                user.r = 'companny_add_master_Consecutive';
                user.iam = localStorage.getItem('userName');
                user.sessionKey = localStorage.getItem('sessionKey');
                user.idMasterUser = localStorage.getItem('idMasterUser');
                user.idUser = data.resp.idUser;
                API_PostRequest(user,
                        function (data) {
                            if (data.resp == '14') {
                                sweetAlert({title: "Reistro exitoso!", text: "Registro exitoso", type: "success"});
                            } else {
                                console.log("Error registrando consecutivos del usuario");
                            }
                        },
                        function (data) {
                            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                        });
            },
            function (data) {
                sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
            });
}











function addRow(inIdRow) {
    var idRow = inIdRow;
    if (!idRow) {
        idRow = 0;
    }
    $("#tabla").append('<tr id="' + idRow + '">' +
            '          <td class="align-middle" onfocusout="Api_formUpdate()">' +
            '              <input id="code' + idRow + '" autofocus onkeypress="feTableEnter(this);" onchange="Api_formUpdate();" type="text" class="form-control" placeholder="Camara">' +
            '          </td>' +
            '          <td class="align-middle"><input id="unidad' + idRow + '" onfocusout="Api_formUpdate()" onchange="Api_formUpdate();" type="text" class="form-control"  placeholder=""></td>' +
            '          <td class="align-middle"><input value="1" id"cantidad' + idRow + '" onfocusout="Api_formUpdate()" value="0" onchange="Api_formUpdate();" type="number" class="form-control"  placeholder=""></td>' +
            '          <td class="align-middle"><input id="precio' + idRow + '" onfocusout="Api_formUpdate()" onchange="Api_formUpdate();" type="text" class="form-control"  placeholder=""></td>' +
            '          <td class="align-middle"><input id"descuento' + idRow + '" onfocusout="Api_formUpdate()" value="0" onchange="Api_formUpdate()" type="number" class="form-control"  placeholder=""></td>' +
            '          <td class="align-middle"><input readonly value="0" id="iv' + idRow + '" onfocusout="Api_formUpdate()" onchange="Api_formUpdate();" type="text" class="form-control"  placeholder=""><input hiden value="0" id="idIV' + idRow + '" type="hidden" class="form-control"  placeholder=""></td>' +
            '          <td class="align-middle"><input id"subtotal' + idRow + '" onfocusout="Api_formUpdate()" value="0" readonly onchange="Api_formUpdate()" type="number" class="form-control"  placeholder=""></td>' +
            '          <td class="align-middle"><input id"total' + idRow + '" onfocusout="Api_formUpdate()" value="0" readonly onchange="Api_formUpdate()" type="number" class="form-control"  placeholder=""></td>' +
            '          <td class="align-middle text-right">' +
            '              <button onfocusout="Api_formUpdate()" onclick="$(this).parent().parent().remove();Api_formUpdate();" class="btn btn-danger btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Delete">' +
            '                  <i class="material-icons md-14 align-middle">delete</i>' +
            '              </button>' +
            '          </td>' +
            '      </tr>');
}


function feTableEnter(id) {

    if (event.keyCode === 13) {
        idRow = ($(id).closest('tr')[0].rowIndex);
        Api_getProduct(idRow - 1);
        idRow = ($(id).closest('tr')[0].rowIndex);
        addRow(idRow);
        console.log("Row index is: " + idRow);
        $('#tabla tr:eq(' + idRow + ') td :input:enabled:visible:first').focus();
    } else if (event.keyCode === 9) {
        idRow = ($(id).closest('tr')[0].rowIndex);
        Api_getProduct(idRow - 1);
        console.log("Row index is: " + idRow);
    }
}

function createAndOpenFile(xmlFirmadoBase64) {
    var xml= atob(xmlFirmadoBase64);    
    download(descargaXMLClave+".xml",xml);
}

function download(filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);
}


function Api_companny_consecutive_by_terminal(tipoComprobante) {

    var user = [];
    user.w = 'admin';
    user.r = 'company_get_env';
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idMasterUser = localStorage.getItem('idMasterUser');
    API_PostRequest(user,
            function (data) {
                if (typeof data.resp == 'object') {
                    var user = [];
                    user.w = 'admin';
                    user.r = 'companny_getMyConsecutive';
                    user.iam = localStorage.getItem('userName');
                    user.sessionKey = localStorage.getItem('sessionKey');
                    user.idMasterUser = localStorage.getItem('idMasterUser');
                    user.tipoComprobante = tipoComprobante;
                    user.env = data.resp[0].env;
                    API_PostRequest(user,
                            function (data) {
                                var consecutivo = data.resp[0].consecutivo;
                                var siguienteConsecutivo = parseInt(consecutivo) + 1;
                                $("#feConsecutivo").val(siguienteConsecutivo);
                            },
                            function (data) {
                                sweetAlert({title: "Ocurrio un error cargando las provincias", type: "error"});
                            });

                }
            },
            function (data) {
                sweetAlert({title: "Ocurrio un error cargando las provincias", type: "error"});
            });


}

function Api_make_clave(tipoDocumento, tipoCedula, cedula, codigoPais, consecutivo, situacion, codigoSeguridad) {

    var user = [];
    user.w = 'clave';
    user.r = 'clave';
    user.tipoDocumento = tipoDocumento;
    user.tipoCedula = tipoCedula;
    user.cedula = cedula;
    user.codigoPais = codigoPais;
    user.consecutivo = consecutivo;
    user.situacion = situacion;
    user.codigoSeguridad = codigoSeguridad;
    API_PostRequest(user,
            function (data) {
                var resp = [];
                resp.clave = data.resp[0].clave;
                resp.consecutivo = data.resp[0].consecutivo;
                return resp;
            },
            function (data) {
                sweetAlert({title: "Ocurrio un error cargando las provincias", type: "error"});
            });
}






