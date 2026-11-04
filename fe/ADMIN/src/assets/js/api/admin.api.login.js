
function API_checkAdminLogin(success, error, timeout) {
    var req = {
            w: "users",
            r: "users_get_my_details",
            iam: localStorage.getItem('userName'),
            sessionKey: localStorage.getItem('sessionKey')
        }
    ;
    API_PostRequest(req,
        function (data) {
            if (data.resp != ERROR_USER_WRONG_LOGIN_INFO && data.resp != ERROR_USER_ACCESS_DENIED && data.resp != ERROR_BAD_REQUEST) {
                if (success != null) {
                    API_debug("It seems we are logged in as " + data.resp.userName);
                    api_user = data.resp.userName;
                    success(data);
                }
            } else {
                if (error != null) {
                    error(data);
                }
            }
        },
        function () {
            API_debug("Exec error func");
            if (error != null) {
                error();
            }
        },
        function () {
            API_debug("Exec callback func");
            if (timeout != null) {
                timeout();
            }
        });
}



/*********************************************/
/* Function to check login users             */
/* Req success, error                        */
/*********************************************/
function API_checkLogin(success, error, timeout) {
    var req = {
            w: "admin",
            r: "companny_users_getMyDetails",
            iam: localStorage.getItem('userName'),
            sessionKey: localStorage.getItem('sessionKey'),
            idMasterUser: localStorage.getItem('idMasterUser')
        }
    ;
    API_PostRequest(req,
        function (data) {
            if (data.resp != ERROR_USER_WRONG_LOGIN_INFO && data.resp != null && data.resp != ERROR_USER_ACCESS_DENIED && data.resp != ERROR_BAD_REQUEST) {
                if (success != null) {
                    API_debug("It seems we are logged in as " + data.resp.userName);
                    api_user = data.resp.userName;
                    success(data);
                }
            } else {
                if (error != null) {
                    error(data);
                } else {
                    iGoTo("./user_login.html");
                }
            }
        },
        function () {
            API_debug("Exec error func");
            if (error != null) {
                error();
            }
        },
        function () {
            API_debug("Exec callback func");
            if (timeout != null) {
                timeout();
            }
        });
}
function API_companny_registerUser(userData, success, error) {

    var req = {
        w: "admin",
        r: "companny_users_register",
        fullName: userData.fullName,
        userName: userData.userName,
        email: userData.userEmail,
        pwd: userData.pwd,
        about: userData.about,
        country: userData.userCountry,
        idMasterUser: userData.idMasterUser,
        settings: userData.settings
    };
    API_PostRequest(req,
        function (d) {
            success(d);
        }, function (d) {
            error(d);
        });
}


function API_registerUser(userData, success, error) {

    var req = {
        w: "users",
        r: "users_register",
        fullName: userData.fullName,
        userName: userData.userName,
        email: userData.userEmail,
        pwd: userData.pwd,
        about: userData.about,
        country: userData.userCountry
    };
    API_PostRequest(req,
        function (d) {
            API_setLocalStorage('userName', d.resp.userName);
            API_setLocalStorage('sessionKey', d.resp.sessionKey);
            var user = [];
            user.w = 'apiadmin';
            user.r = 'copy_master_tables';
            user.iam = localStorage.getItem('userName');
            user.sessionKey = localStorage.getItem('sessionKey');

            API_PostRequest(user,
                function (data) {
                    if (typeof data.resp == '1') {

                    } else {
                        console.log("Error copiando tablas maestras");
                    }
                },
                function (data) {
                    sweetAlert({title: "Ocurrio un error en la configuracion inicial", type: "error"});
                });

            success(d);
        }, function (d) {
            error(d);
        });
}


/*********************************************/
/* Function to create users                   */
/* Req userData, func success, func error    */
/*********************************************/
function API_login(userData, success, error) {
    var req = {
        w: "users",
        r: "users_log_me_in",
        userName: userData.userName,
        pwd: userData.pwd
    };
    API_PostRequest(req,
        function (data) {
            if (data.resp != ERROR_USER_WRONG_LOGIN_INFO) {
                API_setLocalStorage('idMasterUser', data.resp.idUser);
                API_setLocalStorage('userName', data.resp.userName);
                API_setLocalStorage('sessionKey', data.resp.sessionKey);

                if (success != null) {
                    success(data);
                }
            } else {
                if (error != null) {
                    error(data);
                }
            }
        },
        function (data) {
            if (error != null) {
                error(data);
            }
        });
}
