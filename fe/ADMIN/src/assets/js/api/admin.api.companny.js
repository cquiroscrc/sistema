
function API_companny_login(userData, success, error) {
    var req = {
        w: "admin",
        r: "companny_users_logMeIn",
    };
    API_PostRequest(req,
        function (data) {
            if (data.resp != ERROR_USER_WRONG_LOGIN_INFO) {
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
