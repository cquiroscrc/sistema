<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
date_default_timezone_set("America/Costa_Rica"); 

if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
    $datos = $_REQUEST['datos'];
} else {
    error_log('cierres.php: datos vacios o ausentes');
    exit;
}

$conteo=0;

function array_recibe($arr) { 

    $datos_tmp = stripslashes($arr); 

    $datos_tmp = urldecode($datos_tmp); // Si antes comentareaste el urlenconde en la pagina envia_array.php entonces esta linea debes comentarearla tambien 

    $datos_tmp = unserialize($datos_tmp); 

    return $datos_tmp; 

} 

$array1 = array_recibe($datos); 



  $fecha1=$array1[1];

  $varsubtotal=$array1[2];

  $vardescuento=$array1[3];

  $varimpuesto=$array1[4];

  $vartotal=$array1[5];

  $varsubtotalC=$array1[6];

  $vardescuentoC=$array1[7];

  $varimpuestoC=$array1[8];

  $vartotalC=$array1[9];

  $varmensajeria=$array1[10];

  $totalrecibos=$array1[11];

  $e_descuentos=$array1[12];

  $e_repuestos=$array1[13];

  $e_papeleria=$array1[14];

  $e_combustible=$array1[15];

  $e_atencion=$array1[16];

  $e_encomiendas=$array1[17];

  $e_luz=$array1[18];

  $e_agua=$array1[19];

  $e_telefono=$array1[20];

  $e_otros=$array1[21];

  $e_alfaro=$array1[22];

  $totalingresos=$array1[23];

  $totalegresos=$array1[24];

  $totalcaja=$array1[25];

  $totaldepositos=$array1[26];

  $totaldif=$array1[27];

  $meta=$array1[28];

  $metatotal=$array1[29];


    // Desactivado: destinatarios solo desde Configuración del panel (Carlos 2026-11-14)
    // $to = "hhernandezv@lospaticas.com";
    // // Destinatarios configurables en panel-hacienda (Tareas programadas > Cierre de caja > Configuracion).
    // $__cfg = @json_decode((string)@file_get_contents('/var/lib/lospaticas/panel/caja-destinatarios.json'), true);
    // if (is_array($__cfg) && !empty($__cfg['destinatarios']) && is_array($__cfg['destinatarios'])) {
    //     $__ok = array_values(array_filter($__cfg['destinatarios'], function ($e) { return is_string($e) && !preg_match('/[\r\n,;]/', $e) && filter_var($e, FILTER_VALIDATE_EMAIL); }));
    //     if ($__ok) $to = implode(', ', $__ok);
    // }

    //$fecha1=date("Y-m-d");

    $subject = 'Cierre de hoy '. $fecha1.'';

    $from = 'info@lospaticas.com';

     

    // To send HTML mail, the Content-type header must be set

    $headers  = 'MIME-Version: 1.0' . "\r\n";

    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

     

    // Create email headers

    $headers .= 'From: '.$from."\r\n".

        'Reply-To: '.$from."\r\n" .

        'X-Mailer: PHP/' . phpversion();

     

    // Compose a simple HTML email message

    $message = '<html><body>';

    $message .= '<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>';

    $message .= '<div align="center">'; 

    $message .= '<center>';

    $message .= '<h1>Cierre de caja de :'.$fecha1.'</h1>';

    $message .= "<table border = '1'> ";

    $message .= "<tr> ";

    $message .= "<td><b>Subtotal</b></td> ";

    $message .= "<td><b>Descuento</b></td>";

    $message .= "<td><b>Impuesto</b></td> ";

    $message .= "<td><b>Total</b></td> ";

    $message .= "<td><b>Credito</b></td> ";

    $message .= "<td><b>Subtotal</b></td> ";

    $message .= "<td><b>Descuento</b></td> ";

    $message .= "<td><b>Impuesto</b></td> ";

    $message .= "<td><b>Total</b></td> ";

    $message .= "<td><b>Mensajeria</b></td> ";

    $message .= "</tr> ";

    $message .= "<td>".number_format($varsubtotal,2)."</td>";

    $message .= "<td>".number_format($vardescuento,2)."</td>";

    $message .= "<td>".number_format($varimpuesto,2)."</td> ";

    $message .= "<td>".number_format($vartotal,2)."</td> ";

    $message .= "<td><b>Credito -></b></td> ";

    $message .= "<td>".number_format($varsubtotalC,2)."</td> ";

    $message .= "<td>".number_format($vardescuentoC,2)."</td> ";

    $message .= "<td>".number_format($varimpuestoC,2)."</td> ";

    $message .= "<td>".number_format($vartotalC,2)."</td> ";

    $message .= "<td>".number_format($varmensajeria,2)."</td> ";

    $message .= "</table>";

    $message .= "</br>";

    $message .= "</br>";

    $message .= '<div id="footer">';

    $message .= '<h1>Totales de cierre de caja de :'.$fecha1.'</h1>';

    $message .= '<a name="Totales" id="a"></a>';

    $message .= '<center>';

    $message .= "<table border = '1'> ";

    $message .= "<tr> ";

    $message .= "<td><b>Contado</b></td> ";

    $message .= "<td><b>Credito</b></td>";

    $message .= "<td><b>Recibos</b></td>";

    $message .= "<td><b>Descuentos</b></td>";

    $message .= "<td><b>Repuestos</b></td> ";

    $message .= "<td><b>Papeleria</b></td> ";

    $message .= "<td><b>Combustible</b></td> ";

    $message .= "<td><b>Atencion</b></td> ";

    $message .= "<td><b>Encomiendas</b></td> ";

    $message .= "<td><b>Luz</b></td> ";

    $message .= "<td><b>Agua</b></td> ";

    $message .= "<td><b>Telefono</b></td> ";

    $message .= "<td><b>Otros</b></td> ";

    $message .= "<td><b>Cierres Tarjetas</b></td> "; //******

    $message .= "</tr> ";

    $message .= "<tr> ";

    $message .= "<td>".number_format($vartotal,2)."</td> ";

    $message .= "<td>".number_format($vartotalC,2)."</td> ";

    $message .= "<td>".number_format($totalrecibos,2)."</td> ";

    $message .= "<td>".number_format($e_descuentos,2)."</td> ";

    $message .= "<td>".number_format($e_repuestos,2)."</td> ";

    $message .= "<td>".number_format($e_papeleria,2)."</td> ";

    $message .= "<td>".number_format($e_combustible,2)."</td> ";

    $message .= "<td>".number_format($e_atencion,2)."</td> ";

    $message .= "<td>".number_format($e_encomiendas,2)."</td> ";

    $message .= "<td>".number_format($e_luz,2)."</td> ";

    $message .= "<td>".number_format($e_agua,2)."</td> ";

    $message .= "<td>".number_format($e_telefono,2)."</td> ";

    $message .= "<td>".number_format($e_otros,2)."</td> ";

    $message .= "<td>".number_format($e_alfaro,2)."</td> ";

    $message .= "</table>";

    $message .= "<br>";

    $message .= "<table border = '1'> ";

    $message .= "<tr> ";

    $message .= "<td><b>Ingresos</b></td> ";

    $message .= "<td><b>Egresos</b></td>";

    $message .= "<td><b>Total Caja</b></td>";

    $message .= "<td><b>Depositos</b></td>";

    $message .= "<td><b>Diferencia</b></td> ";

    $message .= "</tr> ";

    $message .= "<tr> ";

    $message .= '<td>'.number_format($totalingresos,2).'</td>';

    $message .= '<td>'.number_format($totalegresos,2).'</td> ';

    $message .= '<td>'.number_format($totalcaja,2).'</td> ';

    $message .= '<td>'.number_format($totaldepositos,2).'</td>';

    if ($totaldif<0) {

        $message .= '<td style="color:red"><b>'.number_format($totaldif,2).'</b></td>';

    }else{

        $message .= '<td style="color:green"><b>'.number_format($totaldif,2).'</b></td>';  

    }

  

    $message .= '</tr>';

    $message .= '</div>';

    $message .= '</table>';

    /// Cierre

    $message .= '</div></center>';

    $message .= '<h3 style="color:blue">Meta hasta el momento: '.number_format($meta,2).'</h3>';
    $message .= '<h3 style="color:blue">Meta de este mes: '.number_format($metatotal,2).'</h3>';

    $message .= '</body></html>';     

    // Sending email

    if(mail($to, $subject, $message, $headers)){

        echo 'Your mail has been sent successfully. :)';

    } else{

        echo 'Unable to send email. Please try again.';

    }



?>