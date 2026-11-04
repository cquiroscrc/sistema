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


$sql_desc="SELECT valor FROM tbl_Utiles WHERE IDUtilidad=2";
if(($result2 = odbc_exec($id,$sql_desc)) === false)
        die("Error en Select: ". odbc_errormsg($id));
while($row2 = odbc_fetch_array($result2)){
    $tipo_desc = $row2['valor'];
} 

$sql = "SELECT * from acc_usuarios";

 if(($result = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    
    


    echo '<h1>Listado de Usuarios</h1>';
    echo '<a href="usuarios_ingreso.php"><img width="48"  height="48" src= "img/add_usuario.png">Agregar Usuario</a>';      
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Id</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Acceso</th>
                <th>Dir IP</th>
                <th>Ult Acceso</th>
                <th>Edita</th>
                <th>Eliminar</th>';
        echo '</tr>';

    while($row = odbc_fetch_array($result)){    
    
    
            $id_usuario = $row['id'];
            $nombre = $row['nombre'];
            $usuario = $row['usuario'];
            $acceso = $row['acceso'];
            $ip = $row['ip'];
            $fecha = $row['fecha'];
            echo '<tr>';
            echo '<td>'.$id_usuario.'</td><td>'.$nombre.'</td><td>'.$usuario.'</td><td>'.$acceso.'</td><td>'.$ip.'</td><td>'.$fecha.'</td>';       
            echo '<td><a href="usuarios_actualizar.php?id='.$id_usuario.'"><img width="48"  height="48" src= "img/editar.png"></a></td>';
            //echo '<td><a href="usuarioS_eliminar.php?id='.$id_usuario.'"><img width="48"  height="48" src= "img/eliminar.png"></a></td></tr>';        
            echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este Usuario?\")) 
                        location.href=\"usuarios_eliminar.php?id=".$id_usuario."\";
                        '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
        
    }
    echo '</table></center>';
        

        echo '<form class="form-signin" action="cambiar_descuento.php" method="POST">
            <p>
          <center><h3>Autorizar Descuento</h3></center>
          <select class="form-control" id="descuento" name="descuento">
              <optgroup >
                <option value="2">NO</option>;
                <option value="1">SI</option>;

              }';
              echo '</optgroup>
          </select>
          <br>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar</button>
        </form>';

?>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>