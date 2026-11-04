<?php

define('ADMIN_MODULE_VERSION', 'V.2.0');
define('DOC_ROOT', dirname(__FILE__, 4));

global $continuar;


if (PHP_SAPI === 'cli') {
    include_once DOC_ROOT . "/www/settings.php";    
    include_once DOC_ROOT . "/api44/core/conf.php";
    include_once DOC_ROOT . "/api44/core/error_handler.php";
    include_once DOC_ROOT . "/api44/core/checks.php";
    include_once DOC_ROOT . "/api44/core/grace.php";
    include_once DOC_ROOT . "/api44/core/params.php";
    include_once DOC_ROOT . "/api44/core/modules.php";
    include_once DOC_ROOT . "/api44/core/tools.php";
    include_once DOC_ROOT . "/api44/modules/db/module.php";
    include_once DOC_ROOT . "/api44/contrib/token/mhToken.php";
    include_once DOC_ROOT . "/api44/contrib/genXML/xmlGenerator.php";
    include_once DOC_ROOT . "/api44/contrib/firmarXML/firmar.php";
    include_once DOC_ROOT . "/api44/contrib/send/send.php";
    include_once DOC_ROOT . "/api44/contrib/fpdf/factura.php";
    include_once DOC_ROOT . "/api44/contrib/consultar/consultar.php";
    include_once DOC_ROOT . "/api44/contrib/template/template.php";
    require_once dirname(__FILE__, 5) . '/ADMIN/vendor/autoload.php';
    if (db_Connect()) {
        //echo "********************************************************************************************\n";
        //echo "    S i s t e m a   d e   E n v i o   d e   D o c u m e n t o s   E l e c t r o n i c o s     \n";
        //echo "********************************************************************************************\n";
        echo "*************************************INICIA PROCESO****************************************\n";
        Iniciar();
        echo "*************************************TERMINA PROCESO****************************************\n";

    } else {
        echo 'Error no se ha logrado conectar a la base de datos';
    }

}

/*
 * Function getDocElec()
 * Retorna un listado de documentos procesados
 */
function getDocElec()
{

    $fechaInicio = params_get("fechaInicio");
    $fechaFinal = params_get("fechaFinal");

    if (isset($fechaInicio) && isset($fechaFinal)) {
        $q = "SELECT  TIPO_DOC,NUMERO_DOC,ENVIADO,ACEPTADO,FECHA_HORA,NIT_RECEPTOR,ESTADO,SELLO_SAT,
        ULTIMO_ERROR,ERROR_LOCAL,ERROR_HACIENDA from dbo.DOC_ELECTRONICO_PROCESADO
        WHERE FECHA_HORA BETWEEN '$fechaInicio' and  '$fechaFinal' ";
        return sql_exec($q, 2, false, true);
    } else {
        return -1;
    }

}

/**
 * Generates a user hash (for passwords mostly)
 * @todo Use php's function
 */
function module_info()
{
    return array(
        "info" => "Modulo de interface para administracion",
        "version" => ADMIN_MODULE_VERSION,
    );

}

/*
 *       Guarda una bitacora de errores en la base de datos
 *
 */

/**
 *  Guarda en base de datos los errores
 *
 * @param string $Texto
 * @param string $Documento
 * @param string $TipoDoc
 */
function Bitacora(string $sessionID, string $Texto, string $Documento = "", string $TipoDoc = "")
{
    try {
        global $dbConn;
        //grace_debug($Texto);
        echo  "[" . date('d/m/Y H:i:s') . "] " ."|$Documento|". $Texto . "\n";
        $sql = "INSERT INTO dbo.BITACORA(SESSION_ID,TIPO,TEXTO,FECHA_HORA,DOCUMENTO,TIPO_DOCUMENTO)
                  VALUES(:sessionID,:Tipo,:Texto,SYSDATETIME(),:Documento,:TipoDoc)";
        $stm = $dbConn->prepare($sql);
        $stm->execute([':sessionID' => $sessionID,
            ':Tipo' => 'Debug',
            ':Texto' => $Texto,
            ':Documento' => $Documento,
            ':TipoDoc' => $TipoDoc]);

    } catch (PDOException $ex) {
        echo "Exception:" . $ex->getMessage() . " en documento " . $Documento . "de tipo " . $TipoDoc . " al insertar la bitacora" . "\n";
        grace_debug("Exception:" . $ex->getMessage() . " en documento " . $Documento . "de tipo " . $TipoDoc . " al insertar la bitacora");
    }
}

/**
 *       Guarda si esta en proceso un envio a hacienda
 *
 **/
function EnProceso(string $sessionID)
{

    try {

        $sql = "SELECT  isnull(EN_PROCESO,'N') from PROCESO (nolock)";
        $result = sql_exec($sql, 1, false, true);
        foreach ($result as $r) {
            if ($r === "SI") {
                return true;
            }

        }

        return false;
    } catch (Exception $ex) {
        Bitacora($sessionID, $ex->getMessage(), "", "");
        return true;
    }
}

/**
 *       Establece el estado del proceso de envio en SI o NO
 *
 **/
function SetEnProceso(string $sessionID, string $Estado)
{
    try {
        $existe = false;

        $sql = "SELECT isnull(EN_PROCESO,'N') from PROCESO (nolock)";
        $result = sql_exec($sql, 1, false, true);
        $existe = count($result) > 0;

        if (!$existe) {
            $rins = "INSERT INTO  PROCESO (EN_PROCESO) VALUES($Estado)";
            sql_exec($sql, 1, true, false);
        } else {
            $rupd = "UPDATE  PROCESO SET EN_PROCESO=$Estado";
            sql_exec($sql, 1, true, false);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "SetEnProceso Error: {$ex->getMessage()}({$ex->getLine()})", "", "");
        return "ERROR";
    }
}

/**
 * @param string $sessionID
 * @param string $Dato
 * @param string $Documento
 * @param string $TipoDoc
 *  Obtiene datos de la compañia
 *
 * @return string
 */
function getDatoCia(string $sessionID, string $Dato, string $Documento, string $TipoDoc): string
{

    try {
        $sql = "SELECT  $Dato   from COMPAÑIAS WHERE CONFIGURADA='SI'";
        $result = sql_exec($sql, 1, false, true);
        return $result[$Dato];

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "getDatoCia Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
        return "ERROR";
    }

}

/**
 *      Obtiene los totales de la factura ya creada
 *
 **/
