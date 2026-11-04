
function Api_certiUp_call() {
    var formData = new FormData();
    formData.append('w', 'fileUploader');
    formData.append('r', 'subir_certif');
    formData.append('iam', localStorage.getItem('userName'));
    formData.append('sessionKey', localStorage.getItem('sessionKey'));
    formData.append('fileToUpload', $('#fileToUpload')[0].files[0]);
    API_PostRequestFormData(formData,
        function (data) {
            if (data.resp !== 'null') {
                var user = [];
                user.w = 'admin';
                var client_id = $("#client_id").val();
                if (client_id == 'api-stag') {
                    user.r = 'company_stag_users';
                } else if (client_id === 'api-prod') {
                    user.r = 'company_prod_users';
                }
                console.log(data.resp);
                var fileCode = data.resp.downloadCode;
                user.downloadCode = fileCode;
                user.userName = $("#userName").val();
                user.password = $("#password").val();
                user.pinCerti = $("#pinCerti").val();
                user.iam = localStorage.getItem('userName');
                user.sessionKey = localStorage.getItem('sessionKey');
                API_PostRequest(user,
                    function (data) {
                        if (data.resp == "ok") {
                            sweetAlert({title: "Registro de credenciales exitoso!", text: "Se guardo con exito los credenciales\n", type: "success"});
                        } else {
                            sweetAlert({title: "Error al guardar!", text: "Se presento un error al guardar\nPor favor refrescar la paguina y intentar de nuevo", type: "error"});
                        }
                    },
                    function (data) {
                        sweetAlert({title: "Error!", text: "Error intentando registrar.\n", type: "error"});
                    });
            } else {
                sweetAlert({title: "Error al guardar!", text: "Se presento un error al guardar el certificado", type: "error"});
            }
        },
        function (data) {
            sweetAlert({title: "Error!", text: "Error intentando guardar certificados.\n", type: "error"});
        });

}
