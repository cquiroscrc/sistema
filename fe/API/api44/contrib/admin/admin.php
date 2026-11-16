<?php

define('ADMIN_MODULE_VERSION', 'V.2.0');
define('DOC_ROOT', dirname(__FILE__, 4));

global $continuar;

require_once __DIR__ . '/bitacora-lib.php';


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
        echo "********************************************************************************************\n";
        echo "    S i s t e m a   d e   E n v i o   d e   D o c u m e n t o s   E l e c t r o n i c o s   4.4   \n";
        echo "********************************************************************************************\n";
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
 * Guarda una bitacora de errores/eventos en la base de datos y muestra en consola
 *
 * @param string $sessionID ID de la sesión actual
 * @param string $Texto Mensaje a registrar
 * @param string $Documento Número de documento (opcional)
 * @param string $TipoDoc Tipo de documento (opcional)
 * @param string $nivel Nivel del log: Debug, Info, Warning, Error
 * @param bool $mostrarConsola Si debe mostrar en consola o no
 */
function normalizarTextoBitacora(string $texto): string
{
    $texto = corregirEncodingUtf8($texto);
    $texto = preg_replace('/[\r\n\t]+/', ' | ', $texto);
    $texto = preg_replace('/\s{2,}/', ' ', $texto);
    return trim($texto);
}

function corregirEncodingUtf8(string $texto): string
{
    if ($texto === '' || mb_check_encoding($texto, 'UTF-8') === false) {
        return $texto;
    }

    if (preg_match('/Ã[\x80-\xBF¡-¿]/u', $texto)) {
        $corregido = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
        if ($corregido !== false && mb_check_encoding($corregido, 'UTF-8')) {
            return $corregido;
        }
    }

    return $texto;
}


function bitacora_http_hacienda(int $status): string
{
    switch ($status) {
        case 200:
            return 'Hacienda procesó el XML (HTTP 200): recibido correctamente; se lee el estado (aceptado o rechazado)';
        case 202:
            return 'Hacienda recibió el XML (HTTP 202): pendiente de ser procesado; se consultará el estado en la próxima corrida';
        case 400:
            return 'Hacienda rechazó la solicitud (HTTP 400): XML, clave o esquema inválidos';
        case 401:
            return 'Hacienda no autorizó la petición (HTTP 401): token inválido o vencido';
        case 403:
            return 'Hacienda denegó el acceso (HTTP 403)';
        case 404:
            return 'Hacienda no encontró el recurso (HTTP 404)';
        case 429:
            return 'Hacienda limitó las peticiones (HTTP 429): demasiadas solicitudes; se reintentará en una corrida posterior';
        default:
            if ($status >= 500) {
                return "Error interno de Hacienda (HTTP $status): se reintentará en una corrida posterior";
            }
            if ($status >= 400) {
                return "Error de cliente en la API de Hacienda (HTTP $status)";
            }
            return "Respuesta HTTP $status de Hacienda";
    }
}

function bitacora_clave_corta(?string $clave = null): string
{
    if ($clave === null || $clave === '') {
        try {
            if (function_exists('params_get')) {
                $clave = (string) (params_get('clave') ?? '');
            }
        } catch (Throwable $e) {
            $clave = '';
        }
    }
    $c = preg_replace('/\D/', '', (string) $clave);
    if (strlen($c) >= 8) {
        return 'clave …' . substr($c, -8);
    }
    return '';
}

function bitacora_con_clave(string $texto, ?string $clave = null): string
{
    $c = bitacora_clave_corta($clave);
    if ($c === '' || str_contains($texto, $c)) {
        return $texto;
    }
    return rtrim($texto, " .") . " ($c)";
}

