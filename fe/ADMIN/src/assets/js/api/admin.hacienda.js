//This is just to make a test traying to get a token.
function Api_test() {
    var clien_id = $("#client_id").val();
    var user = [];
    user.w = 'admin';
    if (clien_id == 'api-stag') {
        user.r = 'get_stag_credentials';
    } else if (clien_id == 'api-prod') {
        user.r = 'get_prod_credentials';
    }
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idMasterUser = localStorage.getItem('idMasterUser');
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                getToken(data.resp[0].value, data.resp[1].value, clien_id);
            } else {
                console.log("Error recuperando las credenciales para el token");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error recuperando las credenciales para el token", type: "error"});
        });
}

function Api_consultaClave() {
    var clien_id = $("#client_id").val();
    var user = [];
    user.w = 'admin';
    if (clien_id == 'api-stag') {
        user.r = 'get_stag_companny_credentials';
    } else if (clien_id == 'api-prod') {
        user.r = 'get_prod_companny_credentials';
    }
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                var user = [];
                user.username = data.resp[0].value;
                user.password = data.resp[1].value;
                user.w = 'token';
                user.r = 'gettoken';
                user.grant_type = 'password';
                user.client_id = $("#client_id").val();
                API_PostRequest(user,
                    function (data) {
                        var token = data.resp.access_token;
                        console.log("mhToken: " + token);
                        var user = [];
                        user.w = 'consultar';
                        user.r = 'consultarCom';
                        user.token = token;
                        user.clave = $("#mh_clave").val();
                        user.client_id = $("#client_id").val();
                        API_PostRequest(user,
                            function (data) {
                                if (data.resp === null) {
                                    sweetAlert({title: "Hacienda no respondio la consulta!", text: "No se tiene respuesta al comprobante, es posible que hacienda nunca lo recibiera.", type: "error"});
                                } else {
                                    if (data.resp['ind-estado'] === "aceptado") {
                                        console.log("Clave=>" + data.resp.clave);
                                        console.log("Fecha=>" + data.resp.fecha);
                                        sweetAlert({title: "Hacienda Conexion Exitosa!", text: "Respuesta\nClave:\n" + data.resp.clave + "\nFecha:\n" + data.resp.fecha + "\nEstado:\n" + data.resp['ind-estado'], type: "success"});
                                    } else if (data.resp['ind-estado'] === "rechazado") {
                                        sweetAlert({title: "Hacienda Rechazado!", text: "Respuesta\nClave:\n" + data.resp.clave + "\nFecha:\n" + data.resp.fecha + "\nEstado:\n" + data.resp['ind-estado'], type: "error"});
                                    } else {
                                        sweetAlert({title: "Hacienda sin respuesta!", text: "Respuesta\nClave:\n" + data.resp.clave + "\nFecha:\n" + data.resp.fecha + "\nEstado:\n" + data.resp[2], type: "error"});
                                    }
                                }
                            },
                            function (data) {
                                sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                            });
                    },
                    function (data) {
                        sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                    });

            } else {
                console.log("Error recuperando las credenciales");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error recuperando las credenciales", type: "error"});
        });
}

function Api_get_is_env_prod() {

    var user = [];
    user.w = 'admin';
    user.r = 'company_get_env';
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                if (data.resp[0].env === "api-stag") {
                    $("input[name='bootstrap-switch']").bootstrapSwitch('state', false);
                } else if (data.resp[0].env === "api-prod") {
                    $("input[name='bootstrap-switch']").bootstrapSwitch('state', true);
                }
            } else {
                console.log("Error cambiando el env");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando el entorno de facturacion", type: "error"});
        });
}


