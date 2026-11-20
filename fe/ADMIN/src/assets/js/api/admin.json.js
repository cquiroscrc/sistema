
 
function showDocElec(urlToConnect)
{
           
            // read our JSON
            var fi = moment(document.getElementById("fechaInicio").value,'DD/MM/YYYY hh:mm:ss').format('YYYY-MM-DD hh:mm:ss');
            var ff = moment(document.getElementById("fechaFinal").value,'DD/MM/YYYY hh:mm:ss').format('YYYY-MM-DD hh:mm:ss');
           
            if( urlToConnect!='empty') {
                $("#grid").empty();
               
                urlToConnect="http://localhost/API/www/api.php?w=admin&r=getDocElec&fechaInicio="+fi+"&fechaFinal="+ff            
            }
            
            else {
                $("#grid").empty();
               
                urlToConnect="#";
            }

            $("#grid").kendoGrid({
                dataSource: {
                    transport: {
                        read:  {
                          url: urlToConnect,
                          dataType: "json", // "jsonp" is required for cross-domain requests; use "json" for same-domain requests
                          serverPaging: false
                        }
                      },
                    schema: {
                        data: function(response) {
                            return response.resp;
                        },
                        total: function(response) {   
                            var data = this.data(response);                        
                            return data ? data.length : 0;
                        },
                        model: {
                            fields: {
                                TIPO_DOC: { type: "string" },
                                NUMERO_DOC: { type: "string" },
                                ENVIADO: { type: "string" },
                                ACEPTADO: { type: "string" },
                                FECHA_HORA: { type: "date",parse: function(value) { return new Date(value); } },
                                NIT_RECEPTOR: { type: "string" },
                                ESTADO: { type: "string" },
                                SELLO_SAT: { type: "string" },
                                ULTIMO_ERROR: { type: "string" },
                                ERROR_LOCAL: { type: "string" },
                                ERROR_HACIENDA: { type: "string" }
                            }
                        }
                    },
                    pageSize: 25
                },
                height: 550,
                scrollable: true,
                sortable: true,
                filterable: true,
                pageable: {
                    alwaysVisible: true,
                    pageSizes: [5, 10, 20,50,100,200,1000,10000,1000000]
                },
                columns: [
                    { field: "TIPO_DOC", title: "Tipo", width: "75px" },
                    { field: "NUMERO_DOC", title: "Documento", width: "170px" },            
                    { field: "ENVIADO", title: "Enviado", width: "80px" },
                    { field: "ACEPTADO", title: "Aceptado", width: "90px" },
                    { field: "FECHA_HORA", title: "Fecha", width: "130px", template: "#= kendo.toString(kendo.parseDate(FECHA_HORA, 'yyyy-MM-dd'), 'MM/dd/yyyy') #" },
                    { field: "NIT_RECEPTOR", title: "NIT", width: "130px" },
                    { field: "ESTADO", title: "Estado", width: "90px" },
                    { field: "SELLO_SAT", title: "Consecutivo", width: "450px" },
                    { field: "ULTIMO_ERROR", title: "Errores", width: "220px" },
                    { field: "ERROR_LOCAL", title: "Err/Loc", width: "80px" },
                    { field: "ERROR_HACIENDA", title: "Err/Hac.", width: "90px" },
                ]
                
            });
        
          
     
}

