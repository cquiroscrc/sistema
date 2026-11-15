<?php
/**
 * Cierre de caja por correo.
 * Misma lectura que caja_correo.php (hfacturas, cxc, egresos, bancos y metas)
 * y el mismo destinatario que cierres.php, con un cuerpo que se lee en el teléfono.
 *
 * Vista, no envía:  /utilitarios/caja_correo_nuevo.php
 *                   /utilitarios/caja_correo_nuevo.php?Date2=2026-11-05
 *                   /utilitarios/caja_correo_nuevo.php?muestra=1
 * Envío, solo desde este servidor: ?enviar=1
 */
date_default_timezone_set('America/Costa_Rica');

// Desactivado: destinatarios solo desde Configuración del panel (Carlos 2026-11-14)
// const LP_CIERRE_PARA = 'hhernandezv@lospaticas.com';
const LP_CIERRE_DE = 'info@lospaticas.com';

function lp_monto(float $n): string
{
    return number_format($n, 2, '.', ',');
}

function lp_campo(array $fila, string $nombre)
{
    if (array_key_exists($nombre, $fila)) {
        return $fila[$nombre];
    }
    $buscado = strtolower($nombre);
    foreach ($fila as $clave => $valor) {
        if (strtolower((string) $clave) === $buscado) {
            return $valor;
        }
    }
    return null;
}

function lp_fecha_pedida(): string
{
    $fecha = $_REQUEST['Date2'] ?? $_REQUEST['Date1'] ?? date('Y-m-d');
    if (!is_string($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return date('Y-m-d');
    }
    $partes = explode('-', $fecha);
    if (!checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])) {
        return date('Y-m-d');
    }
    return $fecha;
}

function lp_fecha_texto(string $ymd): string
{
    $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    $meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $momento = strtotime($ymd . ' 12:00:00');
    if ($momento === false) {
        return $ymd;
    }
    return $dias[(int) date('w', $momento)] . ' ' . (int) date('j', $momento) . ' de ' . $meses[(int) date('n', $momento)] . ' de ' . date('Y', $momento);
}

function lp_consulta($id, string $sql)
{
    $resultado = odbc_exec($id, $sql);
    if ($resultado === false) {
        error_log('caja_correo_nuevo: ' . odbc_errormsg($id));
        throw new RuntimeException('No se pudo leer el cierre de caja.');
    }
    return $resultado;
}

