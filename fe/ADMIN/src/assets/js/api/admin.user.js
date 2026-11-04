
function Api_recover_pwd() {

    var user = [];
    user.w = 'users';
    user.r = 'users_recover_pwd';
    user.userName = $("#userName").val();
    API_PostRequest(user,
        function (data) {
            if (data.resp == 1) {
                sweetAlert({title: "Se envio un correo electronico con la nueva contraseña", type: "success"});
                $("#userName").val("");
            } else {
                sweetAlert({title: "Error!", text: "Error intentando rescuperar contraseña.\n", type: "error"});
            }
        },
        function (data) {
            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
        });
}

function Api_recover_user_pwd() {

    var user = [];
    user.w = 'admin';
    user.r = 'companny_users_recover_pwd';
    user.userName = $("#userName").val();
    user.idMasterUser = $("#idMasterUser").val();
    API_PostRequest(user,
        function (data) {
            if (data.resp == 1) {
                sweetAlert({title: "Se envio un correo electronico con la nueva contraseña", type: "success"});
                $("#userName").val("");
            } else {
                sweetAlert({title: "Error!", text: "Error intentando rescuperar contraseña.\n", type: "error"});
            }
        },
        function (data) {
            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
        });
}

