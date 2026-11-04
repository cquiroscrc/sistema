
//Esta funcion resive 3 parametros
//table=tabla de donde vamos a contar
//columna=columnas a sumar
//id=id de html que vamos a remplazar
function Api_sumar_call(table, columna, id) {
    var cantidadLineas = document.getElementById(table).getElementsByTagName("tr").length;
    var cantidadColumnas = document.getElementById(table).rows[0].getElementsByTagName("td").length - 1;
    var data = [];
    for (i = 0; i < cantidadLineas; i++) {
        data[i] = [];
        for (j = 0; j < cantidadColumnas; j++) {
            data[i][j] = document.getElementById(table).rows[i].cells[j].getElementsByTagName('input')[0].value;
        }
    }
    var total = 0;
    columna = parseInt(columna);
    for (r = 0; r < cantidadLineas; r++) {
        total = parseInt(total) + parseInt(data[r][columna]);
    }
    document.getElementById(id).value = total;
}

function blockUIElement(el,mensaje,state){
    if(state=='block'){
        var msg=mensaje;

        $(el).block({
            message: ''+mensaje+'',
            showOverlay: true,
            cursor:  'wait',
            centerX: true, // <-- only effects element blocking (page block controlled via css above)
            centerY: true,
            ignoreIfBlocked: false,
            overlayCSS: {
                backgroundColor: '#cccccc'
            },
            css: {
                width: '25em',
                top: '10px',
                left: '',
                right: '10px',
                border: '1px',
                padding: '5px',
                backgroundColor: '#b3b3cc', //del mensaje
                color: '#ffffff', //del mensaje
                fontsize:'11px',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                fontSize:'20px'
            }
        });
    }
    else
    {
        $(el).unblock();
    }

}

function SetSelected(elem, val,texto){



    $('#'+elem+' option ').each(
        function(i,d){
        //console.log('searching match for '+ elem + '  ' + d.value + ' equal to '+ val +' or text equal to ' +text);

            if(texto.length > 0){
            if($.trim(d.value).toLowerCase() == $.trim(val).toLowerCase() && $.trim(d.text).toLowerCase() == $.trim(texto).toLowerCase()  ){
                $('#'+elem).prop('selectedIndex', i);
            }
        }
        else {
            if($.trim(d.value).toLowerCase() == $.trim(val).toLowerCase()   ){
                $('#'+elem).prop('selectedIndex', i);
            }
        }

    });
}
//Esta funcion resive 3 parametros
//table=tabla de donde vamos a contar
//columna=columnas a sumar
//id=id de html que vamos a remplazar
function Api_CalculaSubTotal(table, columna, id) {
    var cantidadLineas = document.getElementById(table).getElementsByTagName("tr").length;
    var cantidadColumnas = document.getElementById(table).rows[0].getElementsByTagName("td").length - 1;
    var data = [];
    for (i = 0; i < cantidadLineas; i++) {
        data[i] = [];
        for (j = 0; j < cantidadColumnas; j++) {
            data[i][j] = document.getElementById(table).rows[i].cells[j].getElementsByTagName('input')[0].value;
        }
    }
    var subTotal = 0;
    var totalDescuento = 0;
    var totalImpuesto = 0;
    columna = parseInt(columna);
    for (r = 0; r < cantidadLineas; r++) {
        var precioTotal = (data[r][2] * parseInt(data[r][3])); // cantidad * unidad
        var descuento = precioTotal * (parseInt(data[r][4]) / 100);
        totalDescuento = totalDescuento + descuento;
        var IV = precioTotal * parseInt(data[r][5]) / 100;
        totalImpuesto = totalImpuesto + IV;
        var subTotal = precioTotal - descuento + IV;
    }
    document.getElementById(id).value = subTotal;
}




//Esta funcion resive 3 parametros
//table=tabla de donde vamos a contar
//columna=columnas a sumar
//id=id de html que vamos a remplazar
function Api_total_descuento(table, columna, id) {
    var cantidadLineas = document.getElementById(table).getElementsByTagName("tr").length;
    var cantidadColumnas = document.getElementById(table).rows[0].getElementsByTagName("td").length - 1;
    var data = [];
    for (i = 0; i < cantidadLineas; i++) {
        data[i] = [];
        for (j = 0; j < cantidadColumnas; j++) {
            data[i][j] = document.getElementById(table).rows[i].cells[j].getElementsByTagName('input')[0].value;
        }
    }
    var subTotal = 0;
    var totalDescuento = 0;
    columna = parseInt(columna);
    for (r = 0; r < cantidadLineas; r++) {
        var precioTotal = (data[r][2] * parseInt(data[r][3])); // cantidad * unidad
        var descuento = precioTotal * (parseInt(data[r][4]) / 100);
        totalDescuento = totalDescuento + descuento;
    }
    document.getElementById(id).value = totalDescuento;
}

