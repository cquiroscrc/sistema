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



$sql = "SELECT * from tmpTras order by codigo";

 if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

    echo '<h1>Ventas Perdidas</h1>';
    echo '<a href="ventas_perdidas_ingreso.php"><img width="48"  height="48" src= "img/envios.png">Agregar Codigo</a>';      
    echo '<a href="ventas_perdidas_excel.php"><img width="48"  height="48" src= "img/proforma.png">Listado</a>';      
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Proveedor</th>
                <th>Editar</th>
                <th>Eliminar</th>';
        echo '</tr>';
    
         while($arr = odbc_fetch_array($resultado)){
                  
            $id_codigo = $arr['codigo'];
            $descripcion = $arr['descripcion'];
            $ubicacion = $arr['ubicacion'];
            echo '<tr>';
            echo '<td>'.$id_codigo.'</td><td>'.$descripcion.'</td><td>'.$ubicacion.'</td>';       
            echo '<td><a href="ventas_perdidas_actualizar.php?id='.$id_codigo.'"><img width="48"  height="48" src= "img/editar.png"></a></td>';
            echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Eliminar el codigo?\")) 
                    location.href=\"ventas_perdidas_eliminar.php?codigo=".$id_codigo."\";
                    '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
        }
    
    echo '</table></center>';


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ventas Perdidas';
</script>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>