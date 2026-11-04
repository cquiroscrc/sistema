<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php


if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
  $factura = $_REQUEST['factura'];
} 

if(isset($_REQUEST['llegada']) && !empty($_REQUEST['llegada'])){
  $llegada = $_REQUEST['llegada'];
} 

if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
  $nombre = $_REQUEST['nombre'];
} 

date_default_timezone_set("America/Costa_Rica"); 

$conteo=0;

$to = "hhernandezv@lospaticas.com,galfaroa@lospaticas.com,formulario@lospaticas.com";
//$to = "hhernandezv@lospaticas.com";

    //$fecha1=date("Y-m-d");

    $subject = 'Pedidos en transito de los Paticas '.$factura.' de '.$nombre;

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

    $message .= '<center>';

    $message .= '<h1>Pedidos en transito</h1>';

    $message .= '</center>';

    $message .= '<h3>Adjunto reporte de pedido en transito de '.$nombre.'</h3>';

    $message .= '<h3>Posible llegada *** '.$llegada.' ***</h3>';

    $message .='<a target="_blank" href="https://sistema.lospaticas.com/reportes/Rep_mail_'.$factura.'.pdf">**** Abrir el pedido '.$factura.'****</a>';
    
    $message .= "</br>";

    $message .= '</body></html>';  

    

    //$file_name='http://170.81.32.82:88/reportes/Rep_mail.pdf';
        //attachment
        
    // Sending email
    // http://170.81.32.82:88/reportes/Rep_mail.pdf

    if(mail($to, $subject, $message, $headers)){

        echo 'El correo fue enviado correctamente';
        echo '<br>';


        echo '<a href="/index.php"><img width="48"  height="48" src= "send.png">Volver</a> ';

    } else{

        echo 'No se pudo enviar el correo. Intentelo nuevamente';

    }



?>