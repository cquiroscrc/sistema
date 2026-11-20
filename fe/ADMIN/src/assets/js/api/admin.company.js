
/*
 * Get all types of ids
 */
function Api_getTypeOfId(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'get_type_of_id';
    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                $("#" + selectID).append('<option value="">---</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].codigo + '">' + data.resp[i].descripcion + '</option>');
                }
            } else {
                console.log("Error recuperando la lista de provincias");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando la provincias.", type: "error"});
        });
}


function Api_getSucursal(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'get_type_of_id';
    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                $("#" + selectID).append('<option value="">---</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].codigo + '">' + data.resp[i].descripcion + '</option>');
                }
            } else {
                console.log("Error recuperando las provincias");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando las provincias", type: "error"});
        });
}


/*
 *This function get all your receptor by your userName and your sessionkey
 */
function Api_getAllreceptor(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'get_active_receptor';
    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<tr><td>' + data.resp[i].nombreCliente + '</td><td>' + data.resp[i].correoPrincipal + '</td><td>' + data.resp[i].nombreProvincia + '</td><td>' + data.resp[i].telefono + '</td><td>' + data.resp[i].tipoCedula + '</td><td>' + data.resp[i].numeroCedula + '</td><td><button value="' + data.resp[i].idReceptor + '" onclick="addRow($(this).val());"class="btn btn-warning btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">edit</i></button><button value="' + data.resp[i].idReceptor + '" onclick="Api_deleteReciver($(this).val());" class="btn btn-danger btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">delete</i></button></td></tr>');
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


function Api_AllreceptorCB(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'get_active_receptor';

    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                $("#" + selectID).append('<option value="">---</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].idReceptor + '">' + data.resp[i].nombreCliente + '</option>');
                }
            } else {
                console.log("Error recuperando los clientes");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
        });
}

function Api_add_sucursal() {
    var user = [];
    user.w = 'admin';
    user.r = 'addSucursales';

    user.numeroSucursal = $("#numeroSucursal").val();
    user.nombreSucursal = $("#nombreSucursal").val();

    API_PostRequest(user,
        function (data) {
            if (data.resp == '1') {
                sweetAlert({title: "Se creo correctamente la sucursal", type: "success"});
            } else {
                console.log("Error recuperando las sucursales");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error creando la sucursal", type: "error"});
        });
}

function Api_add_terminal() {
    var user = [];
    user.w = 'admin';
    user.r = 'add_terminal';

    user.numeroTerminal = $("#numeroTerminal").val();
    user.nombreTerminal = $("#nombreTerminal").val();
    user.idSucursal = $("#idSucursal").val();

    API_PostRequest(user,
        function (data) {
            if (data.resp == '1') {
                sweetAlert({title: "Se creo correctamente la terminal", type: "success"});
            } else {
                console.log("Error recuperando las terminales");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error creando la terminal", type: "error"});
        });
}

function compannyUpdateTipoCambio() {
    var user = [];
    user.w = 'admin';
    user.r = 'compannyUpdateTipoCambio';

    user.tipoCambio = $("#tipoCambio").val();
    API_PostRequest(user,
        function (data) {
            if (data.resp == '1') {
                sweetAlert({title: "Tipo Cambio actualizado", type: "success"});
                Api_getCompannyInformationAdmin();

            } else {
                console.log("Error actualizando tipo de cambio");
            }

        },
        function (data) {
            sweetAlert({title: "Ocurrio un error actualizando el tipo de cambio", type: "error"});
        });
}
function compannyUpdateLocation() {
    var user = [];
    user.w = 'admin';
    user.r = 'compannyUpdateLocation';

    user.idProvincia = $("#provinceSelect").val();
    user.idCanton = $("#cantonSelect").val();
    user.idDistrito = $("#distritoSelect").val();
    user.idBarrio = $("#barrioSelect").val();
    user.sennas = $("#otrasSenas").val();
    API_PostRequest(user,
        function (data) {
            if (data.resp == '1') {
                sweetAlert({title: "Se actualizo la ubicacion", type: "success"});
                Api_getCompannyInformationAdmin();

            } else {
                console.log("Error actualizando ubicacion");
            }

        },
        function (data) {
            sweetAlert({title: "Ocurrio un error actualizando la ubicacion", type: "error"});
        });
}

function Api_getSucursalCB(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'getSucursales';

    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {

                $("#" + selectID).append('<option value="">---</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].idSucursal + '">' + data.resp[i].nombreSucursal + "-" + data.resp[i].sucursal + '</option>');
                }

            } else {
                console.log("Error recuperando los clientes");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
        });

}

function Api_getTipoImpuestoCB(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'get_tipo_impuesto';

    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {

                $("#" + selectID).append('<option value="">---</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].idImpuesto + '">' + data.resp[i].Descripcion + '</option>');
                }

            } else {
                console.log("Error recuperando los tipos impuesto");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los tipos de impuesto", type: "error"});
        });

}
function Api_getUnidCB(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'getUnid';

    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {

                $("#" + selectID).append('<option value="">---</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].simbolo + '">' + data.resp[i].simbolo + '</option>');
                }

            } else {
                console.log("Error recuperando los tipos impuesto");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los tipos de impuesto", type: "error"});
        });

}