function lp_cierre_calcular($id, string $fecha): array
{
    $desde = $fecha . ' 00:00:00.000';
    $hasta = $fecha . ' 23:59:59.000';
    $contadoSub = 0.0;
    $contadoDesc = 0.0;
    $contadoImp = 0.0;
    $contadoTot = 0.0;
    $creditoSub = 0.0;
    $creditoDesc = 0.0;
    $creditoImp = 0.0;
    $creditoTot = 0.0;

    $facturas = lp_consulta($id, "SELECT Tipo, subtotal, descuento, impuesto, total FROM hfacturas WHERE fechafact BETWEEN '$desde' AND '$hasta'");
    while ($fila = odbc_fetch_array($facturas)) {
        $subtotal = (float) lp_campo($fila, 'subtotal');
        $descuento = (float) lp_campo($fila, 'descuento');
        $impuesto = (float) lp_campo($fila, 'impuesto');
        $total = (float) lp_campo($fila, 'total');
        if ((int) lp_campo($fila, 'Tipo') === 0) {
            $contadoSub += $subtotal;
            $contadoDesc += $descuento;
            $contadoImp += $impuesto;
            $contadoTot += $total;
        } else {
            $creditoSub += $subtotal;
            $creditoDesc += $descuento;
            $creditoImp += $impuesto;
            $creditoTot += $total;
        }
    }

    $mensajeria = ($contadoTot - ($contadoSub + $contadoImp - $contadoDesc))
        + ($creditoTot - ($creditoSub + $creditoImp - $creditoDesc));

    $recibos = 0.0;
    $consultaRecibos = lp_consulta($id, "SELECT Total FROM cxc WHERE Fecha BETWEEN '$desde' AND '$hasta' AND NFAC LIKE 'R-%'");
    while ($fila = odbc_fetch_array($consultaRecibos)) {
        $recibos += (float) lp_campo($fila, 'Total');
    }

    $egresos = [
        'descuentos' => 0.0,
        'repuestos' => 0.0,
        'papeleria' => 0.0,
        'combustible' => 0.0,
        'atencion' => 0.0,
        'encomiendas' => 0.0,
        'luz' => 0.0,
        'agua' => 0.0,
        'telefono' => 0.0,
        'otros' => 0.0,
        'tarjetas' => 0.0,
    ];
    $totalEgresos = 0.0;
    $consultaEgresos = lp_consulta($id, "SELECT Tipo, Monto FROM tbl_Egresos WHERE Fecha BETWEEN '$desde' AND '$hasta'");
    while ($fila = odbc_fetch_array($consultaEgresos)) {
        $monto = (float) lp_campo($fila, 'Monto');
        $totalEgresos += $monto;
        switch ((int) lp_campo($fila, 'Tipo')) {
            case 0:
                $egresos['descuentos'] += $monto;
                break;
            case 1:
                $egresos['repuestos'] += $monto;
                break;
            case 2:
                $egresos['papeleria'] += $monto;
                break;
            case 3:
            case 4:
                $egresos['atencion'] += $monto;
                break;
            case 5:
                $egresos['encomiendas'] += $monto;
                break;
            case 6:
                $egresos['luz'] += $monto;
                break;
            case 7:
                $egresos['agua'] += $monto;
                break;
            case 8:
                $egresos['telefono'] += $monto;
                break;
            case 9:
                $egresos['otros'] += $monto;
                break;
            case 10:
                $egresos['tarjetas'] += $monto;
                break;
        }
    }

    $depositos = 0.0;
    $consultaDepositos = lp_consulta($id, "SELECT Credito FROM tbl_Bancos WHERE Fecha BETWEEN '$desde' AND '$hasta' AND Descripcion LIKE 'Caja del %'");
    while ($fila = odbc_fetch_array($consultaDepositos)) {
        $depositos += (float) lp_campo($fila, 'Credito');
    }

    $ingresos = $contadoTot + $recibos;
    $caja = $ingresos - $totalEgresos;
    $diferencia = $depositos - $ingresos + $totalEgresos;

    $momentoFecha = strtotime($fecha . ' 12:00:00');
    $inicioMes = date('Y-m-01', $momentoFecha);
    $finMes = date('Y-m-t', $momentoFecha);
    $ventaMes = 0.0;
    $consultaVenta = lp_consulta($id, "SELECT SUM(subtotal) - SUM(descuento) AS venta FROM hfacturas WHERE fechafact >= '" . $inicioMes . " 00:00:00.000' AND fechafact <= '" . $finMes . " 23:59:00.000' AND impuesto <> 0");
    $filaVenta = odbc_fetch_array($consultaVenta);
    if (is_array($filaVenta)) {
        $ventaMes = (float) lp_campo($filaVenta, 'venta');
    }

    $metaMes = 0.0;
    $consultaMeta = lp_consulta($id, 'SELECT meta FROM tbl_metas WHERE mes = ' . (int) date('m', $momentoFecha) . ' AND ano = ' . (int) date('Y', $momentoFecha));
    while ($fila = odbc_fetch_array($consultaMeta)) {
        $metaMes = (float) lp_campo($fila, 'meta') * 4;
    }

    return [
        'fecha' => $fecha,
        'contado_subtotal' => $contadoSub,
        'contado_descuento' => $contadoDesc,
        'contado_impuesto' => $contadoImp,
        'contado_total' => $contadoTot,
        'credito_subtotal' => $creditoSub,
        'credito_descuento' => $creditoDesc,
        'credito_impuesto' => $creditoImp,
        'credito_total' => $creditoTot,
        'mensajeria' => $mensajeria,
        'recibos' => $recibos,
        'e_descuentos' => $egresos['descuentos'],
        'e_repuestos' => $egresos['repuestos'],
        'e_papeleria' => $egresos['papeleria'],
        'e_combustible' => $egresos['combustible'],
        'e_atencion' => $egresos['atencion'],
        'e_encomiendas' => $egresos['encomiendas'],
        'e_luz' => $egresos['luz'],
        'e_agua' => $egresos['agua'],
        'e_telefono' => $egresos['telefono'],
        'e_otros' => $egresos['otros'],
        'e_tarjetas' => $egresos['tarjetas'],
        'ingresos' => $ingresos,
        'egresos' => $totalEgresos,
        'caja' => $caja,
        'depositos' => $depositos,
        'diferencia' => $diferencia,
        'venta_mes' => $ventaMes,
        'meta_mes' => $metaMes,
    ];
}