/* Bitacora() vive en bitacora-lib.php */
function module_info()
{
    return array(
        "info" => "Modulo de interface para administracion",
        "version" => ADMIN_MODULE_VERSION,
    );

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
function ConvertirCIIU3ACIIU4(string $sessionID, string $codigoCIIU3, string $Documento, string $TipoDoc): string
{
    try {
        // Normalizamos el código CIIU3 a 6 dígitos con ceros a la izquierda
        $codigoCIIU3Normalizado = str_pad(trim($codigoCIIU3), 6, '0', STR_PAD_LEFT);

        // Consulta SQL para obtener el código CIIU4 correspondiente
        $sql = "
            SELECT RIGHT(CONCAT('000000', TRIM(CAST(CODIGO_ACTIVIDAD_CIIU4 AS VARCHAR(20)))), 6) AS CODIGO_ACTIVIDAD_CIIU4 
            FROM CODIGOS_ACTIVIDAD
            WHERE RIGHT(CONCAT('000000', TRIM(CAST(CODIGO_ACTIVIDAD_CIIU3 AS VARCHAR(20)))), 6) ='$codigoCIIU3Normalizado'
        ";

        $result = sql_exec($sql, 1);

        foreach ($result as $row) {
            $codigoCIIU4 = $row  ?? null;
            return is_null($codigoCIIU4) ? '' : $codigoCIIU4;
        }

        return "";
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ConvertirCIIU3ACIIU4: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
        return "ERROR";
    }
}
function ObtenerDatoClienteCIIU4(string $sessionID, string $dato, string $cliente, string $Documento, string $TipoDoc): string
{
    try {
        // Consulta SQL para obtener el dato desde CODIGOS_ACTIVIDAD, basado en el CIIU3 del cliente
        $sql = "
            SELECT $dato 
            FROM CLIENTES cli
            LEFT JOIN CODIGOS_ACTIVIDAD ca
                ON RIGHT('000000' + TRIM(cli.actividad_comercial), 6) = RIGHT('000000' + TRIM(ca.CODIGO_ACTIVIDAD_CIIU3), 6)
            WHERE cli.codigo = '$cliente'
        ";

        $result = sql_exec($sql, 1);

        foreach ($result as $ElDato) {
            return is_null($ElDato) ? '' : $ElDato;
        }

        return "";
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "ObtenerDatoClienteCIIU4: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
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
        $correo = getDatoCia($sessionID, "CORREO", $Documento, $TipoDoc);
        // Elimina espacios al inicio/final (trim) y espacios en blanco internos si los hubiera
        $correoLimpio = $correo ? preg_replace('/\s+/', '', trim($correo)) : '';

        params_set("emisor_fax", getDatoCia($sessionID, "TELEFONO_2", $Documento, $TipoDoc));
        params_set("emisor_email", $correoLimpio);

        //$codigoCIIU3 = rtrim(getDatoCia($sessionID, "CODIGO_ACTIVIDAD_CIIU4", $Documento, $TipoDoc));
        $codigoCIIU4 = rtrim(getDatoCia($sessionID, "CODIGO_ACTIVIDAD_CIIU4", $Documento, $TipoDoc));
        //$codigoCIIU4 = ConvertirCIIU3ACIIU4($sessionID, $codigoCIIU3, $Documento, $TipoDoc);
        params_set("codigo_actividad", $codigoCIIU4, $Documento, $TipoDoc);


        //params_set("codigo_actividad", rtrim(getDatoCia($sessionID, "CODIGO_ACTIVIDAD", $Documento, $TipoDoc)));
        params_set("proveedor_sistemas", rtrim(getDatoCia($sessionID, "NIT", $Documento, $TipoDoc)));

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
          // Elimina espacios al inicio/final (trim) y espacios en blanco internos si los hubiera
        $correoLimpio =$receptor_email ? preg_replace('/\s+/', '', trim($receptor_email)) : '';
        params_set("receptor_email", $correoLimpio);

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

function obtenerFechaCalendarioDesdeTimestamp(int $timestamp): string
{
    return date('Y-m-d', $timestamp);
}

function obtenerFechaCalendarioHoy(): string
{
    return date('Y-m-d');
}

/**
 * Detecta fechas invalidas para Hacienda antes de generar/enviar el documento.
 * Retorna mensaje de error si debe bloquearse el envio; null si puede continuar.
 */
function validarFechasAntesDeProcesar(string $sessionID, string $Documento, string $TipoDoc): ?string
{
    $hoy = obtenerFechaCalendarioHoy();

    $timestampDocumento = obtenerTimestampFechaDocumento($sessionID, $Documento, $TipoDoc);
    if ($timestampDocumento !== null) {
        $fechaDocumento = obtenerFechaCalendarioDesdeTimestamp($timestampDocumento);
        if ($fechaDocumento > $hoy) {
            return "Documento NO enviado: fechafact ($fechaDocumento) es posterior a hoy ($hoy). "
                . "Probable cierre contable con fecha del dia siguiente. Corrija la fecha en el ERP y reintente.";
        }
    }

    $clave = ObtenerDatoTabla($sessionID, "SAT", "V_DOCUMENTO_ELECTRONICO", "DOCUMENTO", $Documento);
    if (!empty($clave) && $clave !== '0' && strlen($clave) >= 9) {
        $segmentoClave = extraerSegmentoFechaClave($clave);
        $fechaClave = formatearSegmentoFechaClave($segmentoClave);
        $dia = substr($clave, 3, 2);
        $mes = substr($clave, 5, 2);
        $ano = substr($clave, 7, 2);
        $timestampClave = strtotime("20{$ano}-{$mes}-{$dia}");
        if ($timestampClave !== false) {
            $fechaClaveCal = obtenerFechaCalendarioDesdeTimestamp($timestampClave);
            if ($fechaClaveCal > $hoy) {
                return "Documento NO enviado: la clave SAT tiene fecha $fechaClave, posterior a hoy ($hoy). "
                    . "Corrija la clave/fecha en el ERP y reintente.";
            }
        }
    }

    return null;
}

function registrarDocumentoNoEnviado(string $sessionID, string $Documento, string $TipoDoc, string $motivo): void
{
    $codigo = 'DOC_NO_ENVIADO';
    $motivoCorto = 'no enviado';
    if (stripos($motivo, 'fechafact') !== false || stripos($motivo, 'posterior a hoy') !== false) {
        $codigo = (stripos($motivo, 'clave SAT') !== false) ? 'DOC_CLAVE_FECHA_FUTURA' : 'DOC_FECHA_FUTURA';
        $motivoCorto = 'fecha futura';
    }
    bitacora_run_contar('omitidos', 1, $motivoCorto);
    [$escribir, $veces] = bitacora_alerta_registrar($codigo, $motivo, $Documento, $TipoDoc);
    if ($escribir) {
        $texto = $motivo;
        if ($veces > 1) {
            $texto .= " (omitido $veces veces, último intento " . date('H:i') . ')';
        }
        Bitacora($sessionID, $texto, $Documento, $TipoDoc, "Warning", true, "envio", $codigo);
    }

    try {
        global $dbConn;
        if (!$dbConn) {
            return;
        }

        $sql = "SELECT NUMERO_DOC FROM DOC_ELECTRONICO_PROCESADO
                WHERE NUMERO_DOC = :documento AND TIPO_DOC = :tipoDoc";
        $stm = $dbConn->prepare($sql);
        $stm->execute([':documento' => $Documento, ':tipoDoc' => $TipoDoc]);

        // Fecha futura no se marca error: debe seguir en la cola hasta su fecha.
        if ($codigo === 'DOC_FECHA_FUTURA' || $codigo === 'DOC_CLAVE_FECHA_FUTURA') {
            return;
        }
        if ($stm->fetchColumn()) {
            $sqlUpdate = "UPDATE DOC_ELECTRONICO_PROCESADO SET
                ENVIADO = 'N', ACEPTADO = 'N', ERROR_LOCAL = 'S', ERROR_HACIENDA = 'N',
                ESTADO = 'error', ULTIMO_ERROR = :motivo
                WHERE NUMERO_DOC = :documento AND TIPO_DOC = :tipoDoc";
            $dbConn->prepare($sqlUpdate)->execute([
                ':motivo' => mb_substr($motivo, 0, 500),
                ':documento' => $Documento,
                ':tipoDoc' => $TipoDoc,
            ]);
        }
    } catch (Exception $ex) {
        Bitacora($sessionID, "registrarDocumentoNoEnviado Error: {$ex->getMessage()}", $Documento, $TipoDoc, "Warning");
    }
}

function resolverTimestampEmisionHacienda(string $sessionID, string $Documento, string $TipoDoc, string $clave = ''): int
{
    $cacheKey = 'timestamp_emision_' . $Documento . '_' . $TipoDoc;
    $cached = params_get($cacheKey);
    if ($cached !== null && $cached !== '' && is_numeric($cached)) {
        return (int) $cached;
    }

    $timestampDocumento = obtenerTimestampFechaDocumento($sessionID, $Documento, $TipoDoc);

    if ($timestampDocumento !== null) {
        params_set($cacheKey, (string) $timestampDocumento);
        return $timestampDocumento;
    }

    if (empty($clave) || $clave === '0') {
        $clave = params_get('clave') ?? '';
    }
    if (empty($clave) || $clave === '0') {
        $clave = ObtenerDatoTabla($sessionID, "SAT", "V_DOCUMENTO_ELECTRONICO", "DOCUMENTO", $Documento);
    }

    if (!empty($clave) && $clave !== '0' && strlen($clave) >= 9) {
        $dia = substr($clave, 3, 2);
        $mes = substr($clave, 5, 2);
        $ano = substr($clave, 7, 2);
        $timestamp = strtotime("20{$ano}-{$mes}-{$dia}");
        if ($timestamp !== false) {
            params_set($cacheKey, (string) $timestamp);
            return $timestamp;
        }
    }

    $timestamp = time();
    params_set($cacheKey, (string) $timestamp);
    return $timestamp;
}

function obtenerFechaEmisionDocumento(string $sessionID, string $Documento, string $TipoDoc): string
{
    $timestamp = resolverTimestampEmisionHacienda($sessionID, $Documento, $TipoDoc);
    return date("Y-m-d\TH:i:sP", $timestamp);
}

function extraerSegmentoFechaClave(string $clave): string
{
    if (strlen($clave) < 9) {
        return '';
    }
    return substr($clave, 3, 6);
}

function segmentoFechaClaveDesdeTimestamp(int $timestamp): string
{
    return date('d', $timestamp) . date('m', $timestamp) . date('y', $timestamp);
}

function actualizarSegmentoFechaEnClave(string $clave, int $timestamp): string
{
    if (strlen($clave) < 50) {
        return $clave;
    }
    return substr($clave, 0, 3) . segmentoFechaClaveDesdeTimestamp($timestamp) . substr($clave, 9);
}

function formatearSegmentoFechaClave(string $segmento): string
{
    if (strlen($segmento) !== 6) {
        return $segmento;
    }
    return substr($segmento, 0, 2) . '/' . substr($segmento, 2, 2) . '/20' . substr($segmento, 4, 2);
}

function obtenerTimestampFechaDocumento(string $sessionID, string $Documento, string $TipoDoc): ?int
{
    $fechafact = ObtenerDatoFACTURA($sessionID, 'fechafact', $Documento, $TipoDoc);
    if (!empty($fechafact) && $fechafact !== '0') {
        $timestamp = strtotime($fechafact);
        if ($timestamp !== false) {
            return $timestamp;
        }
    }

    $fechaView = ObtenerDatoTabla($sessionID, "FECHA", "V_DOCUMENTO_ELECTRONICO", "DOCUMENTO", $Documento);
    if (!empty($fechaView) && $fechaView !== '0') {
        $timestamp = strtotime($fechaView);
        if ($timestamp !== false) {
            return $timestamp;
        }
    }

    return null;
}

function documentoPermiteCorregirClaveEnBd(string $sessionID, string $Documento, string $TipoDoc): bool
{
    $sql = "SELECT ENVIADO, ACEPTADO, ULTIMO_ERROR, ESTADO
            FROM DOC_ELECTRONICO_PROCESADO
            WHERE NUMERO_DOC='$Documento' AND TIPO_DOC='$TipoDoc'";
    $result = sql_exec($sql, 1, false, true);

    if ($result === "-200" || empty($result)) {
        return true;
    }

    if (!empty($result['ULTIMO_ERROR']) && stripos($result['ULTIMO_ERROR'], 'recibido anteriormente') !== false) {
        return false;
    }

    if (($result['ACEPTADO'] ?? '') === 'S') {
        return false;
    }

    // Ya enviado a Hacienda (aceptado o rechazado): no se corrige ni reenvia
    if (($result['ENVIADO'] ?? '') === 'S') {
        return false;
    }

    if (in_array(strtolower($result['ESTADO'] ?? ''), ['rechazado', 'error'], true)) {
        return false;
    }

    return true;
}

function actualizarSatEnBd(string $sessionID, string $Documento, string $TipoDoc, string $claveNueva): bool
{
    global $dbConn;

    $actualizado = false;

    try {
        if (!$dbConn) {
            return false;
        }

        $tablasSat = [
            ["HFACTURAS", "SAT", "nfac"],
            ["HFACTURAS", "sat", "nfac"],
            ["DOCUMENTOS", "sat", "DOCUMENTO"],
        ];

        foreach ($tablasSat as [$tabla, $columna, $filtro]) {
            try {
                if ($tabla === "DOCUMENTOS") {
                    $sql = "UPDATE $tabla SET $columna = :clave
                            WHERE $filtro = :documento AND TIPO_DOC = :tipoDoc";
                    $stm = $dbConn->prepare($sql);
                    $stm->execute([
                        ':clave' => $claveNueva,
                        ':documento' => $Documento,
                        ':tipoDoc' => $TipoDoc,
                    ]);
                } else {
                    $sql = "UPDATE $tabla SET $columna = :clave WHERE $filtro = :documento";
                    $stm = $dbConn->prepare($sql);
                    $stm->execute([
                        ':clave' => $claveNueva,
                        ':documento' => $Documento,
                    ]);
                }

                if ($stm->rowCount() > 0) {
                    $actualizado = true;
                }
            } catch (Exception $ex) {
                continue;
            }
        }

        $sqlProc = "SELECT NUMERO_DOC FROM DOC_ELECTRONICO_PROCESADO
                    WHERE NUMERO_DOC = :documento AND TIPO_DOC = :tipoDoc
                    AND (ENVIADO IS NULL OR ENVIADO = 'N') AND ACEPTADO = 'N'";
        $stmCheck = $dbConn->prepare($sqlProc);
        $stmCheck->execute([':documento' => $Documento, ':tipoDoc' => $TipoDoc]);
        if ($stmCheck->fetchColumn()) {
            $ambiente = params_get("ambiente");
            if (empty($ambiente)) {
                $ambiente = strtoupper(rtrim(getDatoCia($sessionID, "AMBIENTE", $Documento, $TipoDoc)));
            }
            $urlBase = rtrim(getDatoCia($sessionID, "API_" . $ambiente . "_URL", $Documento, $TipoDoc));
            $linkConsulta = $urlBase . $claveNueva;

            $sqlUpdateProc = "UPDATE DOC_ELECTRONICO_PROCESADO
                              SET SELLO_SAT = :clave, LINK_CONSULTA = :linkConsulta
                              WHERE NUMERO_DOC = :documento AND TIPO_DOC = :tipoDoc AND ACEPTADO = 'N'";
            $stmProc = $dbConn->prepare($sqlUpdateProc);
            $stmProc->execute([
                ':clave' => $claveNueva,
                ':linkConsulta' => $linkConsulta,
                ':documento' => $Documento,
                ':tipoDoc' => $TipoDoc,
            ]);
            if ($stmProc->rowCount() > 0) {
                $actualizado = true;
            }
        }
    } catch (Exception $ex) {
        Bitacora($sessionID, "actualizarSatEnBd Error: {$ex->getMessage()}", $Documento, $TipoDoc, "Warning");
    }

    return $actualizado;
}

function validarYCorregirClaveDocumento(string $sessionID, string $Documento, string $TipoDoc, string $clave): string
{
    if (empty($clave) || $clave === '0' || strlen($clave) < 50) {
        return $clave;
    }

    $timestampEmision = resolverTimestampEmisionHacienda($sessionID, $Documento, $TipoDoc, $clave);

    $segmentoClave = extraerSegmentoFechaClave($clave);
    $segmentoEmision = segmentoFechaClaveDesdeTimestamp($timestampEmision);

    if ($segmentoClave === $segmentoEmision) {
        return $clave;
    }

    if (!documentoPermiteCorregirClaveEnBd($sessionID, $Documento, $TipoDoc)) {
        Bitacora(
            $sessionID,
            "Inconsistencia clave/fecha detectada (clave: " . formatearSegmentoFechaClave($segmentoClave) .
            ", emision: " . formatearSegmentoFechaClave($segmentoEmision) .
            ") pero el documento ya fue enviado o rechazado por Hacienda y no permite correccion",
            $Documento,
            $TipoDoc,
            "Warning"
        );
        return $clave;
    }

    $claveCorregida = actualizarSegmentoFechaEnClave($clave, $timestampEmision);

    Bitacora(
        $sessionID,
        "Correccion de clave: fecha en SAT era " . formatearSegmentoFechaClave($segmentoClave) .
        ", se ajusta a fecha de emision " . formatearSegmentoFechaClave($segmentoEmision),
        $Documento,
        $TipoDoc,
        "Warning"
    );

    if (actualizarSatEnBd($sessionID, $Documento, $TipoDoc, $claveCorregida)) {
        Bitacora($sessionID, "Clave SAT actualizada en base de datos", $Documento, $TipoDoc);
    } else {
        Bitacora(
            $sessionID,
            "Clave SAT corregida en memoria para el envio; no se encontro registro actualizable en BD",
            $Documento,
            $TipoDoc,
            "Warning"
        );
    }

    return $claveCorregida;
}

function setEncabezadoDocumento(string $sessionID, string $Documento, string $TipoDoc)
{
    try {

        //Esto para cuando se genere el xml para NC
        if ($TipoDoc == "NC") {
            //Facturas
            params_set("tipo_doc", "01");
            params_set("numero", ObtenerDatoFACTURA($sessionID, "referencia", $Documento, $TipoDoc));

            //Codigo de motivo
            $codigo = ObtenerDatoFACTURA($sessionID, "motivo", $Documento, $TipoDoc);
            params_set("codigo_razon" , str_pad($codigo, 2, "0",STR_PAD_LEFT));

            //Consecuti de la factura, para obtener luego la referencia
            $referencia=ObtenerDatoTabla($sessionID, "referencia", "hfacturas", "nfac", $Documento);
            //...13055

            //Documento de referencia, pero buscando por el consecutivo interno
            $consecutivo_interno=ObtenerDatoTabla($sessionID, "nfac", "hfacturas", "consecutivo", $referencia);
            $tipo_doc=ObtenerDatoTabla($sessionID, "tipo_doc", "DOC_ELECTRONICO_PROCESADO", "numero_doc", $consecutivo_interno);
            $tipo_doc_referencia=$tipo_doc=="FE"?"01":"04";
            params_set("tipo_doc_referencia" , $tipo_doc_referencia);


            $fecha_emision_ir=ObtenerDatoTabla($sessionID, "fecha_hora", "DOC_ELECTRONICO_PROCESADO", "numero_doc", $consecutivo_interno);
            params_set("fecha_emision_ir" , $fecha_emision_ir);

            $sello_sat_50=ObtenerDatoTabla($sessionID, "sello_sat", "DOC_ELECTRONICO_PROCESADO", "numero_doc", $consecutivo_interno);
            params_set("sello_sat_50" , $sello_sat_50);

            //Descripcion del motivo
            params_set("razon", ObtenerDatoTabla($sessionID, "motivo", "tbl_Motivos", "cod_motivo", $codigo));

        }

        //Consecutivos y Tipo
        params_set('tipoDocumento', $TipoDoc);
        $clave = ObtenerDatoTabla($sessionID, "SAT", "V_DOCUMENTO_ELECTRONICO", "DOCUMENTO", $Documento);
        $clave = validarYCorregirClaveDocumento($sessionID, $Documento, $TipoDoc, $clave);
        params_set('clave', $clave);
        params_set('consecutivo', ObtenerDatoTabla($sessionID, 'CLAVE', "V_DOCUMENTO_ELECTRONICO","DOCUMENTO",$Documento));




        // Fecha de emision debe coincidir con la fecha embebida en la clave numerica (SAT)
        params_set("fecha_emision", obtenerFechaEmisionDocumento($sessionID, $Documento, $TipoDoc));

        //Datos del cliente para definir otras codiciones
        $cod_cliente = ObtenerDatoFACTURA($sessionID, "PROVEEDOR", $Documento, $TipoDoc);
        $condicion_venta = $cod_cliente > 10000 ? "02" : "01";

        $plazo_credito = $cod_cliente > 10000
            ? "0"
            : (empty(ObtenerDatoCLIENTE($sessionID, "credito", $cod_cliente, $Documento, $TipoDoc)) || ObtenerDatoCLIENTE($sessionID, "credito", $cod_cliente, $Documento, $TipoDoc) == "0"
                ? "30"
                : ObtenerDatoCLIENTE($sessionID, "credito", $cod_cliente, $Documento, $TipoDoc));

        //Codigo de actividad del receptor
        params_set('codigo_actividad_receptor',  ObtenerDatoCLIENTE($sessionID, "ACTIVIDAD_COMERCIAL", $cod_cliente, $Documento, $TipoDoc));

        //$codigoCIIU4 = rtrim(ObtenerDatoClienteCIIU4($sessionID, 'CODIGO_ACTIVIDAD_CIIU4', $cod_cliente, $Documento, $TipoDoc));
        //$codigoActividadReceptor = str_pad($codigoCIIU4, 6, "0", STR_PAD_LEFT);
        //params_set('codigo_actividad_receptor',$codigoActividadReceptor);

        params_set("condicion_venta", $condicion_venta);
        params_set("plazo_credito", $plazo_credito);
        params_set("medio_pago", ObtenerDatoFACTURA($sessionID, 'medio_pago', $Documento, $TipoDoc));
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

        $motivoBloqueo = validarFechasAntesDeProcesar($sessionID, $Documento, $TipoDoc);
        if ($motivoBloqueo !== null) {
            registrarDocumentoNoEnviado($sessionID, $Documento, $TipoDoc, $motivoBloqueo);
            SetContinuar(false);
            return;
        }

        //Creacion del XML
        Bitacora($sessionID, "Generando XML del comprobante", $Documento, $TipoDoc);
        $xml = IniciaProcesoCreacionXML($sessionID, $Documento, $TipoDoc);

        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en la creación del XML", $Documento, $TipoDoc);
            bitacora_doc_fallo($sessionID, $Documento, $TipoDoc, 'DOC_PROCESO_DETENIDO', 'El proceso se detuvo en la creación del XML', 'envio', true);
            return;
        }
        $TIEMPO_EXPIRA_DOCHA_HORAS = getDatoCia($sessionID, "TIEMPO_EXPIRA_DOCHA_HORAS", $Documento, $TipoDoc);
        //Si el estado de hacienda es 1 , se procede con el guardado pero no se envia
        if (getEstadoHacienda($Documento, $TipoDoc) == "3") {
            Bitacora($sessionID, "No hay conexión con Hacienda (API/IDP). Se reintentará el envío durante $TIEMPO_EXPIRA_DOCHA_HORAS horas más", $Documento, $TipoDoc);
            //UpdateInsert("N", "N", 'N', 'N', 'No hay conexion a hacienda, este documento se intentara procesar durante 48 horas mas', 'error', $xml, null,  $sessionID, $Documento, $TipoDoc);
            return;
        } else {
            Bitacora($sessionID, "Conexión con Hacienda (API/IDP) disponible; se obtendrá o reutilizará el token y sigue el firmado y envío del XML", $Documento, $TipoDoc);
        }

        $TIEMPO_EXPIRA_DOCHA_HORAS = getDatoCia($sessionID, "TIEMPO_EXPIRA_DOCHA_HORAS", $Documento, $TipoDoc);
        $sql = "select FECHA,DATEDIFF(MINUTE,FECHA,getdate()) as DIF_MINS from V_DOCUMENTO_ELECTRONICO where DOCUMENTO='$Documento'";
        $result = sql_exec($sql, 2, false, true);
        if ($result !== "-200") {
            if ($result[0]["DIF_MINS"] > ($TIEMPO_EXPIRA_DOCHA_HORAS * 60)) {
                //Bitacora($sessionID, "El documento $Documento NO sera procesado, ha pasado las $TIEMPO_EXPIRA_DOCHA_HORAS horas limite para enviarlo", $Documento, $TipoDoc);
                //@todo Poner aca un return al terminar debugging
                //return;
            }
        }

        //Una vez creado iniciamos el proceso de firmado, pasando el xml que acabamos de crear y lo retorna firmado.
        Bitacora($sessionID, "Firmando el XML con el certificado digital", $Documento, $TipoDoc);
        $xmlFirmado = IniciaProcesoFirmado($xml, $sessionID, $Documento, $TipoDoc);
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en el firmado del XML", $Documento, $TipoDoc);
            bitacora_doc_fallo($sessionID, $Documento, $TipoDoc, 'DOC_PROCESO_DETENIDO', 'El proceso se detuvo en el firmado del XML', 'envio', true);
            return;
        }

        //Intenta salvar el documento en el disco en ruta segun settings.php
        if (isset($xmlFirmado["xmlFirmado"])) {
            saveFile(params_get("clave") . ".xml", base64_decode($xmlFirmado["xmlFirmado"]), "XML", params_get("fecha_timbrado"), $Documento, $TipoDoc, $sessionID);
            $dirXml = params_get("directory_xml") ?: "";
            $nomXml = (params_get("clave") ?: $Documento) . ".xml";
            Bitacora($sessionID, "XML firmado guardado en disco" . ($dirXml !== "" ? ": $dirXml/$nomXml" : ""), $Documento, $TipoDoc);
        } else {
            Bitacora($sessionID, "No se pudo guardar el XML firmado de $Documento en disco; no se continúa el envío");
        }
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en el guardado del XML firmado en disco", $Documento, $TipoDoc);
            bitacora_doc_fallo($sessionID, $Documento, $TipoDoc, 'DOC_PROCESO_DETENIDO', 'El proceso se detuvo en el guardado del XML firmado en disco', 'envio', true);
            return;
        }

        //******************** FIN DE PROCESO DE GENERACION*************************

        //Una vez creado inicio el proceso de envio del XML,aca se procesa el token primero
        Bitacora($sessionID, bitacora_con_clave("Enviando el XML firmado a la API de recepción de Hacienda"), $Documento, $TipoDoc);
        IniciaProcesoEnvioXML($xmlFirmado, $sessionID, $Documento, $TipoDoc);
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo en el envío del XML a Hacienda", $Documento, $TipoDoc);
            bitacora_doc_fallo($sessionID, $Documento, $TipoDoc, 'DOC_PROCESO_DETENIDO', 'El proceso se detuvo en el envío del XML a Hacienda', 'envio', true);
            return;
        }

        //Hasta no enviarse no se crean los totales, y hasta no crear los totales no se genera el PDF
        //Generamos el PDF y lo guardamos en la base de datos
        Bitacora($sessionID, "Generando PDF del comprobante", $Documento, $TipoDoc);
        $strPDF = GenerarPDF($sessionID, $Documento, $TipoDoc);
        //Intenta salvar el documento en el disco en ruta segun settings.php
        if (strlen($strPDF) > 0) {
            saveFile(params_get("clave") . ".pdf", $strPDF, "PDF", params_get("fecha_timbrado"), $Documento, $TipoDoc, $sessionID);
            $dirPdf = params_get("directory_pdf") ?: "";
            $nomPdf = (params_get("clave") ?: $Documento) . ".pdf";
            Bitacora($sessionID, "PDF guardado en disco" . ($dirPdf !== "" ? ": $dirPdf/$nomPdf" : ""), $Documento, $TipoDoc);
        } else {
            Bitacora($sessionID, "No se pudo generar el PDF de $Documento");
        }

        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo al guardar el PDF en disco", $Documento, $TipoDoc);
            bitacora_doc_fallo($sessionID, $Documento, $TipoDoc, 'DOC_PROCESO_DETENIDO', 'El proceso se detuvo al guardar el PDF en disco', 'envio', true);
            return;
        }
        Bitacora($sessionID, "Guardando el PDF en la columna PDF de DOC_ELECTRONICO_PROCESADO", $Documento, $TipoDoc);
        UpdateDBPDF($sessionID, $Documento, $TipoDoc, $strPDF);
        if (!$continuar) {
            Bitacora($sessionID, "El proceso se detuvo al guardar el PDF en la base de datos", $Documento, $TipoDoc);
            bitacora_doc_fallo($sessionID, $Documento, $TipoDoc, 'DOC_PROCESO_DETENIDO', 'El proceso se detuvo al guardar el PDF en la base de datos', 'envio', true);
            return;
        }

        //Finalmente, envio el correo
        ProcesarCorreosPendientes($sessionID, $Documento, $TipoDoc);
    } catch (Throwable $ex) {
        $msg = "ProcesarDocumento Error: {$ex->getMessage()}({$ex->getLine()})";
        Bitacora($sessionID, $msg, $Documento, $TipoDoc, "Error", true, "envio", "DOC_EXCEPCION");
        bitacora_doc_fallo($sessionID, $Documento, $TipoDoc, 'DOC_EXCEPCION', $msg, 'envio', true);
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
        $fechaEmision = params_get('fecha_emision');
        params_set('fecha', !empty($fechaEmision) ? $fechaEmision : date("Y-m-d\TH:i:sP"));

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
        ConsultarDocumento($respuesta, $XMLFirmado, $sessionID, $Documento, $TipoDoc);

        $xmlRespuesta = params_get("xmlRespuesta");
        $ind_estado   = params_get("ind_estado");
        $clave        = params_get("clave");
        $fecha_timbrado = params_get("fecha_timbrado");

        // Obtener causa del error de forma segura
        $x_error_cause = params_get("x_error_cause");
        if (empty($x_error_cause)) {
            $x_error_cause = $respuesta['text'][9] ??
                $respuesta['message'] ??
                $respuesta['error'] ??
                json_encode($respuesta) ?? 'Error desconocido';
        }

        Bitacora($sessionID, bitacora_con_clave(bitacora_http_hacienda((int) ($respuesta["Status"] ?? 400)) . " — $x_error_cause | estado Hacienda: $ind_estado"), $Documento, $TipoDoc);

        // Procesar según el estado devuelto por Hacienda
        if (!empty($ind_estado)) {
            switch ($ind_estado) {
                case "aceptado":
                    UpdateInsert("S", "N", "S", "N", "El documento fue aceptado",
                        $ind_estado, $XMLFirmado["xmlFirmado"] ?? $XMLFirmado,
                        $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
                    break;

                case "rechazado":
                    UpdateInsert("S", "N", "S", "S", "El documento fue rechazado ($x_error_cause)",
                        $ind_estado, $XMLFirmado["xmlFirmado"] ?? $XMLFirmado,
                        $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
                    break;

                case "procesando":
                    UpdateInsert("N", "N", "S", "S", "El documento se está procesando",
                        $ind_estado, $XMLFirmado["xmlFirmado"] ?? $XMLFirmado,
                        $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
                    break;

                default:
                    UpdateInsert("N", "N", "N", "S",
                        "El documento tiene el siguiente error: $x_error_cause",
                        "error", $XMLFirmado["xmlFirmado"] ?? $XMLFirmado,
                        $xmlRespuesta, $sessionID, $Documento, $TipoDoc);
                    break;
            }
        } else {
            // No hay estado → error genérico
            UpdateInsert("N", "N", "N", "S",
                "Error 400 sin estado devuelto: $x_error_cause",
                "error", $XMLFirmado["xmlFirmado"] ?? $XMLFirmado,
                null, $sessionID, $Documento, $TipoDoc);
        }

        // Guardar XML de respuesta
        if (!empty($xmlRespuesta)) {
            saveFile(
                "RSP_" . $clave . ".xml",
                base64_decode($xmlRespuesta),
                "XML",
                $fecha_timbrado,
                $Documento,
                $TipoDoc,
                $sessionID
            );
        } else {
            Bitacora($sessionID, "HTTP 4xx de Hacienda sin XML de respuesta; se conserva el estado para consultarlo luego (no se reenvía el XML)", $Documento, $TipoDoc);
            UpdateInsert("N", "N", "N", "S",
                "No se logro obtener un XML de respuesta de hacienda",
                $ind_estado ?? "error",
                $XMLFirmado["xmlFirmado"] ?? $XMLFirmado,
                null, $sessionID, $Documento, $TipoDoc);
        }

    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "Procesa400 Error: " . $ex->getMessage(), $Documento, $TipoDoc);
        // Opcional: loggear también el trace
         Bitacora($sessionID, $ex->getTraceAsString(), $Documento, $TipoDoc);
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
                $detalle_mensaje = interpretarCodigoMensaje($mensaje);

                if ($mensaje != "1" && !empty($array["DetalleMensaje"])) {
                    $detalleCompleto = procesarDetalleMensaje($array["DetalleMensaje"]);
                    if (!empty($detalleCompleto)) {
                        $detalle_mensaje .= " - " . $detalleCompleto;
                    }
                }

                $resp = "Mensaje=$mensaje, Detalle:$detalle_mensaje";

                $sql = "UPDATE DOC_ELECTRONICO_PROCESADO SET
                    RESPUESTA_HACIENDA=:resp, XML_RESPUESTA=:xmlRespuesta
                    WHERE NUMERO_DOC=:Documento and TIPO_DOC=:TipoDoc";

                $stm = $dbConn->prepare($sql);
                $stm->execute([
                    ':resp' => $resp,
                    ':xmlRespuesta' => $xmlRespuesta,
                    ':Documento' => $Documento,
                    ':TipoDoc' => $TipoDoc
                ]);

                Bitacora($sessionID, bitacora_con_clave("Respuesta de Hacienda: el documento ha sido $detalle_mensaje"), $Documento, $TipoDoc);
            }
        } else {
            Bitacora($sessionID, "Sin XML de respuesta de Hacienda todavía: el comprobante sigue en procesamiento. Se consultará el estado en la próxima corrida (no se reenvía el XML)", $Documento, $TipoDoc);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "GuardarRespuestaHacienda Error: {$ex->getMessage()}", $Documento, $TipoDoc);
    }
}
function procesarDetalleMensaje(string $detalleMensaje): string
{
    $detalleMensaje = corregirEncodingUtf8($detalleMensaje);

    $contiene = function($texto, $buscar) {
        return strpos($texto, $buscar) !== false;
    };

    if (!$contiene($detalleMensaje, '[')) {
        return normalizarTextoBitacora($detalleMensaje);
    }

    try {
        $inicioArray = strpos($detalleMensaje, '[');
        $finArray = strrpos($detalleMensaje, ']');

        if ($inicioArray === false || $finArray === false) {
            return normalizarTextoBitacora($detalleMensaje);
        }

        $preambulo = trim(substr($detalleMensaje, 0, $inicioArray));
        $arrayErrores = substr($detalleMensaje, $inicioArray + 1, $finArray - $inicioArray - 1);

        $lineas = preg_split('/\R/', $arrayErrores);
        $erroresFormateados = [];

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (empty($linea) || $contiene($linea, 'codigo, mensaje, fila, columna')) {
                continue;
            }

            if (preg_match('/^(-?\d+),\s*"?"?([^"]+)"?"?,\s*(\d+),\s*(\d+)/', $linea, $matches)) {
                $codigo = $matches[1];
                $mensaje = trim($matches[2]);
                $fila = $matches[3];
                $columna = $matches[4];

                $errorFormateado = "Error $codigo: $mensaje";
                if ($fila > 0 || $columna > 0) {
                    $errorFormateado .= " (Linea: $fila, Columna: $columna)";
                }
                $erroresFormateados[] = $errorFormateado;
            } elseif (!empty($linea)) {
                $erroresFormateados[] = normalizarTextoBitacora($linea);
            }
        }

        if (!empty($erroresFormateados)) {
            $partes = [];
            if (!empty($preambulo)) {
                $partes[] = $preambulo;
            }
            $partes = array_merge($partes, $erroresFormateados);
            return implode(' | ', $partes);
        }

        return normalizarTextoBitacora($detalleMensaje);

    } catch (Exception $e) {
        return normalizarTextoBitacora($detalleMensaje);
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
        UpdateInsert("N", "N", "N", "S", $UltimoError, "error", $XML, null, $sessionID, $Documento, $TipoDoc); // antes faltaban 2 argumentos (ArgumentCountError)
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

function extraerClaveDeMensajeHacienda(string $mensaje): string
{
    if (preg_match('/\[(\d{50})\]/', $mensaje, $matches)) {
        return $matches[1];
    }
    return '';
}

function construirUrlConsultaPorClave(string $sessionID, string $Documento, string $TipoDoc, string $clave): string
{
    if (empty($clave)) {
        return '';
    }

    $ambiente = params_get("ambiente");
    if (empty($ambiente)) {
        $ambiente = strtoupper(rtrim(getDatoCia($sessionID, "AMBIENTE", $Documento, $TipoDoc)));
    }

    $urlConsulta = getDatoCia($sessionID, "API_" . $ambiente . "_URL", $Documento, $TipoDoc);
    return rtrim($urlConsulta) . $clave;
}

function aplicarRespuestaConsultaHacienda($jsonRespuesta, string $sessionID, string $Documento, string $TipoDoc): bool
{
    if (empty($jsonRespuesta)) {
        return false;
    }

    if (!empty($jsonRespuesta->{"Status"}) && (string) $jsonRespuesta->{"Status"} === "400") {
        $causa = $jsonRespuesta->text->{"X-Error-Cause"} ?? 'Documento no encontrado en Hacienda';
        Bitacora($sessionID, $causa, $Documento, $TipoDoc);
        return false;
    }

    if (!empty($jsonRespuesta->{"message"})) {
        Bitacora($sessionID, "Error al consultar: " . $jsonRespuesta->{"message"}, $Documento, $TipoDoc);
        return false;
    }

    params_set("ind_estado", $jsonRespuesta->{"ind-estado"} ?? null);
    params_set("xmlRespuesta", $jsonRespuesta->{"respuesta-xml"} ?? null);

    return !empty($jsonRespuesta->{"ind-estado"}) || !empty($jsonRespuesta->{"respuesta-xml"});
}

function resolverUrlConsultaHacienda(array $respuesta, string $sessionID, string $Documento, string $TipoDoc, string $x_error_cause): string
{
    $header_location = "";
    foreach ($respuesta["text"] as $textpart) {
        if (stripos($textpart, 'location:') !== false) {
            $header_location = $textpart;
            break;
        }
    }

    if (strlen($header_location) > 0) {
        $partes = preg_split('/location:/i', $header_location, 2);
        if (!empty($partes[1]) && preg_match('/https:\/\/[^\s]+/', $partes[1], $matches)) {
            return rtrim($matches[0]);
        }
    }

    $clave = params_get('clave');
    if (empty($clave)) {
        $clave = extraerClaveDeMensajeHacienda($x_error_cause);
    }

    if (!empty($clave)) {
        params_set('clave', $clave);
        return construirUrlConsultaPorClave($sessionID, $Documento, $TipoDoc, $clave);
    }

    return '';
}

/*
 *  Obtiene datos del documento luego de enviarlo
 *  $respuesta , es la respuesta del envio inicial
 * */
function ConsultarDocumento($respuesta, $XMLFirmado, $sessionID, $Documento, $TipoDoc)
{

    try {
        $error_cause = "";
        $x_error_cause = "";

        if (!empty($respuesta["text"]) && is_array($respuesta["text"])) {
            foreach ($respuesta["text"] as $textpart) {
                if (stripos($textpart, 'x-error-cause:') !== false) {
                    $error_cause = $textpart;
                    break;
                }
            }
        }

        if (strlen($error_cause) > 0) {
            $partesError = preg_split('/x-error-cause:/i', $error_cause, 2);
            $x_error_cause = trim($partesError[1] ?? '');
        }

        if (empty($x_error_cause)) {
            $x_error_cause = params_get("x_error_cause") ?? '';
        }

        params_set("x_error_cause", $x_error_cause);

        $apiURLConsulta = resolverUrlConsultaHacienda($respuesta, $sessionID, $Documento, $TipoDoc, $x_error_cause);

        if (empty($apiURLConsulta)) {
            if (stripos($x_error_cause, 'recibido anteriormente') !== false) {
                Bitacora($sessionID, "No se pudo construir URL de consulta para comprobante ya recibido: $x_error_cause", $Documento, $TipoDoc);
            }
            return;
        }

        params_set("api_url", $apiURLConsulta);

        if (stripos($x_error_cause, 'recibido anteriormente') !== false) {
            Bitacora($sessionID, bitacora_con_clave("Consultando en Hacienda un comprobante ya recibido (no se reenvía el XML)"), $Documento, $TipoDoc);
        }

        $jsonRespuesta = json_decode(consultar());
        aplicarRespuestaConsultaHacienda($jsonRespuesta, $sessionID, $Documento, $TipoDoc);
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
            if (!aplicarRespuestaConsultaHacienda($jsonRespuesta, $sessionID, $Documento, $TipoDoc)) {
                return;
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
                Bitacora($sessionID, "Sin XML de respuesta de Hacienda todavía: el comprobante sigue en procesamiento. Se consultará el estado en la próxima corrida (no se reenvía el XML)", $Documento, $TipoDoc);
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
            Bitacora($sessionID, bitacora_con_clave(bitacora_http_hacienda(200)), $Documento, $TipoDoc);
            Procesa200($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc);
        }

        //Procesandose,fue enviado pero no fue aceptado
        if ($respuesta["Status"] == 202) {
            Bitacora($sessionID, bitacora_con_clave(bitacora_http_hacienda(202)), $Documento, $TipoDoc);
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
            //Bitacora($sessionID, "{$x_error_cause}");
            Procesa400($respuesta, $sessionID, $XMLFirmado, $Documento, $TipoDoc, $XMLFirmado);
        }




        //Error causado por error el servidor
        if ($respuesta["Status"] >= 500) {
            Bitacora($sessionID, bitacora_con_clave(bitacora_http_hacienda((int) $error)), $Documento, $TipoDoc);
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

            Bitacora($sessionID, "Actualizados datos del comprobante en DOC_ELECTRONICO_PROCESADO (estado Hacienda: $Estado)", $Documento, $TipoDoc);
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
                // Validar y limpiar datos antes del execute
                $executeData = [
                    ':TipoDoc' => $TipoDoc ?? '',
                    ':Documento' => $Documento ?? '',
                    ':emisor_num_identif' => $emisor_num_identif ?? '',
                    ':emisor_email' => $emisor_email ?? '',
                    ':emisor_gln' => $emisor_gln ?? '',
                    ':fecha_timbrado' => $fecha_timbrado ?? date('Y-m-d H:i:s'),
                    ':receptor_nit' => $receptor_nit ?? '',
                    ':receptor_email' => $receptor_email ?? '',
                    ':receptor_gln' => $receptor_gln ?? '',
                    ':Enviado' => $Enviado ?? 'N',
                    ':Aceptado' => $Aceptado ?? 'N',
                    ':Estado' => $Estado ?? '',
                    ':XML' => $XML ?? '',
                    ':LinkConsulta' => $LinkConsulta ?? '',
                    ':clave' => $clave ?? '',
                    ':fecha_emision' => $fecha_emision ?? date('Y-m-d H:i:s'),
                    ':UltimoError' => $UltimoError ?? '',
                    ':XMLRespuesta' => $XMLRespuesta ?? '',
                    ':TotalServGravados' => $Totales->TotalServGravados ?? 0.00,
                    ':TotalServExentos' => $Totales->TotalServExentos ?? 0.00,
                    ':TotalServExonerado' => $Totales->TotalServExonerado ?? 0.00,
                    ':TotalMercanciasGravadas' => $Totales->TotalMercanciasGravadas ?? 0.00,
                    ':TotalMercanciasExentas' => $Totales->TotalMercanciasExentas ?? 0.00,
                    ':TotalMercExonerada' => $Totales->TotalMercExonerada ?? 0.00,
                    ':TotalGravado' => $Totales->TotalGravado ?? 0.00,
                    ':TotalExento' => $Totales->TotalExento ?? 0.00,
                    ':TotalExonerado' => $Totales->TotalExonerado ?? 0.00,
                    ':TotalVenta' => $Totales->TotalVenta ?? 0.00,
                    ':TotalDescuentos' => $Totales->TotalDescuentos ?? 0.00,
                    ':TotalVentaNeta' => $Totales->TotalVentaNeta ?? 0.00,
                    ':TotalImpuesto' => $Totales->TotalImpuesto ?? 0.00,
                    ':TotalComprobante' => $Totales->TotalComprobante ?? 0.00,
                    ':TotalIVADevuelto' => $Totales->TotalIVADevuelto ?? 0.00,
                    ':TotalOtrosCargos' => $Totales->TotalOtrosCargos ?? 0.00,
                    ':EmailEnviado' => 'NO',
                    ':estado_comprobante' => $estado_comprobante ?? '0',
                    ':pdf_path' => $pdf_path ?? '',
                    ':xml_path' => $xml_path ?? ''
                ];
                foreach ($executeData as $key => $value) {
                    if (is_null($value)) {
                        Bitacora($sessionID, "Valor NULL detectado para parámetro: $key", $Documento, $TipoDoc, "Warning");
                    }
                }

                $resultado = $stm->execute($executeData);

                if (!$resultado) {
                    $errorInfo = $stm->errorInfo();
                    Bitacora($sessionID, "Execute falló - SQLSTATE: {$errorInfo[0]}, Error: {$errorInfo[2]}", $Documento, $TipoDoc, "Error");
                }

            } catch (PDOException $ex) {
                Bitacora($sessionID, "UpdateInsert Error: {$ex->getMessage()}", $Documento, $TipoDoc);
            }
            Bitacora($sessionID, "Insertados datos del comprobante en DOC_ELECTRONICO_PROCESADO (estado Hacienda: $Estado)", $Documento, $TipoDoc);
            GuardarRespuestaHacienda($sessionID, $XMLRespuesta, $Documento, $TipoDoc);
        }
    } catch (Exception $ex) {
        SetContinuar(false);
        Bitacora($sessionID, "UpdateInsert Error: {$ex->getMessage()}", $Documento, $TipoDoc);
    }
}