//Pendiente de uso, obtiene todas las terminales de UNA sucursal
function Api_getTerminalesCB(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'getTerminales';

    user.idSucursal = $("#idSucursal").val();
    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].idTerminal + '">' + data.resp[i].nombreTerminal + "-" + data.resp[i].terminal + '</option>');
                }
            } else {
                console.log("Error recuperando las terminales");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando las terminales", type: "error"});
        });

}


//Pendiente de uso, obtiene todas las terminales
function Api_getTerminales(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'getTerminales';

    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<tr><td>' + data.resp[i].nombreTerminal + '</td><td>' + data.resp[i].terminal + '</td><td>' + data.resp[i].nombreSucursal + '</td><td>' + data.resp[i].sucursal + '</td><td><button value="' + data.resp[i].idTerminal + '" onclick="" class="btn btn-warning btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">edit</i></button><button value="' + data.resp[i].idTerminal + '" onclick="" class="btn btn-danger btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">delete</i></button></td></tr>');
                }
            } else {
                console.log("Error recuperando las terminales");
            }
            $('#data-table').dataTable();
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error obteniendo las terminales", type: "error"});
        });

}

//Sin uso
function Api_getUsersCompanny(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'getUsersCompanny';

    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<tr><td>' + data.resp[i].fullName + '</td><td>' + data.resp[i].email + '</td><td>' + data.resp[i].country + '</td><td>' + data.resp[i].nombreTerminal + '</td><td>' + data.resp[i].terminal + '</td><td>' + data.resp[i].nombreSucursal + '</td><td>' + data.resp[i].sucursal + '</td><td><button value="' + data.resp[i].idUser + '" onclick="addRow($(this).val());"class="btn btn-warning btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">edit</i></button><button value="' + data.resp[i].idTerminal + '" onclick="" class="btn btn-danger btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">delete</i></button></td></tr>');
                }
            } else {
                console.log("Error recuperando los roles");
            }
            $('#data-table').dataTable();
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
        });

}
//Sin uso
function Api_getUsersRol(selectID) {
    var user = [];
    user.w = 'admin';
    user.r = 'getUserPermissionById';

    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<tr><td>' + data.resp[i].FullName + '</td><td>' + data.resp[i].userName + '</td><td>' + data.resp[i].descripcion + '</td><td><button value="' + data.resp[i].idUser + '" onclick="addRow($(this).val());"class="btn btn-warning btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">edit</i></button><button value="' + data.resp[i].idTerminal + '" onclick="Api_deleteReciver($(this).val());" class="btn btn-danger btn-sm " data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar"><i class="material-icons md-12 align-middle">delete</i></button></td></tr>');
                }
            } else {
                console.log("Error recuperando los roles");
            }
            $('#data-table').dataTable();
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los roles", type: "error"});
        });

}


function Api_getCompannyLocationInformation(idProvincia, idCanton, idDistrito, idBarrio) {

    var user = [];

    user.w = 'admin';
    user.r = 'getCompannyLocationInformation';

    user.idProvincia = idProvincia;
    user.idCanton = idCanton;
    user.idDistrito = idDistrito;
    user.idBarrio = idBarrio;

    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {

                SetSelected("provincia",data.resp[0].P1,data.resp[0].PROVINCIA);
                SetSelected("canton",data.resp[0].C1,data.resp[0].CANTON);
                SetSelected("distrito",data.resp[0].D1,data.resp[0].DISTRITO);
                SetSelected("barrio",data.resp[0].B1,data.resp[0].BARRIO);

            } else {
                console.log("Error recuperando datos de ubicacion,error:"+data.resp);
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando ubicacion,error:"+data.resp, type: "error"});
        });
}

