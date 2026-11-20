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



$sql = "SELECT * from clientes where codigo < '9999'";

 if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

    echo '<h1>Listado de Clientes Frecuentes</h1>';
    echo '<a href="clientes_resultado_imprime.php"><img width="48"  height="48" src= "img/cierre.png">Imprimir Listado</a>';      
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Id</th>
                <th>Nombre</th>
                <th>Cedula</th>
                <th>Telefono</th>
                <th>Celular</th>
                <th>Direccion</th>
                <th>Correo</th>
                <th>Nacimiento</th>
                <th>Afiliación</th>
                <th>Comentario</th>';
        echo '</tr>';
    
         while($arr = odbc_fetch_array($resultado)){
                  
            $id_cliente = $arr['codigo'];
            $nombre = $arr['nombre'];
            $telefono = $arr['telefono'];
            $direccion = $arr['direccion'];
            $cedula = $arr['cedula'];
            $celular=$arr['celular'];
            $correo=$arr['correo'];
            $afiliacion=strtotime($arr['afiliacion']);
            $nacimiento=strtotime($arr['nacimiento']);
            $comentario = $arr['comentario'];
            echo '<tr>';
            echo '<td>'.$id_cliente.'</td><td>'.$nombre.'</td><td>'.$cedula.'</td><td>'.$telefono.'</td><td>'.$celular.'</td><td>'.$direccion.'</td>';       
            echo '<td>'.$correo.'</td><td>'.date("d-m-Y", $nacimiento).'</td><td>'.date("d-m-Y", $afiliacion).'</td><td>'.$comentario.'</td>';       
           
        }
    
    echo '</table></center>';


?>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>