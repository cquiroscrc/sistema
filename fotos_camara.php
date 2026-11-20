<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    include "menu.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Camara web</title>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/anular.css">
    <style type="text/css">
        .contenedor{ width: 350px; float: center;}
        .titulo{ font-size: 12pt; font-weight: bold;}
        #camara, #foto{
            width: 320px;
            min-height: 240px;
            border: 1px solid #008000;
        }
    </style>
    <script type="text/javascript">
        window.URL = window.URL || window.webkitURL;
		navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || function(){alert('Su navegador no soporta navigator.getUserMedia().');};
 
		jQuery(document).ready(function(){
		    //Este objeto guardará algunos datos sobre la cámara
		    window.datosVideo = {
		        'StreamVideo': null,
		        'url' : null
		    };
		     
		    jQuery('#botonIniciar').on('click', function(e){
		        //Pedimos al navegador que nos de acceso a 
		        //algún dispositivo de video (la webcam)
		        navigator.getUserMedia({'audio':false, 'video':true}, function(streamVideo){
		            datosVideo.StreamVideo = streamVideo;
		            datosVideo.url = window.URL.createObjectURL(streamVideo);
		            jQuery('#camara').attr('src', datosVideo.url);
		        }, function(){
		            alert('No fue posible obtener acceso a la cámara.');
		        });
		 
		    });
		 
		    jQuery('#botonDetener').on('click', function(e){
		        if(datosVideo.StreamVideo){
		            datosVideo.StreamVideo.stop();
		            window.URL.revokeObjectURL(datosVideo.url);
		        };
		    });
		    jQuery('#botonFoto').on('click', function(e) {
			    var oCamara, oFoto, oContexto, w, h;

			    oCamara = jQuery('#camara');
			    oFoto = jQuery('#foto');
			    w = oCamara.width();
			    h = oCamara.height();
			    oFoto.attr({
			        'width': w,
			        'height': h
			    });
			    oContexto = oFoto[0].getContext('2d');
			    oContexto.drawImage(oCamara[0], 0, 0, w, h);

			});
		});
    </script>
</head>
<body>
	<br>
	<br>
	<br>
	<center>
    <div id='botonera'>
        <input id='botonIniciar' type='button' value = 'Iniciar'></input>
        <input id='botonDetener' type='button' value = 'Detener'></input>
        <input id='botonFoto' type='button' value = 'Foto'></input>
    </div>
    <div class="contenedor">
        <div class="titulo">Cámara</div>
        <video id="camara" autoplay controls></video>
    </div>
    <div class="contenedor">
        <div class="titulo">Foto</div>
        <canvas id="foto" name='foto de codigo'></canvas>
    </div>  
    </center>  
</body>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Camara';
</script>	
<script src="js/bootstrap.min.js"></script>	
</html>