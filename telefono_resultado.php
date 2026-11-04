<?php
session_start();
include "menu.php";
?>
<html>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Usuarios</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> <!--  Imagenes fancy -->
        <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
        <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script> <!--  Imagenes fancy -->

        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/anular.css">
        
        <link rel="icon" href="favicon.ico">
    </head>
    <body>
<?php
    require("db.php");



$sql = "SELECT * from tbl_TEL_Numeros order by Descripcion";

 if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

    echo '<h1>Listado de Telefonos</h1>';
    echo '<a href="telefono_ingreso.php"><img width="48"  height="48" src= "img/add_usuario.png">Agregar Telefono</a>';      
    echo '<a href="telefono_update_mayusculas.php"><img width="48"  height="48" src= "img/profile.png">Cambiar a Mayusculas</a>';      
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Id</th>
                <th>Nombre</th>
                <th>Telefono</th>
                <th>Correo</th>
                <th>Editar</th>
                <th>Eliminar</th>';
        echo '</tr>';
    
         while($arr = odbc_fetch_array($resultado)){
                  
            $id = $arr['id'];
            $nombre = $arr['Descripcion'];
            $telefono = $arr['Numero'];
            $correo = $arr['Correo'];
            echo '<tr>';
            echo '<td>'.$id.'</td><td>'.$nombre.'</td><td>'.$telefono.'</td><td>'.$correo.'</td>';       
            echo '<td><a href="telefono_actualizar.php?id='.$id.'"><img width="48"  height="48" src= "img/editar.png"></a></td>';
            //echo '<td><a href="usuarioS_eliminar.php?id='.$id_usuario.'"><img width="48"  height="48" src= "img/eliminar.png"></a></td></tr>';        
            echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este Telefono?\")) 
                        location.href=\"telefono_eliminar.php?id=".$id."\";
                        '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
        }
    
    echo '</table></center>';


?>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>