function lp_cierre_muestra(): array
{
    $serial = 'a:29:{i:1;s:10:"2026-11-05";i:2;d:1228150;i:3;d:125205.85;i:4;d:143382.78499999992;i:5;d:1246326.9049999998;i:6;d:6850;i:7;d:1370;i:8;d:712.4;i:9;d:6192.4;i:10;d:-0.030000000027939677;i:11;d:2440;i:12;i:0;i:13;i:0;i:14;i:0;i:15;i:0;i:16;i:0;i:17;d:35700.22;i:18;d:22400;i:19;i:0;i:20;i:0;i:21;i:0;i:22;d:416842.88;i:23;d:1248766.9049999998;i:24;d:474943.1;i:25;d:773823.8049999998;i:26;d:772701.49;i:27;d:-1122.3149999998277;i:28;s:12:"7968922.4500";i:29;d:42000000;}';
    $lista = unserialize($serial, ['allowed_classes' => false]);
    if (!is_array($lista)) {
        throw new RuntimeException('No se pudo armar la muestra del cierre.');
    }
    return [
        'fecha' => (string) $lista[1],
        'contado_subtotal' => (float) $lista[2],
        'contado_descuento' => (float) $lista[3],
        'contado_impuesto' => (float) $lista[4],
        'contado_total' => (float) $lista[5],
        'credito_subtotal' => (float) $lista[6],
        'credito_descuento' => (float) $lista[7],
        'credito_impuesto' => (float) $lista[8],
        'credito_total' => (float) $lista[9],
        'mensajeria' => (float) $lista[10],
        'recibos' => (float) $lista[11],
        'e_descuentos' => (float) $lista[12],
        'e_repuestos' => (float) $lista[13],
        'e_papeleria' => (float) $lista[14],
        'e_combustible' => (float) $lista[15],
        'e_atencion' => (float) $lista[16],
        'e_encomiendas' => (float) $lista[17],
        'e_luz' => (float) $lista[18],
        'e_agua' => (float) $lista[19],
        'e_telefono' => (float) $lista[20],
        'e_otros' => (float) $lista[21],
        'e_tarjetas' => (float) $lista[22],
        'ingresos' => (float) $lista[23],
        'egresos' => (float) $lista[24],
        'caja' => (float) $lista[25],
        'depositos' => (float) $lista[26],
        'diferencia' => (float) $lista[27],
        'venta_mes' => (float) $lista[28],
        'meta_mes' => (float) $lista[29],
    ];
}

function lp_estilo_fuente(): string
{
    return "font-family:'Atkinson Hyperlegible',Verdana,Geneva,sans-serif;font-variant-numeric:tabular-nums lining-nums;font-feature-settings:'tnum' 1,'lnum' 1;";
}

function lp_renglon(string $etiqueta, float $monto, bool $atenuarCero = true): string
{
    $cero = abs($monto) < 0.005;
    $color = ($atenuarCero && $cero) ? '#7f93a3' : '#1a1a1a';
    $peso = ($atenuarCero && $cero) ? 'normal' : 'bold';
    $fuente = lp_estilo_fuente();
    return '<tr>'
        . '<td style="padding-top:9px;padding-bottom:9px;border-bottom:1px solid #d5e0e8;' . $fuente . 'font-size:16px;color:#3d4f5f;">' . htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td align="right" style="padding-top:9px;padding-bottom:9px;border-bottom:1px solid #d5e0e8;' . $fuente . 'font-size:18px;white-space:nowrap;color:' . $color . ';font-weight:' . $peso . ';">' . lp_monto($monto) . '</td>'
        . '</tr>';
}