function GetTiposIdentificacion(selectID) {

    var params = [];
    params.w = 'admin';
    params.r = 'GetTiposIdentificacion';

    $("#" + selectID).empty();

    API_PostRequest(params,
        function (data) {
            if (typeof data.resp == 'object') {
                $("#" + selectID).append('<option value="00">Seleccione opcion</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].CODIGO + '">' + data.resp[i].TIPO_IDENTIFICACION + '</option>');
                }
            } else {
                console.log("Error recuperando los tipos de Identificaciones");
            }
        },
        function (data) {
            Swal.fire("Error recuperando los tipos de Identificaciones", "error");
        },async=false);
}

/*
 * Get all Provinces in Select options
 */
function GetProvincias(idProvincia="",selectID) {

    var params = [];
    params.w = 'admin';
    params.r = 'GetProvincias';
    params.idProvincia = idProvincia;

    $("#" + selectID).empty();

    API_PostRequest(params,
        function (data) {
            if (typeof data.resp == 'object') {
                $("#" + selectID).append('<option value="">Seleccione una provincia</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].P1 + '">' + data.resp[i].PROVINCIA + '</option>');
                }
            } else {
                blockUIElement($("#" + selectID),'','unblock');
                console.log("Error recuperando las provincias");
            }
        },
        function (data) {
            blockUIElement($("#" + selectID),'','unblock');
            Swal.fire("Ocurrio un error cargando las provincias", "error");
        });
}
function GetCantones(idProvince="", idCanton="",selectID) {

    var params = [];
    params.w = 'admin';
    params.r = 'GetCantones';
    params.idProvincia = idProvince;
    params.idCanton = idCanton;

    $("#" + selectID).empty();
    API_PostRequest(params,
        function (data) {
            if (typeof data.resp == 'object') {
                $("#" + selectID).append('<option value="">Seleccione un canton</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].C1 + '">' + data.resp[i].CANTON + '</option>');
                }
            } else {
                console.log("Error recuperando los cantones");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los cantones", type: "error"});
        });
}
function  GetDistritos(idProvince="", idCanton="",idDistrito="", selectID) {

    var params = [];
    params.w = 'admin';
    params.r = 'GetDistritos';
    params.idProvincia = idProvince;
    params.idCanton = idCanton;
    params.idDistrito = idDistrito;
    blockUIElement($(selectID),'Cargando distritos, por favor espere','block');
    $("#" + selectID).empty();
    API_PostRequest(params,
        function (data) {
            if (typeof data.resp == 'object') {
                $("#"+selectID).append('<option value="">Seleccione un distrito</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].D1 + '">' + data.resp[i].DISTRITO + '</option>');
                }
                blockUIElement($(selectID),'','unblock');
            } else {
                console.log("Error recuperando los distritos");
            }
        },
        function (data) {
            Swal.fire("Ocurrio un error cargando los distritos", "error");
        });
}
function  GetBarrios(idProvince="", idCanton="", idDistrito="",idBarrio="", selectID) {

    var params = [];
    params.w = 'admin';
    params.r = 'GetBarrios';
    params.idProvincia = idProvince;
    params.idCanton = idCanton;
    params.idDistrito = idDistrito;
    params.idBarrio = idBarrio;

    $("#" + selectID).empty();
    API_PostRequest(params,
        function (data) {
            if (typeof data.resp == 'object') {
                $("#" + selectID).append('<option value="">Seleccione un barrio</option>');
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].B1 + '">' + data.resp[i].BARRIO + '</option>');
                }
            } else {
                console.log("Error recuperando los barrios");
            }
            $(document).trigger('RefrescarCompletado');
            blockUIElement('#datos-ubicacion','','unblock');
        },
        function (data) {
            Swal.fire("Ocurrio un error cargando los barrios", "error");
            blockUIElement('#datos-ubicacion','','unblock');
        });
}


function compannyUpdateInformation() {

    var params = [];
    params.w = 'admin';
    params.r = 'compannyUpdateInformation';

    params.NOMBRE = $("#nombre").val();
    params.NOMBRE_COMERCIAL = $("#nombre-comercial").val();
    params.CODIGO_PAIS = $("#codigo-pais").val();

    params.TELEFONO = $("#telefono").val();
    params.TELEFONO2 = $("#telefono2").val();
    params.CORREO = $("#correo").val();
    params.GLN = $("#gln").val();



    params.TIPO_NIT = $("#tipo-cedula").val();
    params.NIT = $("#nit").val();
    params.EMAIL_DOC_ELECTRONICO = $("#correo-envios").val();

    //Datos de Ubicacion

    params.COD_PAIS_ISO = $("#codigo-pais-iso").val();
    params.PROVINCIA = $("#provincia").val();
    params.CANTON = $("#canton").val();
    params.DISTRITO = $("#distrito").val();
    params.BARRIO = $("#barrio").val();
    params.OTRAS_SENAS= $("#otras-senas").val();

    params.NORMATIVA= $("#normativa").val();
    params.FECHA_NORMATIVA= moment($('#fecha-normativa').val()).format("DD/MM/YYYY");

    API_PostRequest(params,
        function (data) {
            if (data.resp == '1') {
                Swal.fire("Se actualizo la informacion correctamente","","success").
                then(()=> {
                    //$(document).trigger("ActualizacionDatosTerminada");
                    Refrescar("DatosEmpresa");
                });

            } else {
                Swal.fire("Ocurrio un error actualizando la informacion de la compañia,error:\n"+data.resp,"","error")
            }

        },
        function (data) {
            Swal.fire("Ocurrio un error actualizando la informacion de la compañia,error: \n"+data.resp, "", "error");
        });

}