function ObtenerTotales(string $sessionID, string $Documento, string $TipoDoc, string $TipoRetorno, string $Gravado): float
{
    $tr = $TipoRetorno === 'SERVICIOS' ? "='Sp'" : "<>'Sp'";
    $ts1 = $Gravado === 'SI' ? "<>0 and MontoExoneracionLinea = 0" : "=0";

    $sql = "SELECT sum(MontoGravado) as Monto from DOC_ELEC_DETALLE lin
         WHERE lin.nfac ='$Documento'
         AND UnidadMedida $tr
         AND ImpuestoTarifa $ts1";

    try {
        $result = sql_exec($sql, 1, false, true);
        if (count($result)) {
            return (float) $result["Monto"];
        } else {
            return (float) 0;
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "getDatoCia Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
        return (float) 0;
    }
}

function ObtenerDatoFACTURA(string $sessionID, string $dato, string $Documento, string $TipoDoc): string
{
    try {
        $sql = "SELECT $dato  from HFACTURAS WHERE nfac='$Documento'";
        $result = sql_exec($sql, 1, false, true);
        if (!is_null($result[$dato])) {
            foreach ($result as $ElDato) {
                return is_null($ElDato) ? "0" : $ElDato;
            }
        }
        return "0";
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ObtenerDatoFactura Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
        return "ERROR";
    }
}

function ObtenerDatoTabla(string $sessionID, string $Columna, string $Tabla, string $ColumnaFiltro, string $ValorFiltro): string
{
    try {
        $sql = "SELECT  $Columna from $Tabla WHERE $ColumnaFiltro='$ValorFiltro'";
        $result = sql_exec($sql, 1);
        if ($result != "-200") {
            foreach ($result as $dato) {
                return empty($dato) ? "" : $dato;
            }
        }
        return "0";
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ObtenerDatoTabla Error: {$ex->getMessage()}({$ex->getLine()})");
        return "ERROR";
    }
}

function ObtenerDatoCLIENTE(string $sessionID, string $dato, string $cliente, string $Documento, string $TipoDoc): string
{
    try {
        $sql = "SELECT  $dato from CLIENTES WHERE codigo='$cliente'";
        $result = sql_exec($sql, 1);
        foreach ($result as $ElDato) {
            return is_null($ElDato) ? '' : $ElDato;
        }

        return "";
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ObtenerDatoCliente: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
        return "ERROR";
    }
}

function setDatosEmisor(string $sessionID, string $Documento, string $TipoDoc)
{

    //Emisor
    try {

        $gln = getDatoCia($sessionID, "GLN", $Documento, $TipoDoc);
        $emisor_gln = (is_null($gln) or $gln == "") ? "0000000000000" : $gln;

        params_set("emisor_nombre", getDatoCia($sessionID, "NOMBRE", $Documento, $TipoDoc));
        params_set("emisor_tipo_identif", getDatoCia($sessionID, "TIPO_NIT", $Documento, $TipoDoc));
        params_set("emisor_num_identif", getDatoCia($sessionID, "NIT", $Documento, $TipoDoc));
        params_set("nombre_comercial", getDatoCia($sessionID, "NOMBRE_COMERCIAL", $Documento, $TipoDoc));
        params_set("emisor_gln", $emisor_gln);

        params_set("emisor_provincia", getDatoCia($sessionID, "DIVISION_GEOGRAFICA1", $Documento, $TipoDoc));
        params_set("emisor_canton", getDatoCia($sessionID, "DIVISION_GEOGRAFICA2", $Documento, $TipoDoc));
        params_set("emisor_distrito", getDatoCia($sessionID, "DIVISION_GEOGRAFICA3", $Documento, $TipoDoc));
        params_set("emisor_barrio", getDatoCia($sessionID, "DIVISION_GEOGRAFICA4", $Documento, $TipoDoc));
        params_set("emisor_otras_senas", getDatoCia($sessionID, "OTRAS_SENAS", $Documento, $TipoDoc));

        params_set("emisor_cod_pais_tel", getDatoCia($sessionID, "COD_PAIS_ISO", $Documento, $TipoDoc));
        params_set("emisor_tel", getDatoCia($sessionID, "TELEFONO_1", $Documento, $TipoDoc));
        params_set("emisor_cod_pais_fax", getDatoCia($sessionID, "COD_PAIS_ISO", $Documento, $TipoDoc));
        params_set("emisor_fax", getDatoCia($sessionID, "TELEFONO_2", $Documento, $TipoDoc));
        params_set("emisor_email", getDatoCia($sessionID, "CORREO", $Documento, $TipoDoc));
        params_set("codigo_actividad", rtrim(getDatoCia($sessionID, "CODIGO_ACTIVIDAD", $Documento, $TipoDoc)));

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "SetDatosEmisor Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function setDatosReceptor(string $sessionID, string $Documento, string $TipoDoc)
{
    try {
        $cod_cliente = params_get("cod_cliente");
        $mail = ObtenerDatoCLIENTE($sessionID, "correo", $cod_cliente, $Documento, $TipoDoc);
        $receptor_email = $mail == "1" ? "facturaelectronica@lospaticas.com" : $mail;
        $gln = ObtenerDatoCLIENTE($sessionID, "gln", $cod_cliente, $Documento, $TipoDoc);
        $receptor_gln = (is_null($gln) or $gln == "") ? "0000000000000" : $gln;

        // Datos receptor
        params_set("receptor_nombre", ObtenerDatoCLIENTE($sessionID, "NOMBRE", $cod_cliente, $Documento, $TipoDoc));
        params_set("receptor_tipo_identif", ObtenerDatoCLIENTE($sessionID, "TIPO_NIT", $cod_cliente, $Documento, $TipoDoc));
        params_set("receptor_num_identif", ObtenerDatoCLIENTE($sessionID, "CEDULA", $cod_cliente, $Documento, $TipoDoc));
        params_set("receptor_provincia", getDatoCia($sessionID, "DIVISION_GEOGRAFICA1", $Documento, $TipoDoc));
        params_set("receptor_canton", getDatoCia($sessionID, "DIVISION_GEOGRAFICA2", $Documento, $TipoDoc));
        params_set("receptor_distrito", getDatoCia($sessionID, "DIVISION_GEOGRAFICA3", $Documento, $TipoDoc));
        params_set("receptor_barrio", getDatoCia($sessionID, "DIVISION_GEOGRAFICA4", $Documento, $TipoDoc));
        params_set("receptor_otras_senas", getDatoCia($sessionID, "OTRAS_SENAS", $Documento, $TipoDoc));
        params_set("receptor_cod_pais_tel", ObtenerDatoCLIENTE($sessionID, "TEL_COD_PAIS_ISO", $cod_cliente, $Documento, $TipoDoc));
        params_set("receptor_tel", ObtenerDatoCLIENTE($sessionID, "telefono", $cod_cliente, $Documento, $TipoDoc));
        params_set("receptor_cod_pais_fax", ObtenerDatoCLIENTE($sessionID, "TEL_COD_PAIS_ISO", $cod_cliente, $Documento, $TipoDoc));
        params_set("receptor_fax", ObtenerDatoCLIENTE($sessionID, "celular", $cod_cliente, $Documento, $TipoDoc));
        params_set("receptor_email", $receptor_email);
        params_set("receptor_gln", $receptor_gln);

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "SetDatosReceptor Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function setResumenDocumento(string $sessionID, string $Documento, string $TipoDoc)
{

    try {

        $total_serv_gravados = ObtenerTotales($sessionID, $Documento, $TipoDoc, "SERVICIOS", "SI");
        $total_serv_exentos = ObtenerTotales($sessionID, $Documento, $TipoDoc, "SERVICIOS", "NO");

        $total_merc_gravada = ObtenerTotales($sessionID, $Documento, $TipoDoc, "MERCANCIAS", "SI");
        $total_merc_exenta = ObtenerTotales($sessionID, $Documento, $TipoDoc, "MERCANCIAS", "NO");

        $total_merc_exonerada = ObtenerTotalExoneraciones($sessionID, $Documento, $TipoDoc, "MERCANCIAS");
        $total_serv_exonerados = ObtenerTotalExoneraciones($sessionID, $Documento, $TipoDoc, "SERVICIOS");

        $total_gravados = $total_serv_gravados + $total_merc_gravada;
        $total_exentos = $total_serv_exentos + $total_merc_exenta;
        $total_exonerado = $total_merc_exonerada + $total_serv_exonerados;

        $total_ventas = $total_gravados + $total_exentos + $total_exonerado;
        $total_descuentos = ObtenerTotalDescuentoOImpuestoDeLineas($sessionID, $Documento, $TipoDoc, "MontoDescuento");
        $total_ventas_neta = $total_ventas - $total_descuentos;
        $total_impuestos = ObtenerTotalDescuentoOImpuestoDeLineas($sessionID, $Documento, $TipoDoc, "TotalIVA");
        $total_comprobante = $total_ventas_neta + $total_impuestos;

        params_set("TotalServGravados", $total_serv_gravados);
        params_set("TotalServExentos", $total_serv_exentos);
        params_set("TotalServExonerado", $total_serv_exonerados);
        params_set("TotalMercanciasGravadas", $total_merc_gravada);
        params_set("TotalMercanciasExentas", $total_merc_exenta);
        params_set("TotalMercExonerada", $total_merc_exonerada);
        params_set("TotalGravado", $total_gravados);
        params_set("TotalExento", $total_exentos);
        params_set("TotalExonerado", $total_exonerado);
        params_set("TotalDescuentos", $total_descuentos);
        params_set("TotalImpuesto", $total_impuestos);
        params_set("TotalIVADevuelto", 0);
        params_set("TotalOtrosCargos", 0);
        params_set("TotalVenta", $total_ventas);
        params_set("TotalVentaNeta", $total_ventas_neta);
        params_set("TotalComprobante", $total_comprobante);
        params_set("Otros", "");
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "SetResumenDocumento Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function getSigningTime($XML, $sessionID, $Documento, $TipoDoc)
{
    try {
        $xml = simplexml_load_string($XML);
        $signatureNode = $xml->children('ds', true);
        $signingTime = $signatureNode->Signature->Object->children('xades', true)->QualifyingProperties->SignedProperties->SignedSignatureProperties->SigningTime[0];
        return (string) $signingTime;
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "getSigningTime Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function setEncabezadoDocumento(string $sessionID, string $Documento, string $TipoDoc)
{
    try {

        //Esto para cuando se genere el xml para NC
        if ($TipoDoc == "NC") {
            //Facturas
            params_set("tipo_doc", "01");
            params_set("numero", ObtenerDatoFACTURA($sessionID, "referencia", $Documento, $TipoDoc));
            //Fecha de Emision del documento de referencia
            //$fechaEmision = ObtenerDatoTabla($sessionID, "fechafact", "hfacturas", "nfac", params_get("numero"));
            $fechaEmision = date("Y-m-d\TH:i:sP");
            params_set("fecha_emision", $fechaEmision);

            //Codigo de motivo
            $codigo = ObtenerDatoFACTURA($sessionID, "motivo", $Documento, $TipoDoc);
            params_set("codigo_razon" , str_pad($codigo, 2, "0",STR_PAD_LEFT));
            //Descripcion del motivo
            params_set("razon", ObtenerDatoTabla($sessionID, "motivo", "tbl_Motivos", "cod_motivo", $codigo));

        }

        //Consecutivos y Tipo
        params_set('tipoDocumento', $TipoDoc);
        params_set('clave', ObtenerDatoFACTURA($sessionID, "SAT", $Documento, $TipoDoc));
        params_set('consecutivo', ObtenerDatoFACTURA($sessionID, 'consecutivo', $Documento, $TipoDoc));

        //Fecha de Emision
        //$fecha = date("Y-m-d\TH:i:sP", strtotime(ObtenerDatoFACTURA($sessionID, 'fechafact', $Documento, $TipoDoc)));
        $fechaEmision = date("Y-m-d\TH:i:sP");
        params_set("fecha_emision", $fechaEmision);

        //Datos del cliente para definir otras codiciones
        $cod_cliente = ObtenerDatoFACTURA($sessionID, "PROVEEDOR", $Documento, $TipoDoc);
        $condicion_venta = $cod_cliente > 10000 ? "02" : "01";
        $plazo_credito = $cod_cliente > 10000 ? "0" : ObtenerDatoCLIENTE($sessionID, "credito", $cod_cliente, $Documento, $TipoDoc);

        params_set("condicion_venta", $condicion_venta);
        params_set("plazo_credito", $plazo_credito);
        params_set("medio_pago", "01");

        params_set("cod_moneda", "CRC");
        params_set("tipo_cambio", 1);
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "SetEncabezadoDocumento Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function IniciaProcesoCreacionXML(string $sessionID, string $Documento, string $TipoDoc)
{
    try {
        //Establece los datos del receptor o cliente
        params_set("cod_cliente", ObtenerDatoFACTURA($sessionID, "PROVEEDOR", $Documento, $TipoDoc));

        setDatosEmisor($sessionID, $Documento, $TipoDoc);
        setDatosReceptor($sessionID, $Documento, $TipoDoc);

        setEncabezadoDocumento($sessionID, $Documento, $TipoDoc);
        setDetallesXML($sessionID, $Documento, $TipoDoc);
        setResumenDocumento($sessionID, $Documento, $TipoDoc);

        return genXML();

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "IniciaProcesoCreacionXML Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }

}

function IniciaProcesoEnvioXML($XMLFirmado, string $sessionID, string $Documento, string $TipoDoc)
{
    try {
        // Envio el documento y obtengo una respuesta, puede ser indicando que se recibio y en ese
        // caso viene el Xml de respuesta y el estado de aceptacion o rechazo
        //Ejecuta el envio
        $respuesta = EnvioDocumento($XMLFirmado, $sessionID, $Documento, $TipoDoc);
        //Procesa la respuesta obtenida del envio
        ProcesaResultadoEnvio($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc);
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "IniciaProcesoEnvioXML Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function SetContinuar($state)
{
    global $continuar;
    $continuar = $state;
}

function ProcesarDocumento(string $sessionID, string $Documento, string $TipoDoc)
{
    try {

        //global $params;
        global $continuar;

        //unset($params); // $params is gone
        //$params = array(); // $params is here again

        //Inicializo continuar a true sino aca no vinimos a naaaa
        SetContinuar(true);

        //************************INICIO DE PROCESO DE GENERACION*******************
        //Define el ambiente de pruebas o produccion. Importante definirlo aca para que afecte a todos los procesos
        // despues de aqui.
        params_set("ambiente", strtoupper(rtrim(getDatoCia($sessionID, "AMBIENTE", $Documento, $TipoDoc))));
        //Creacion del XML
        Bitacora($sessionID, "Creando XML", $Documento, $TipoDoc);
        $xml = IniciaProcesoCreacionXML($sessionID, $Documento, $TipoDoc);
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en la creacion del XML", $Documento, $TipoDoc);
            return;
        }
        $TIEMPO_EXPIRA_DOCHA_HORAS = getDatoCia($sessionID, "TIEMPO_EXPIRA_DOCHA_HORAS", $Documento, $TipoDoc);
        //Si el estado de hacienda es 1 , se procede con el guardado pero no se envia
        if (getEstadoHacienda($Documento, $TipoDoc) == "3") {
            Bitacora($sessionID, "No hay conexion a hacienda, este documento se intentara procesar durante $TIEMPO_EXPIRA_DOCHA_HORAS horas mas", $Documento, $TipoDoc);
            //UpdateInsert("N", "N", 'N', 'N', 'No hay conexion a hacienda, este documento se intentara procesar durante 48 horas mas', 'error', $xml, null,  $sessionID, $Documento, $TipoDoc);
            return;
        } else {
            Bitacora($sessionID, "Conexion a hacienda exitosa.Inicia proceso de firmado y envio del documento  ", $Documento, $TipoDoc);
        }

        $TIEMPO_EXPIRA_DOCHA_HORAS = getDatoCia($sessionID, "TIEMPO_EXPIRA_DOCHA_HORAS", $Documento, $TipoDoc);
        $sql = "select FECHA,DATEDIFF(MINUTE,FECHA,getdate()) as DIF_MINS from V_DOCUMENTO_ELECTRONICO where DOCUMENTO='$Documento'";
        $result = sql_exec($sql, 2, false, true);
        if ($result !== "-200") {
            if ($result[0]["DIF_MINS"] > ($TIEMPO_EXPIRA_DOCHA_HORAS * 60)) {
                Bitacora($sessionID, "El documento $Documento NO sera procesado, ha pasado las $TIEMPO_EXPIRA_DOCHA_HORAS horas limite para enviarlo", $Documento, $TipoDoc);
                //@todo Poner aca un return al terminar debugging
                //return;
            }
        }

        //Una vez creado iniciamos el proceso de firmado, pasando el xml que acabamos de crear y lo retorna firmado.
        Bitacora($sessionID, "Firmando XML", $Documento, $TipoDoc);
        $xmlFirmado = IniciaProcesoFirmado($xml, $sessionID, $Documento, $TipoDoc);
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en el firmado del documento", $Documento, $TipoDoc);
            return;
        }

        //Intenta salvar el documento en el disco en ruta segun settings.php
        Bitacora($sessionID, "Guardando XML firmado al sistema de archivos", $Documento, $TipoDoc);
        if (isset($xmlFirmado["xmlFirmado"])) {
            saveFile(params_get("clave") . ".xml", base64_decode($xmlFirmado["xmlFirmado"]), "XML", params_get("fecha_timbrado"), $Documento, $TipoDoc, $sessionID);
        } else {
            Bitacora($sessionID, "No se logro guardar  el documento  $Documento despues de firmarlo, por lo tanto no se podra continuar ");
        }
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en el guardado del XML firmado en disco", $Documento, $TipoDoc);
            return;
        }

        //******************** FIN DE PROCESO DE GENERACION*************************

        //Una vez creado inicio el proceso de envio del XML,aca se procesa el token primero
        Bitacora($sessionID, "Inicia proceso de envio a hacienda del  XML", $Documento, $TipoDoc);
        IniciaProcesoEnvioXML($xmlFirmado, $sessionID, $Documento, $TipoDoc);
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en el envio del documento", $Documento, $TipoDoc);
            return;
        }

        //Hasta no enviarse no se crean los totales, y hasta no crear los totales no se genera el PDF
        //Generamos el PDF y lo guardamos en la base de datos
        Bitacora($sessionID, "Creando PDF", $Documento, $TipoDoc);
        $strPDF = GenerarPDF($sessionID, $Documento, $TipoDoc);
        //Intenta salvar el documento en el disco en ruta segun settings.php
        if (strlen($strPDF) > 0) {
            saveFile(params_get("clave") . ".pdf", $strPDF, "PDF", params_get("fecha_timbrado"), $Documento, $TipoDoc, $sessionID);
        } else {
            Bitacora($sessionID, "No se logro generar y guardar el PDF del documento $Documento ");
        }

        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en el guardado del PDF en disco", $Documento, $TipoDoc);
            return;
        }
        Bitacora($sessionID, "Guardando PDF en base de datos", $Documento, $TipoDoc);
        UpdateDBPDF($sessionID, $Documento, $TipoDoc, $strPDF);
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en el guardado del PDF en base de datos", $Documento, $TipoDoc);
            return;
        }

        //Finalmente, envio el correo
        ProcesarCorreosPendientes($sessionID, $Documento, $TipoDoc);
    } catch (Exception $ex) {
        Bitacora($sessionID, "ProcesarDocumento Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function file_force_contents($dir, $contents, $file, $flags, $Documento, $TipoDoc, $sessionID)
{

    try {
        //Si no existe el directorio lo crea
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        //Una vez creado el directorio, lo guardo
        if (!is_null($flags)) {
            file_put_contents("$dir/$file", $contents, $flags);
        } else {
            file_put_contents("$dir/$file", $contents);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "file_force_contents Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function saveFile($filename, $file_content, $tipo, $fecha_timbrado, $Documento, $TipoDoc, $sessionID)
{
    try {
        $xml_path = params_get("xml_path");
        $pdf_path = params_get("pdf_path");
        $overwrite = params_get("overwrite");
        $date = new DateTime($fecha_timbrado);
        $directory_pdf = $pdf_path . "/" . date_format($date, "Y") . '/' . date_format($date, "m") . '/' . date_format($date, "d");
        $directory_xml = $xml_path . "/" . date_format($date, "Y") . '/' . date_format($date, "m") . '/' . date_format($date, "d");

        params_set("directory_pdf", $directory_pdf);
        params_set("directory_xml", $directory_xml);

        if ($tipo == "PDF") {

            if ($overwrite == true) {
                file_force_contents($directory_pdf, $file_content, $filename, null, $Documento, $TipoDoc, $sessionID);
            } else {
                file_force_contents($directory_pdf, $file_content, $filename, FILE_APPEND | LOCK_EX, $Documento, $TipoDoc, $sessionID);
            }
        }
        if ($tipo == "XML") {

            if ($overwrite == true) {
                file_force_contents($directory_xml, $file_content, $filename, null, $Documento, $TipoDoc, $sessionID);
            } else {
                file_force_contents($directory_xml, $file_content, $filename, FILE_APPEND | LOCK_EX, $Documento, $TipoDoc, $sessionID);
            }
        }

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "SaveFile Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function IniciaProcesoFirmado($xml, $sessionID, $Documento, $TipoDoc)
{
    try {
        //Obtengo el PIN y el URL del IDP de hacienda
        $PIN = "IDP_" . params_get("ambiente") . "_PIN";
        $URL = "IDP_" . params_get("ambiente") . "_CERT_URL";

        params_set("pinP12", getDatoCia($sessionID, $PIN, $Documento, $TipoDoc));
        params_set("p12Url", getDatoCia($sessionID, $URL, $Documento, $TipoDoc));

        params_set("inXml", $xml);
        //Firmado
        $xmlFirmado = firmar();
        $signingTIme = getSigningTime(base64_decode($xmlFirmado["xmlFirmado"]), $sessionID, $Documento, $TipoDoc);
        params_set("fecha_timbrado", $signingTIme);

        return $xmlFirmado;
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "IniciarProcesoFirmado Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function EnvioDocumento($xmlFirmado, $sessionID, $Documento, $TipoDoc)
{
    global $continuar;
    try {
        $api_url = "API_" . params_get("ambiente") . "_URL";
        params_set("api_url", getDatoCia($sessionID, $api_url, $Documento, $TipoDoc));
        params_set('callbackUrl', "");

        IniciarProcesoToken($sessionID, $Documento, $TipoDoc);
        if ($continuar) {
            $respuesta = IniciarEnvioDocumentoHacienda($xmlFirmado, $sessionID, $Documento, $TipoDoc);
            return $respuesta;
        }

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "EnvioDocumento Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function IniciarProcesoToken($sessionID, $Documento, $TipoDoc)
{
    try {
        //Genero el client_id y la url para consultar o enviar a la api
        $client_id = "API_" . params_get("ambiente") . "_CLIENT_ID";
        params_set("client_id", getDatoCia($sessionID, $client_id, $Documento, $TipoDoc));

        $url_token = "IDP_" . params_get("ambiente") . "_URL";
        params_set('idp_token_url', getDatoCia($sessionID, $url_token, $Documento, $TipoDoc));
        //Obtiene el token y lo guarda en la base de datos.
        GetAndSaveToken(params_get("ambiente"), $sessionID, $Documento, $TipoDoc);
        //Lo lee de la base de datos y lo almacena en memoria
        params_set("access_token", LeerToken("access_token", params_get("ambiente"), $sessionID, $Documento, $TipoDoc));
        params_set("refresh_token", LeerToken("refresh_token", params_get("ambiente"), $sessionID, $Documento, $TipoDoc));
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "IniciarProcesoToken Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }

}

function IniciarEnvioDocumentoHacienda($xmlFirmado, $sessionID, $Documento, $TipoDoc)
{
    try {
        $TiempoActual = date("Y-m-d\TH:i:sP");
        params_set('fecha', $TiempoActual);

        params_set('emi_tipoIdentificacion', params_get("emisor_tipo_identif"));
        params_set('emi_numeroIdentificacion', params_get("emisor_num_identif"));

        params_set('recp_tipoIdentificacion', params_get("receptor_tipo_identif"));
        params_set('recp_numeroIdentificacion', params_get("receptor_tipo_identif"));

        params_set('comprobanteXml', $xmlFirmado["xmlFirmado"]);

        $respuesta = send();
        return $respuesta;
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "IniciarEnvioDocumentoHacienda Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function LeerToken($tipo, $ambiente, $sessionID, $Documento, $TipoDoc)
{
    try {
        if ($tipo == "access_token") {
            $ACCESS_TOKEN = "IDP_" . $ambiente . "_ACCESS_TOKEN";
            $sql = " select  $ACCESS_TOKEN FROM COMPAÑIAS WHERE CONFIGURADA='SI' ";
            $result = sql_exec($sql, 1, true, true);
            return $result[$ACCESS_TOKEN];
        } elseif ($tipo == "refresh_token") {
            $REFRESH_TOKEN = "IDP_" . $ambiente . "_REFRESH_TOKEN";
            $sql = " select  $REFRESH_TOKEN FROM COMPAÑIAS WHERE CONFIGURADA='SI' ";
            $result = sql_exec($sql, 1, true, true);
            return $result[$REFRESH_TOKEN];
        }

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "LeerToken Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function setDetallesXML($sessionID, $Documento, $TipoDoc)
{
    try {
        $sql = "select * from DOC_ELEC_DETALLE where nfac='$Documento'";
        $result = sql_exec($sql, 2, false, true);
        $json = json_encode($result);

        params_set('detalle', $json);

        return $json;

    } catch (Exception $ex) {
        Bitacora($sessionID, "SetDetallesXML Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
        return null;
    }

}

function GenerarPDF($sessionID, $Documento, $TipoDoc)
{
    try {
        $pdf = new FacturaPDF('P', 'mm', 'A4');
        $strPDF = $pdf->CrearDocumento($sessionID, $Documento, $TipoDoc);
        return $strPDF;
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "GenerarPDF Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function UpdateDBPDF($sessionID, $Documento, $TipoDoc, $xPDF)
{
    try {
        global $dbConn;
        $sql = "select NUMERO_DOC from DOC_ELECTRONICO_PROCESADO where NUMERO_DOC='$Documento' and TIPO_DOC='$TipoDoc'";
        $result = sql_exec($sql, 2, false, true);
        //Ya existe el documento
        $data = [
            'pdf' => $xPDF,
        ];
        if ($result != '-200') {
            $sql = "UPDATE DOC_ELECTRONICO_PROCESADO SET  PDF=convert(varbinary(max),:pdf)
               where NUMERO_DOC='$Documento' and TIPO_DOC='$TipoDoc' ";

            if (db_allGood() === true) {
                $stmt = $dbConn->prepare($sql);
                $stmt->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);
                $stmt->execute($data);
            }
            // sql_exec($sql,0,true,true);
        }

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "UpdateDBPDF Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

//*-----------------------------------------------------------------------
function Procesa200($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc)
{
    //Leer el campo text del array a ver si viene un json o solo ind-estado
    try {

        ConsultarDocumento($respuesta, $XMLFirmado, $sessionID, $Documento, $TipoDoc);
        $xmlRespuesta = params_get("xmlRespuesta");
        $ind_estado = params_get("ind_estado");

        //Actualizar estado en base de datos
        if ($ind_estado == "aceptado") {
            UpdateInsert("S", "N", "S", "S", "El documento fue aceptado", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        } elseif ($ind_estado == "rechazado") {
            UpdateInsert("S", "N", "S", "S", "El documento fue rechazado", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        } elseif ($ind_estado == "procesando") {
            UpdateInsert("N", "N", "N", "S", "El documento se esta procesando", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        } else {
            UpdateInsert("N", "N", "N", "S", "El documento tiene un estado desconocido", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        }

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "Procesa200 Error: {$ex->getMessage()} ({$ex->getLine()})", $Documento, $TipoDoc);
    }
}
/*
 *  Se recibió el documento en la plataforma y está    pendiente de ser validado. En su respuesta hay
un Header Location donde se puede consultar    el estado del documento. Si no se recibe un
202; ese documento NO ha sido recibido para    procesamiento.    Para determinar si el documento se acepta o se
rechaza se debe usar el GET o bien, corroborar    el callback2    realizado al url que opcionalmente
se envía en el payload.
Mis Notas:
 * NO hay XML de respuesta por que el documento se esta procesando sin ser validado por hacienda
 * */
function Procesa202($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc, $XML)
{
    try {
        ConsultarDocumento($respuesta, $XMLFirmado, $sessionID, $Documento, $TipoDoc);

        $xmlRespuesta = params_get("xmlRespuesta");
        $ind_estado = params_get("ind_estado");
        $x_error_cause = params_get("x_error_cause");

        if ($ind_estado == "aceptado") {
            UpdateInsert("S", "N", "S", "S", "El documento fue aceptado", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        } elseif ($ind_estado == "rechazado") {
            UpdateInsert("S", "N", "S", "S", "El documento fue rechazado($x_error_cause)", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        } elseif ($ind_estado == "procesando") {
            UpdateInsert("N", "N", "S", "S", "El documento se esta procesando", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        } else {
            UpdateInsert("N", "N", "N", "S", "El documento tiene un estado desconocido", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        }
        if (strlen($xmlRespuesta) > 0) {
            saveFile("RSP_" . params_get("clave") . ".xml", base64_decode($xmlRespuesta), "XML", params_get("fecha_timbrado"), $Documento, $TipoDoc, $sessionID);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "Procesa202 Error: {$ex->getMessage()} ({$ex->getLine()})", $Documento, $TipoDoc);
    }
}
/*
 * Ocurrió un error de validación en el payload. El header X-Error-Cause muestra la razón. Para
todos los efectos; la clave enviada NO ha sido recibida para ser procesada.Una razón muy común
para recibir este códigoes enviar dos o más veces la misma clave. Apartir de la segunda invocación recibirían un
400 indicando que la clave ya ha sido recibida.
 * */
function Procesa400($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc)
{
    try {

        // Consulta el documento y obtiene varios datos del mismo para proceder a saber porque el error 400 sucedio
        ConsultarDocumento($respuesta, $XMLFirmado, $sessionID, $Documento, $TipoDoc);
        $xmlRespuesta = params_get("xmlRespuesta");
        $ind_estado = params_get("ind_estado");

        //Al menos en la consulta obtuvimos una causa del error y fue diferente a la primera vez que enviamos
        // el documento, pero puede ser que venga vacio
        if (strlen(params_get("x_error_cause")) > 0) {
            $x_error_cause = params_get("x_error_cause");
        }

        Bitacora($sessionID, "$x_error_cause, estado: $ind_estado", $Documento, $TipoDoc);
        //Si ya fue recibido por hacienda hay que consultar por el estado de recibido para proceder a
        //actualizar la base de datos de acuerdo a la respuesta que nos devuelva hacienda
        if (!empty($ind_estado) or strlen($ind_estado) > 0) {
            if ($ind_estado == "aceptado") {
                UpdateInsert("S", "N", "S", "N", "El documento fue aceptado", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
            } elseif ($ind_estado == "rechazado") {
                UpdateInsert("S", "N", "S", "S", "El documento fue rechazado($x_error_cause)", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
            } elseif ($ind_estado == "procesando") {
                UpdateInsert("N", "N", "S", "S", "El documento se esta procesando", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
            } else { //Otro error , no hay XML de respuesta y tampoco estado
                UpdateInsert("N", "N", "N", "S", "El documento tiene el siguiente error: $x_error_cause", "error", $XMLFirmado, null, $sessionID, $Documento, $TipoDoc);
            }
        }
        //Intenta salvar el documento en el disco en ruta segun settings.php
        if (!empty($xmlRespuesta)) {
            saveFile("RSP_" . params_get("clave") . ".xml", base64_decode($xmlRespuesta), "XML", params_get("fecha_timbrado"), $Documento, $TipoDoc, $sessionID);
        } else {
            Bitacora($sessionID, "No se logro  obtener un XML de respuesta de hacienda para guardarla", $Documento, $TipoDoc);
            UpdateInsert("N", "N", "N", "S", "No se logro  obtener un XML de respuesta de hacienda para guardarla ", $ind_estado, $XMLFirmado["xmlFirmado"], $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "Procesa400 Error: {$ex->getMessage()}", $Documento, $TipoDoc);
    }

}

function GuardarRespuestaHacienda($sessionID, $xmlRespuesta, $Documento, $TipoDoc)
{
    try {
        global $dbConn;

        if (strlen($xmlRespuesta) > 0) {

            $xml = simplexml_load_string($xmlRespuesta, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json, true);
            
            if (!empty($array)) {
                $mensaje = $array["Mensaje"];
                if($mensaje=="1")
                {
                    $detalle_mensaje = "Aceptado";
                }
                else{
                    $detalle_mensaje=$array["DetalleMensaje"];
                }   
                $resp = "Mensaje=$mensaje, Detalle:$detalle_mensaje";
                $sql = "UPDATE DOC_ELECTRONICO_PROCESADO SET
                    RESPUESTA_HACIENDA=:resp ,XML_RESPUESTA=:xmlRespuesta
                    WHERE NUMERO_DOC=:Documento and TIPO_DOC=:TipoDoc";
                $stm = $dbConn->prepare($sql);
                $stm->execute([':resp' => $resp, ':xmlRespuesta' => $xmlRespuesta,
                    ':Documento' => $Documento, ':TipoDoc' => $TipoDoc]);
                Bitacora($sessionID, "Respuesta de hacienda obtenida($mensaje), se aplica al documento.", $Documento, $TipoDoc);
            }
        } else {
            Bitacora($sessionID, "No hay respuesta de hacienda.El documento se intentará enviar luego", $Documento, $TipoDoc);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "GuardarRespuestaHacienda Error: {$ex->getMessage()}", $Documento, $TipoDoc);
    }
}

/*Este sería un error no esperado en la plataforma. Se recomienda esperar un periodo de tiempo razonable 4
para descartar que se trate de un problema de disponibilidad de la plataforma. De persistir el problema de manera
general, se debe abrir un caso a la mesa de soporte del Ministerio de Hacienda.
 */
function Procesa500($respuesta, $sessionID, $Documento, $TipoDoc, $XML)
{
    try {
        //Actualizar estado en base de datos
        $x_error_cause = "#ERR#";
        //Intento obtener al menos un X-Error-Cause por si la consulta no devuelve nada
        foreach ($respuesta["text"] as $textpart) {
            if (strpos($textpart, 'x-error-cause') !== false) {
                $x_error_cause = $textpart;
            }
        }

        $UltimoError = "El servidor ha devuelto el siguiente error: $x_error_cause";
        UpdateInsert("N", "N", "N", "S", $UltimoError, "error", $XML, $Documento, $TipoDoc);
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "Procesa500 Error: {$ex->getMessage()} ({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

function ObtenerTotalesDeXML($XMLFirmado, $sessionID, $Documento, $TipoDoc)
{
    try {
        //$xmlDecode = base64_decode($XMLFirmado);
        $arr = simplexml_load_string($XMLFirmado);
        return $arr->ResumenFactura;
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ObtenerTotalesXML Error: {$ex->getMessage()} ({$ex->getLine()})", $Documento, $TipoDoc);
    }
}

/*
 *  Obtiene datos del documento luego de enviarlo
 *  $respuesta , es la respuesta del envio inicial
 * */
function ConsultarDocumento($respuesta, $sessionID, $Documento, $TipoDoc)
{

    try {
        $header_location = "";
        $error_cause = "";
        $x_error_cause = "";
        $apiURLConsulta = "";
        //*****************************CONSULTAR POR DOCUMENTO***************************
        foreach ($respuesta["text"] as $textpart) {
            if (strpos($textpart, 'Location') !== false) {
                $header_location = $textpart;
            }
            if (strpos($textpart, 'X-Error-Cause:') !== false) {
                $error_cause = $textpart;
            }
        }
        if (strlen($error_cause) > 0) {
            $x_error_cause = explode('X-Error-Cause:', $error_cause)[1];
        }

        if (strlen($header_location) > 0) {
            $strURL = explode("location:", $header_location);
        }

        if (!empty($strURL)) {
            $apiURLConsulta = ltrim(rtrim($strURL[0]));
        } else {
            $apiURLConsulta = "error";
        }

        if ($apiURLConsulta != "error") {
            if (preg_match('/https:\/\/[^\s]+/', $apiURLConsulta, $matches)) {
                $cleanURL = rtrim($matches[0]);
                params_set("api_url", $cleanURL);
            }
            params_set("x_error_cause", $x_error_cause);

            //Esta es la respuesta despues de consultar
            $jsonRespuesta = json_decode(consultar());

            if (isset($jsonRespuesta->{"ind-estado"})) {
                params_set("ind_estado", $jsonRespuesta->{"ind-estado"});
            } else {
                params_set("ind_estado", null);
            }

            if (isset($jsonRespuesta->{"respuesta-xml"})) {
                params_set("xmlRespuesta", $jsonRespuesta->{"respuesta-xml"});
            } else {
                params_set("xmlRespuesta", null);
            }
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ConsultarDocumento Error: {$ex->getMessage()} ({$ex->getLine()})", $Documento, $TipoDoc);
    }

}
function ConsultarDirecto($sessionID, $Documento, $TipoDoc)
{

    try {
        $ambiente = strtoupper(rtrim(getDatoCia($sessionID, "AMBIENTE", $Documento, $TipoDoc)));
        $URL_CONSULTA = "API_" . $ambiente . "_URL";
        //$apiURLConsulta=ObtenerDatoTabla($sessionID, "SELLO_SAT", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC",$Documento);
        $apiURLConsulta = getDatoCia($sessionID, $URL_CONSULTA, $Documento, $TipoDoc);
        $clave = ObtenerDatoTabla($sessionID, "SELLO_SAT", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", $Documento);
        $linkConsulta = $apiURLConsulta . $clave;
        $XMLFirmado = ObtenerDatoTabla($sessionID, "DOCUMENTO_ELECTRONICO", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", $Documento);

        if ($linkConsulta != "") {

            params_set("api_url", ltrim(rtrim($linkConsulta)));
            params_set("clave", $clave);
            params_set("ambiente", $ambiente);
            IniciarProcesoToken($sessionID, $Documento, $TipoDoc);

            //Esta es la respuesta despues de consultar

            $jsonRespuesta = json_decode(consultar());
            //  print_r($jsonRespuesta);
            if (!empty($jsonRespuesta->{"Status"})) {
                if ($jsonRespuesta->{"Status"} == "400") {
                    //No se ha encontrado el documento
                    $header = $jsonRespuesta->text;
                    Bitacora($sessionID, $header->{"X-Error-Cause"}, $Documento, $TipoDoc);
                    return;
                }
            }
            if (!empty($jsonRespuesta->{"message"})) {
                Bitacora($sessionID, "Error al consultar:" . $jsonRespuesta->{"message"}, $Documento, $TipoDoc);
            }

            if (!empty($jsonRespuesta->{"ind-estado"})) {
                params_set("ind_estado", $jsonRespuesta->{"ind-estado"});
            } else {
                params_set("ind_estado", null);
            }

            if (!empty($jsonRespuesta->{"respuesta-xml"})) {
                params_set("xmlRespuesta", $jsonRespuesta->{"respuesta-xml"});
            } else {
                params_set("xmlRespuesta", null);
            }
            $ind_estado = params_get("ind_estado");
            $xmlRespuesta = params_get("xmlRespuesta");
            if ($ind_estado == "aceptado") {
                UpdateInsert("S", "N", "S", "S", "El documento fue aceptado", $ind_estado, $XMLFirmado, params_get("xmlRespuesta"), $sessionID, $Documento, $TipoDoc);
            } elseif ($ind_estado == "rechazado") {
                UpdateInsert("S", "N", "S", "S", "El documento fue rechazado", $ind_estado, $XMLFirmado, params_get("xmlRespuesta"), $sessionID, $Documento, $TipoDoc);
            } elseif ($ind_estado == "procesando") {
                UpdateInsert("N", "N", "N", "S", "El documento se esta procesando", $ind_estado, $XMLFirmado, params_get("xmlRespuesta"), $sessionID, $Documento, $TipoDoc);
            } else {
                UpdateInsert("N", "N", "N", "S", "El documento tiene un estado desconocido", $ind_estado, $XMLFirmado, params_get("xmlRespuesta"), $sessionID, $Documento, $TipoDoc);
            }
            //Intenta salvar el documento en el disco en ruta segun settings.php
            if (isset($xmlRespuesta) and !empty($xmlRespuesta)) {
                $fecha_timbrado = ObtenerDatoTabla($sessionID, "FECHATIMBRADO", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", $Documento);
                saveFile("RSP_" . params_get("clave") . ".xml", base64_decode(params_get("xmlRespuesta")), "XML", $fecha_timbrado, $Documento, $TipoDoc, $sessionID);
            } else {
                Bitacora($sessionID, "No hay todavia respuesta de hacienda.El documento se intentará enviar luego", $Documento, $TipoDoc);
                UpdateInsert("N", "N", "N", "S", "No hay todavia respuesta de hacienda.El documento se intentará enviar lueg", $ind_estado, $XMLFirmado, $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
            }
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ConsultarDocumento Error: {$ex->getMessage()} ({$ex->getLine()})", $Documento, $TipoDoc);
    }

}

function isBase64Encoded($str)
{
    try {
        $decoded = base64_decode($str, true);

        if (base64_encode($decoded) === $str) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        // If exception is caught, then it is not a base64 encoded string
        return false;
    }

}

function ProcesaResultadoEnvio($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc)
{

    try {
        $error=$respuesta["Status"];
        //Procesado,todo ok, se debe revisar si fue aceptado o no
        if ($respuesta["Status"] == 200) {
            Bitacora($sessionID, "El documento se ha enviado y ha sido recibido correctamente($error)", $Documento, $TipoDoc);
            Procesa200($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc);
        }

        //Procesandose,fue enviado pero no fue aceptado
        if ($respuesta["Status"] == 202) {
            Bitacora($sessionID, "El documento fue enviado pero esta pendiente de ser procesado por hacienda($error)", $Documento, $TipoDoc);
            Procesa202($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc, $XMLFirmado);
        }

        if ($respuesta["Status"] >= 400 and $respuesta["Status"] < 500) {
            $x_error_cause = "#ERR#";
            // Intento obtener al menos un X-Error-Cause por si la consulta no devuelve nada
            foreach ($respuesta["text"] as $textpart) {
                if (stripos($textpart, 'x-error-cause:') !== false) {
                    $x_error_cause = $textpart;
                    break; // Salir del bucle una vez encontrado
                }
            }
            Bitacora($sessionID, "Ha ocurrido un error al enviar el documento($error), causa:{$x_error_cause}");
            Procesa400($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc, $XMLFirmado);
        }




        //Error causado por error el servidor
        if ($respuesta["Status"] >= 500) {
            Bitacora($sessionID, "Ha ocurrido un error en el servidor de hacienda($error)(sorpresaaaa)!!!.", $Documento, $TipoDoc);
            Procesa500($respuesta, $sessionID, $Documento, $TipoDoc, $XMLFirmado);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ProcesaResultadoEnvio Error: {$ex->getMessage()} {$ex->getLine()}", $Documento, $TipoDoc);
    }
}
function getEstadoHacienda($Documento, $TipoDoc)
{
    $api_ok = true;
    $idp_ok = true;
    try {

        //Prueba API
        if (!$sock = @fsockopen('ssl://api.comprobanteselectronicos.go.cr', 443, $errno, $errstr, 30)) {
            $api_ok = false;
        } else { //Conectado
            $ch = curl_init('https://api.comprobanteselectronicos.go.cr/recepcion/v1/recepcion/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            if (!curl_errno($ch)) {
                $api_ok = true;
                curl_close($ch);
            } else {
                $api_ok = false;
            }
        }
        //Prueba IDP
        if (!$sock = @fsockopen('ssl://idp.comprobanteselectronicos.go.cr', 443, $errno, $errstr, 30)) {
            $idp_ok = false;
        } else { //Conectado
            $ch = curl_init('https://idp.comprobanteselectronicos.go.cr/auth/realms/rut/protocol/openid-connect/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            if (!curl_errno($ch)) {
                $idp_ok = true;
                curl_close($ch);
            } else {
                $idp_ok = false;
            }
        }

        if ($api_ok and $idp_ok) {
            return "1";
        } else {
            return "3";
        }

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "getEstadoHacienda Error: {$ex->getMessage()} {$ex->getLine()}", $Documento, $TipoDoc);
    }

}
function UpdateInsert($Aceptado, $ErrorLocal, $Enviado, $ErrorHacienda, $UltimoError, $Estado, $XML, $XMLRespuesta,
    $sessionID, $Documento, $TipoDoc) {

    try {
        global $dbConn;
        if (isBase64Encoded($XML)) {
            $XML = base64_decode($XML);
        }
        if (isBase64Encoded($XMLRespuesta)) {
            $XMLRespuesta = base64_decode($XMLRespuesta);

        }
        $estado_comprobante = getEstadoHacienda($Documento, $TipoDoc);

        $where = " NUMERO_DOC='$Documento' and TIPO_DOC='$TipoDoc'";
        $emisor_num_identif = params_get("emisor_num_identif");
        $emisor_email = params_get("emisor_email");
        $emisor_gln = params_get("emisor_gln");
        $receptor_nit = params_get("receptor_num_identif");
        $receptor_email = params_get('receptor_email');
        $receptor_gln = params_get("receptor_gln");
        $fecha_emision = date("Y-m-d H:i:s", strtotime(params_get("fecha_emision")));
        $fecha_timbrado = date("Y-m-d H:i:s", strtotime(params_get("fecha_timbrado")));
        $clave = params_get('clave');
        $Totales = ObtenerTotalesDeXML($XML, $sessionID, $Documento, $TipoDoc);
        $URL_CONSULTA_API = "API_" . params_get("ambiente") . "_URL";
        $LinkConsulta = getDatoCia($sessionID, $URL_CONSULTA_API, $Documento, $TipoDoc) . $clave;
        $pdf_path = params_get("directory_pdf");
        $xml_path = params_get("directory_xml");

        $sql = "select NUMERO_DOC from DOC_ELECTRONICO_PROCESADO where $where";
        $result = sql_exec($sql, 2, false, false);

        //Ya existe el documento
        if ($result != "-200" and empty($result) == false) { //O sea si no es -200 es que si existe o tiene valor
            $sql = "UPDATE DOC_ELECTRONICO_PROCESADO SET
                ACEPTADO=:Aceptado, ERROR_LOCAL=:ErrorLocal,
                ENVIADO=:Enviado, ERROR_HACIENDA=:ErrorHacienda, ESTADO=:Estado,
                ULTIMO_ERROR=:UltimoError ,XML_PATH=:xml_path,PDF_PATH=:pdf_path
                WHERE NUMERO_DOC=:Documento and TIPO_DOC=:TipoDoc ";
            $dbConn->prepare($sql)->execute([':Aceptado' => $Aceptado, ':ErrorLocal' => $ErrorLocal,
                ':Enviado' => $Enviado, ':ErrorHacienda' => $ErrorHacienda,
                ':Estado' => $Estado, ':UltimoError' => $UltimoError,
                ':xml_path' => $xml_path, ':pdf_path' => $pdf_path,
                ':Documento' => $Documento, ':TipoDoc' => $TipoDoc]);

            Bitacora($sessionID, "Datos actualizados en DOC_ELECTRONICO_PROCESADO", $Documento, $TipoDoc);
            GuardarRespuestaHacienda($sessionID, $XMLRespuesta, $Documento, $TipoDoc);

        } else {
            $sql = " INSERT INTO DOC_ELECTRONICO_PROCESADO(
                    TIPO_DOC,NUMERO_DOC,NIT_EMISOR,EMAIL_EMISOR,GLN_EMISOR,FECHATIMBRADO,
                    NIT_RECEPTOR,EMAIL_RECEPTOR,GLN_RECEPTOR,ENVIADO,ACEPTADO,ESTADO,DOCUMENTO_ELECTRONICO,LINK_CONSULTA,
                    SELLO_SAT,FECHA_HORA,ULTIMO_ERROR,XML_RESPUESTA,
                    TOTALSERVGRAVADOS,TOTALSERVEXENTOS,TOTAL_SERVICIOS_EXONERADOS,TOTALMERCANCIASGRAVADAS,TOTALMERCANCIASEXENTAS,
                    TOTAL_MERCANCIAS_EXONERADAS,TOTALGRAVADO,TOTALEXENTO,TOTAL_EXONERADO,TOTALVENTA,TOTALDESCUENTOS,TOTALVENTANETA,TOTALIMPUESTO,
                    TOTALCOMPROBANTE,TOTAL_IVA_DEVUELTO,TOTAL_OTROS_CARGOS,EMAIL_ENVIADO,ESTADO_COMPROBANTE,PDF_PATH,XML_PATH)
                    VALUES(:TipoDoc,:Documento,:emisor_num_identif,
                            :emisor_email,:emisor_gln,:fecha_timbrado,
                            :receptor_nit,:receptor_email,:receptor_gln,
                            :Enviado,:Aceptado,:Estado,
                            :XML,:LinkConsulta,:clave,
                            :fecha_emision,:UltimoError,:XMLRespuesta,
                    :TotalServGravados,             :TotalServExentos,
                    :TotalServExonerado,            :TotalMercanciasGravadas,
                    :TotalMercanciasExentas,        :TotalMercExonerada,    :TotalGravado,
                    :TotalExento,                   :TotalExonerado,        :TotalVenta,
                    :TotalDescuentos,               :TotalVentaNeta,        :TotalImpuesto,
                    :TotalComprobante,              :TotalIVADevuelto,      :TotalOtrosCargos,
                    :EmailEnviado,:estado_comprobante,:pdf_path,:xml_path)";
            try {
                $stm = $dbConn->prepare($sql);
                $stm->execute([':TipoDoc' => $TipoDoc, ':Documento' => $Documento, ':emisor_num_identif' => $emisor_num_identif,
                    ':emisor_email' => $emisor_email, ':emisor_gln' => $emisor_gln, ':fecha_timbrado' => $fecha_timbrado,
                    ':receptor_nit' => $receptor_nit, ':receptor_email' => $receptor_email, ':receptor_gln' => $receptor_gln,
                    ':Enviado' => $Enviado, ':Aceptado' => $Aceptado, ':Estado' => $Estado,
                    ':XML' => $XML, ':LinkConsulta' => $LinkConsulta, ':clave' => $clave,
                    ':fecha_emision' => $fecha_emision, ':UltimoError' => $UltimoError, ':XMLRespuesta' => $XMLRespuesta,
                    ':TotalServGravados' => $Totales->TotalServGravados, ':TotalServExentos' => $Totales->TotalServExentos,
                    ':TotalServExonerado' => $Totales->TotalServExonerado, ':TotalMercanciasGravadas' => $Totales->TotalMercanciasGravadas,
                    ':TotalMercanciasExentas' => $Totales->TotalMercanciasExentas, ':TotalMercExonerada' => $Totales->TotalMercExonerada, ':TotalGravado' => $Totales->TotalGravado,
                    ':TotalExento' => $Totales->TotalExento, ':TotalExonerado' => $Totales->TotalExonerado, ':TotalVenta' => $Totales->TotalVenta,
                    ':TotalDescuentos' => $Totales->TotalDescuentos, ':TotalVentaNeta' => $Totales->TotalVentaNeta, ':TotalImpuesto' => $Totales->TotalImpuesto,
                    ':TotalComprobante' => $Totales->TotalComprobante, ':TotalIVADevuelto' => $Totales->TotalIVADevuelto, ':TotalOtrosCargos' => $Totales->TotalOtrosCargos,
                    ':EmailEnviado' => 'NO', ':estado_comprobante' => $estado_comprobante, ':pdf_path' => $pdf_path, ':xml_path' => $xml_path]);
            } catch (PDOException $ex) {
                Bitacora($sessionID, "UpdateInsert Error: {$ex->getMessage()}", $Documento, $TipoDoc);
            }
            Bitacora($sessionID, "Datos guardados exitosamente.", $Documento, $TipoDoc);
            GuardarRespuestaHacienda($sessionID, $XMLRespuesta, $Documento, $TipoDoc);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "UpdateInsert Error: {$ex->getMessage()}", $Documento, $TipoDoc);
    }
}

function ObtenerTotalExoneraciones($sessionID, $Documento, $TipoDoc, $Tipo)
{

    $tr = $Tipo === 'SERVICIOS' ? "='Sp' and MontoExoneracionLinea>0" : "<>'Sp' and MontoExoneracionLinea>0";

    $sql = "SELECT isnull(round(SUM(MontoTotalLinea),2),0) as MONTO
         from  DOC_ELEC_DETALLE lin
         WHERE lin.nfac ='$Documento'
         AND UnidadMedida $tr
         ";

    try {
        $result = sql_exec($sql, 1, false, true);
        if (count($result)) {
            return (float) $result["MONTO"];
        } else {
            Bitacora($sessionID, "ObtenerTotalExoneraciones Error al obtener totales de exoneraciones", $Documento, $TipoDoc);
            return (float) 0;
        }
    } catch (Exception $ex) {
        Bitacora($sessionID, "ObtenerTotalExoneraciones Error:  {$ex->getMessage()}", $Documento, $TipoDoc);
        return (float) 0;
    }
}

function GetAndSaveToken($ambiente, $sessionID, $Documento, $TipoDoc)
{
    try {

        $ACCESS_TOKEN = "IDP_" . $ambiente . "_ACCESS_TOKEN";
        $REFRESH_TOKEN = "IDP_" . $ambiente . "_REFRESH_TOKEN";
        $ACCESS_EXPIRES_IN = "IDP_" . $ambiente . "_TOKEN_EXPIRES_IN";
        $REFRESH_EXPIRES_IN = "IDP_" . $ambiente . "_REFRESH_EXPIRES_IN";
        $TiempoActual = date("Y-m-d H:i:s");

        params_set("client_id", rtrim(getDatoCia($sessionID, "API_" . $ambiente . "_CLIENT_ID", $Documento, $TipoDoc)));
        params_set("username", rtrim(getDatoCia($sessionID, "API_" . $ambiente . "_USERNAME", $Documento, $TipoDoc)));
        params_set("password", rtrim(getDatoCia($sessionID, "API_" . $ambiente . "_PASSWORD", $Documento, $TipoDoc)));

        $sql = " select  isnull($ACCESS_TOKEN,'') as $ACCESS_TOKEN,isnull($REFRESH_TOKEN,'') as $REFRESH_TOKEN,
                isnull($ACCESS_EXPIRES_IN,'') as $ACCESS_EXPIRES_IN,
                isnull($REFRESH_EXPIRES_IN,'') as $REFRESH_EXPIRES_IN  FROM COMPAÑIAS
                WHERE CONFIGURADA='SI'
            ";
        $result = sql_exec($sql, 1, true, true);

        //Si esta guardado un access token en la base de datos
        if ((!empty($result[$ACCESS_TOKEN]) or $result = "-200") and !empty($result[$ACCESS_EXPIRES_IN])) {
            //Access Token Expirado?
            if (!empty($result[$ACCESS_TOKEN]) and $result[$ACCESS_EXPIRES_IN] < $TiempoActual) {
                //Refresh Token Expirado?
                if ($result[$REFRESH_EXPIRES_IN] < $TiempoActual) { // O sea los dos tipos de token expiraron
                    //Entonces se solicita un nuevo token(que trae un nuevo access_token y un nuevo_refresh
                    Bitacora($sessionID, "Obteniendo  access_token via password, refresh_token expiró", $Documento, $TipoDoc);
                    params_set("grant_type", "password");
                    $token = token();

                    SaveToken($token, $ambiente, $sessionID, $Documento, $TipoDoc);
                } else //El refresh token no esta vencido, se usa para obtener el access_token
                {
                    Bitacora($sessionID, "Obteniendo access_token via refresh_token", $Documento, $TipoDoc);
                    params_set("grant_type", "refresh_token");
                    params_set("refresh_token", $result[$REFRESH_TOKEN]);
                    $token = token();
                    SaveToken($token, $ambiente, $sessionID, $Documento, $TipoDoc);
                }
            }
        } else { //Es la primera vez que se solicita token
            params_set("grant_type", "password");
            //Aca se obtiene el token
            Bitacora($sessionID, "Obteniendo access_token nuevo via password ", $Documento, $TipoDoc);
            $token = token();
            //Aca lo guardamos en la bd
            SaveToken($token, $ambiente, $sessionID, $Documento, $TipoDoc);
        }

    } catch (Exception $ex) {
        Bitacora($sessionID, "RefreshToken Error: $ex.getMessage() ( {$ex->getLine()})", $Documento, $TipoDoc);
        return "";
    }
}

function SaveToken($token, $ambiente, $sessionID, $Documento, $TipoDoc)
{
    try {

        $ACCESS_TOKEN = "IDP_" . $ambiente . "_ACCESS_TOKEN";
        $REFRESH_TOKEN = "IDP_" . $ambiente . "_REFRESH_TOKEN";

        $ACCESS_EXPIRES_IN = "IDP_" . $ambiente . "_TOKEN_EXPIRES_IN";
        $REFRESH_EXPIRES_IN = "IDP_" . $ambiente . "_REFRESH_EXPIRES_IN";

        $RefreshExpiresOn = date_create(date('Y-m-d H:i:s'));
        $AccessExpiresOn = date_create(date('Y-m-d H:i:s'));
        if (isset($token->error)) {
            Bitacora($sessionID, "SaveToken Error: $token->error, Descripcion:$token->error_description", $Documento, $TipoDoc);
            SetContinuar(false);
            return;
        }

        $AccessExpiresOn = date_format(date_add($AccessExpiresOn, date_interval_create_from_date_string($token->expires_in . ' seconds')), 'Y-m-d H:i:s');
        $RefreshExpiresOn = date_format(date_add($RefreshExpiresOn, date_interval_create_from_date_string($token->refresh_expires_in . ' seconds')), 'Y-m-d H:i:s');

        Bitacora($sessionID, "Guardando token en base de datos", $Documento, $TipoDoc);
        $sql = "UPDATE COMPAÑIAS SET
                $ACCESS_TOKEN='$token->access_token',
                $REFRESH_TOKEN='$token->refresh_token',
                $ACCESS_EXPIRES_IN='$AccessExpiresOn',
                $REFRESH_EXPIRES_IN='$RefreshExpiresOn'
                WHERE CONFIGURADA='SI'
            ";

        sql_exec($sql, 0, true, false);
    } catch (Exception $ex) {
        Bitacora($sessionID, "SaveToken Error: $ex.getMessage()", $Documento, $TipoDoc);
        return "";
    }

}

function ObtenerTotalDescuentoOImpuestoDeLineas(string $sessionID, string $Documento, string $TipoDoc, string $Columna)
{
    //Obtener
    $sql = "SELECT round(ABS(isnull(sum($Columna),0)),2)  as MONTO from DOC_ELEC_DETALLE fac WHERE fac.nfac ='$Documento'";
    try {
        $result = sql_exec($sql, 2, false, true);
        $dataArr = is_array($result) ? $result : array($result);
        // Call Read before accessing data.
        foreach ($dataArr as $monto) {
            if ($monto == -200) {
                return 0;
            } else {
                return $monto["MONTO"];
            }

        }
        return 0;
    } catch (Exception $ex) {
        Bitacora($sessionID, "ObtenerTotalDescuentoOImpuestoDeLineas:  {$ex->getMessage()}", $Documento, $TipoDoc);
        return 0;
    }
}

function getSessionID()
{
    $mt = microtime(true);

    $mt = $mt * 1000; //microsecs
    return (string) $mt * 10; //14341946614384

}
function CargarConfiguraciones($sessionID)
{
    //Documentos a procesar por ejecucion
    $docs_lote = ObtenerDatoTabla($sessionID, "DOCS_LOTE", "COMPAÑIAS", "CONFIGURADA", "SI");
    if (!empty($docs_lote)) {
        $top = " TOP $docs_lote";
        params_set("batch_docs", $top);
    }
    $xml_path = ObtenerDatoTabla($sessionID, "XML_PATH", "COMPAÑIAS", "CONFIGURADA", "SI");
    if (!empty($xml_path)) {
        params_set("xml_path", $xml_path);
    }
    $pdf_path = ObtenerDatoTabla($sessionID, "PDF_PATH", "COMPAÑIAS", "CONFIGURADA", "SI");
    if (!empty($xml_path)) {
        params_set("pdf_path", $pdf_path);
    }
    $overwrite = ObtenerDatoTabla($sessionID, "OVERWRITE_FILES", "COMPAÑIAS", "CONFIGURADA", "SI");
    if (!empty($overwrite)) {
        params_set("overwrite", $overwrite);
    }
    $template_path = ObtenerDatoTabla($sessionID, "TEMPLATE_PATH", "COMPAÑIAS", "CONFIGURADA", "SI");
    if (!empty($template_path)) {
        params_set("template_path", $template_path);
    }
}

function Iniciar()
{
    date_default_timezone_set('America/Costa_Rica');

    $sessionID = getSessionID();
    CargarConfiguraciones($sessionID);

    if (EnProceso($sessionID) == true) {
        Bitacora($sessionID, "El proceso ya fue iniciado desde otra instancia, esta bloqueado y no se permite mas de un proceso...salimos", "", "");
        return;
    }

    try {
        if (!TestCertificateDates($sessionID)) {
            return;
        }

        ProcesarPendientesEnvio($sessionID);
        ProcesarPendientesRespuesta($sessionID);
        ProcesarCorreosPendientes($sessionID);
        SetEnProceso($sessionID, "NO");
        CloseTokenSession($sessionID);

    } catch (Exception $ex) {
        Bitacora($sessionID, $ex->getMessage(), "", "");
        SetEnProceso($sessionID, "NO");
    }

}
function CloseTokenSession($sessionID)
{

    try {

        $ambiente = strtoupper(rtrim(getDatoCia($sessionID, "AMBIENTE", "", "")));
        $URL = "IDP_" . $ambiente . "_LOGOUT_URL";
        $cid = "API_" . $ambiente . "_CLIENT_ID";
        $rft = "IDP_" . $ambiente . "_REFRESH_TOKEN";
        $rft_time = "IDP_" . $ambiente . "_REFRESH_EXPIRES_IN";
        params_set("ambiente", $ambiente);
        $urlCierre = getDatoCia($sessionID, "$URL", "", "");
        $client_id = getDatoCia($sessionID, "$cid", "", "");
        IniciarProcesoToken($sessionID, "", "");

        $refresh_token = params_get("refresh_token");

        // Validation
        if ($client_id == '') {
            return "El parametro Client ID es requerido";
        } else if ($refresh_token == '') {
            return "El parametro Refresh Token es requerido";
        }

        $data = array(
            'client_id' => $client_id,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
        );

        $curl = curl_init($urlCierre);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, 'Content-Type: application/x-www-form-urlencoded');
        $data = http_build_query($data);
        //$data = rtrim($data, '&');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        grace_debug(__METHOD__ . "-> JSON: " . $data);

        $respuesta = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = json_decode(curl_error($curl));
        curl_close($curl);

        if ($err) {
            Bitacora($sessionID, "err=$err,resp=$respuesta,status=$status");
        } else {
            Bitacora($sessionID, "Se cierra sesion con el IDP de hacienda correctamente");
            $sql = "update dbo.COMPAÑIAS SET $rft=NULL,$rft_time=NULL WHERE CONFIGURADA='SI'";
            sql_exec($sql);

        }
    } catch (Exception $ex) {
        Bitacora($sessionID, $ex->getMessage(), "", "");
    }

}
function TestCertificateDates($sessionID)
{

    $ambiente = strtoupper(rtrim(getDatoCia($sessionID, "AMBIENTE", "", "")));

    $PIN = "IDP_" . $ambiente . "_PIN";
    $URL = "IDP_" . $ambiente . "_CERT_URL";

    if (!$cert_store = file_get_contents(getDatoCia($sessionID, $URL, "", ""))) {
        Bitacora($sessionID, "No se logro obtener el archivo de certificado para firmar documentos.El proceso de documentos no sera ejecutado.", "", "");
        return false;
    }

    if (openssl_pkcs12_read($cert_store, $cert_info, getDatoCia($sessionID, $PIN, "", ""))) {
        // print_r($cert_info);
        $certdata = openssl_x509_parse($cert_info['cert'], 0);
        $valid_from = new DateTime(date(DATE_RFC2822, $certdata['validFrom_time_t']));
        $valid_to = new DateTime(date(DATE_RFC2822, $certdata['validTo_time_t']));
        $now = new DateTime('NOW');

        if ($now > $valid_to) {
            Bitacora($sessionID, "El certificado ha vencido.El proceso de documentos no sera ejecutado.", "", "");
            return false;
        }
        if ($now < $valid_from) {
            Bitacora($sessionID, "El certificado no puede usarse todavia, la fecha actual es menor que la del certificado.El proceso de documentos no sera ejecutado.", "", "");
            return false;
        }
        return true;
    } else {
        Bitacora($sessionID, "No se logro obtener los datos del certstore .El proceso de documentos no sera ejecutado.", "", "");
        return false;
    }

}
function ProcesarCorreosPendientes($sessionID, $Documento = "", $TipoDoc = "")
{
    try {

        if (db_allGood()) {
            $top = params_get("batch_docs");
            $sql = " SELECT $top NUMERO_DOC,TIPO_DOC,EMAIL_EMISOR,EMAIL_RECEPTOR,cli.correo as CORREO_CLIENTE,NIT_RECEPTOR,cli.NOMBRE,DOCUMENTO_ELECTRONICO,SELLO_SAT,
             FECHATIMBRADO,XML_RESPUESTA,LINK_CONSULTA,PDF,TOTALCOMPROBANTE,TOTALIMPUESTO,XML_PATH,PDF_PATH 
             
			 FROM DOC_ELECTRONICO_PROCESADO dep
			 inner join clientes cli
			 on dep.NIT_RECEPTOR =cli.cedula
             WHERE EMAIL_ENVIADO='NO'
             and not XML_PATH is null
             and not PDF_PATH is null";
            if ($Documento != "") {
                $sql = $sql . " AND NUMERO_DOC='$Documento'";
            }

            $res = sql_exec($sql, 2, false, true);

        }
        if ($res == '-200') {
            if (!empty($Documento)) {
                Bitacora($sessionID, "Este documento no tiene correo pendiente de enviar", $Documento, $TipoDoc);
                return;
            } else {
                Bitacora($sessionID, "No se encontraron correos pendientes de procesar  ");
                return;
            }

        }

        $total_documentos = count($res);
        $i = 1;
        foreach ($res as $correo) {
            $procesar = true;

            $Documento = $correo["NUMERO_DOC"];
            $TipoDoc = $correo["TIPO_DOC"];

            $clave = ObtenerDatoTabla($sessionID, "SELLO_SAT", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", "$Documento");
            if ($clave == "0") {
                Bitacora($sessionID, "El no tiene una clave asignada", $Documento, $TipoDoc);
                return;
            }
            $fecha_timbrado=$correo["FECHATIMBRADO"];
            $directory_pdf = ObtenerDatoTabla($sessionID, "PDF_PATH", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", "$Documento");
            $directory_xml = ObtenerDatoTabla($sessionID, "XML_PATH", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", "$Documento");

            $pdf_filename = $clave . ".pdf";
            $xml_filename = $clave . ".xml";
            $xml_resp_filename = "RSP_" . $clave . ".xml";

            //Si no tienen este dato no se pueden enviar los correos
            if (strlen($directory_pdf) <= 0) {
                Bitacora($sessionID, "El directorio de almacenamiento de PDFs obtenido de la base de datos,  no existe", $Documento, $TipoDoc);
                return;
            }
            if (strlen($directory_xml) <= 0) {
                Bitacora($sessionID, "El directorio de almacenamiento de XMLs obtenido de la base de datos, no existe", $Documento, $TipoDoc);
                return;
            }
            Bitacora($sessionID, "Procesando correo $i de $total_documentos ", rtrim($Documento), rtrim($TipoDoc));
            //Prueba si tengo todos los adjuntos,sino los tengo no debo enviar los correos.
            $existe_pdf = file_exists($directory_pdf . "/" . $pdf_filename);
            $existe_xml = file_exists($directory_xml . "/" . $xml_filename);
            $existe_rsp = file_exists($directory_xml . "/" . $xml_resp_filename);
            //Intento crearlo del que este en la base de datos
           

            if (!$existe_rsp) {
                try {
                    $xmlRespuesta = ObtenerDatoTabla($sessionID, "XML_RESPUESTA", "DOC_ELECTRONICO_PROCESADO", "SELLO_SAT", $clave);
                } catch (Exception $ex) {
                    //No hacemos nada, la idea es que no se detenga
                }
                if (!empty($xmlRespuesta)) {
                    try {
                        $fecha_timbrado = ObtenerDatoTabla($sessionID, "FECHATIMBRADO", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", $Documento);
                    } catch (Exception $ex) {
                        //No hacemos nada, la idea es que no se detenga
                    }
                    if (!empty($fecha_timbrado) and !empty($xmlRespuesta)) {
                        saveFile("RSP_" . $clave . ".xml", $xmlRespuesta, "XML", $fecha_timbrado, $Documento, $TipoDoc, $sessionID);
                        Bitacora($sessionID, "XML Respuesta creado a partir de la base de datos", $Documento, $TipoDoc);
                        $existe_rsp = true;
                    }

                }
            }
            if (!$existe_pdf){
                GenerarPDF($sessionID,$Documento,$TipoDoc);
            }
            if (!$existe_pdf or !$existe_xml or !$existe_rsp) {
                if (!$existe_pdf) {
                    Bitacora($sessionID, "El PDF no existe , no se enviara el correo", $Documento, $TipoDoc);
                }

                if (!$existe_xml) {
                    Bitacora($sessionID, "El XML no existe , no se enviara el correo", $Documento, $TipoDoc);
                }

                if (!$existe_rsp) {
                    Bitacora($sessionID, "El XML de respuesta no existe , no se enviara el correo", $Documento, $TipoDoc);
                }

                $procesar = false; //Pasa al siguiente correo
            }
            if ($procesar) {
                $host = getDatoCia($sessionID, "MAIL_HOST", $Documento, $TipoDoc);
                $port = getDatoCia($sessionID, "MAIL_PORT", $Documento, $TipoDoc);
                $username = getDatoCia($sessionID, "MAIL_USERNAME", $Documento, $TipoDoc);
                $password = getDatoCia($sessionID, "MAIL_PASSWORD", $Documento, $TipoDoc);

                $ssl_tls = "";
                if (getDatoCia($sessionID, "MAIL_SSL", $Documento, $TipoDoc) == true and getDatoCia($sessionID, "MAIL_TLS", $Documento, $TipoDoc) == false) {
                    $ssl_tls = "ssl";
                }
                if (getDatoCia($sessionID, "MAIL_SSL", $Documento, $TipoDoc) == false and getDatoCia($sessionID, "MAIL_TLS", $Documento, $TipoDoc) == true) {
                    $ssl_tls = "ssl";
                }
                if (getDatoCia($sessionID, "MAIL_SSL", $Documento, $TipoDoc) == false and getDatoCia($sessionID, "MAIL_TLS", $Documento, $TipoDoc) == false) {
                    $ssl_tls = "";
                }

                $transport = (new Swift_SmtpTransport($host, $port, $ssl_tls))
                    ->setUsername($username)
                    ->setPassword($password);
                //************************REGISTRO DE PLUGINS****************************
                // Create the Mailer using your created Transport
                $mailer = new Swift_Mailer($transport);

                // And specify a time in seconds to pause fo
                $af_limit = getDatoCia($sessionID, "MAIL_AF_LIMIT", $Documento, $TipoDoc);
                $af_pause = getDatoCia($sessionID, "MAIL_AF_LIMIT", $Documento, $TipoDoc);
                $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin($af_limit, $af_pause));

                // Rate limit to 100 emails per-minute
                $thr_msg_min = getDatoCia($sessionID, "MAIL_THR_MSG_MINUTE", $Documento, $TipoDoc);
                $thr_mb_min = getDatoCia($sessionID, "MAIL_THR_MB_MINUTE", $Documento, $TipoDoc);
                $mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
                    $thr_msg_min, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
                ));
                // Rate limit to 10MB per-minute
                $mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
                    1024 * 1024 * $thr_mb_min, Swift_Plugins_ThrottlerPlugin::BYTES_PER_MINUTE
                ));
                // To use the ArrayLogger
                //$logger = new Swift_Plugins_Loggers_ArrayLogger();
                //$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

                // Create a message
                $message = new Swift_Message();

                // Set a "subject"
                $ambiente = strtoupper(rtrim(getDatoCia($sessionID, "AMBIENTE", $Documento, $TipoDoc)));

                if($TipoDoc=="FE") $DescDocumento = "Factura";
                if ($TipoDoc=="NC") $DescDocumento = "Nota de Credito ";
                if ($TipoDoc=="TE") $DescDocumento = "Tiquete";
                if ($TipoDoc=="ND") $DescDocumento = "Nota de Debito";
                
                $subject = "Los Paticas, $DescDocumento: $Documento";
                if ($ambiente == "STAG") {
                    $subject = $subject . " -  AMBIENTE DE PRUEBAS -";
                }
                $message->setSubject($subject);

                $cliente = $correo["NOMBRE"];

                // Set the "From address"
                $mail_emisor = $correo["EMAIL_EMISOR"];
                $nombre_emisor = getDatoCia($sessionID, "NOMBRE_COMERCIAL", $Documento, $TipoDoc);
                $message->setFrom([$mail_emisor => $nombre_emisor]);

                // Set the "To address" [Use setTo method for multiple recipients, argument should be array]
                $mail_receptors = explode(';',$correo["CORREO_CLIENTE"]);
                foreach($mail_receptors as $mail_receptor){
                    $message->addTo($mail_receptor, $cliente);
                    Bitacora($sessionID, "Agregando destinatario: $mail_receptor", $Documento, $TipoDoc);

                }

                //PDF
                $attachment = Swift_Attachment::fromPath($directory_pdf . "/" . $pdf_filename);
                $attachment->setFilename($pdf_filename);
                $message->attach($attachment);
                //XML
                $attachment = Swift_Attachment::fromPath($directory_xml . "/" . $xml_filename);
                $attachment->setFilename($xml_filename);
                $message->attach($attachment);
                //XML RESPUESTA
                $attachment = Swift_Attachment::fromPath($directory_xml . "/" . $xml_resp_filename);
                $attachment->setFilename($xml_resp_filename);
                $message->attach($attachment);

                // Set the plain-text "Body"
                $total_comprobamte = $correo["TOTALCOMPROBANTE"];
                $total_impuesto = $correo["TOTALIMPUESTO"];

                // Set a "Body" in html

                if (!defined("TEMPLATE_PATH")) {
                    define("TEMPLATE_PATH", params_get("template_path"));
                }
                $view = new Template();

                $path = getDatoCia($sessionID, "TEMPLATE_PATH", $Documento, $TipoDoc);
                $view->setScriptPath($path);
                $view->numero_doc = $clave;
                $view->total_impuesto = $total_impuesto;
                $view->total_doc = $total_comprobamte;
                $cidWH = $message->embed(Swift_Image::fromPath($path . "images/whatsapp.png"));
                $cidFA = $message->embed(Swift_Image::fromPath($path . "images/facebook.png"));
				$cidLogo = $message->embed(Swift_Image::fromPath($path . "images/cuerpo.jpg"));
                $view->cidWH = $cidWH;
                $view->cidFA = $cidFA;
				$view->cidLogo = $cidLogo;
                $html = $view->render('documento.php');

                $message->setBody($html, "text/html");
                // Send the message
                $numSent = $mailer->send($message);
                if (!$numSent) {
                    Bitacora($sessionID, "El mensaje no puedo ser enviado para el documento # $clave", $Documento, $TipoDoc);
                    UpdateMailSend('NO', $clave);
                } else {
                    Bitacora($sessionID, "**El mensaje fue enviado al cliente exitosamente**", $Documento, $TipoDoc);
                    if ($clave != "") {
                        UpdateMailSend('SI', $clave);
                    }

                }
            }
            $i++;
        }

    } catch (Exception $e) {
        if (isset($logger)) {
            Bitacora($sessionID, "Error al enviar correo, Descripcion:" . $e->getMessage() . "(" . $e->getLine() . "), \n Dump:\n" . $logger->dump(), $Documento, $TipoDoc);
            Bitacora($sessionID, "Dump:\n" . $logger->dump(), $Documento, $TipoDoc);
        }

    }

}
function UpdateMailSend($enviado, $clave)
{
    sql_exec("update DOC_ELECTRONICO_PROCESADO set EMAIL_ENVIADO ='$enviado' WHERE SELLO_SAT='$clave'", 0, true, false);
}
function ProcesarPendientesRespuesta(string $sessionID)
{
    $top = params_get("batch_docs");
    $sql = "  SELECT $top d.TIPO_DOC, d.NUMERO_DOC, d.LINK_CONSULTA,d.SELLO_SAT
         FROM  DOC_ELECTRONICO_PROCESADO  d
        WHERE (DATALENGTH( d.XML_RESPUESTA) < 6  OR d.XML_RESPUESTA is NULL)        
        AND  d.FECHATIMBRADO  >= '2019-07-01'
        ORDER BY d.FECHATIMBRADO ASC
";

    $result = sql_exec($sql, 2, false, true);
    if ($result == "-200") {
        Bitacora($sessionID, "No se encontraron documentos pendientes");
        SetEnProceso($sessionID, 'NO');
    } else {
        $total_documentos = count($result);
        $i = 1;
        Bitacora($sessionID, "  INICIA PROCESAMIENTO DE PENDIENTES DE RESPUESTA");
        foreach ($result as $doc) {
            SetEnProceso($sessionID, 'SI');
            Bitacora($sessionID, "Procesando documento   " . $doc["NUMERO_DOC"] . "( $i de $total_documentos )", rtrim($doc["NUMERO_DOC"]), rtrim($doc["TIPO_DOC"]));
            //ConsultarDirecto($sessionID, rtrim($doc["NUMERO_DOC"]), rtrim($doc["TIPO_DOC"]));
            ProcesarDocumento($sessionID, rtrim($doc["NUMERO_DOC"]), rtrim($doc["TIPO_DOC"]));
            $i++;
        }
        Bitacora($sessionID, "TERMINA PROCESAMIENTO DE PENDIENTES");
        SetEnProceso($sessionID, 'NO');
    }
}

function ProcesarPendientesEnvio(string $sessionID)
{
    //Seleccionar lista de documentos a procesar
    try {

        //Iniciar proceso
        SetEnProceso($sessionID, 'S');
        $top = params_get("batch_docs");

        $myNit = getDatoCia($sessionID, "NIT", "", "");
        $fechaInicio = getDatoCia($sessionID, "FECHA_NORMATIVA", "", "");
        $sql = "SELECT  $top V.TIPO_DOCUMENTO, V.DOCUMENTO, V.NIT, case V.CORREO when '1' then 'facturaelectronica@lospaticas.com' else correo end as CORREO,
                         V.GLN,v.CLAVE,v.SAT
                         FROM V_DOCUMENTO_ELECTRONICO v
                         LEFT JOIN DOC_ELECTRONICO_PROCESADO p
                         ON v.DOCUMENTO=p.NUMERO_DOC
                         WHERE v.ACEPTA_DOC_ELECTRONICO = 'S' AND v.NIT NOT IN ('$myNit')
                         AND (p.ENVIADO='S' AND p.ACEPTADO='N')
                         AND V.TIPO_DOCUMENTO IN ('FE','NC','ND','TE')                         
                         AND NOT v.SAT is NULL
                         and v.FECHA >= '$fechaInicio'
                         
                        OR (
                               ENVIADO is NULL AND ACEPTADO is NULL  and v.FECHA >= '$fechaInicio'
                               AND v.ACEPTA_DOC_ELECTRONICO = 'S' AND v.NIT NOT IN('$myNit')
                               AND V.TIPO_DOCUMENTO IN('FE', 'NC', 'ND', 'TE')
                               AND v.DOCUMENTO <> 'F-00000000027825'                               
                           )
                        OR (
                               ENVIADO ='N'  and v.FECHA >= '$fechaInicio'
                               AND v.ACEPTA_DOC_ELECTRONICO = 'S' AND v.NIT NOT IN('$myNit')
                               AND V.TIPO_DOCUMENTO IN('FE', 'NC', 'ND', 'TE')
                               AND v.DOCUMENTO <> 'F-00000000027825'                               
                           )
                         AND v.DOCUMENTO <> 'F-00000000027825'
                         ORDER BY v.DOCUMENTO";
        $result = sql_exec($sql, 2, false, true);
        //echo $sql;
        if ($result == "-200") {
            Bitacora($sessionID, "No se encontraron nuevos documentos para procesar");
            return;
        } else {
            Bitacora($sessionID, "*******************INICIA PROCESAMIENTO DE PENDIENTES DE ENVIO*****************");
            $total_documentos = count($result);
            $i = 1;
            foreach ($result as $doc) {
                Bitacora($sessionID, "----------------------- INICIA DOCUMENTO-------------------------------", rtrim($doc["DOCUMENTO"]), rtrim($doc["TIPO_DOCUMENTO"]));
                Bitacora($sessionID, "Procesando documento " . $doc["DOCUMENTO"] . "( $i de $total_documentos )");
                ProcesarDocumento($sessionID, rtrim($doc["DOCUMENTO"]), rtrim($doc["TIPO_DOCUMENTO"]));
                $i++;
                //Bitacora($sessionID, "----------------------- TERMINA DOCUMENTO-------------------------------", rtrim($doc["DOCUMENTO"]),rtrim($doc["TIPO_DOCUMENTO"]) );
            }
            SetEnProceso($sessionID, 'NO');
        }
    } catch (Exception $ex) {
        Bitacora($sessionID, "ProcesarPendientesEnvio Error: {$ex->getMessage()}", "", "");
    }
}

function getProdCredentials()
{
    $q = "SELECT  PROD_USERNAME,PROD_PASSWORD,PROD_PIN,PROD_RUTA_CERT,PROD_FILE_CERT";
    return sql_exec($q, 2);
}

function getStagCredentials()
{
    $q = "SELECT  STAG_USERNAME,STAG_PASSWORD,STAG_PIN,STAG_RUTA_CERT,STAG_FILE_CERT";
    return sql_exec($q, 2);
}

function getCompannyInformation()
{

    $q = "select    *  from COMPAÑIAS WHERE CONFIGURADA='SI'";
    $result = sql_exec($q, 2);
    return $result;

}

function GetTiposIdentificacion()
{
    $q = "SELECT * FROM DE_TIPOS_IDENTIFICACION ";
    $result = sql_exec($q, 2);
    return $result;
}

/*
 *  Obtiene todos los datos de la compana
 *
 */
function getCompannyInformationAdmin()
{
    //$idMasterUser = params_get("idMasterUser");
    $q = "select * FROM  COMPAÑIAS  WHERE CONFIGURADA='SI'";
    $result = sql_exec($q, 2); //Con 2 se obtienen todos los registros
    return $result;
}

function getCompannyLocationInformation()
{
    $idProvincia = params_get("idProvincia");
    $idCanton = params_get("idCanton");
    $idDistrito = params_get("idDistrito");
    $idBarrio = params_get("idBarrio");

    $q = "SELECT P1,PROVINCIA,C1,CANTON,D1,DISTRITO,B1,BARRIO FROM UBICACIONES  WHERE P1='" . (int) $idProvincia . "' and C1='" . (int) $idCanton . "' and D1='" . (int) $idDistrito . "' and B1='" . (int) $idBarrio . "'";
    $result = sql_exec($q, 2);

    return $result;
}

function compannyUpdateTipoCambio()
{
    $idMasterUser = params_get("idMasterUser");
    $tipoCambio = params_get("tipoCambio");
    $q = "UPDATE  SET `value` = '" . $tipoCambio . "' WHERE `name`='TIPOCAMBIO'";
    $result = sql_exec($q, 0);
    return $result;
}

function compannyUpdateLocation()
{
    $idMasterUser = params_get("idMasterUser");
    $idProvincia = params_get("idProvincia");
    $idCanton = params_get("idCanton");
    $idDistrito = params_get("idDistrito");
    $idBarrio = params_get("idBarrio");
    $sennas = params_get("sennas");
    $q = "UPDATE  SET `value` = '" . $idProvincia . "' WHERE `name`='PROVINCIA'";
    sql_exec($q, 0);
    $q = "UPDATE  SET `value` = '" . $idCanton . "' WHERE `name`='CANTON'";
    sql_exec($q, 0);
    $q = "UPDATE  SET `value` = '" . $idDistrito . "' WHERE `name`='DISTRITO'";
    sql_exec($q, 0);
    $q = "UPDATE  SET `value` = '" . $idBarrio . "' WHERE `name`='BARRIO'";
    sql_exec($q, 0);
    $q = "UPDATE  SET `value` = '" . $sennas . "' WHERE `name`='SENNAS'";
    $result = sql_exec($q, 0);
    return $result;
}

function compannyUpdateInformation()
{

    $NOMBRE = params_get('NOMBRE');
    $NOMBRE_COMERCIAL = params_get('NOMBRE_COMERCIAL');
    $CODIGO_PAIS = params_get("CODIGO_PAIS");

    $TELEFONO = params_get("TELEFONO");
    $TELEFONO2 = params_get("TELEFONO2");
    $CORREO = params_get("CORREO");
    $GLN = params_get("GLN");

    $TIPO_NIT = params_get("TIPO_NIT");
    $NIT = params_get("NIT");
    $EMAIL_DOC_ELECTRONICO = params_get("EMAIL_DOC_ELECTRONICO");

    $COD_PAIS_ISO = params_get("COD_PAIS_ISO");
    $PROVINCIA = params_get("PROVINCIA");
    $CANTON = params_get("CANTON");
    $DISTRITO = params_get("DISTRITO");
    $BARRIO = params_get("BARRIO");
    $OTRAS_SENAS = params_get("OTRAS_SENAS");

    $NORMATIVA = params_get("NORMATIVA");
    $FECHA_NORMATIVA = params_get("FECHA_NORMATIVA");

    $q = "UPDATE dbo.COMPAÑIAS
                SET NOMBRE='$NOMBRE',
                    NOMBRE_COMERCIAL='$NOMBRE_COMERCIAL',
                    PAIS='$CODIGO_PAIS',
                    TELEFONO_1='$TELEFONO',
                    TELEFONO_2='$TELEFONO2',
                    CORREO='$CORREO',
                    GLN='$GLN',
                    TIPO_NIT='$TIPO_NIT',
                    NIT='$NIT',
                    EMAIL_DOC_ELECTRONICO='$EMAIL_DOC_ELECTRONICO',
                    COD_PAIS_ISO='$COD_PAIS_ISO',
                    DIVISION_GEOGRAFICA1='$PROVINCIA',
                    DIVISION_GEOGRAFICA2='$CANTON',
                    DIVISION_GEOGRAFICA3='$DISTRITO',
                    DIVISION_GEOGRAFICA4='$BARRIO',
                    OTRAS_SENAS='$OTRAS_SENAS',
                    NORMATIVA='$NORMATIVA',
                    FECHA_NORMATIVA='$FECHA_NORMATIVA'
                WHERE NIT='$NIT' ";

    return sql_exec($q, -1, true);
    //return 1;
}

function companyChangeEnv()
{

    $envProduccion = params_get("envProduccion");

    if ($envProduccion == 'true') {
        $q = "update  COMPAÑIAS set AMBIENTE = 'api-prod' WHERE CONFIGURADA='SI' ";
    } else {
        $q = "update COMPAÑIAS  set AMBIENTE = 'api-stag' WHERE CONFIGURADA='SI'";
    }
    sql_exec($q, 0);
    return "ok";
}

function companyGetEnv()
{
    $q = "select AMBIENTE from COMPAÑIAS WHERE CONFIGURADA='SI'";
    $result = sql_exec($q, 2);
    return $result;
}

function companyProdUsers()
{

    $userName = params_get("userName");
    $password = params_get("password");
    $pinCerti = params_get("pinCerti");

    $q = "update COMPAÑIAS set PROD_USERNAME = ' $userName' WHERE CONFIGURADA='SI' ";
    sql_exec($q, 0);
    $q = "update COMPAÑIAS set PROD_PASSWORD = ' $password' WHERE CONFIGURADA='SI'";
    sql_exec($q, 0);
    $q = "update COMPAÑIAS set PROD_PIN= ' $pinCerti' WHERE CONFIGURADA='SI'";
    sql_exec($q, 0);
    return "ok";
}

function GetProvincias()
{
    $idProvincia = params_get("idProvincia");

    if ($idProvincia) {
        $q = "SELECT DISTINCT(P1),PROVINCIA  FROM   UBICACIONES  where P1  = '" . $idProvincia . "' order by P1";
    } else {
        $q = "SELECT DISTINCT(P1),PROVINCIA  FROM   UBICACIONES order by P1";
    }

    $result = sql_exec($q, 2);

    return $result;
}

/* Return cantons by province */
function GetCantones()
{

    $idProvincia = params_get("idProvincia");
    $idCanton = params_get("idCanton");

    if ($idProvincia && $idCanton) {
        $q = "SELECT DISTINCT(C1),CANTON  FROM   UBICACIONES  where P1  = '" . $idProvincia . "' and C1 = '" . $idCanton . "' ORDER by CANTON";
    } elseif ($idProvincia) {
        $q = "SELECT DISTINCT(C1),CANTON  FROM   UBICACIONES  where P1  = '" . $idProvincia . "' ORDER by CANTON";
    } else {
        $q = "SELECT DISTINCT(C1),CANTON  FROM   UBICACIONES ORDER by CANTON";
    }

    $result = sql_exec($q, 2);

    return $result;
}

/* Return district by province and canton */
function GetDistritos()
{

    $idProvincia = params_get("idProvincia");
    $idCanton = params_get("idCanton");
    $idDistrito = params_get("idDistrito");

    if ($idProvincia && $idCanton && $idDistrito) {
        $q = "SELECT DISTINCT(D1),DISTRITO  FROM   UBICACIONES  where P1  = '" . $idProvincia . "' and C1 = '" . $idCanton . "' and D1 = '" . $idDistrito . "' ORDER by DISTRITO";
    } elseif ($idProvincia && $idCanton) {
        $q = "SELECT DISTINCT(D1),DISTRITO  FROM   UBICACIONES  where P1  = '" . $idProvincia . "' and C1 = '" . $idCanton . "' ORDER by DISTRITO";
    } else {
        $q = "SELECT DISTINCT(D1),DISTRITO  FROM   UBICACIONES ORDER by DISTRITO";
    }
    $result = sql_exec($q, 2);
    return $result;
}

function GetBarrios()
{
    $idProvincia = params_get("idProvincia");
    $idCanton = params_get("idCanton");
    $idDistrito = params_get("idDistrito");
    $idBarrio = params_get("idBarrio");

    if ($idProvincia && $idCanton && $idDistrito && $idBarrio) {
        $q = "SELECT DISTINCT(B1),BARRIO  FROM   UBICACIONES  where P1  = '" . $idProvincia . "' and C1 = '" . $idCanton . "' and D1 = '" . $idDistrito . "' and B1='" . $idBarrio . "' ORDER by BARRIO";
    } elseif ($idProvincia && $idCanton && $idDistrito) {
        $q = "SELECT DISTINCT(B1),BARRIO  FROM   UBICACIONES  where P1  = '" . $idProvincia . "' and C1 = '" . $idCanton . "' and D1 = '" . $idDistrito . "' ORDER by BARRIO";
    } else {
        $q = "SELECT DISTINCT(B1),BARRIO  FROM   UBICACIONES ORDER by BARRIO";
    }
    $result = sql_exec($q, 2);
    return $result;
}

/* Return type of id */
