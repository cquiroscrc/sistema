
/**************************************************/
/*Función para firmar XML                         */
/**************************************************/
function API_signXML(data, success, error) {
    var req = {
        w: "signXML",
        r: "signFE",
        p12Url: data.p12Url,
        inXml: data.inXml,
        tipodoc: data.tipodoc
    };
    API_PostRequest(req, success, error);
}