function Api_total_impuesto(table, columna, id) {
    var cantidadLineas = document.getElementById(table).getElementsByTagName("tr").length;
    var cantidadColumnas = document.getElementById(table).rows[0].getElementsByTagName("td").length - 1;
    var data = [];
    for (i = 0; i < cantidadLineas; i++) {
        data[i] = [];
        for (j = 0; j < cantidadColumnas; j++) {
            data[i][j] = document.getElementById(table).rows[i].cells[j].getElementsByTagName('input')[0].value;
        }
    }
    var subTotal = 0;
    var totalDescuento = 0;
    var totalImpuesto = 0;
    columna = parseInt(columna);
    for (r = 0; r < cantidadLineas; r++) {
        var precioTotal = (data[r][2] * parseFloat(data[r][3])); // cantidad * unidad
        var descuento = precioTotal * (parseFloat(data[r][4]) / 100);
        totalDescuento = totalDescuento + descuento;
        subTotal = precioTotal - descuento;
        var IV = subTotal * parseInt(data[r][5]) / 100;
        totalImpuesto = totalImpuesto + IV;

    }
    document.getElementById(id).value = totalImpuesto;
}


function Api_get_te_lines(table, appendTable, clave, consecutivo, nombreComercial, fecha, cedula, telefono) {

    $("#teNombreComercial").append(nombreComercial);
    $("#teFecha").append(fecha);
    $("#teClave").append("Clave: " + clave);
    $("#teConsucutivo").append("Consecutivo: " + consecutivo);
    $("#teCedula").append("Cedula: " + cedula);
    $("#teTel").append("TEL: " + telefono);
    $("#" + appendTable).empty();
    var cantidadLineas = document.getElementById(table).getElementsByTagName("tr").length;
    var cantidadColumnas = document.getElementById(table).rows[0].getElementsByTagName("td").length - 1;

    var data = [];
    for (i = 0; i < cantidadLineas; i++) {
        data[i] = [];
        for (j = 0; j < cantidadColumnas; j++) {
            data[i][j] = document.getElementById(table).rows[i].cells[j].getElementsByTagName('input')[0].value;

        }
    }
    for (r = 0; r < cantidadLineas; r++) {
        $("#" + appendTable).append('<tr><td class="cantidad">' + data[r][2] + '</td><td  class="producto">' + data[r][0] + '</td><td class="precio">' + data[r][3] + '</td></tr>');
    }



    $("#" + appendTable).append('<tr><td><br></td><td><br></td><td><br></td></tr><tr>');
    $("#" + appendTable).append('<tr><td>Total Venta:<br></td><td>' + $("#totalVenta").val() + '</td><td>' + $("#tipoMoneda").val() + '</td></tr>');
    $("#" + appendTable).append('<tr><td>Descuento Total:<br></td><td>' + $("#descuenteTotal").val() + '</td><td>' + $("#tipoMoneda").val() + '</td></tr>');
    $("#" + appendTable).append('<tr><td>Total Venta Neta:<br></td><td>' + $("#ventaNeta").val() + '</td><td>' + $("#tipoMoneda").val() + '</td></tr>');
    $("#" + appendTable).append('<tr><td>Impuesto Total:<br></td><td>' + $("#impuestoTotal").val() + '</td><td>' + $("#tipoMoneda").val() + '</td></tr>');
    $("#" + appendTable).append('<tr><td>Total:<br></td><td>' + $("#total").val() + '</td><td>' + $("#tipoMoneda").val() + '</td></tr>');

}


