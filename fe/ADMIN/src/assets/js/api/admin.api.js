/*******************************/
/*            CONS             */
/*******************************/

// Users
var ERROR_NO_VALID_USER = "-300";
var ERROR_USER_WRONG_LOGIN_INFO = "-301";
var ERROR_USER_NO_VALID_SESSION = "-302"; // Not in use I think
var ERROR_USER_ACCESS_DENIED = "-303";
var ERROR_USER_EXISTS = "-304";
//Database
var ERROR_DB_NO_RESULTS_FOUND = "-200";
var ERROR_BAD_REQUEST = "-1";
var DEFAULT_INIT;
/*******************************/
/*          Settings           */
/*******************************/
//var api_url = "http://localhost/API_Hacienda/www/api.php";
var api_url = "http://localhost/API/www/api.php";
//var api_url = "http://192.168.0.118/API_Hacienda/www/api.php";
var api_front = "index.html";
var api_user = "";
var api_debugMode = true;
var api_title = "Paticas API";
var api_title_circule = "PA";
var api_favicon = 'https://crlibre.org/wp-content/uploads/2018/03/cropped-CRLibre-Logo_03-32x32.png';
var api_site_img = 'https://crlibre.org/wp-content/uploads/2018/03/cropped-CRLibre-Logo_15-1.png';

/*******************************/
/*          Loading Settings   */
/*******************************/
function bootSettings() {
    //set title of the page
    document.title = api_title;
    //set favicon of the site
    var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
    link.type = 'image/x-icon';
    link.rel = 'shortcut icon';
    link.href = api_favicon;
    document.getElementsByTagName('head')[0].appendChild(link);
    $("#siteTitle").html(api_title);
    $("#siteTitleCircule").html(api_title_circule);
    $("#siteIMG").attr("src", api_site_img);
}




/*********************************************/
/* Function set local storage                */
/* Req key, value                            */
/*********************************************/
function API_setLocalStorage(k, v) {
    API_debug("Saving in storage K: " + k + " V: " + v);
    localStorage.setItem(k, v);
}

/*********************************************/
/* Function set local storage                */
/* Req key                                   */
/* Return value                              */
/*********************************************/
function API_getLocalStorage(k) {
    var v = window.localStorage.getItem(k);
    API_debug("Getting from storage K: " + k + " GOT: " + v);
    return v;
}


/*********************************************/
/* Function to make post reqs                */
/* Req request data, func success, func error*/
/*********************************************/
// progress on transfers from the server to the client (downloads)
function updateProgress(oEvent) {
    if (oEvent.lengthComputable) {
        var percentComplete = oEvent.loaded / oEvent.total * 100;
        //console.log(" " + oEvent.loaded+' completado de '+ oEvent.total);
    } else {
        //console.log(" oEvent.lengthComputable =" +oEvent.lengthComputable );
    }
}

function transferComplete(evt) {
    //console.log("The transfer is complete.");
}

function transferFailed(evt) {
    console.log("An error occurred while transferring the file.");
}

function transferCanceled(evt) {
    console.log("The transfer has been canceled by the user.");
}

function loadEnd(e) {
    //console.log("The transfer finished (although we don't know if it succeeded or not).");
    //console.log(e);
    //blockUIElement(e,'','unblock')
}

function API_PostRequest(req, success, error, timeout = 180000, times = 0, async = true) {


    API_debug("Enviando POST a la url: " + api_url);
    /*generate the form*/
    var _data = new FormData();
    for (var key in req) {
        var value = req[key];
        API_debug(" to " + key + " -> " + value);
        _data.append(key, value);
    }

    //_data.append("iam", API_getLocalStorage('userName'));
    //_data.append("sessionKey", API_getLocalStorage('sessionKey'));

    var oReq = new XMLHttpRequest();

    //oReq.addEventListener("progress", updateProgress);
    //oReq.addEventListener("load", transferComplete);
    //oReq.addEventListener("error", transferFailed);
    //oReq.addEventListener("abort", transferCanceled);
    //oReq.addEventListener("loadend", loadEnd);

    oReq.open("POST", api_url, async);

    oReq.timeout = timeout;

    oReq.onload = function (oEvent) {
        if (oReq.readyState === 4) {
            if (oReq.status == 200) { //Exito
                var r = oReq.responseText;
                //console.log(r);
                r = JSON.parse(r);
                success(r);
                API_debug("Done!");

            } else {
                var r2 = oReq.responseText;
                API_debug("Error ocurrido:" + r2);
                error(r2);
            }
        }
    }

    oReq.ontimeout = function (e) {
        times = times + 1;
        if (times < 3) {
            //API_PostRequest(req, success, error, timeout, times);
            API_debug("Timeout " + times + ",intentando nuevamente");
        } else {
            API_doSomethingAfter(function () {
                API_debug("Timeout fallo.!!");
                //API_PostRequest(req, success, error, timeout, 0);
                API_debug("Funcion Ejecutada...");
            }, 5000);
        }
    }
    oReq.onerror = function (e) {
        console.error(oReq.statusText);
    };

    oReq.send(_data);
}


/*********************************************/
/* Function to make post reqs                */
/* Req request data, func success, func error*/
/*********************************************/
function API_PostRequestFormData(req, success, error, timeout, times) {
    timeout = 8000;
    times = 0;
    API_debug("Making a post request to " + api_url);
    /*generate the form*/
    var _data = new FormData();
    _data = req;
    _data.append("iam", API_getLocalStorage('userName'));
    _data.append("sessionKey", API_getLocalStorage('sessionKey'));
    var oReq = new XMLHttpRequest();
    oReq.open("POST", api_url, true);
    oReq.timeout = timeout;
    oReq.onload = function (oEvent) {
        if (oReq.status == 200) {
            var r = oReq.responseText;
            console.log(r);
            r = JSON.parse(r);
            success(r);
            API_debug("Done!");
        } else {
            var r = oReq.responseText;
            API_debug("There was an error");
            error(r);
        }
    };
    oReq.ontimeout = function (e) {
        times = times + 1;
        if (times < 3) {
            API_PostRequest(req, success, error, timeout, times);
            API_debug("Timeout " + times + ", lets try again");
        } else {
            API_doSomethingAfter(function () {
                API_debug("Timeout does not work, retrying in a sec");
                API_PostRequest(req, success, error, timeout, 0);
                API_debug("Function called...");
            }, 3000);
        }
    }
    oReq.send(_data);
}


function API_debug(msg) {
    if (api_debugMode) {
        console.log("[API] >> " + msg);
    }

}