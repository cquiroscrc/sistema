function showDocElec(urlToConnect)
{
    var products=APIGetDocElec();
    
    $("#grid").kendoGrid({
        dataSource: {
            data: products,
            schema: {
                model: {
                    fields: {
                        TipoDoc: { type: "string" },
                        NumeroDoc: { type: "nustringmber" },
                        Enviado: { type: "string" },
                        Aceptado: { type: "string" },
                        FechaHora: { type: "date" },
                        NitReceptor: { type: "string" },
                        Estado: { type: "string" },
                        SelloSat: { type: "string" },
                        UltimoError: { type: "string" },
                        ErrorLocal: { type: "string" },
                        ErrorHacienda: { type: "string" },
                    }
                }
            },
            pageSize: 15
        },
        height: 550,
        scrollable: true,
        sortable: true,
        filterable: true,
        pageable: {
            input: true,
            numeric: false
        },
        columns: [
            { field: "TipoDoc", title: "Tipo", width: "130px" },
            { field: "NumeroDoc", title: "Documento", width: "130px" },            
            { field: "Enviado", title: "Enviado", width: "130px" },
            { field: "Aceptado", title: "Aceptado", width: "130px" },
            { field: "FechaHora", title: "Fecha", width: "130px" },
            { field: "NitReceptor", title: "NIT", width: "130px" },
            { field: "Estado", title: "Estado", width: "130px" },
            { field: "SelloSat", title: "Consecutivo", format: "{0:c}", width: "130px" },
            { field: "UltimoError", title: "Errores", width: "130px" },
            { field: "ErrorLocl", title: "Err/Loc", width: "130px" },
            { field: "ErrorHacienda", title: "Err/Hac.", width: "130px" },
        ]
    });

    return dataSource;
}