function lp_tarjeta_venta(string $titulo, string $nota, float $subtotal, float $descuento, float $impuesto, float $total, string $borde): string
{
    $fuente = lp_estilo_fuente();
    $linea = function (string $etiqueta, float $valor, bool $cierre = false) use ($fuente): string {
        $peso = $cierre ? 'bold' : 'normal';
        $tamano = $cierre ? '24px' : '16px';
        $color = $cierre ? '#2f618a' : '#1a1a1a';
        $reborde = $cierre ? 'border-top:1px solid #d5e0e8;padding-top:10px;' : 'padding-top:6px;';
        return '<tr>'
            . '<td style="' . $reborde . $fuente . 'font-size:14px;color:#3d4f5f;">' . $etiqueta . '</td>'
            . '<td align="right" style="' . $reborde . $fuente . 'font-size:' . $tamano . ';white-space:nowrap;font-weight:' . $peso . ';color:' . $color . ';">' . lp_monto($valor) . '</td>'
            . '</tr>';
    };
    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-top:4px solid ' . $borde . ';border-right:1px solid #d5e0e8;border-bottom:1px solid #d5e0e8;border-left:1px solid #d5e0e8;">'
        . '<tr><td style="padding:16px 16px 14px 16px;">'
        . '<div style="' . $fuente . 'font-size:12px;letter-spacing:0.14em;text-transform:uppercase;color:#2f618a;">' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</div>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;">'
        . $linea('Subtotal', $subtotal)
        . $linea('Descuento', $descuento)
        . $linea('Impuesto', $impuesto)
        . $linea('Total', $total, true)
        . '</table>'
        . '<div style="padding-top:8px;' . $fuente . 'font-size:13px;line-height:1.4;color:#5c7080;">' . $nota . '</div>'
        . '</td></tr></table>';
}

function lp_titulo_seccion(string $texto): string
{
    $fuente = lp_estilo_fuente();
    return '<table role="presentation" cellpadding="0" cellspacing="0"><tr>'
        . '<td width="14" bgcolor="#a53231" style="width:14px;background-color:#a53231;font-size:0;line-height:0;">&nbsp;</td>'
        . '<td style="padding-left:10px;' . $fuente . 'font-size:13px;letter-spacing:0.14em;text-transform:uppercase;color:#2f618a;font-weight:bold;">' . htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') . '</td>'
        . '</tr></table>';
}

