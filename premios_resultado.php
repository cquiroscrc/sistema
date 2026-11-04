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
        
        <link rel="icon" href="img/favicon.ico">
    </head>
    <body>
<?php
    require("db.php");



$sql = "SELECT * from tbl_premios order by Id";

 if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

    echo '<h1>Listado de Premios</h1>';
    echo '<a href="premios_ingreso.php"><img width="48"  height="48" src= "img/add_usuario.png">Agregar Premios</a>';
    echo "<a class = 'echo_link' href='#' onclick='if(confirm(\"Desea limpiar el reporte de premios?\")) location.href=\"premios_eliminar.php\";'><img width='48'  height='48' src= 'img/eliminar.png'>Limpiar Reporte</a>";       
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Id</th>
                <th>Premio</th>
                <th>Nombre</th>
                <th>Cedula</th>
                <th>Telefono</th>
                <th>Correo</th>
                <th>Editar</th>';
        echo '</tr>';
    
         while($arr = odbc_fetch_array($resultado)){
                  
            $id_premio = $arr['ID'];
            $premio = $arr['Premio'];
            $nombre = $arr['Nombre'];
            $cedula = $arr['Identificacion'];
            $celular=$arr['Telefono'];
            $correo=$arr['Correo'];
            echo '<tr>';
            echo '<td>'.$id_premio.'</td><td>'.$premio.'</td><td>'.$nombre.'</td><td>'.$cedula.'</td><td>'.$celular.'</td>';       
            echo '<td>'.$correo.'</td>';       
            echo '<td><a href="premios_actualizar.php?id='.$id_premio.'"><img width="48"  height="48" src= "img/editar.png"></a></td>';
            
        }
    
    echo '</table></center>';


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Premios';
</script>

<script src="js/bootstrap.min.js"></script> 
    </body>
</html>