/**
 * Interpreta los códigos de mensaje según XSLT de Hacienda
 *
 * @param string $codigo Código numérico del mensaje
 * @return string Descripción del mensaje
 */
function interpretarCodigoMensaje(string $codigo): string
{
    $codigos = [
        "1" => "Aceptado",
        "2" => "Aceptado Parcialmente",
        "3" => "Rechazado"
    ];

    return $codigos[$codigo] ?? "Código desconocido ($codigo)";
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
                    Bitacora($sessionID, "Obteniendo access_token en el IDP (grant password: el refresh_token expiró)", $Documento, $TipoDoc);
                    params_set("grant_type", "password");
                    $token = token();

                    SaveToken($token, $ambiente, $sessionID, $Documento, $TipoDoc);
                } else //El refresh token no esta vencido, se usa para obtener el access_token
                {
                    Bitacora($sessionID, "Obteniendo access_token en el IDP (grant refresh_token)", $Documento, $TipoDoc);
                    params_set("grant_type", "refresh_token");
                    params_set("refresh_token", $result[$REFRESH_TOKEN]);
                    $token = token();
                    SaveToken($token, $ambiente, $sessionID, $Documento, $TipoDoc);
                }
            }
        } else { //Es la primera vez que se solicita token
            params_set("grant_type", "password");
            //Aca se obtiene el token
            Bitacora($sessionID, "Obteniendo access_token nuevo en el IDP (grant password)", $Documento, $TipoDoc);
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

        Bitacora($sessionID, "Guardando token de Hacienda en COMPAÑIAS (IDP)", $Documento, $TipoDoc);
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
    params_set("max_fallos_doc", (string)bitacora_max_fallos_doc());
}