function lp_cierre_cuerpo(array $cierre): string
{
    $fuente = lp_estilo_fuente();
    $diferencia = (float) $cierre['diferencia'];
    if ($diferencia < -0.004) {
        $palabra = 'Faltante';
        $fondoDif = '#f8e8e7';
        $tintaDif = '#a53231';
    } elseif ($diferencia > 0.004) {
        $palabra = 'Sobrante';
        $fondoDif = '#e7f0f6';
        $tintaDif = '#2f618a';
    } else {
        $palabra = 'En cero';
        $fondoDif = '#eef2f5';
        $tintaDif = '#1a1a1a';
    }
    $colorMensajeria = abs((float) $cierre['mensajeria']) < 0.05 ? '#7f93a3' : ((float) $cierre['mensajeria'] < 0 ? '#a53231' : '#1a1a1a');
    $preheader = $palabra . ' de ' . lp_monto($diferencia) . '. Ingresos ' . lp_monto((float) $cierre['ingresos']) . '. Depósitos ' . lp_monto((float) $cierre['depositos']) . '.';
    $sinMovimiento = abs((float) $cierre['contado_total']) < 0.005
        && abs((float) $cierre['credito_total']) < 0.005
        && abs((float) $cierre['recibos']) < 0.005
        && abs((float) $cierre['egresos']) < 0.005;

    $avance = '';
    if ((float) $cierre['meta_mes'] > 0) {
        $porcentaje = (int) max(0, min(100, (int) round(((float) $cierre['venta_mes'] / (float) $cierre['meta_mes']) * 100)));
        $resto = 100 - $porcentaje;
        $avance = '<tr><td style="padding-top:12px;">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>'
            . '<td width="' . $porcentaje . '%" bgcolor="#2f618a" style="height:8px;background-color:#2f618a;font-size:0;line-height:0;">&nbsp;</td>'
            . '<td width="' . $resto . '%" bgcolor="#d5e0e8" style="height:8px;background-color:#d5e0e8;font-size:0;line-height:0;">&nbsp;</td>'
            . '</tr></table>'
            . '<div style="padding-top:6px;' . $fuente . 'font-size:14px;color:#3d4f5f;">' . $porcentaje . '% de la meta del mes</div>'
            . '</td></tr>';
    }

    $aviso = $sinMovimiento
        ? '<tr><td style="padding:18px 32px 0 32px;' . $fuente . 'font-size:15px;color:#a53231;">Este día no tiene ventas, recibos ni egresos.</td></tr>'
        : '';

    $egresos = lp_renglon('Descuentos', (float) $cierre['e_descuentos'])
        . lp_renglon('Repuestos', (float) $cierre['e_repuestos'])
        . lp_renglon('Papelería', (float) $cierre['e_papeleria'])
        . lp_renglon('Combustible', (float) $cierre['e_combustible'])
        . lp_renglon('Atención', (float) $cierre['e_atencion'])
        . lp_renglon('Encomiendas', (float) $cierre['e_encomiendas'])
        . lp_renglon('Luz', (float) $cierre['e_luz'])
        . lp_renglon('Agua', (float) $cierre['e_agua'])
        . lp_renglon('Teléfono', (float) $cierre['e_telefono'])
        . lp_renglon('Otros', (float) $cierre['e_otros'])
        . lp_renglon('Cierres de tarjetas', (float) $cierre['e_tarjetas']);

    return '<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">' . htmlspecialchars($preheader, ENT_QUOTES, 'UTF-8') . '</div>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#e4edf3" style="background-color:#e4edf3;">'
        . '<tr><td align="center" style="padding:28px 12px 36px 12px;">'
        . '<table role="presentation" width="640" class="lp-hoja" cellpadding="0" cellspacing="0" bgcolor="#f7fafc" style="width:640px;max-width:640px;background-color:#f7fafc;border:1px solid #c5d4e0;">'
        . '<tr><td align="center" bgcolor="#ffffff" style="background-color:#ffffff;padding:16px 24px 12px 24px;">'
        . '<img src="https://sistema.lospaticas.com/img/logo-cierre.png" width="280" alt="Los Paticas" style="display:block;border:0;outline:none;text-decoration:none;width:280px;max-width:100%;height:auto;">'
        . '</td></tr>'
        . '<tr><td bgcolor="#2f618a" style="background-color:#2f618a;padding:22px 32px 20px 32px;">'
        . '<div style="' . $fuente . 'font-size:34px;line-height:1.05;color:#ffffff;font-weight:bold;">Cierre de caja</div>'
        . '<div style="' . $fuente . 'font-size:18px;color:#e8f0f6;padding-top:8px;">' . htmlspecialchars(lp_fecha_texto((string) $cierre['fecha']), ENT_QUOTES, 'UTF-8') . '</div>'
        . '<div style="' . $fuente . 'font-size:15px;letter-spacing:0.04em;color:#e8f0f6;padding-top:4px;">' . htmlspecialchars((string) $cierre['fecha'], ENT_QUOTES, 'UTF-8') . '</div>'
        . '</td></tr>'
        . $aviso
        . '<tr><td style="padding:26px 32px 0 32px;">' . lp_titulo_seccion('Ventas del día') . '</td></tr>'
        . '<tr><td style="padding:14px 32px 0 32px;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>'
        . '<td class="lp-col" width="49%" valign="top">' . lp_tarjeta_venta('Contado', 'Entra al efectivo del día.', (float) $cierre['contado_subtotal'], (float) $cierre['contado_descuento'], (float) $cierre['contado_impuesto'], (float) $cierre['contado_total'], '#2f618a') . '</td>'
        . '<td class="lp-col-gap" width="2%" style="font-size:0;line-height:0;">&nbsp;</td>'
        . '<td class="lp-col" width="49%" valign="top">' . lp_tarjeta_venta('Crédito', 'No entra al efectivo del día.', (float) $cierre['credito_subtotal'], (float) $cierre['credito_descuento'], (float) $cierre['credito_impuesto'], (float) $cierre['credito_total'], '#111111') . '</td>'
        . '</tr></table>'
        . '</td></tr>'
        . '<tr><td style="padding:14px 32px 0 32px;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">'
        . '<tr><td style="' . $fuente . 'font-size:16px;color:#3d4f5f;">Mensajería</td><td align="right" style="' . $fuente . 'font-size:18px;white-space:nowrap;font-weight:bold;color:' . $colorMensajeria . ';">' . lp_monto((float) $cierre['mensajeria']) . '</td></tr>'
        . '<tr><td style="padding-top:8px;' . $fuente . 'font-size:16px;color:#3d4f5f;">Recibos</td><td align="right" style="padding-top:8px;' . $fuente . 'font-size:18px;white-space:nowrap;font-weight:bold;color:#1a1a1a;">' . lp_monto((float) $cierre['recibos']) . '</td></tr>'
        . '</table></td></tr>'
        . '<tr><td style="padding:28px 32px 0 32px;">' . lp_titulo_seccion('Egresos') . '</td></tr>'
        . '<tr><td style="padding:8px 32px 0 32px;"><table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $egresos . '</table></td></tr>'
        . '<tr><td style="padding:28px 32px 0 32px;">' . lp_titulo_seccion('Cuadre de caja') . '</td></tr>'
        . '<tr><td style="padding:12px 32px 0 32px;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">'
        . lp_renglon('Ingresos', (float) $cierre['ingresos'], false)
        . lp_renglon('Egresos', (float) $cierre['egresos'], false)
        . lp_renglon('Total en caja', (float) $cierre['caja'], false)
        . lp_renglon('Depósitos', (float) $cierre['depositos'], false)
        . '</table></td></tr>'
        . '<tr><td style="padding:16px 32px 0 32px;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="' . $fondoDif . '" style="background-color:' . $fondoDif . ';"><tr><td style="padding:18px 18px 16px 18px;">'
        . '<div style="' . $fuente . 'font-size:12px;letter-spacing:0.16em;text-transform:uppercase;color:' . $tintaDif . ';">' . $palabra . '</div>'
        . '<div style="' . $fuente . 'font-size:42px;line-height:1.05;font-weight:bold;letter-spacing:0.02em;color:' . $tintaDif . ';padding-top:4px;">' . lp_monto($diferencia) . '</div>'
        . '</td></tr></table></td></tr>'
        . '<tr><td style="padding:28px 32px 0 32px;">' . lp_titulo_seccion('Metas') . '</td></tr>'
        . '<tr><td style="padding:8px 32px 0 32px;"><table role="presentation" width="100%" cellpadding="0" cellspacing="0">'
        . lp_renglon('Meta hasta el momento', (float) $cierre['venta_mes'], false)
        . lp_renglon('Meta de este mes', (float) $cierre['meta_mes'], false)
        . $avance
        . '</table></td></tr>'
        . '<tr><td style="padding:22px 32px 26px 32px;' . $fuente . 'font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#5c7080;">Los Paticas · cierre del día</td></tr>'
        . '</table></td></tr></table>';
}

