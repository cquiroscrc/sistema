
//Esta funcion crea el XMl, lo firma pide token y lo envia a hacienda

function Api_FE() {

    var token;
    var usuario;
    var pass;
    var client_id;
    var p12Code;
    var pin;
    var tipoDocumento = 'FE';
   
    var situacion;
    var codigoSeguridad = Math.floor(Math.pow(10, 8 - 1) + Math.random() * (Math.pow(10, 8) - Math.pow(10, 8 - 1) - 1));
    var consecutivoXML;
    var clave;
    var consecutivo = $("#feConsecutivo").val();
    var codigoPais;
    
    var emisor_tipo_indetif;
    var emisor_num_identif;
    var nombre_comercial;
    var emisor_provincia;
    var emisor_canton;
    var emisor_distrito;
    var emisor_barrio;
    var emisor_otras_senas;
    var emisor_cod_pais_tel;
    var emisor_tel;
    var emisor_cod_pais_fax;
    var emisor_fax;
    var emisor_email;
    var emisor_nombre;
    var receptor_tipo_identif;
    var receptor_num_identif;
    var receptor_nombre;
    var receptor_provincia;
    var receptor_canton;
    var receptor_distrito;
    var receptor_barrio;
    var receptor_cod_pais_tel;
    var receptor_tel;
    var receptor_cod_pais_fax;
    var receptor_fax;
    var tipo_cambio;
    var receptor_email;
     
    var condicion_venta = $("#condicionVenta").val();
    var plazo_credito = $("#plazoCreditoInput").val();
    var medio_pago = $("#medioPago").val();
    var cod_moneda = $("#tipoMoneda").val();
    var total_serv_gravados = $("#TotalServGravados").val();
    var total_serv_exentos = $("#TotalServExentos").val();
    var total_merc_gravada = $("#TotalMercanciasGravadas").val();
    var total_merc_exenta = $("#TotalMercanciasExentas").val();
    var total_gravados = $("#TotalGravado").val();
    var total_exentos = $("#TotalExento").val();
    var total_ventas = $("#totalVenta").val();
    var total_descuentos = $("#descuenteTotal").val();
    var total_ventas_neta = $("#ventaNeta").val();
    var total_impuestos = $("#impuestoTotal").val();
    var total_comprobante = $("#total").val();
    var _data = new FormData();
    //var consecutivo = $("#feConsecutivo").val();
    var fecha_emision = $("#feDate").val();

    var user = [];
    user.w = 'admin';
    user.r = 'get_recedptor_by_id';
    user.iam = localStorage.getItem('userName');
    user.sessionKey = localStorage.getItem('sessionKey');
    user.idMasterUser = localStorage.getItem('idMasterUser');
    user.idReceptor = $("#idReceptor").val();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                receptor_tipo_identif = data.resp[0].tipoCedula;
                switch (data.resp[0].tipoCedula) {
                    case "fisico":
                        receptor_tipo_identif = '01';
                        break;
                    case "juridico":
                        receptor_tipo_identif = '02';
                        break;
                    case "dimex":
                        receptor_tipo_identif = '03';
                        break;
                    case "nite":
                        receptor_tipo_identif = '04';
                        break;
                   default:
                            receptor_tipo_identif = '';
                        break;
                }
                receptor_num_identif = data.resp[0].numeroCedula;
                receptor_nombre = data.resp[0].nombreCliente;
                receptor_provincia = data.resp[0].idProvincia;
                receptor_canton = data.resp[0].idCanton;
                receptor_distrito = data.resp[0].idDistrito;
                receptor_barrio = data.resp[0].idBarrio;
                var receptor_otras_senas = data.resp[0].otrasSenas;
                receptor_cod_pais_tel = data.resp[0].codigoPais;
                receptor_tel = data.resp[0].telefono;
                receptor_cod_pais_fax = data.resp[0].codigoPais;
                receptor_fax = data.resp[0].numeroFax;
                receptor_email = data.resp[0].correoPrincipal;


                var user = [];
                user.w = 'admin';
                user.r = 'get_companny_information';
                user.iam = localStorage.getItem('userName');
                user.sessionKey = localStorage.getItem('sessionKey');
                user.idMasterUser = localStorage.getItem('idMasterUser');
                API_PostRequest(user,
                    function (data) {
                        if (typeof data.resp == 'object') {

                            for (var i = 0; i < data.resp.length; i++) {
                                for (var key in data.resp[i]) {
                                    var value = data.resp[i].value;
                                    Api_debug("Adding " + data.resp[i].name + " -> " + value);
                                    _data.append(data.resp[i].name, value);
                                }
                            }

                            emisor_tipo_indetif = _data.get('TIPOCED');

                            emisor_barrio = _data.get('BARRIO');
                            emisor_canton = _data.get('CANTON');
                            emisor_distrito = _data.get('DISTRITO');
                            emisor_provincia = _data.get('PROVINCIA');

                            emisor_otras_senas = _data.get('SENNAS');

                            emisor_cod_pais_fax = _data.get('FCODPAIS');
                            emisor_cod_pais_tel = _data.get('NCODPAIS');
                            emisor_tel = _data.get('NNUMER');
                            emisor_fax = _data.get('FNUMER');

                            emisor_num_identif = _data.get('CEDULA');
                            nombre_comercial = _data.get('NOMCOMER');
                            emisor_nombre = _data.get('NOMBRE');
                            emisor_email = _data.get('EMAIL');

                            situacion = _data.get('situacion');
                            tipo_cambio = _data.get('TIPOCAMBIO');

                            var user = [];
                            user.w = 'clave';
                            user.r = 'clave';
                            user.tipoDocumento = tipoDocumento;
                            user.tipoCedula = emisor_tipo_indetif;
                            user.cedula = emisor_num_identif;
                            user.codigoPais = emisor_cod_pais_tel;
                            user.consecutivo = consecutivo;
                            user.situacion = situacion;
                            user.codigoSeguridad = codigoSeguridad;
                            user.iam = localStorage.getItem('userName');
                            user.sessionKey = localStorage.getItem('sessionKey');
                            API_PostRequest(user,
                                function (data) {
                                    clave = data.resp.clave;
                                    consecutivoXML = data.resp.consecutivo;
                                    var user = [];
                                    user.w = 'admin';
                                    user.r = 'company_get_env';
                                    user.iam = localStorage.getItem('userName');
                                    user.sessionKey = localStorage.getItem('sessionKey');
                                    user.idMasterUser = localStorage.getItem('idMasterUser');
                                    API_PostRequest(user,
                                        function (data) {
                                            if (typeof data.resp == 'object') {

                                                if (data.resp[0].env === "api-stag") {
                                                    client_id = "api-stag";
                                                } else if (data.resp[0].env === "api-prod") {
                                                    client_id = "api-prod";
                                                }
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
                                                            usuario = data.resp[0].value;
                                                            pass = data.resp[1].value;
                                                            p12Code = data.resp[2].value;
                                                            pin = data.resp[3].value;
                                                            var user = [];
                                                            user.w = 'token';
                                                            user.r = 'gettoken';
                                                            user.grant_type = 'password';
                                                            if (usuario == '' || pass == '' || client_id == '') {
                                                                sweetAlert({title: "Error!", text: "Todos los campos son requeridos deben contener datos.\n", type: "error"});
                                                            } else {
                                                                user.username = usuario;
                                                                user.password = pass;
                                                                user.client_id = client_id;
                                                                API_PostRequest(user,
                                                                    function (data) {
                                                                        token = data.resp.access_token;
                                                                        console.log("mhToken: " + token);
                                                                        var user = [];
                                                                        user.w = 'genXML';
                                                                        user.r = 'gen_xml_fe';
                                                                        user.clave = clave;
                                                                        user.consecutivo = consecutivoXML;
                                                                        user.fecha_emision = fecha_emision;
                                                                        user.emisor_nombre = emisor_nombre;
                                                                        user.emisor_tipo_indetif = emisor_tipo_indetif;
                                                                        user.emisor_num_identif = emisor_num_identif;
                                                                        user.nombre_comercial = nombre_comercial;
                                                                        user.emisor_provincia = emisor_provincia;
                                                                        user.emisor_canton = emisor_canton;
                                                                        user.emisor_distrito = emisor_distrito;
                                                                        user.emisor_barrio = emisor_barrio;
                                                                        user.emisor_otras_senas = emisor_otras_senas;
                                                                        user.emisor_cod_pais_tel = emisor_cod_pais_tel;
                                                                        user.emisor_tel = emisor_tel;
                                                                        user.emisor_cod_pais_fax = emisor_cod_pais_fax;
                                                                        user.emisor_fax = emisor_fax;
                                                                        user.emisor_email = emisor_email;
                                                                        user.receptor_nombre = receptor_nombre;
                                                                        user.receptor_tipo_identif = receptor_tipo_identif;
                                                                        user.receptor_num_identif = receptor_num_identif;
                                                                        user.receptor_provincia = receptor_provincia;
                                                                        user.receptor_canton = receptor_canton;
                                                                        user.receptor_distrito = receptor_distrito;
                                                                        user.receptor_barrio = receptor_barrio;
                                                                        user.receptor_otras_senas = receptor_otras_senas;
                                                                        user.receptor_cod_pais_tel = receptor_cod_pais_tel;
                                                                        user.receptor_tel = receptor_tel;
                                                                        user.receptor_cod_pais_fax = '';
                                                                        user.receptor_fax = receptor_fax;
                                                                        user.receptor_email = receptor_email;
                                                                        user.condicion_venta = condicion_venta;
                                                                        user.plazo_credito = plazo_credito;
                                                                        user.medio_pago = medio_pago;
                                                                        user.cod_moneda = cod_moneda;
                                                                        user.tipo_cambio = tipo_cambio;
                                                                        user.total_serv_gravados = total_serv_gravados;
                                                                        user.total_serv_exentos = total_serv_exentos;
                                                                        user.total_merc_gravada = total_merc_gravada;
                                                                        user.total_merc_exenta = total_merc_exenta;
                                                                        user.total_gravados = total_gravados;
                                                                        user.total_exentos = total_exentos;
                                                                        user.total_ventas = total_ventas;
                                                                        user.total_descuentos = total_descuentos;
                                                                        user.total_ventas_neta = total_ventas_neta;
                                                                        user.total_impuestos = total_impuestos;
                                                                        user.total_comprobante = total_comprobante;
                                                                        user.otros = $("#note").val();
                                                                        user.otrosType = 'OtroTexto';
                                                                        user.detalles = makeJsonFE('FETable');
                                                                        API_PostRequest(user,
                                                                            function (data) {
                                                                                if (data.resp[0] != "") {
                                                                                    var xml = data.resp.xml;
                                                                                    var user = [];
                                                                                    user.w = 'signXML';
                                                                                    user.r = 'signFE';
                                                                                    user.p12Url = p12Code;
                                                                                    user.pinP12 = pin;
                                                                                    user.inXml = xml;
                                                                                    user.tipodoc = tipoDocumento;
                                                                                    API_PostRequest(user,
                                                                                        function (data) {
                                                                                            if (data.resp.xmlFirmado != "") {
                                                                                                var xmlFirmado = data.resp.xmlFirmado;
                                                                                                console.log
                                                                                                var user = [];
                                                                                                user.w = 'send';
                                                                                                user.r = 'json';
                                                                                                user.token = token;
                                                                                                user.clave = clave;
                                                                                                user.fecha = fecha_emision;
                                                                                                user.emi_tipoIdentificacion = emisor_tipo_indetif;
                                                                                                user.emi_numeroIdentificacion = emisor_num_identif;
                                                                                                user.recp_tipoIdentificacion = receptor_tipo_identif;
                                                                                                user.recp_numeroIdentificacion = receptor_num_identif;
                                                                                                user.client_id = client_id;
                                                                                                user.callbackUrl = encodeURI('https://api-demo..org/api.php?w=callback&r=callback');
                                                                                                user.comprobanteXml = xmlFirmado;
                                                                                                API_PostRequest(user,
                                                                                                    function (data) {

                                                                                                        if (data.resp.xmlFirmado != "") {
                                                                                                            if (data.resp.Status == "202") {
                                                                                                                $("#myBtn").show();
                                                                                                                descargaXMLFirmado = xmlFirmado;
                                                                                                                descargaXMLClave = clave;
                                                                                                                sweetAlert({title: "Envio Exitoso!", text: "Se envio correctamente el comprobante.", type: "success"});
                                                                                                                var user = [];
                                                                                                                user.w = 'admin';
                                                                                                                user.r = 'companny_updateConsecutive';
                                                                                                                user.idMasterUser = localStorage.getItem('idMasterUser');
                                                                                                                user.env = client_id;
                                                                                                                user.tipoDocumento = tipoDocumento;
                                                                                                                API_PostRequest(user,
                                                                                                                    function (data) {
                                                                                                                        if (data.resp == '1') {
                                                                                                                            Api_add_voucher(clave, consecutivoXML, "enviado", xmlFirmado, "FE", "NULL", " NULL", client_id);
                                                                                                                            console.log("Consecutivo aumentado");
                                                                                                                        } else {
                                                                                                                            console.log("Error recuperando las provincias");
                                                                                                                        }
                                                                                                                    },
                                                                                                                    function (data) {
                                                                                                                        sweetAlert({title: "Ocurrio aumentando consecutivo", type: "error"});
                                                                                                                    });
                                                                                                            }
                                                                                                        } else {
                                                                                                            sweetAlert({title: "Hacienda sin respuesta!", text: "Respuesta\nClave:\n" + data.resp.clave + "\nFecha:\n" + data.resp.fecha + "\nEstado:\n" + data.resp[2], type: "error"});
                                                                                                        }

                                                                                                    },
                                                                                                    function (data) {
                                                                                                        sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                                                                                                    });
                                                                                            } else {
                                                                                                sweetAlert({title: "Hacienda sin respuesta!", text: "Respuesta\nClave:\n" + data.resp.clave + "\nFecha:\n" + data.resp.fecha + "\nEstado:\n" + data.resp[2], type: "error"});
                                                                                            }

                                                                                        },
                                                                                        function (data) {
                                                                                            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                                                                                        });
                                                                                } else {
                                                                                    sweetAlert({title: "Error en envio!", text: "Respuesta\nStatus:\n" + data.resp.Status + "\n", type: "error"});
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

                                                        } else {
                                                            console.log("Error recuperando las credenciales");
                                                        }
                                                    },
                                                    function (data) {
                                                        sweetAlert({title: "Ocurrio un error recuperando las credenciales", type: "error"});
                                                    });
                                            } else {
                                                console.log("Error cambiando el env");
                                            }
                                        },
                                        function (data) {
                                            sweetAlert({title: "Ocurrio un error cargando el entorno de facturacion", type: "error"});
                                        });
                                },
                                function (data) {
                                    sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
                                });
                        } else {
                            console.log("Error recuperando los clientes");
                        }
                    },
                    function (data) {
                        sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
                    });
            } else {
                console.log("Error recuperando los clientes");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
        });
}


//Esta funcion crea el XMl, lo firma pide token y lo envia a hacienda

function Api_TE() {
    var token;
    var usuario;
    var pass;
    var client_id;
    var p12Code;
    var pin;
    var tipoDocumento = 'TE';
    var codigoPais;
    var situacion;
    var codigoSeguridad = Math.floor(Math.pow(10, 8 - 1) + Math.random() * (Math.pow(10, 8) - Math.pow(10, 8 - 1) - 1));
    var consecutivoXML;
    var clave;
    var consecutivo = $("#feConsecutivo").val();
    var codigoPais;
    var situacion;
    var emisor_tipo_indetif;
    var emisor_num_identif;
    var nombre_comercial;
    var emisor_provincia;
    var emisor_canton;
    var emisor_distrito;
    var emisor_barrio;
    var emisor_otras_senas;
    var emisor_cod_pais_tel;
    var emisor_tel;
    var emisor_cod_pais_fax;
    var emisor_fax;
    var emisor_email;
    var emisor_nombre;
    var condicion_venta = $("#condicionVenta").val();
    var plazo_credito = $("#plazoCreditoInput").val();
    var medio_pago = $("#medioPago").val();
    var cod_moneda = $("#tipoMoneda").val();
    var total_serv_gravados = $("#TotalServGravados").val();
    var total_serv_exentos = $("#TotalServExentos").val();
    var total_merc_gravada = $("#TotalMercanciasGravadas").val();
    var total_merc_exenta = $("#TotalMercanciasExentas").val();
    var total_gravados = $("#TotalGravado").val();
    var total_exentos = $("#TotalExento").val();
    var total_ventas = $("#totalVenta").val();
    var total_descuentos = $("#descuenteTotal").val();
    var total_ventas_neta = $("#ventaNeta").val();
    var total_impuestos = $("#impuestoTotal").val();
    var total_comprobante = $("#total").val();



    var _data = new FormData();
    //var consecutivo = $("#feConsecutivo").val();

    var fecha_emision = $("#feDate").val();
    var user = [];
    user.w = 'admin';
    user.r = 'get_companny_information';

    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                for (i = 0; i < data.resp.length; i++) {
                    for (var key in data.resp[i]) {
                        var value = data.resp[i].value;
                        Api_debug("Adding " + data.resp[i].name + " -> " + value);
                        _data.append(data.resp[i].name, value);
                    }
                }

                emisor_fax = _data.get('FNUMER');
                emisor_tipo_indetif = _data.get('TIPOCED');
                emisor_barrio = _data.get('BARRIO');
                emisor_canton = _data.get('CANTON');
                emisor_distrito = _data.get('DISTRITO');
                emisor_provincia = _data.get('PROVINCIA');
                emisor_otras_senas = _data.get('SENNAS');
                emisor_cod_pais_fax = _data.get('FCODPAIS');
                emisor_cod_pais_tel = _data.get('NCODPAIS');
                emisor_tel = _data.get('NNUMER');
                emisor_num_identif = _data.get('CEDULA');
                nombre_comercial = _data.get('NOMCOMER');
                emisor_nombre = _data.get('NOMBRE');
                emisor_email = _data.get('EMAIL');
                situacion = _data.get('situacion');
                var user = [];
                user.w = 'clave';
                user.r = 'clave';
                user.tipoDocumento = tipoDocumento;
                user.tipoCedula = emisor_tipo_indetif;
                user.cedula = emisor_num_identif;
                user.codigoPais = emisor_cod_pais_tel;
                user.consecutivo = consecutivo;
                user.situacion = situacion;
                user.codigoSeguridad = codigoSeguridad;
                user.iam = localStorage.getItem('userName');
                user.sessionKey = localStorage.getItem('sessionKey');
                API_PostRequest(user,
                    function (data) {
                        clave = data.resp.clave;
                        consecutivoXML = data.resp.consecutivo;
                        var user = [];
                        user.w = 'admin';
                        user.r = 'company_get_env';
                        user.iam = localStorage.getItem('userName');
                        user.sessionKey = localStorage.getItem('sessionKey');
                        user.idMasterUser = localStorage.getItem('idMasterUser');
                        API_PostRequest(user,
                            function (data) {
                                if (typeof data.resp == 'object') {

                                    if (data.resp[0].env === "api-stag") {
                                        client_id = "api-stag";
                                    } else if (data.resp[0].env === "api-prod") {
                                        client_id = "api-prod";
                                    }
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
                                                usuario = data.resp[0].value;
                                                pass = data.resp[1].value;
                                                p12Code = data.resp[2].value;
                                                pin = data.resp[3].value;
                                                var user = [];
                                                user.w = 'token';
                                                user.r = 'gettoken';
                                                user.grant_type = 'password';
                                                if (usuario == '' || pass == '' || client_id == '') {
                                                    sweetAlert({title: "Error!", text: "Todos los campos son requeridos deben contener datos.\n", type: "error"});
                                                } else {
                                                    user.username = usuario;
                                                    user.password = pass;
                                                    user.client_id = client_id;
                                                    API_PostRequest(user,
                                                        function (data) {
                                                            token = data.resp.access_token;
                                                            console.log("mhToken: " + token);
                                                            var user = [];
                                                            user.w = 'genXML';
                                                            user.r = 'gen_xml_te';
                                                            user.clave = clave;
                                                            user.consecutivo = consecutivoXML;
                                                            user.fecha_emision = fecha_emision;
                                                            user.emisor_nombre = emisor_nombre;
                                                            user.emisor_tipo_indetif = emisor_tipo_indetif;
                                                            user.emisor_num_identif = emisor_num_identif;
                                                            user.nombre_comercial = nombre_comercial;
                                                            user.emisor_provincia = emisor_provincia;
                                                            user.emisor_canton = emisor_canton;
                                                            user.emisor_distrito = emisor_distrito;
                                                            user.emisor_barrio = emisor_barrio;
                                                            user.emisor_otras_senas = emisor_otras_senas;
                                                            user.emisor_cod_pais_tel = emisor_cod_pais_tel;
                                                            user.emisor_tel = emisor_tel;
                                                            user.emisor_cod_pais_fax = emisor_cod_pais_fax;
                                                            user.emisor_fax = emisor_fax;
                                                            user.emisor_email = emisor_email;
                                                            user.condicion_venta = condicion_venta;
                                                            user.plazo_credito = plazo_credito;
                                                            user.medio_pago = medio_pago;
                                                            user.cod_moneda = cod_moneda;
                                                            user.tipo_cambio = '564.48';
                                                            user.total_serv_gravados = total_serv_gravados;
                                                            user.total_serv_exentos = total_serv_exentos;
                                                            user.total_merc_gravada = total_merc_gravada;
                                                            user.total_merc_exenta = total_merc_exenta;
                                                            user.total_gravados = total_gravados;
                                                            user.total_exentos = total_exentos;
                                                            user.total_ventas = total_ventas;
                                                            user.total_descuentos = total_descuentos;
                                                            user.total_ventas_neta = total_ventas_neta;
                                                            user.total_impuestos = total_impuestos;
                                                            user.total_comprobante = total_comprobante;
                                                            user.otros = $("#note").val();
                                                            user.otrosType = 'OtroTexto';
                                                            user.detalles = makeJsonFE('FETable');
                                                            API_PostRequest(user,
                                                                function (data) {
                                                                    if (data.resp[0] != "") {
                                                                        var xml = data.resp.xml;
                                                                        var user = [];
                                                                        user.w = 'signXML';
                                                                        user.r = 'signFE';
                                                                        user.p12Url = p12Code;
                                                                        user.pinP12 = pin;
                                                                        user.inXml = xml;
                                                                        user.tipodoc = 'TE';
                                                                        API_PostRequest(user,
                                                                            function (data) {
                                                                                if (data.resp.xmlFirmado != "") {
                                                                                    var xmlFirmado = data.resp.xmlFirmado;
                                                                                    console.log
                                                                                    var user = [];
                                                                                    user.w = 'send';
                                                                                    user.r = 'sendTE';
                                                                                    user.token = token;
                                                                                    user.clave = clave;
                                                                                    user.fecha = fecha_emision;
                                                                                    user.emi_tipoIdentificacion = emisor_tipo_indetif;
                                                                                    user.emi_numeroIdentificacion = emisor_num_identif;
                                                                                    user.client_id = client_id;
                                                                                    user.callbackUrl = encodeURI('https://api-demo..org/api.php?w=callback&r=callback');
                                                                                    user.comprobanteXml = xmlFirmado;
                                                                                    API_PostRequest(user,
                                                                                        function (data) {
                                                                                            if (data.resp.xmlFirmado != "") {
                                                                                                if (data.resp.Status == "202") {
                                                                                                    Api_get_te_lines('tabla', 'linesTE', clave, consecutivoXML, nombre_comercial, fecha_emision, emisor_num_identif, emisor_tel);
                                                                                                    $("#myBtn").show();
                                                                                                    sweetAlert({title: "Envio Exitoso!", text: "Se envio correctamente el comprobante", type: "success"});
                                                                                                    var user = [];
                                                                                                    user.w = 'admin';
                                                                                                    user.r = 'companny_updateConsecutive';
                                                                                                    user.idMasterUser = localStorage.getItem('idMasterUser');
                                                                                                    user.env = client_id;
                                                                                                    user.tipoDocumento = tipoDocumento;
                                                                                                    API_PostRequest(user,
                                                                                                        function (data) {
                                                                                                            if (data.resp == '1') {
                                                                                                                console.log("Consecutivo aumentado");
                                                                                                                Api_add_voucher(clave, consecutivoXML, "enviado", xmlFirmado, "TE", "NULL", " NULL", client_id);

                                                                                                            } else {
                                                                                                                console.log("Error recuperando las provincias");
                                                                                                            }
                                                                                                        },
                                                                                                        function (data) {
                                                                                                            sweetAlert({title: "Ocurrio aumentando consecutivo", type: "error"});
                                                                                                        });
                                                                                                }
                                                                                            } else {
                                                                                                sweetAlert({title: "Hacienda sin respuesta!", text: "Respuesta\nClave:\n" + data.resp.clave + "\nFecha:\n" + data.resp.fecha + "\nEstado:\n" + data.resp[2], type: "error"});
                                                                                            }

                                                                                        },
                                                                                        function (data) {
                                                                                            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                                                                                        });
                                                                                } else {
                                                                                    sweetAlert({title: "Hacienda sin respuesta!", text: "Respuesta\nClave:\n" + data.resp.clave + "\nFecha:\n" + data.resp.fecha + "\nEstado:\n" + data.resp[2], type: "error"});
                                                                                }

                                                                            },
                                                                            function (data) {
                                                                                sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
                                                                            });
                                                                    } else {
                                                                        sweetAlert({title: "Error en envio!", text: "Respuesta\nStatus:\n" + data.resp.Status + "\n", type: "error"});
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

                                            } else {
                                                console.log("Error recuperando las credenciales");
                                            }
                                        },
                                        function (data) {
                                            sweetAlert({title: "Ocurrio un error recuperando las credenciales", type: "error"});
                                        });
                                } else {
                                    console.log("Error cambiando el env");
                                }
                            },
                            function (data) {
                                sweetAlert({title: "Ocurrio un error cargando el entorno de facturacion", type: "error"});
                            });
                    },
                    function (data) {
                        sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
                    });
            } else {
                console.log("Error recuperando los clientes");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
        });
}


