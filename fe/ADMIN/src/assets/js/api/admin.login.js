
function Api_companny_logIn_call() {

    var user = [];
    user.userName = $("#username").val();
    user.pwd = $("#password").val();
    user.idMasterUser = $("#idMasterUser").val();

    Api_companny_login(user,
        function (data) {
            Api_setLocalStorage('idMasterUser', $("#idMasterUser").val());
            var user = [];
            user.w = 'admin';
            user.r = 'companny_getMyInfo';
            user.iam = localStorage.getItem('userName');
            user.sessionKey = localStorage.getItem('sessionKey');
            user.idMasterUser = localStorage.getItem('idMasterUser');
            API_PostRequest(user,
                function (data) {
                    if (typeof data.resp == 'object') {
                        for (i = 0; i < data.resp.length; i++) {
                            Api_setLocalStorage('terminal', data.resp[i].terminal);
                            Api_setLocalStorage('nombreSucursal', data.resp[i].nombreTerminal);
                            Api_setLocalStorage('sucursal', data.resp[i].sucursal);
                            Api_setLocalStorage('nombreTerminal', data.resp[i].nombreTerminal);
                            iGoTo("pages_dashboard_api.php");
                        }
                    } else {
                        console.log("Error recuperando los clientes");
                    }
                },
                function (data) {
                    sweetAlert({title: "Ocurrio un error cargando los clientes", type: "error"});
                });


        },
        function (data) {
            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
        });
}

function Api_logIn_call() {

    var user = [];
    user.userName = $("#username").val();
    user.pwd = $("#password").val();

    Api_login(user,
        function (data) {
            sweetAlert({title: "Bienvenido!", text: "Bienvenido al sistema\n" + data.userName, type: "success"});
            iGoTo("/ADMIN/src/pages_dashboard_api.php");
        },
        function (data) {
            sweetAlert({title: "Error!", text: "Error intentando ingresar.\n", type: "error"});
        });
}