function lp_cierre_correo(array $cierre): string
{
    $fuente = lp_estilo_fuente();
    $estilo = '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap">'
        . '<style type="text/css">@media only screen and (max-width:680px){.lp-hoja{width:100%!important;}.lp-col,.lp-col-gap{display:block!important;width:100%!important;}.lp-col-gap{height:12px;}}</style>';
    return '<!DOCTYPE html><html lang="es"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Cierre de caja ' . htmlspecialchars((string) $cierre['fecha'], ENT_QUOTES, 'UTF-8') . '</title>'
        . $estilo
        . '</head><body style="margin:0;padding:0;' . $fuente . '">'
        . lp_cierre_cuerpo($cierre)
        . '</body></html>';
}

function lp_cierre_vista(array $cierre, string $nota): string
{
    $fuente = lp_estilo_fuente();
    $asunto = 'Cierre de hoy ' . $cierre['fecha'];
    $barra = '<div style="padding:16px 20px;background-color:#2f618a;color:#ffffff;text-align:center;' . $fuente . '">'
        . '<div style="font-size:13px;letter-spacing:0.14em;text-transform:uppercase;color:#e8f0f6;font-weight:bold;">Vista previa · el correo no se envió</div>'
        . '<div style="padding-top:6px;font-size:16px;">Para los destinatarios de Configuración del panel · Asunto: ' . htmlspecialchars($asunto, ENT_QUOTES, 'UTF-8') . '</div>'
        . '<div style="padding-top:6px;font-size:14px;color:#e8f0f6;">' . htmlspecialchars($nota, ENT_QUOTES, 'UTF-8') . '</div>'
        . '</div>';
    $correo = lp_cierre_correo($cierre);
    $cuerpo = preg_replace('/<body([^>]*)>/', '<body$1>' . $barra, $correo, 1);
    return is_string($cuerpo) ? $cuerpo : $correo;
}