function makeJsonFE(table) {
    var myTab = document.getElementById(table);
    // LOOP THROUGH EACH ROW OF THE TABLE AFTER HEADER.1706
    var json = ''; //{"1": {"cantidad":"1","unidadMedida":"Sp","detalle":"Impresora","precioUnitario":"10000","montoTotal":"10000","subtotal":"10000","montoTotalLinea":"10000"}}
    for (var i = 1; i < myTab.rows.length - 1; i++) {
        var objCells = myTab.rows.item(i).cells;
        var cantidad = 0;
        var unidadMedida = 0;
        var detalle = 0;
        var precioUnitario = 0;
        var montoTotal = 0;
        var subTotal = 0;
        var montoTotalLinea = 0;
        var cantidadDescuento = 0;
        var cantidadImpuesto = 0;
        var naturalezaDescuento = 0;
        var impuesto = 0;
        var descuento = 0;
        var codigoImpuesto = 0;
        var motoImpuesto = 0;
        
        cantidadDescuento = parseFloat(objCells.item(4).children[0].value);
        cantidadImpuesto = parseFloat(objCells.item(5).children[0].value);
        codigoImpuesto = (objCells.item(5).children[1].value);        
        cantidad = objCells.item(2).children[0].value;
        unidadMedida = objCells.item(1).children[0].value;
        detalle = objCells.item(0).children[0].value;
        precioUnitario = objCells.item(3).children[0].value;
        montoTotal = cantidad * precioUnitario;
        descuento = montoTotal * (cantidadDescuento / 100);
        subTotal = montoTotal - descuento;
        motoImpuesto = subTotal * (cantidadImpuesto / 100);
        montoTotalLinea = subTotal + motoImpuesto;

        if (cantidadDescuento === 0 && cantidadImpuesto === 0) {
            if (json === '') {
                json = '"' + i + '": {"cantidad":"' + cantidad + '","unidadMedida":"' + unidadMedida + '","detalle":"' + detalle + '","precioUnitario":"' + precioUnitario + '","montoTotal":"' + montoTotal + '","subtotal":"' + subTotal + '","montoTotalLinea":"' + montoTotalLinea + '"}';
            } else {
                json = json + ',"' + i + '": {"cantidad":"' + cantidad + '","unidadMedida":"' + unidadMedida + '","detalle":"' + detalle + '","precioUnitario":"' + precioUnitario + '","montoTotal":"' + montoTotal + '","subtotal":"' + subTotal + '","montoTotalLinea":"' + montoTotalLinea + '"}';
                // GET THE CELLS COLLECTION OF THE CURRENT ROW.
            }
        } else if (cantidadDescuento !== 0 && cantidadImpuesto === 0) {
            if (json === '') {
                json = '"' + i + '": {"cantidad":"' + cantidad + '","unidadMedida":"' + unidadMedida + '","detalle":"' + detalle + '","precioUnitario":"' + precioUnitario + '","montoTotal":"' + montoTotal + '","subtotal":"' + subTotal + '","montoTotalLinea":"' + montoTotalLinea + '","montoDescuento":"' + descuento + '","naturalezaDescuento":"NA"}';
            } else {
                json = json + ',"' + i + '": {"cantidad":"' + cantidad + '","unidadMedida":"' + unidadMedida + '","detalle":"' + detalle + '","precioUnitario":"' + precioUnitario + '","montoTotal":"' + montoTotal + '","subtotal":"' + subTotal + '","montoTotalLinea":"' + montoTotalLinea + '","montoDescuento":"' + descuento + '","naturalezaDescuento":"NA"}';
                // GET THE CELLS COLLECTION OF THE CURRENT ROW.
            }
        } else if (cantidadDescuento === 0 && cantidadImpuesto !== 0) {
            if (json === '') {
                json = '"' + i + '": {"cantidad":"' + cantidad + '","unidadMedida":"' + unidadMedida + '","detalle":"' + detalle + '","precioUnitario":"' + precioUnitario + '","montoTotal":"' + montoTotal + '","subtotal":"' + subTotal + '","montoTotalLinea":"' + montoTotalLinea + '","impuesto":{"1": {"codigo":"' + codigoImpuesto + '","tarifa":"' + cantidadImpuesto + '","monto":"' + motoImpuesto + '"}}}';
            } else {
                json = json + ',"' + i + '": {"cantidad":"' + cantidad + '","unidadMedida":"' + unidadMedida + '","detalle":"' + detalle + '","precioUnitario":"' + precioUnitario + '","montoTotal":"' + montoTotal + '","subtotal":"' + subTotal + '","montoTotalLinea":"' + montoTotalLinea + '","impuesto":{"1": {"codigo":"' + codigoImpuesto + '","tarifa":"' + cantidadImpuesto + '","monto":"' + motoImpuesto + '"}}}';
                // GET THE CELLS COLLECTION OF THE CURRENT ROW.
            }
        } else if (cantidadDescuento !== 0 && cantidadImpuesto !== 0) {
            if (json === '') {
                json = '"' + i + '": {"cantidad":"' + cantidad + '","unidadMedida":"' + unidadMedida + '","detalle":"' + detalle + '","precioUnitario":"' + precioUnitario + '","montoTotal":"' + montoTotal + '","subtotal":"' + subTotal + '","montoTotalLinea":"' + montoTotalLinea + '","montoDescuento":"' + descuento + '","naturalezaDescuento":"NA","impuesto":{"1": {"codigo":"' + codigoImpuesto + '","tarifa":"' + cantidadImpuesto + '","monto":"' + motoImpuesto + '"}}}';
            } else {
                json = json + ',"' + i + '": {"cantidad":"' + cantidad + '","unidadMedida":"' + unidadMedida + '","detalle":"' + detalle + '","precioUnitario":"' + precioUnitario + '","montoTotal":"' + montoTotal + '","subtotal":"' + subTotal + '","montoTotalLinea":"' + montoTotalLinea + '","montoDescuento":"' + descuento + '","naturalezaDescuento":"NA","impuesto":{"1": {"codigo":"' + codigoImpuesto + '","tarifa":"' + cantidadImpuesto + '","monto":"' + motoImpuesto + '"}}}';
                // GET THE CELLS COLLECTION OF THE CURRENT ROW.
            }
        }
    }
    json = "{" + json + "}";


    return json;
}