//Esta funcion resive 3 parametros
//table=tabla de donde vamos a contar
//columna=columnas a sumar
//id=id de html que vamos a remplazar
function Api_CalculaTotalGravadoExcento(table) {
    var totalMercansiaGravada = 0;
    var totalMercansiaExcento = 0;
    var totalServicioGravado = 0;
    var totalServioExcenta = 0;
    var totalGravado = 0;
    var totalExcento = 0;
    var totalVenta = 0;
    var subTotal = 0;
    var cantidadLineas = document.getElementById(table).getElementsByTagName("tr").length;
    var cantidadColumnas = document.getElementById(table).rows[0].getElementsByTagName("td").length - 1;
    var data = [];
    for (i = 0; i < cantidadLineas; i++) {
        data[i] = [];
        for (j = 0; j < cantidadColumnas; j++) {
            data[i][j] = document.getElementById(table).rows[i].cells[j].getElementsByTagName('input')[0].value;
        }
    }
    for (r = 0; r < cantidadLineas; r++) {
        var precioTotal = (data[r][2] * parseInt(data[r][3])); // cantidad * unidad
        if (data[r][1] === 'Sp' && data[r][5] === '0') {
            totalServioExcenta = totalServioExcenta + (data[r][2] * parseFloat(data[r][3])); // cantidad * unidad
            console.log("totalServicioExcento " + data[r][1] + " " + data[r][5] + " :" + totalServioExcenta);
        } else if (data[r][1] === 'Sp' && data[r][5] !== '0') {
            totalServicioGravado = totalServicioGravado + (precioTotal + (precioTotal * parseInt(data[r][5]) / 100)); // cantidad * unidad
            console.log("totalServicioGravado" + data[r][1] + " " + data[r][5] + " :" + totalServicioGravado);
        } else if (data[r][1] !== 'Sp' && data[r][5] === '0') {
            console.log("totalMercanciaExcento " + data[r][1] + " " + data[r][5] + " :" + totalMercansiaExcento);
            totalMercansiaExcento = totalMercansiaExcento + (data[r][2] * parseFloat(data[r][3])); // cantidad * unidad
        } else if (data[r][1] !== 'Sp' && data[r][5] !== '0') {
            console.log("totalMercanciaGravada " + data[r][1] + " " + data[r][5] + " :" + totalMercansiaGravada);
            totalMercansiaGravada = totalMercansiaGravada + (precioTotal + (precioTotal * parseInt(data[r][5]) / 100)); // cantidad * unidad
        }
    }
    totalGravado = totalServicioGravado + totalMercansiaGravada;
    totalExcento = totalServioExcenta + totalMercansiaExcento;
    totalVenta = totalGravado + totalExcento;

    $("#TotalServGravados").val(totalServicioGravado);
    $("#TotalServExentos").val(totalServioExcenta);
    // $("#impuestoTotal").val(totalGravado);
    $("#TotalMercanciasGravadas").val(totalMercansiaGravada);
    $("#TotalMercanciasExentas").val(totalMercansiaExcento);
    $("#TotalGravado").val(totalGravado);
    $("#TotalExento").val(totalExcento);
    $("#TotalServGravados").val(totalServicioGravado);
    $("#totalVenta").val(totalVenta);
}

//Esta funcion resive 3 parametros
//table=tabla de donde vamos a contar
//columna=columnas a sumar
//id=id de html que vamos a remplazar
function Api_CalculaTotal(table, columna, id) {
    var cantidadLineas = document.getElementById(table).getElementsByTagName("tr").length;
    var cantidadColumnas = document.getElementById(table).rows[0].getElementsByTagName("td").length - 1;
    var data = [];
    for (i = 0; i < cantidadLineas; i++) {
        data[i] = [];
        for (j = 0; j < cantidadColumnas; j++) {
            data[i][j] = document.getElementById(table).rows[i].cells[j].getElementsByTagName('input')[0].value;
        }
    }
    var subTotal = 0;
    columna = parseInt(columna);
    for (r = 0; r < cantidadLineas; r++) {
        var precioTotal = (data[r][2] * parseInt(data[r][3])); // cantidad * unidad
        var descuento = precioTotal * (parseInt(data[r][4]) / 100);
        var IV = precioTotal * parseInt(data[r][5]) / 100;
        var subTotal = precioTotal - descuento + IV;
    }
    document.getElementById(id).value = subTotal;
}


function Api_CalculaLineas(table) {
    var cantidadLineas = document.getElementById(table).getElementsByTagName("tr").length;
    var cantidadColumnas = document.getElementById(table).rows[0].getElementsByTagName("td").length - 1;
    var data = [];
    for (i = 0; i < cantidadLineas; i++) {
        data[i] = [];
        for (j = 0; j < cantidadColumnas; j++) {
            data[i][j] = document.getElementById(table).rows[i].cells[j].getElementsByTagName('input')[0].value;
        }
    }
    var subTotal = 0;
    var precioTotal = 0;
    for (r = 0; r < cantidadLineas; r++) {
        precioTotal = (data[r][2] * parseInt(data[r][3])); // cantidad * unidad
        var descuento = precioTotal * (parseInt(data[r][4]) / 100);
        var IV = precioTotal * parseInt(data[r][5]) / 100;
        var subTotal = precioTotal - descuento + IV;
        document.getElementById(table).rows[r].cells[6].getElementsByTagName('input')[0].value = precioTotal;
        document.getElementById(table).rows[r].cells[7].getElementsByTagName('input')[0].value = subTotal;
    }

}


function Api_formUpdate() {
    Api_sumar_call('tabla', 2, 'cantidad');

    Api_total_descuento('tabla', 4, 'descuenteTotal');
    Api_total_impuesto('tabla', 5, 'impuestoTotal');
    Api_CalculaLineas('tabla');
    Api_CalculaTotalGravadoExcento('tabla');
    $("#ventaNeta").val($("#totalVenta").val() - $("#descuenteTotal").val());
    $("#total").val(parseFloat($("#ventaNeta").val()) + parseFloat($("#impuestoTotal").val()));
}




