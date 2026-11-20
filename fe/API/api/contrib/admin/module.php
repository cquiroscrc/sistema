<?php

/** @file module.php
 * A brief file description.
 * A more elaborated file description.
 */
/** \addtogroup Core 
 *  @{
 */
/**
 * \defgroup Module
 * @{
 */
global $compannyUser;

/**
 * Boot up procedure
 */
function admin_bootMeUp() {
    // Just booting up
    companny_users_loadCurrentUser();
}

function companny_users_loadCurrentUser() {
    global $compannyUser;

    # If I am running on emebed mode I don't have any users, so I will just load it from the session
    $user = users_load(array('userName' => params_get('iam', '')));
    /*
      if(conf_get('embeded', 'core', false)){
      $user = users_createBasic();
      $tmpUserId = users_confirmSessionKey();
      $user->idUser = $tmpUserId;
      }else{
      }
     */
}

/**
 * Init function
 */
function admin_init() {
    
    $fileName='admin.php';

    $paths = array(
        array(
            'r' => 'info',
            'action' => 'module_info',
            'access_params' => 'accessName',
            'file' =>  $fileName
        ),
        array(
            'r' => 'copy_master_tables',
            'action' => 'copyMasterTables',            
            'file' =>  $fileName
        ),
        array(
            'r' => 'getDocElec',        //Obtiene lista de documentos electronicos filtrados por fecha
            'action' => 'getDocElec',            
            'file' =>  $fileName,
            'params' => array(
                array("key" => "fechaInicio", "def" => "", "req" => false),
                array("key" => "fechaFinal", "def" => "", "req" => false)
            ),
        ),
        array(
            'r' => 'getConsecutivoPorTipoDoc',        //Obtiene lista de documentos electronicos filtrados por fecha
            'action' => 'getConsecutivoPorTipoDoc',            
            'file' =>  $fileName,
            'params' => array(
                array("key" => "CodigoConsecutivo", "def" => "", "req" => false),                
            ),
        ),
        array(
            'r' => 'companny_add_master_Consecutive',
            'action' => 'companny_add_master_Consecutive',                        
            'file' =>  $fileName
        ),
        array(
            'r' => 'getSucursales',
            'action' => 'getSucursales',
            'access_params' => 'accessName',
            'file' =>  $fileName
        ),
        array(
            'r' => 'addSucursales',
            'action' => 'addSucursales',

            'access_params' => 'accessName',
            'params' => array(
                array("key" => "numeroSucursal", "def" => "", "req" => true),
                array("key" => "nombreSucursal", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'add_terminal',
            'action' => 'addTerminal',

            'access_params' => 'accessName',
            'params' => array(
                array("key" => "numeroTerminal", "def" => "", "req" => true),
                array("key" => "nombreTerminal", "def" => "", "req" => true),
                array("key" => "idSucursal", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getTerminales',
            'action' => 'getTerminales',

            'access_params' => 'accessName',
            'params' => array(
                array("key" => "idSucursal", "def" => "", "req" => false)
            ),
            'file' =>  $fileName
        ),
        
        array(
            'r' => 'get_active_receiver',
            'action' => 'getActiveReceiver',

            'access_params' => 'accessName',
            'file' =>  $fileName
        ),
        array(
            'r' => 'get_receiver_by_id',
            'action' => 'getReceiverById',

            'access_params' => 'accessName',
            'params' => array(
                array("key" => "idReceptor", "def" => "", "req" => true),
                array("key" => "idMasterUser", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getTiquetes',
            'action' => 'getTiquetes',

            'access_params' => 'accessName',
            'params' => array(
                array("key" => "env", "def" => "", "req" => true),
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getProductByCode',
            'action' => 'getProductByCode',

            'access_params' => 'accessName',
            'params' => array(
                array("key" => "codigo", "def" => "", "req" => true),
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "sucursal", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'get_inventory',
            'action' => 'getInventory',

            'access_params' => 'accessName',
            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "sucursal", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'backup_user',
            'action' => 'backUpUser',

            'access_params' => 'accessName',
            'file' =>  $fileName
        ),
        array(
            'r' => 'getTypeOfId',
            'action' => 'getTypeOfId',
            'access' => 'users_openAccess',
            'file' =>  $fileName
        ),
        array(
            'r' => 'GetProvincias',
            'action' => 'GetProvincias',
            'params' => array(
                array("key" => "idProvincia", "def" => "", "req" => false)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'GetCantones',
            'action' => 'GetCantones',
            'params' => array(
                array("key" => "idProvincia", "def" => "", "req" => false),
                array("key" => "idCanton", "def" => "", "req" => false)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'GetDistritos',
            'action' => 'GetDistritos',
            'params' => array(
                array("key" => "idProvincia", "def" => "", "req" => false),
                array("key" => "idCanton", "def" => "", "req" => false),
                array("key" => "idDistrito", "def" => "", "req" => false)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'GetBarrios',
            'action' => 'GetBarrios',
            'params' => array(
                array("key" => "idProvincia", "def" => "", "req" => false),
                array("key" => "idCanton", "def" => "", "req" => false),
                array("key" => "idDistrito", "def" => "", "req" => false),
                array("key" => "idBarrio", "def" => "", "req" => false)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'deleteReceiver',
            'action' => 'deleteReceiver',
            'params' => array(
                array("key" => "idReceptor", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'companyStagUsers',
            'action' => 'companyStagUsers',
            'params' => array(
                array("key" => "userName", "def" => "", "req" => true),
                array("key" => "password", "def" => "", "req" => true),
                array("key" => "pinCerti", "def" => "", "req" => true),
                array("key" => "downloadCode", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'companyChangeEnv',
            'action' => 'companyChangeEnv',

            'params' => array(
                array("key" => "envProduccion", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'companySetEnv',
            'action' => 'companySetEnv',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'companyGetEnv',
            'action' => 'companyGetEnv',

            'params' => array(
                array("key" => "idMasteruser", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'companyProdUsers',
            'action' => 'companyProdUsers',

            'params' => array(
                array("key" => "userName", "def" => "", "req" => true),
                array("key" => "password", "def" => "", "req" => true),
                array("key" => "pinCerti", "def" => "", "req" => true),
                array("key" => "downloadCode", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getStagCredentials',
            'action' => 'getStagCredentials',

            'file' =>  $fileName
        ),
        array(
            'r' => 'getProdCredentials',
            'action' => 'getProdCredentials',
            'file' =>  $fileName
        ),
        array(
            'r' => 'getProdCompannyCredentials',
            'action' => 'getProdCompannyCredentials',
            'file' =>  $fileName
        ),
        array(
            'r' => 'addCompannyReceiver',
            'action' => 'addCompannyReceiver',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "nombreCliente", "def" => "", "req" => true),
                array("key" => "numeroCedula", "def" => "", "req" => true),
                array("key" => "tipoCedula", "def" => "", "req" => true),
                array("key" => "telefono", "def" => "", "req" => false),
                array("key" => "idProvincia", "def" => "", "req" => true),
                array("key" => "idCanton", "def" => "", "req" => true),
                array("key" => "idDistrito", "def" => "", "req" => true),
                array("key" => "idBarrio", "def" => "", "req" => true),
                array("key" => "otrasSenas", "def" => "", "req" => true),
                array("key" => "nombreComercial", "def" => "", "req" => true),
                array("key" => "correoPrincipal", "def" => "", "req" => false),
                array("key" => "copiasCorreo", "def" => "", "req" => false),
                array("key" => "numeroFax", "def" => "", "req" => false),
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getStagCompannyCredentials',
            'action' => 'getStagCompannyCredentials',
            'access' => 'companny_users_loggedIn',
            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'companyAddTicket',
            'action' => 'companyAddTicket',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "clave", "def" => "", "req" => true),
                array("key" => "consecutivo", "def" => "", "req" => true),
                array("key" => "estado", "def" => "", "req" => true),
                array("key" => "xmlEnviadoBase64", "def" => "", "req" => true),
                array("key" => "tipoDocumento", "def" => "", "req" => true),
                array("key" => "respuestaMHBase64", "def" => "", "req" => false),
                array("key" => "idReceptor", "def" => "", "req" => false),
                array("key" => "env", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'compannyUpdateConsecutive',
            'action' => 'compannyUpdateConsecutive',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "tipoDocumento", "def" => "", "req" => true),
                array("key" => "env", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getCompannyInformation',
            'action' => 'getCompannyInformation',
            'params' => array(
                array("def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getCompannyInformationAdmin',
            'action' => 'getCompannyInformationAdmin',
            'file' =>  $fileName
        ),
        array(
            'r' => 'compannyUpdateTipoCambio',
            'action' => 'compannyUpdateTipoCambio',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "tipoCambio", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'compannyUpdateLocation',
            'action' => 'compannyUpdateLocation',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "idProvincia", "def" => "", "req" => true),
                array("key" => "idCanton", "def" => "", "req" => true),
                array("key" => "idDistrito", "def" => "", "req" => true),
                array("key" => "idBarrio", "def" => "", "req" => true),
                array("key" => "sennas", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'compannyUpdateInformation',
            'action' => 'compannyUpdateInformation',
            'params' => array(
                array("key" => "NOMBRE", "def" => "", "req" => true),
                array("key" => "NOMBRE_COMERCIAL", "def" => "", "req" => true),
                array("key" => "CODIGO_PAIS", "def" => "", "req" => true),
                array("key" => "TELEFONO", "def" => "", "req" => true),
                array("key" => "TELEFONO2", "def" => "", "req" => false),
                array("key" => "CORREO", "def" => "", "req" => false),
                array("key" => "GLN", "def" => "", "req" => false),
                array("key" => "TIPO_NIT", "def" => "", "req" => true),
                array("key" => "NIT", "def" => "", "req" => true),
                array("key" => "EMAIL_DOC_ELECTRONICO", "def" => "", "req" => true),
                array("key" => "COD_PAIS_ISO", "def" => "", "req" => true),
                array("key" => "PROVINCIA", "def" => "", "req" => true),
                array("key" => "DISTRITO", "def" => "", "req" => false),
                array("key" => "BARRIO", "def" => "", "req" => false),
                array("key" => "OTRAS_SENAS", "def" => "", "req" => false),
                array("key" => "NORMATIVA", "def" => "", "req" => true),
                array("key" => "FECHA_NORMATIVA", "def" => "", "req" => false)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getCompannyLocationInformation',
            'action' => 'getCompannyLocationInformation',
            'params' => array(
                array("key" => "idProvincia", "def" => "", "req" => true),
                array("key" => "idCanton", "def" => "", "req" => true),
                array("key" => "idDistrito", "def" => "", "req" => true),
                array("key" => "idBarrio", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'GetTiposIdentificacion',
            'action' => 'GetTiposIdentificacion',
            'file' =>  $fileName
        ),
        array(
            'r' => 'insertToLogTable',
            'action' => 'insertToLogTable',
            'params' => array(
                array("key" => "json", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'compannygetMyInfo',
            'action' => 'compannygetMyInfo',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'compannyUsersGetMyDetails',
            'action' => 'compannyUsersGetMyDetails',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true)
            ),
            'file' => 'companny_user.php'
        ),
        array(
            'r' => 'companny_getMyConsecutive',
            'action' => 'companny_getMyConsecutive',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "tipoComprobante", "def" => "", "req" => true),
                array("key" => "env", "def" => "", "req" => true)
            ),
            'file' =>  $fileName
        ),
        array(
            'r' => 'companny_users_register',
            'action' => 'companny_users_registerNew',

            'params' => array(
                array("key" => "fullName", "def" => "", "req" => true),
                array("key" => "userName", "def" => "", "req" => true),
                array("key" => "email", "def" => "", "req" => true),
                array("key" => "about", "def" => "", "req" => false),
                array("key" => "country", "def" => "", "req" => true),
                array("key" => "pwd", "def" => "", "req" => true),
                array("key" => "idMasterUser", "def" => "", "req" => true)
            ),
            'file' => 'companny_user.php'
        ),
        array(
            'r' => 'companny_users_get_my_details',
            'action' => 'companny_users_getMyDetails',

            'params' => array(array("key" => "idMasterUser", "def" => "", "req" => false)),
            'file' => 'companny_user.php'
        ),
        array(
            'r' => 'get_tipo_impuesto',
            'action' => 'getTipoImpuesto',

            'params' => array(array("key" => "idMasterUser", "def" => "", "req" => false)),
            'file' =>  $fileName
        ),
        array(
            'r' => 'getUnid',
            'action' => 'getUnid',

            'params' => array(array("key" => "idMasterUser", "def" => "", "req" => false)),
            'file' =>  $fileName
        ),
         
        array(
            'r' => 'companny_users_recover_pwd',
            'action' => 'companny_users_recoverPwd',

            'params' => array(
                array("key" => "userName", "def" => "", "req" => true),
                array("key" => "idMasterUser", "def" => "", "req" => true)
            ),
            'file' => 'companny_user.php'
        ),
        array(
            'r' => 'companny_users_update_profile',
            'action' => 'companny_users_updateProfile',

            'file' => 'companny_user.php'
        ),
        array(
            'r' => 'users_log_me_out',
            'action' => 'companny_users_logMeOut',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true)
            ),
            'file' => 'companny_user.php'
        ),
        array(
            'r' => 'companny_users_logMeIn',
            'action' => 'companny_users_logMeIn',

            'params' => array(
                array("key" => "idMasterUser", "def" => "", "req" => true),
                array("key" => "userName", "def" => "", "req" => true),
                array("key" => "pwd", "def" => "", "req" => true)
            ),
            'file' => 'companny_user.php'
        )
    );

    return $paths;
}

/* * *********************************************** */
//In the access you can use users_openAccess if you want anyone can use the function
// or users_loggedIn if the user must be logged in
/* * *********************************************** */

/**
 * Get the perms for this module
 */
function fileUploader_access() {

    $perms = array(
        array(
            # A human readable name
            'name' => 'Do something with this module',
            # Something to remember what it is for
            'description' => 'What can be achieved with this permission',
            # Internal machine name, no spaces, no funny symbols, same rules as a variable
            # Use yourmodule_ prefix
            'code' => 'mymodule_access_one',
            # Default value in case it is not set
            'def' => false, //Or true, you decide
        ),
    );
}

/**@}*/
/** @}*/
