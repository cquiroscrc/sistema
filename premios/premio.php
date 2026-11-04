<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php



date_default_timezone_set("America/Costa_Rica"); 



if(isset($_REQUEST['correo']) && !empty($_REQUEST['correo'])){

    $correo = $_REQUEST['correo'];

}

if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){

    $nombre = $_REQUEST['nombre'];

}

if(isset($_REQUEST['premio']) && !empty($_REQUEST['premio'])){

    $premio = $_REQUEST['premio'];

}

if(isset($_REQUEST['id_premio']) && !empty($_REQUEST['id_premio'])){

    $id_premio = $_REQUEST['id_premio'];

}




    $to = 'info@lospaticas.com,'.$correo;

    $subject = 'Gracias por Participar Pata';

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

    $message .= '<h1 style="color:blue;">'.$nombre.' gracias por ser parte de nuestra familia.</h1>';

    $message .= '<a href="http://lospaticas.com"><img src="http://lospaticas.com/premio.jpg" height="400" width="680" alt="Los Paticas"></a>';
    $message .= '<H2 style="color:red;">Ganaste un '.$premio.' </h2>';
    $message .= '<p>'.$id_premio.' </p>';
    $message .= '</div></center>';

    $message .= '</body></html>';

     

    // Sending email

    if(mail($to, $subject, $message, $headers)){

        echo 'Correo enviado correctamente';

        echo "<strong>La dirección IP de tu proxy es:</strong> {$_SERVER['REMOTE_ADDR']}<br />"; 
        echo "<strong>Tu dirección IP real es:</strong> {$_SERVER['HTTP_X_FORWARDED_FOR']}<br />"; 
        echo "<strong>Tu dirección IP es:</strong> {$_SERVER['REMOTE_ADDR']}<br />"; 
         
        echo "<strong>El nombre del servidor es:</strong> {$_SERVER['SERVER_NAME']}<br />"; 
        echo "<strong>Vienes procedente de la página:</strong> {$_SERVER['HTTP_REFERER']}<br />"; 
        echo "<strong>Te has conectado usando el puerto:</strong> {$_SERVER['REMOTE_PORT']}<br />"; 
        echo "<strong>El agente de usuario de tu navegador es:</strong> {$_SERVER['HTTP_USER_AGENT']}";

    } else{

        echo 'No se pudo enviar email. Intente de nuevo.';
        echo "<strong>La dirección IP de tu proxy es:</strong> {$_SERVER['REMOTE_ADDR']}<br />"; 
        echo "<strong>Tu dirección IP real es:</strong> {$_SERVER['HTTP_X_FORWARDED_FOR']}<br />"; 
        echo "<strong>Tu dirección IP es:</strong> {$_SERVER['REMOTE_ADDR']}<br />"; 
        $ip_premio= $_SERVER['REMOTE_ADDR'];
        echo "<strong>El nombre del servidor es:</strong> {$_SERVER['SERVER_NAME']}<br />"; 
        echo "<strong>Vienes procedente de la página:</strong> {$_SERVER['HTTP_REFERER']}<br />"; 
        echo "<strong>Te has conectado usando el puerto:</strong> {$_SERVER['REMOTE_PORT']}<br />"; 
        echo "<strong>El agente de usuario de tu navegador es:</strong> {$_SERVER['HTTP_USER_AGENT']}";

    }



///Fin
?>

<SCRIPT LANGUAGE="javascript"> // Redirecciona al home  
    if ($ip_premio="170.81.32.82") {
        setTimeout("location.href = 'http://172.16.12.3:88/premios/fin.php';",100);    
    }else{
        if ($ip_premio="201.195.22.182") {
            setTimeout("location.href = 'http://172.16.12.3:88/premios/fin.php';",100);    
        }else{
            setTimeout("location.href = 'http://lospaticas.servehttp.com:88/premios/fin.php';",100);
        }
    }
</SCRIPT> 