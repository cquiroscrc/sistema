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
    include("db.php");



$sql = "SELECT v.id_empleado, v.fecha, v.cantidad, e.nombre, e.apellido from vacaciones v, rh_empleados e WHERE v.id_empleado= e.id_empleado order by v.id_empleado, v.fecha";


 
    echo '<h1>Listado de Vacaciones</h1>';
    echo '<a href="planilla.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';      
    echo '<a href="planilla_vacaciones.php"><img width="48"  height="48" src= "img/add_usuario.png">Agregar Vacaciones</a>';      
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Id</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Eliminar</th>';
        echo '</tr>';
        $total=0;
        $empleado='';

        if(($result = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    
    
            while($arr = odbc_fetch_array($result)){

                $id_empleado = $arr['id_empleado'];
                if ($empleado=='') {
                    $empleado=$id_empleado;
                }else{
                    
                    if ($empleado <> $id_empleado) {
                        echo '<tr>';
                        echo '<th>'.$empleado.'</th><th>'.$nombre.'</th><th></th><th></th><th>'.$total.'</th><th></th>';           
                        $total=0;
                        $empleado=$id_empleado;    
                    }
                }
                $nombre = $arr['nombre'].' '.$arr['apellido'];
                $fecha = $arr['fecha'];
                $cantidad = $arr['cantidad'];
                $total=$total+$arr['cantidad'];
                echo '<tr>';
                echo '<td>'.$id_empleado.'</td><td>'.$nombre.'</td><td>'.$fecha.'</td><td>'.$cantidad.'</td><td>'.$total.'</td>';       
                echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar esta Vacacion?\")) 
                            location.href=\"planilla_vacaciones_eliminar.php?id=".$id_empleado."&fecha=".$fecha."\";
                            '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
            }
                echo '<tr>';
                    echo '<th>'.$empleado.'</th><th>'.$nombre.'</th><th></th><th></th><th>'.$total.'</th><th></th>';           
            
        

    echo '</table></center>';


?>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>