//This is just to change env into api-stag or api-prod
function Api_change_env(envProduccion) {
    var env;
    var user = [];
    user.w = 'admin';
    user.r = 'company_change_env';
    user.envProduccion = envProduccion;
    if (envProduccion) {
        env = "produccion";
    } else {
        env = "pruebas";
    }
    API_PostRequest(user,
        function (data) {
            if (data.resp == 'ok') {
                sweetAlert({title: "Entorno!", text: "El sistema ha cambiado a un entorno de " + env, type: "success"});
            } else {
                console.log("Error cambiando el ambiente");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cambiando el entorno", type: "error"});
        });
}



//this function get the token in the seet alert
function getToken(userName, pass, client_id, success) {

    var user = [];
    user.w = 'token';
    user.r = 'gettoken';
    user.grant_type = 'password';
    user.username = userName;
    user.password = pass;
    user.client_id = client_id;
    API_PostRequest(user,
        function (data) {
            success;
            sweetAlert({title: "Conexion a Hacienda Exitosa!", text: "Respuesta\nToken en " + client_id + ":\n" + data.resp.access_token, type: "success"});
        },
        function (data) {
            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
        });
}


function Api_mensaje_comprobacion() {
    var clave = $("#clave").val();
    var fecha = $("#mrDate").val();
    var emi_tipoIdentificacion = $("#tipoCedulaEmisor").val();
    var emi_numeroIdentificacion = $("#emisorNumero").val();
    var recp_tipoIdentificacion = $("#tipoCedulaReceptor").val();
    var recp_numeroIdentificacion = $("#cedulaReceptor").val();
    var terminal = localStorage.getItem('terminal');
    var sucursal = localStorage.getItem('sucursal');
    var tipoDocumento = $("#mensaje").val();
    var consecutivo = $("#feConsecutivo").val();
    consecutivo = padDigits(consecutivo, 10);
    var numero_consecutivo_receptor = sucursal + terminal + tipoDocumento + consecutivo;

    var mensaje;
    var XML;
    var env;
    var token;
    var user;
    var pass;
    var downloadCode;
    var pinP12;
    var tipoDoc;

    if ($("#mensaje").val() == '05') {
        tipoDoc = "CCE";
        mensaje = '1';
    } else if ($("#mensaje").val() == '06') {
        tipoDoc = "CPCE";
        mensaje = '2';
    } else if ($("#mensaje").val() == '07') {
        tipoDoc = "RCE";
        mensaje = '3';
    }

    var user = [];
    user.w = 'genXML';
    user.r = 'gen_xml_mr';
    user.clave = clave;
    user.numero_cedula_emisor = emi_numeroIdentificacion;
    if ($("#impuesto").val() == "") {
    } else {
        user.monto_total_impuesto = $("#impuesto").val();
    }
    user.fecha_emision_doc = fecha;
    user.mensaje = mensaje;
    user.detalle_mensaje = $("#detalle").val();
    user.total_factura = $("#total").val();
    user.numero_cedula_receptor = recp_numeroIdentificacion;
    user.numero_consecutivo_receptor = numero_consecutivo_receptor;
    user.emi_tipoIdentificacion = emi_tipoIdentificacion;
    user.recp_tipoIdentificacion = recp_tipoIdentificacion;
    user.emi_numeroIdentificacion = $("#emisorNumero").val();
    user.recp_numeroIdentificacion = $("#cedulaReceptor").val();


    API_PostRequest(user,
        function (data) {
            if (data.resp.xml != "") {
                XML = data.resp.xml;
                var user = [];
                user.w = 'admin';
                user.r = 'company_get_env';
                user.iam = localStorage.getItem('userName');
                user.sessionKey = localStorage.getItem('sessionKey');
                user.idMasterUser = localStorage.getItem('idMasterUser');
                API_PostRequest(user,
                    function (data) {
                        if (typeof data.resp == 'object') {
                            env = data.resp[0].env;
                            var client_id = env;
                            var user = [];
                            user.w = 'admin';
                            if (client_id == 'api-stag') {
                                user.r = 'get_stag_companny_credentials';
                            } else if (client_id == 'api-prod') {
                                user.r = 'get_prod_companny_credentials';
                            }
                            user.iam = localStorage.getItem('userName');
                            user.sessionKey = localStorage.getItem('sessionKey');
                            user.idMasterUser = localStorage.getItem('idMasterUser');
                            API_PostRequest(user,
                                function (data) {
                                    if (typeof data.resp == 'object') {
                                        user = data.resp[0].value;
                                        pass = data.resp[1].value;
                                        downloadCode = data.resp[2].value;
                                        pinP12 = data.resp[3].value;
                                        var user = [];
                                        user.w = 'token';
                                        user.r = 'gettoken';
                                        user.grant_type = 'password';
                                        user.username = data.resp[0].value;
                                        user.password = pass;
                                        user.client_id = env;
                                        API_PostRequest(user,
                                            function (data) {
                                                token = data.resp.access_token;
                                                var user = [];
                                                user.w = 'signXML';
                                                user.r = 'signFE';
                                                user.p12Url = downloadCode;
                                                user.pinP12 = pinP12;
                                                user.inXml = XML;
                                                user.tipodoc = tipoDoc;
                                                API_PostRequest(user,
                                                    function (data) {
                                                        if (data.resp.xmlFirmado != "") {
                                                            XML = data.resp.xmlFirmado;
                                                            var user = [];
                                                            user.w = 'send';
                                                            user.r = 'sendMensaje';
                                                            user.token = token;
                                                            user.clave = clave;
                                                            user.fecha = fecha;
                                                            user.emi_tipoIdentificacion = emi_tipoIdentificacion;
                                                            user.emi_numeroIdentificacion = emi_numeroIdentificacion;
                                                            user.recp_tipoIdentificacion = recp_tipoIdentificacion;
                                                            user.recp_numeroIdentificacion = recp_numeroIdentificacion;
                                                            user.client_id = env;
                                                            user.comprobanteXml = XML;
                                                            user.consecutivoReceptor = numero_consecutivo_receptor;
                                                            API_PostRequest(user,
                                                                function (data) {
                                                                    if (data.resp.xmlFirmado != "") {
                                                                        sweetAlert({title: "Envio Exitoso!", text: "Respuesta\nStatus:\n" + data.resp.Status + "\n", type: "success"});
                                                                        var user = [];
                                                                        user.w = 'admin';
                                                                        user.r = 'companny_updateConsecutive';
                                                                        user.idMasterUser = localStorage.getItem('idMasterUser');
                                                                        user.env = client_id;
                                                                        user.tipoDocumento = tipoDoc;
                                                                        API_PostRequest(user,
                                                                            function (data) {
                                                                                if (typeof data.resp == '1') {
                                                                                    console.log("Consecutivo aumentado");
                                                                                } else {
                                                                                    console.log("Error recuperando las provincias");
                                                                                }
                                                                            },
                                                                            function (data) {
                                                                                sweetAlert({title: "Ocurrio un error cargando las provincias", type: "error"});
                                                                            });
                                                                    } else {
                                                                        sweetAlert({title: "Error Firmando", text: "No se pudo obtener el xml firmado", type: "error"});
                                                                    }

                                                                },
                                                                function (data) {
                                                                    sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                                                                });
                                                        } else {
                                                            sweetAlert({title: "Error Firmando", text: "No se pudo obtener el xml firmado", type: "error"});
                                                        }

                                                    },
                                                    function (data) {
                                                        sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                                                    });

                                            },
                                            function (data) {
                                                sweetAlert({title: "Error!", text: "Error intentando obtener token.\n", type: "error"});
                                            });
                                    } else {
                                        console.log("Error recuperando las credenciales");
                                    }
                                },
                                function (data) {
                                    sweetAlert({title: "Ocurrio un error recuperando las credenciales", type: "error"});
                                });
                        } else {
                            console.log("Error obteniendo el env");
                        }
                    },
                    function (data) {
                        sweetAlert({title: "Ocurrio un error obteniendo el env", type: "error"});
                    });
            } else {
                sweetAlert({title: "Error!", text: "Error intentando rescuperar contraseña.\n", type: "error"});
            }
        },
        function (data) {
            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
        });
}
