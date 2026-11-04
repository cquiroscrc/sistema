<?php

	session_start();
	include "menu.php";
?>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ingreso Fotos</title>
        
        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="icon" href="favicon.ico">
        
    </head>
    <body>
        

 <?php
	if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
      
    }
    
	//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
	foreach($_FILES["archivo"]['tmp_name'] as $key => $tmp_name)
	{
		//Validamos que el archivo exista
		if($_FILES["archivo"]["name"][$key]) {
			$filename = $_FILES["archivo"]["name"][$key]; //Obtenemos el nombre original del archivo
			$source = $_FILES["archivo"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivo
			
			$directorio = 'subida_cat/'; //Declaramos un  variable con la ruta donde guardaremos los archivos
			$directorio= $directorio.$tipo."/";
			//Validamos si la ruta de destino existe, en caso de no existir la creamos
			if(!file_exists($directorio)){
				mkdir($directorio, 0777) or die("No se puede crear el directorio de extracci&oacute;n");	
			}
			
			$dir=opendir($directorio); //Abrimos el directorio de destino
			$target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivo
			echo 'Direccion creada= '.$target_path;
			//Movemos y validamos que el archivo se haya cargado correctamente
			//El primer campo es el origen y el segundo el destino
			if(move_uploaded_file($source, $target_path)) {	
				echo "El archivo $filename se ha almacenado en forma exitosa.<br>";
				} else {	
				echo "Ha ocurrido un error, por favor inténtelo de nuevo.<br>";
			}
			closedir($dir); //Cerramos el directorio de destino
			
		}
	}
	echo '<a href="fotos_ingresar.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';
?>
 <script type="text/javascript" language="JavaScript">
      document.title = 'Sistema / Ingreso fotos';
    </script> 
    <script src="js/bootstrap.min.js"></script> 
    </body>
</html>