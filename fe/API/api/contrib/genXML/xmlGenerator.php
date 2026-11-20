<?php
/*
 * Copyright (C) 2017-2020 CRLibre <https://crlibre.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


include_once(DOC_ROOT."/api/contrib/genXML/ArrayToXML.php");
use Spatie\ArrayToXml\ArrayToXml;

function genXML()
{
   $detalle                = json_decode(params_get("detalle"),true);

   //$DetalleServicio        = json_decode(params_get("detalles"));


    foreach ($detalle as $key => $value)
    {
        if ($key >= 0)
        {
            $DetalleServicio[] = array(
                'LineaDetalle' => array(
                    'NumeroLinea' => $value["NUMERO_LINEA"],
                    'Codigo' => $value["CODIGO_CABYS"],
                    'CodigoComercial' => array(
                        'Tipo'      =>  $value["ARTICULO_TIPO"], //Codigo de uso interno por defecto,
                        'Codigo'    => $value["ARTICULO_CODIGO"],
                    ),
                    'Cantidad'          =>$value["CANTIDAD"],
                    'UnidadMedida'      => $value["UnidadMedida"],
                    'Detalle'           => $value["Detalle"],
                    'PrecioUnitario'    => round($value["PrecioUnitario"], 5),
                    'MontoTotal'        => round($value["MontoTotal"], 5)
                    ));

                    $Descuento=array('Descuento'         =>[
                        'MontoDescuento'        => round($value["MontoDescuento"],5),
                        'NaturalezaDescuento'   =>$value["NaturalezaDescuento"]
                    ]);

                    $Subtotal = array('SubTotal'=> round($value["SubTotal"], 5));

                    $Impuestos = array(
                    'Impuesto'          =>[
                        'Codigo'         => $value["CODIGO"] ,
                        'CodigoTarifa' =>$value["TARIFA"],
                        'Tarifa' => $value["ImpuestoTarifa"],
                        'Monto' => round($value["ImpuestoMonto"],5)
                        ]
                    );

                    $Exoneracion = array(
                            'Exoneracion' => [
                                'TipoDocumento' => $value["TipoDocumentoExoneracion"],
                                'NumeroDocumento' => $value["NumeroDocumentoExoneracion"],
                                'NombreInstitucion' =>$value["NombreInstitucionExoneracion"],
                                'FechaEmision' => date("Y-m-d\TH:i:sP",strtotime($value["FechaEmisionExoneracion"])),
                                'PorcentajeExoneracion' => round($value["PorcentajeExoneracionLinea"]*100,0),
                                'MontoExoneracion' =>round($value["MontoExoneracionLinea"],5)
                            ]);

                     $FinalLineaDetalle = array(
                        'ImpuestoNeto'          => round($value["TotalIVA"], 5),
                        'MontoTotalLinea'   => round($value["MontoTotalLinea"], 5)
                     );



                     $i=(int)$value["NUMERO_LINEA"]-1;

                     //Agrego Descuentos
                     if($value["MontoDescuento"]>0) {
                         $DetalleServicio[$i]["LineaDetalle"]= array_merge($DetalleServicio[$i]["LineaDetalle"] , $Descuento);
                         $DetalleServicio[$i]["LineaDetalle"]= array_merge($DetalleServicio[$i]["LineaDetalle"] , $Subtotal);
                     }
                     else{
                         $DetalleServicio[$i]["LineaDetalle"]= array_merge($DetalleServicio[$i]["LineaDetalle"] , $Subtotal);
                     }
                     //Agrego el nodo de impuestos
                     $DetalleServicio[$i]["LineaDetalle"]=array_merge($DetalleServicio[$i]["LineaDetalle"], $Impuestos);
                     //Primero uno el impuesto con la exoneracion si hay
                     if($value["MontoExoneracionLinea"]>0) {

                         //$ImpuestosFinal = array_merge($DetalleServicio[$i]["LineaDetalle"]["Impuesto"],$Exoneracion,$FinalExoneracion);
                         $DetalleServicio[$i]["LineaDetalle"]["Impuesto"]=array_merge($DetalleServicio[$i]["LineaDetalle"]["Impuesto"],$Exoneracion);
                     }
                     //Final despues de los impuestos
                    $DetalleServicio[$i]["LineaDetalle"] = array_merge($DetalleServicio[$i]["LineaDetalle"],$FinalLineaDetalle);


             }
        }
 //Cuerpo del XML antes de los detalles
    $InformacionReferencia = array(
        'InformacionReferencia' => [
        'TipoDoc'=>  params_get("tipo_doc"),
        'Numero'=>  params_get("numero"),
        'FechaEmision'=>  params_get("fecha_emision"),
        'Codigo'=>  params_get("codigo_razon"),
        'Razon'=>  params_get("razon"),
        ]);

    $Documento = array(
        'Clave'             =>  params_get("clave"),
        'CodigoActividad'      =>  params_get("codigo_actividad"),
        'NumeroConsecutivo' =>  params_get("consecutivo"),
        'FechaEmision'      =>  params_get("fecha_emision"),
        'Emisor'            => array(
            'Nombre'        =>  params_get("emisor_nombre"),
            'Identificacion' => array(
                'Tipo'      =>  params_get("emisor_tipo_identif"),
                'Numero'    =>  params_get("emisor_num_identif"),
            ),
            'NombreComercial'   => params_get("nombre_comercial"),
            'Ubicacion'         => array(
                'Provincia'     => params_get("emisor_provincia"),
                'Canton'        => params_get("emisor_canton"),
                'Distrito'      => params_get("emisor_distrito"),
                'Barrio'        => params_get("emisor_barrio"),
                'OtrasSenas'    => params_get("emisor_otras_senas"),
            ),
            'Telefono' => array(
                'CodigoPais'    => params_get("emisor_cod_pais_tel"),
                'NumTelefono'   => params_get("emisor_tel"),
            ),
            'Fax' => array(
                'CodigoPais'    => params_get("emisor_cod_pais_fax"),
                'NumTelefono'   => params_get("emisor_fax"),
            ),
            'CorreoElectronico' => params_get("emisor_email"),
        ),
        'Receptor'            => array(
            'Nombre'        => params_get("receptor_nombre"),
            'Identificacion' => array(
                'Tipo'      => params_get("receptor_tipo_identif"),
                'Numero'    => params_get("receptor_num_identif"),
            ),
            'Ubicacion'         => array(
                'Provincia'     => params_get("receptor_provincia"),
                'Canton'        => params_get("receptor_canton"),
                'Distrito'      => params_get("receptor_distrito"),
                'Barrio'        => params_get("receptor_barrio"),
                'OtrasSenas'    => params_get("receptor_otras_senas"),
            ),
            'Telefono' => array(
                'CodigoPais'    => params_get("receptor_cod_pais_tel"),
                'NumTelefono'   => params_get("receptor_tel"),
            ),
            'Fax' => array(
                'CodigoPais'    => params_get("receptor_cod_pais_fax"),
                'NumTelefono'   => params_get("receptor_fax"),
            ),
            'CorreoElectronico' => params_get("receptor_email"),
        ),
        'CondicionVenta'    => params_get("condicion_venta"),
        'PlazoCredito'    => params_get("plazo_credito"),
        'MedioPago'         => params_get("medio_pago"),

        'DetalleServicio'   => $DetalleServicio,            //Lineas de Detalle

        'ResumenFactura'    => array([
            //'CodigoMoneda'      => params_get("cod_moneda"),
           // 'TipoCambio'        => round(params_get("tipo_cambio"), 5),
            'TotalServGravados' => round(params_get("TotalServGravados"), 5),
            'TotalServExentos' => round(params_get("TotalServExentos"), 5),
            'TotalServExonerado' => round(params_get("TotalServExonerado"), 5),
            'TotalMercanciasGravadas' => round(params_get("TotalMercanciasGravadas"), 5),
            'TotalMercanciasExentas' => round(params_get("TotalMercanciasExentas"), 5),
            'TotalMercExonerada' => round(params_get("TotalMercExonerada"), 5),
            'TotalGravado' => round(params_get("TotalGravado"), 5),
            'TotalExento'=> round(params_get("TotalExento"), 5),
            'TotalExonerado' => round(params_get("TotalExonerado"), 5),
            'TotalVenta'        => round(params_get("TotalVenta"), 5),
            'TotalDescuentos' => round(params_get("TotalDescuentos"), 5),
            'TotalVentaNeta'    => round(params_get("TotalVentaNeta"), 5),
            'TotalImpuesto' => round(params_get("TotalImpuesto"), 5),
            'TotalIVADevuelto' => round(params_get("TotalIVADevuelto"), 5),
            'TotalOtrosCargos' => round(params_get("TotalOtrosCargos"), 5),
            'TotalComprobante'  => round(params_get("TotalComprobante"), 5),
        ]),
        //'InformacionReferencia' => array([        ]),
        //'Otros' => array([        ]),

    );



    $headerDoc="";
    $footerDoc="";
     if (params_get("tipoDocumento") == 'FE')
     {
        $headerDoc='<?xml version="1.0" encoding="UTF-8"?><FacturaElectronica xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronica">';
        $footerDoc='</FacturaElectronica>';
    }
    elseif (params_get("tipoDocumento") == 'TE')
    {
        $headerDoc='<?xml version="1.0" encoding="UTF-8"?><TiqueteElectronico xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/tiqueteElectronico" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"> ';
        $footerDoc='</TiqueteElectronico>';
    }
    elseif (params_get("tipoDocumento") == 'NC')
    {
        $Documento=   array_merge($Documento , $InformacionReferencia);
        $headerDoc='<?xml version="1.0" encoding="UTF-8"?><NotaCreditoElectronica xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaCreditoElectronica" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"> ';
        $footerDoc='</NotaCreditoElectronica>';
    }
    elseif (params_get("tipoDocumento") == 'ND')
    {
        $headerDoc='<?xml version="1.0" encoding="UTF-8"?><NotaDebitoElectronica xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaDebitoElectronica" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"> ';
        $footerDoc='</NotaDebitoElectronica>';
    }

     $xmltext=  array_to_xml($Documento);

    $finalXML= $headerDoc . $xmltext . $footerDoc;
    return $finalXML;
}
function array_to_xml($arr)
{
    $xml = "";
    if (!empty($arr)) {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                if (array_key_exists($key, $value)) {
                    foreach ($value as $kk => $vv) {
                        $xml .= "<$key>\n";
                        $xml .= array_to_xml($vv);
                        $xml .= "</$key>";
                    }
                } else {
                    if (!is_numeric($key)) {
                        $xml .= "<$key>\n";
                    }
                    $xml .= array_to_xml($value);
                    if (!is_numeric($key)) {
                        $xml .= "</$key>\n";
                    }
                }
            } else {
                $xml .= "<$key>$value</$key>\n";
            }
        }
    }
    return $xml;
}

?>