function Iniciar()
{
    date_default_timezone_set('America/Costa_Rica');
    bitacora_run_iniciar();

    $sessionID = getSessionID();
    CargarConfiguraciones($sessionID);

    if (EnProceso($sessionID) == true) {
        Bitacora($sessionID, "El proceso ya fue iniciado desde otra instancia, esta bloqueado y no se permite mas de un proceso...salimos", "", "", "Warning", true, "envio", "RUN_BLOQUEADO");
        bitacora_run_cerrar($sessionID);
        return;
    }

    if (!TestCertificateDates($sessionID)) {
        bitacora_run_cerrar($sessionID);
        return;
    }

    try {
        ProcesarPendientesEnvio($sessionID);
    } catch (Throwable $ex) {
        Bitacora($sessionID, 'Etapa envío: ' . $ex->getMessage(), '', '', 'Error', true, 'envio', 'ETAPA_ENVIO');
        bitacora_run_etapa_error('envío');
    }
    try {
        ProcesarPendientesRespuesta($sessionID);
    } catch (Throwable $ex) {
        Bitacora($sessionID, 'Etapa respuesta: ' . $ex->getMessage(), '', '', 'Error', true, 'respuesta', 'ETAPA_RESPUESTA');
        bitacora_run_etapa_error('respuesta');
    }
    try {
        ProcesarCorreosPendientes($sessionID);
    } catch (Throwable $ex) {
        Bitacora($sessionID, 'Etapa correo: ' . $ex->getMessage(), '', '', 'Error', true, 'recepcion', 'ETAPA_CORREO');
        bitacora_run_etapa_error('correo');
    }
    try {
        SetEnProceso($sessionID, "NO");
    } catch (Throwable $ex) {
        Bitacora($sessionID, 'No se pudo liberar el bloqueo de proceso: ' . $ex->getMessage(), '', '', 'Error', true, 'envio', 'ERROR');
    }
    try {
        CloseTokenSession($sessionID);
    } catch (Throwable $ex) {
        Bitacora($sessionID, 'Error al cerrar sesión de token: ' . $ex->getMessage(), '', '', 'Warning', true, 'token', 'TOKEN_CIERRE');
    }
    bitacora_run_cerrar($sessionID);

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

        if ($err) {
            Bitacora($sessionID, "err=$err,resp=$respuesta,status=$status");
        } else {
            Bitacora($sessionID, "Se cierra sesion con el IDP de Hacienda correctamente");
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
                Bitacora($sessionID, "Este comprobante no tiene correo pendiente (EMAIL_ENVIADO distinto de NO)", $Documento, $TipoDoc);
                return;
            } else {
                Bitacora($sessionID, "No se encontraron correos pendientes de procesar");
                return;
            }

        }

        $total_documentos = count($res);
        $i = 0;
        foreach ($res as $correo) {
          try {
            $procesar = true;

            $Documento = $correo["NUMERO_DOC"];
            $TipoDoc = $correo["TIPO_DOC"];
            $i++;

            if (bitacora_correo_agotado((string)$Documento, (string)$TipoDoc)) {
                continue;
            }

            $clave = ObtenerDatoTabla($sessionID, "SELLO_SAT", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", "$Documento");
            if ($clave == "0") {
                bitacora_doc_fallo($sessionID, (string)$Documento, (string)$TipoDoc, 'MAIL_SIN_CLAVE', 'Correo pospuesto: el comprobante no tiene clave (SELLO_SAT)', 'recepcion', false);
                continue;
            }
            $fecha_timbrado=$correo["FECHATIMBRADO"];
            $directory_pdf = ObtenerDatoTabla($sessionID, "PDF_PATH", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", "$Documento");
            $directory_xml = ObtenerDatoTabla($sessionID, "XML_PATH", "DOC_ELECTRONICO_PROCESADO", "NUMERO_DOC", "$Documento");

            $pdf_filename = $clave . ".pdf";
            $xml_filename = $clave . ".xml";
            $xml_resp_filename = "RSP_" . $clave . ".xml";

            //Si no tienen este dato no se pueden enviar los correos
            if (strlen($directory_pdf) <= 0) {
                bitacora_doc_fallo($sessionID, (string)$Documento, (string)$TipoDoc, 'MAIL_SIN_DIR_PDF', 'Correo pospuesto: el directorio de PDFs (PDF_PATH) no existe o está vacío', 'recepcion', false);
                continue;
            }
            if (strlen($directory_xml) <= 0) {
                bitacora_doc_fallo($sessionID, (string)$Documento, (string)$TipoDoc, 'MAIL_SIN_DIR_XML', 'Correo pospuesto: el directorio de XMLs (XML_PATH) no existe o está vacío', 'recepcion', false);
                continue;
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
                        Bitacora($sessionID, "XML de respuesta de Hacienda reconstruido en disco desde la BD: $directory_xml/$xml_resp_filename", $Documento, $TipoDoc);
                        $existe_rsp = file_exists($directory_xml . "/" . $xml_resp_filename);
                    }

                }
            }
            if (!$existe_pdf){
                $strPDF = GenerarPDF($sessionID, $Documento, $TipoDoc);
                //Intenta salvar el documento en el disco en ruta segun settings.php
                if (strlen($strPDF) > 0) {
                    saveFile($clave  . ".pdf", $strPDF, "PDF", $fecha_timbrado, $Documento, $TipoDoc, $sessionID);
                    Bitacora($sessionID, "PDF guardado en disco" . ($directory_pdf !== "" ? ": $directory_pdf/$pdf_filename" : ""), $Documento, $TipoDoc);
                } else {
                    Bitacora($sessionID, "No se pudo generar el PDF de $Documento");
                }
                $existe_pdf = file_exists($directory_pdf . "/" . $pdf_filename);

            }
            if (!$existe_pdf or !$existe_xml or !$existe_rsp) {
                if (!$existe_pdf) {
                    Bitacora($sessionID, "Correo pospuesto hasta tener el PDF en disco", $Documento, $TipoDoc);
                }

                if (!$existe_xml) {
                    Bitacora($sessionID, "Correo pospuesto hasta tener el XML firmado en disco", $Documento, $TipoDoc);
                }

                if (!$existe_rsp) {
                    Bitacora($sessionID, "Correo pospuesto hasta tener el XML de respuesta de Hacienda", $Documento, $TipoDoc);
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
                    Bitacora($sessionID, "Destinatario del correo: $mail_receptor", $Documento, $TipoDoc);

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
                $destinatarios = implode(", ", array_map("trim", $mail_receptors));
                $numSent = $mailer->send($message);
                if (!$numSent) {
                    Bitacora($sessionID, "No se pudo enviar el correo del comprobante (destinatario: $destinatarios) " . bitacora_clave_corta($clave), $Documento, $TipoDoc);
                    UpdateMailSend('NO', $clave);
                } else {
                    Bitacora($sessionID, "El mensaje fue enviado al cliente exitosamente (destinatario: $destinatarios)", $Documento, $TipoDoc);
                    if ($clave != "") {
                        UpdateMailSend('SI', $clave);
                    }

                }
            }
        } catch (Throwable $ex) {
            $docE = isset($Documento) ? (string)$Documento : '';
            $tipoE = isset($TipoDoc) ? (string)$TipoDoc : '';
            $msg = "Excepción al enviar correo: {$ex->getMessage()}({$ex->getLine()})";
            Bitacora($sessionID, $msg, $docE, $tipoE, "Error", true, "recepcion", "DOC_EXCEPCION");
            if ($docE !== '') {
                bitacora_doc_fallo($sessionID, $docE, $tipoE, 'DOC_EXCEPCION', $msg, 'recepcion', false);
            }
        }
        }

    } catch (Throwable $ex) {
        Bitacora($sessionID, 'Error al preparar correos pendientes: ' . $ex->getMessage(), $Documento ?? '', $TipoDoc ?? '', 'Error', true, 'recepcion', 'ETAPA_CORREO');
        throw $ex;
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
        WHERE (d.XML_RESPUESTA IS NULL OR d.XML_RESPUESTA = '')
        AND d.ENVIADO = 'S'
        AND  d.FECHATIMBRADO  >= '2025-01-01'
        ORDER BY d.FECHATIMBRADO ASC
";

    $result = sql_exec($sql, 2, false, true);
    if ($result == "-200") {
        Bitacora($sessionID, "No se encontraron documentos pendientes de respuesta");
        SetEnProceso($sessionID, 'NO');
    } else {
        $total_documentos = count($result);
        $i = 1;
        Bitacora($sessionID, "*******************PROCESAMIENTO DOCUMENTOS PENDIENTES DE RESPUESTA*****************");
        foreach ($result as $doc) {
            $numero = rtrim((string)$doc["NUMERO_DOC"]);
            $tipo = rtrim((string)$doc["TIPO_DOC"]);
            try {
                SetEnProceso($sessionID, 'SI');
                Bitacora($sessionID, "Procesando documento   " . $numero . "( $i de $total_documentos )", $numero, $tipo);
                ConsultarDirecto($sessionID, $numero, $tipo);
            } catch (Throwable $ex) {
                $msg = "Excepción al consultar respuesta de $numero: {$ex->getMessage()}({$ex->getLine()})";
                Bitacora($sessionID, $msg, $numero, $tipo, "Error", true, "respuesta", "DOC_EXCEPCION");
                bitacora_doc_fallo($sessionID, $numero, $tipo, 'DOC_EXCEPCION', $msg, 'respuesta', false);
            }
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
        $sql = "SELECT  $top V.TIPO_DOCUMENTO, RTRIM(V.DOCUMENTO) AS DOCUMENTO, V.NIT, case V.CORREO when '1' then 'facturaelectronica@lospaticas.com' else correo end as CORREO,
                         V.GLN,v.CLAVE,v.SAT
                         FROM V_DOCUMENTO_ELECTRONICO v
                         LEFT JOIN DOC_ELECTRONICO_PROCESADO p
                         ON v.DOCUMENTO=p.NUMERO_DOC
                         WHERE v.ACEPTA_DOC_ELECTRONICO = 'S'
                         AND v.NIT NOT IN ('$myNit')
                         AND V.TIPO_DOCUMENTO IN ('FE','NC','ND','TE')
                         AND NOT v.SAT is NULL
                         AND v.FECHA >= '$fechaInicio'
                         AND v.DOCUMENTO <> 'F-00000000027825'
                         AND (
                               (p.ENVIADO IS NULL AND p.ACEPTADO IS NULL)
                               OR p.ENVIADO = 'N'
                           )
                         AND (p.ESTADO IS NULL OR p.ESTADO NOT IN ('rechazado', 'error'))
                         ORDER BY v.FECHA ASC";
        $result = sql_exec($sql, 2, false, true);
        //echo $sql;
        if ($result == "-200") {
            Bitacora($sessionID, "No se encontraron nuevos documentos para procesar");
            return;
        } else {
            Bitacora($sessionID, "*******************PROCESAMIENTO DOCUMENTOS NUEVOS*****************", "", "", "Debug", true, "envio", "RUN_BANNER");
            $total_documentos = count($result);
            bitacora_run_contar('pendientes', $total_documentos);
            $i = 1;
            foreach ($result as $doc) {
                $numero = rtrim((string)$doc["DOCUMENTO"]);
                $tipo = rtrim((string)$doc["TIPO_DOCUMENTO"]);
                try {
                    Bitacora($sessionID, "Procesando   " . $numero . "( $i de $total_documentos )", $numero, $tipo);
                    ProcesarDocumento($sessionID, $numero, $tipo);
                } catch (Throwable $ex) {
                    $msg = "Excepción al procesar $numero: {$ex->getMessage()}({$ex->getLine()})";
                    Bitacora($sessionID, $msg, $numero, $tipo, "Error", true, "envio", "DOC_EXCEPCION");
                    bitacora_doc_fallo($sessionID, $numero, $tipo, 'DOC_EXCEPCION', $msg, 'envio', true);
                }
                $i++;
            }
            SetEnProceso($sessionID, 'NO');
        }
    } catch (Throwable $ex) {
        Bitacora($sessionID, "ProcesarPendientesEnvio Error: {$ex->getMessage()}", "", "", "Error", true, "envio", "ETAPA_ENVIO");
        bitacora_run_etapa_error('envío');
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
