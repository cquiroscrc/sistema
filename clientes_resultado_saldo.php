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



$sql = "SELECT * from clientes order by codigo desc";

 if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

    echo '<h1>Listado de Clientes Frecuentes</h1>';
    echo '<a href="clientes_saldo_imprime.php"><img width="48"  height="48" src= "img/cierre.png">Imprimir Listado</a>';      
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Id</th>
                <th>Nombre</th>
                <th>Telefono</th>
                <th>Celular</th>
                <th>Direccion</th>
                <th>Correo</th>
                <th>Afiliación</th>
                <th>Saldo</th>';
        echo '</tr>';
    
         while($arr = odbc_fetch_array($resultado)){
                  
            $id_cliente = $arr['codigo'];
            $datos=$arr['codigo'];
            $saldo=0;
            $sql_saldo="SELECT * FROM cxc WHERE Fecha between '2018-01-01 00:00:00.000' AND '2023-12-31 23:59:59.999' AND IDCliente='$datos'";  

                if(($res_saldo = odbc_exec($id,$sql_saldo)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
                while($arr_saldo = odbc_fetch_array($res_saldo)){
                    $saldo=$saldo+$arr_saldo["Total"];
                }

            $nombre = $arr['nombre'];
            $telefono = $arr['telefono'];
            $direccion = $arr['direccion'];
            $cedula = $arr['cedula'];
            $celular=$arr['celular'];
            $correo=$arr['correo'];
            $afiliacion=strtotime($arr['afiliacion']);
            echo '<tr>';
            echo '<td>'.$id_cliente.'</td><td>'.$nombre.'</td><td>'.$telefono.'</td><td>'.$celular.'</td><td>'.$direccion.'</td>';echo '<td>'.$correo.'</td><td>'.date("d-m-Y", $afiliacion).'</td><td>'.$saldo.'</td>';       
           
        }
    
    echo '</table></center>';


?>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>