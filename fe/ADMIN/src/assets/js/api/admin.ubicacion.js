
function Api_getCanton(idProvince, selectID) {

    var user = [];
    user.w = 'admin';
    user.r = 'get_cantons';
    user.idProvince = idProvince;
    $("#" + selectID).empty();
    API_PostRequest(user,
        function (data) {
            if (typeof data.resp == 'object') {
                for (i = 0; i < data.resp.length; i++) {
                    $("#" + selectID).append('<option value="' + data.resp[i].idCanton + '">' + data.resp[i].nombreCanton + '</option>');
                }
            } else {
                console.log("Error recuperando las provincias");
            }
        },
        function (data) {
            sweetAlert({title: "Ocurrio un error cargando las provincias", type: "error"});
        });
}

