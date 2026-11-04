<?php
session_start();
include "menu.php";
?>
<html>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Planilla Empleados</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> <!--  Imagenes fancy -->
        <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
        <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script> <!--  Imagenes fancy -->

        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        
        <link rel="icon" href="img/favicon.ico">
    </head>
    <body>
<?php
    include("db.php");

if((isset($_GET['msg']))&&(!empty($_GET['msg']))){
    $message = '<center><h3>Registro '.$_GET['msg'].'</h3></center>';
}else{
    $message = '';
}

$sql = "SELECT * from rh_empleados";


if(($result = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));


 
    

    echo '<h1>Listado de Empleados</h1>';

   
    
    

    if ($admin==true) {
      echo '<a href="planilla_ingresar.php"><img width="48"  height="48" src= "img/profile.png">Planilla</a>';      
      echo '<a href="planilla_vacaciones_reporte.php"><img width="48"  height="48" src= "img/etiqueta.png">Vacaciones</a>';      
      echo '<a href="planilla_aguinaldos.php"><img width="48"  height="48" src= "img/fecha.png">Aguinaldos</a>';      
    }
    

    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo $message;
    
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Id</th>
                <th>Nombre / Mandado</th>
                <th>Apellido / Comision</th>
                <th>Cedula / Ingreso</th>
                <th>Direccion / Comentarios</th>
                <th>Telefono / Estado</th>
                <th>Cuenta / Foto</th>
                ';
    if ($admin==true) {
        echo '<th>Salario / Editar</th>
              <th>Reajuste / Eliminar</th>
              <th>Sal x Hora</td>
                ';
    } else{
        echo '<th>Salario</th>
              <th>Reajuste</th>
              <th>Sal x Hora</td>';
    }      
        echo '</tr>';

    
    
        while($row = odbc_fetch_array($result)){
            $id_empleado = $row['id_empleado'];
            $nombre = $row['nombre'];
            $apellido = $row['apellido'];
            $cedula = $row['cedula'];
            $direccion = $row['direccion'];
            $telefono = $row['telefono'];
            $cuenta = $row['cuenta'];
            $base = $row['base'];
            $reajuste = $row['reajuste'];
            $salxhora = $row['salxhora'];
            $mandado = $row['mandado'];
            $comision = $row['comision'];
            $ingreso = $row['ingreso'];
            $comentario = $row['comentario'];
            $estado = $row['estado'];
            $foto = $row['foto'];
           

    
            echo '<tr class="registroA">';
            echo '<td>'.$id_empleado.'</td><td>'.$nombre.'</td><td>'.$apellido.'</td><td>'.$cedula.'</td><td>'.$direccion.'</td><td>'.$telefono.'</td><td>'.$cuenta.'</td><td>'.$base.'</td><td>'.$reajuste.'</td><td>'.$salxhora.'</td></tr>';
            echo '<td></td><td>'.$mandado.'</td><td>'.$comision.'</td><td>'.$ingreso.'</td><td>'.$comentario.'</td><td>'.$estado.'</td>';
            //echo '<td><img width="75"  height="75" src= "'.$imagen.'""></td>';
            echo '<td><a class="fancybox" href="'.$foto.'" title="'.$nombre.' '.$apellido.' -- '.$cedula.'"><img width="75"  height="75" src= "'.$foto.'""></a></td>';
            if ($admin==true) {
                echo '<td><a href="planilla_ingresar_profile.php?id='.$id_empleado.'"><img width="48"  height="48" src= "img/editar.png"></a></td>';
                echo '<td><a href="planilla_eliminar_profile.php?id='.$id_empleado.'&ruta='.$foto.'"><img width="48"  height="48" src= "img/eliminar.png"></a></td></tr>';
            }else{
                echo '</tr>';        
            }       
            


        }
    
    if ($admin==true) {
        echo '<td><a class="agregarProducto" href="planilla_ingresar_profile.php"><img width="48"  height="48" src= "img/agregar.png"></a></td></tr>';
    }
    echo '</table></center>';
    ////*****Script para ver imagenes////
    echo '<script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>';
    


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Planilla';
</script>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>