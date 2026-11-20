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
   $detalle= json_decode(params_get("detalle"),true);

   $tipo_doc=params_get("tipoDocumento");

   $tipo_doc_referencia=params_get("tipo_doc_referencia");
   $fecha_emision_ir=params_get("fecha_emision_ir");
   $tsIr = (!empty($fecha_emision_ir) && $fecha_emision_ir !== '0') ? strtotime($fecha_emision_ir) : false;

   $fecha_emision_ir_formateada = ($tsIr !== false) ? date("Y-m-d\TH:i:sP", $tsIr) : '';
   $sello_sat_50 = params_get("sello_sat_50");

   //$DetalleServicio        = json_decode(params_get("detalles"));


    foreach ($detalle as $key => $value)
    {
        if ($key >= 0)
        {
            $DetalleServicio[] = array(
                'LineaDetalle' => array(
                    'NumeroLinea' => $value["NUMERO_LINEA"],
                    'CodigoCABYS' => $value["CODIGO_CABYS"],
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
                        'CodigoDescuento'   =>$value["CodigoDescuento"],
                        'NaturalezaDescuento'   =>$value["NaturalezaDescuento"]

                    ]);

                    $Subtotal = array(
                        'SubTotal'=> round($value["SubTotal"], 5),
                        'BaseImponible'        => round($value["SubTotal"], 5)
                    );

                    $Impuestos = array(
                    'Impuesto'          =>[
                        'Codigo'         => $value["CODIGO"] ,
                        'CodigoTarifaIVA' =>$value["TARIFA"],
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
                        'ImpuestoAsumidoEmisorFabrica' => round(0, 5),
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

    // Agrupar impuestos por código y tarifa
    $impuestosAgrupados = array();
    foreach ($detalle as $value) {
        $codigo = $value["CODIGO"];
        $tarifa = $value["TARIFA"];
        $monto = $value["ImpuestoMonto"];

        $key = $codigo . '_' . $tarifa;

        if (!isset($impuestosAgrupados[$key])) {
            $impuestosAgrupados[$key] = array(
                'Codigo' => $codigo,
                'CodigoTarifaIVA' => $tarifa,
                'TotalMontoImpuesto' => 0
            );
        }

        $impuestosAgrupados[$key]['TotalMontoImpuesto'] += $monto;
    }
    $totalDesgloseImpuesto = array();
    foreach ($impuestosAgrupados as $impuesto) {
        $totalDesgloseImpuesto[] = array(
            'Codigo' => $impuesto['Codigo'],
            'CodigoTarifaIVA' => $impuesto['CodigoTarifaIVA'],
            'TotalMontoImpuesto' => round($impuesto['TotalMontoImpuesto'], 5)
        );
    }




    // Construir el array base sin Receptor
    $DocumentoBase = array(
        'Clave'             =>  params_get("clave"),
        'ProveedorSistemas' =>  params_get("proveedor_sistemas"),
        'CodigoActividadEmisor'   =>  params_get("codigo_actividad"),
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
            'CorreoElectronico' => params_get("emisor_email"),
        ),
    );

    if ($tipo_doc == 'FE') {
        $DocumentoBase = array_merge(
            array_slice($DocumentoBase, 0, 3, true),
            ['CodigoActividadReceptor' => params_get("codigo_actividad_receptor")],
            array_slice($DocumentoBase, 3, null, true)
        );
    }

    // Solo agregar Receptor si no es TE
    // Incluir Receptor solo si es FE o NC (pero en NC solo si el doc de origen NO es TE)
    if (
        $tipo_doc == 'FE' ||
        ($tipo_doc == 'NC' && $tipo_doc_referencia != '04')
    ) {
        $DocumentoBase['Receptor'] = array(
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
            'CorreoElectronico' => params_get("receptor_email"),
        );
    }


    $Documento = array_merge($DocumentoBase, array(
        'CondicionVenta'    => params_get("condicion_venta"),
        'PlazoCredito'    => "30",
        'DetalleServicio'   => $DetalleServicio,            //Lineas de Detalle
        'ResumenFactura'    => array([
            'CodigoTipoMoneda' => array(
                'CodigoMoneda'    => params_get("cod_moneda"),
                'TipoCambio'   => params_get("tipo_cambio"),
            ),

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


            'TotalDesgloseImpuesto' => $totalDesgloseImpuesto,

            'TotalImpuesto' => round(params_get("TotalImpuesto"), 5),
            'TotalImpAsumEmisorFabrica' => round(0, 5),

            'TotalIVADevuelto' => round(params_get("TotalIVADevuelto"), 5),

            'MedioPago' => array(
                'TipoMedioPago'    => $tipo_doc == 'NC' ? '01' : params_get("medio_pago"),
                'TotalMedioPago'   => params_get("TotalComprobante"),
            ),


            'TotalComprobante'  => round(params_get("TotalComprobante"), 5),
        ]),


    ));


    $InformacionReferencia = array(
        'InformacionReferencia' => [
            'TipoDocIR'=>  $tipo_doc_referencia,
            'Numero'=>  $sello_sat_50,
            'FechaEmisionIR'=> $fecha_emision_ir_formateada,
            'Codigo'=>  params_get("codigo_razon"),
            'Razon'=>  params_get("razon"),
        ]);


    $headerDoc="";
    $footerDoc="";
     if ($tipo_doc == 'FE')
     {
        $headerDoc='<FacturaElectronica xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/facturaElectronica">';
        $footerDoc='</FacturaElectronica>';
    }
    elseif ($tipo_doc == 'TE')
    {
        $headerDoc='<TiqueteElectronico xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/tiqueteElectronico">
';
        $footerDoc='</TiqueteElectronico>';
    }
    elseif ($tipo_doc== 'NC')
    {
        $Documento=   array_merge($Documento , $InformacionReferencia);
        $headerDoc='<?xml version="1.0" encoding="UTF-8"?><NotaCreditoElectronica xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/notaCreditoElectronica"> ';
        $footerDoc='</NotaCreditoElectronica>';
    }
    elseif ($tipo_doc == 'ND')
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