function lp_peticion_local(): bool
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    return $ip === '127.0.0.1' || $ip === '::1';
}

function lp_cierre_enviar(array $cierre): bool
{
    $asunto = 'Cierre de hoy ' . $cierre['fecha'];
    $html = lp_cierre_correo($cierre);
    $cuerpo = quoted_printable_encode($html);
    $cabeceras = "MIME-Version: 1.0\r\n"
        . "Content-Type: text/html; charset=utf-8\r\n"
        . "Content-Transfer-Encoding: quoted-printable\r\n"
        . 'From: ' . LP_CIERRE_DE . "\r\n"
        . 'Reply-To: ' . LP_CIERRE_DE . "\r\n"
        . 'X-Mailer: PHP/' . phpversion();
    // Desactivado: destinatarios solo desde Configuración del panel (Carlos 2026-11-14)
    // return mail(LP_CIERRE_PARA, $asunto, $cuerpo, $cabeceras);
    error_log('caja_correo_nuevo.php: envío directo desactivado; el cierre oficial lo envía panel-hacienda/correo/caja-cierre.php');
    return false;
}

function lp_cierre_responder(): void
{
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    $muestra = isset($_GET['muestra']) && (string) $_GET['muestra'] === '1';
    try {
        if ($muestra) {
            $cierre = lp_cierre_muestra();
            $nota = 'Cifras del correo del 5 de noviembre de 2026, para comparar el formato. La tarea programada sigue igual.';
        } else {
            require_once __DIR__ . '/db.php';
            if (empty($id)) {
                throw new RuntimeException('No se pudo abrir la base del cierre.');
            }
            $cierre = lp_cierre_calcular($id, lp_fecha_pedida());
            $nota = 'Calculado con la base de este servidor. La tarea programada sigue igual.';
        }
    } catch (RuntimeException $excepcion) {
        http_response_code(500);
        echo htmlspecialchars($excepcion->getMessage(), ENT_QUOTES, 'UTF-8');
        return;
    }

    $quiereEnviar = !$muestra && isset($_GET['enviar']) && (string) $_GET['enviar'] === '1';
    if ($quiereEnviar && lp_peticion_local()) {
        header('Content-Type: text/plain; charset=utf-8');
        if (abs((float) $cierre['ingresos']) < 0.00001) {
            echo 'No se envió: el día ' . $cierre['fecha'] . " no tiene ingresos.\n";
            return;
        }
        if (lp_cierre_enviar($cierre)) {
            echo 'Correo de cierre enviado (' . $cierre['fecha'] . ").\n";
            return;
        }
        http_response_code(500);
        echo "No se pudo enviar el correo.\n";
        return;
    }

    echo lp_cierre_vista($cierre, $nota);
}

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath((string) $_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)) {
    lp_cierre_responder();
}
