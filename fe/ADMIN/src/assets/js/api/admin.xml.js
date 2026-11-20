
/*
 * Load XML parse to use in Aceptation MH module
 *
 */
function Api_read_xml(event, out) {
    var input = event.target;
    var reader = new FileReader();

    reader.onload = function () {
        var text = reader.result;
        var node = document.getElementById(out);
        //node.innerText = text;
        var $xml;
        var xml = text,
            xmlDoc = $.parseXML(xml),
            $xml = $(xmlDoc);
        var Clave = $xml.find("Clave");
        var TotalComprobante = $xml.find("TotalComprobante");
        var FechaEmision = $xml.find("FechaEmision");
        var CodigoMoneda = $xml.find("CodigoMoneda");
        var emisorNombre = $xml.find("Emisor Nombre");
        var emisorNumero = $xml.find("Emisor Numero");
        var tipoCedulaEmisor = $xml.find("Emisor Identificacion Tipo");
        var emisorNombreComercial = $xml.find("Emisor NombreComercial");
        var existeImpuesto = $xml.find("TotalImpuesto");
        var CorreoElectronico = $xml.find("Emisor CorreoElectronico");
        var nombreReceptor = $xml.find("Receptor Nombre");
        var nombreComercialReceptor = $xml.find("Receptor NombreComercial");
        var tipoCedulaReceptor = $xml.find("Receptor Identificacion Tipo");
        var cedulaReceptor = $xml.find("Receptor Identificacion Numero");
        $("#total").val(TotalComprobante.text());
        $("#impuesto").val(existeImpuesto.text());
        $("#clave").val(Clave.text());
        $("#fecha").val(FechaEmision.text());
        $("#moneda").val(CodigoMoneda.text());
        $("#emisorNombre").val(emisorNombre.text());
        $("#emisorNumero").val(emisorNumero.text());
        $("#emisorNombreComercial").val(emisorNombreComercial.text());
        $("#emisorCorreoElectronico").val(CorreoElectronico.text());
        $("#tipoCedulaEmisor").val(tipoCedulaEmisor.text());
        $("#nombreReceptor").val(nombreReceptor.text());
        $("#nombreComercialReceptor").val(nombreComercialReceptor.text());
        $("#tipoCedulaReceptor").val(tipoCedulaReceptor.text());
        $("#cedulaReceptor").val(cedulaReceptor.text());
    };
    reader.readAsText(input.files[0]);
}