//Obtiene los datos para la pagina pages_compañia.php
//Funcionando correctamente.
function Api_getCompannyInformationAdmin() {

    var _data = new FormData();
    var params = [];

    params.w = 'admin';
    params.r = 'get_companny_information_admin';
    API_PostRequest(params,
        function (data) {
            if (typeof data.resp == 'object') {

                $("#nombre").val(data.resp[0].NOMBRE);
                $("#nombre-comercial").val(data.resp[0].NOMBRE_COMERCIAL);
                $("#codigo-pais").val(data.resp[0].PAIS);

                $("#telefono").val(data.resp[0].TELEFONO_1);
                $("#telefono2").val(data.resp[0].TELEFONO_2);
                $("#correo").val(data.resp[0].CORREO);
                $("#gln").val(data.resp[0].GLN);

                SetSelected('tipo-cedula',data.resp[0].TIPO_NIT,'');

                $("#nit").val(data.resp[0].NIT);
                $("#correo-envios").val(data.resp[0].EMAIL_DOC_ELECTRONICO);

                $("#codigo-pais-iso").val(data.resp[0].COD_PAIS_ISO);
                $("#otras-senas").val(data.resp[0].OTRAS_SENAS)
                //Otros Datos
                $("#normativa").val(data.resp[0].NORMATIVA);

                $("#fecha-normativa").val(moment(data.resp[0].FECHA_NORMATIVA).format("YYYY-MM-DD"));

                Api_getCompannyLocationInformation(data.resp[0].DIVISION_GEOGRAFICA1, data.resp[0].DIVISION_GEOGRAFICA2, data.resp[0].DIVISION_GEOGRAFICA3, data.resp[0].DIVISION_GEOGRAFICA4);

            } else {
                console.log("Error recuperando los datos de la compañia");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error los datos de la compañia", type: "error"});
        });
}

function Refrescar(parte,callback){
    if (parte=="DatosEmpresa"){

        blockUIElement('#datos-ubicacion','Cargando ubicaciones','block');

        GetTiposIdentificacion('tipo-cedula');

        GetProvincias("",'provincia');
        GetCantones("","",'canton');
        GetDistritos("","","",'distrito');
        GetBarrios("","","","",'barrio');

        //Con la funcion siguiente se obtienen datos de la empresa y se asignan a la vez los tipos de identificacion
        //Api_getCompannyInformationAdmin();



    }

}
function Api_add_companny_reciver() {

    var user = [];
    user.w = 'admin';
    user.r = 'add_companny_reciver';
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idMasterUser = localStorage.getItem('idMasterUser');
    user.nombreCliente = $("#nombreCliente").val();
    user.numeroCedula = $("#numeroCedula").val();
    user.tipoCedula = $("#typeIdSelect").val();
    user.telefono = $("#telefono").val();
    user.idProvincia = $("#provinceSelect").val();
    user.idCanton = $("#cantonSelect").val();
    user.idDistrito = $("#distritoSelect").val();
    user.idBarrio = $("#barrioSelect").val();
    user.otrasSenas = $("#otrasSenas").val();
    user.nombreComercial = $("#nombreComercial").val();
    user.correoPrincipal = $("#correoPrincipal").val();
    user.copiasCorreo = $("#copiasCorreo").val();
    user.numeroFax = $("#numeroFax").val();
    API_PostRequest(user,
        function (data) {
            if (data.resp == 1) {
                sweetAlert({title: "Se creo correctamente el cliente\n" + $("#nombreCliente").val(), type: "success"});
                $("#userName").val("");
            } else {
                sweetAlert({title: "Error!", text: "Error intentando crear clienteu.\n", type: "error"});
            }
        },
        function (data) {
            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
        });
}
