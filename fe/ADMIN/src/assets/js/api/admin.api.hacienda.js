/**************************************************/
/*Función para enviar a hacienda                  */
/**************************************************/
function API_send(data, success, error) {

    var req = {
        w: "send",
        r: "sendMensaje",
        token: data.token,
        clave: data.clave,
        fecha: data.fecha,
        emi_tipoIdentificacion: data.tipoIdentifEmisor,
        emi_numeroIdentificacion: data.numIdentifEmisor,
        recp_tipoIdentificacion: data.tipoIdentifRec,
        recp_numeroIdentificacion: data.numIdentifRec,
        consecutivoReceptor: data.consecutivo,
        comprobanteXml: data.XML
    };
    API_PostRequest(req, success, error);
}


/**************************************************/
/*Función para enviar a Haciena Solamente TE      */
/**************************************************/
function API_sendTE(data, success, error) {
    var req = {
        w: "send",
        r: "send",
        token: data.token,
        clave: data.clave,
        fecha: data.fecha,
        emi_tipoIdentificacion: data.emi_tipoIdentificacion,
        emi_numeroIdentificacion: data.emi_numeroIdentificacion,
        comprobanteXml: data.comprobanteXml
    };
    API_PostRequest(req, success, error);
}

/**************************************************/
/*Función para enviar a Haciena FA, NC, ND        */
/**************************************************/
function API_send_json(data, success, error) {
    var req = {
        w: "send",
        r: "json",
        token: data.token,
        clave: data.clave,
        fecha: data.fecha,
        emi_tipoIdentificacion: data.emi_tipoIdentificacion,
        emi_numeroIdentificacion: data.emi_numeroIdentificacion,
        recp_tipoIdentificacion: data.recp_tipoIdentificacion,
        recp_numeroIdentificacion: data.recp_numeroIdentificacion,
        comprobanteXml: data.comprobanteXml
    };
    API_PostRequest(req, success, error);
}



/**************************************************/
/*Función para consultar comprobantes             */
/**************************************************/
function API_consultar(data, success, error) {
    var req = {
        w: "consultar",
        r: "consultarCom",
        clave: data.clave,
        token: data.token
    };
    API_PostRequest(req, success, error);
}
