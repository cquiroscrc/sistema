<?php
session_start();
include "menu.php";
?>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro Empleados</title>
        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="icon" href="favicon.ico">
    </head>
    <body>
      

              <?php
                
               include("db.php");

                if((isset($_GET['id']))&&(!empty($_GET['id']))){
                    $id_empleado =$_GET['id'];
                    
                    $sql = "SELECT * FROM rh_empleados WHERE id_empleado='".$id_empleado."'";

                    if(($result = odbc_exec($id,$sql)) === false)
                        die("Error en Select: ". odbc_errormsg($id));  
                
                
                   //echo $sql;
                  //header("location: resultado_producto.php?msg=Eliminado");
                  
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

                        echo '<form action="planilla_modificar_profile.php" method="post" enctype="multipart/form-data">';
                        echo '<h1>Modificacion de Empleados</h1>';
                        echo '<fieldset>';
                        echo '<legend><span class="number">:)</span>Informacion del Empleado</legend>';

                        echo '<label for="name">Nombre:</label>';
                        echo '<input type="text" id="nombre" name="nombre" value="'.$nombre.'" required>';
                        
                        echo '<label for="name">Apellido:</label>';
                        echo '<input type="text" id="apellido" name="apellido" value="'.$apellido.'" required>';
                        
                        echo '<label for="name">Cedula:</label>';
                        echo '<input type="text" id="cedula" name="cedula" value="'.$cedula.'" required>';

                        echo '<label for="name">Dirección:</label>';
                        echo '<input type="text" id="direccion" name="direccion" value="'.$direccion.'" required>';

                        echo '<label for="name">Teléfono:</label>';
                        echo '<input type="text" id="telefono" name="telefono" value="'.$telefono.'" required>';

                        echo '<label for="name">Cuenta Bancaria:</label>';
                        echo '<input type="text" id="cuenta" name="cuenta" value="'.$cuenta.'" required>';

                        echo '<label for="name">Salario Base:</label>';
                        echo '<input type="text" id="base" name="base" value="'.$base.'" required>';

                        echo '<label for="name">Reajuste:</label>';
                        echo '<input type="text" id="reajuste" name="reajuste" value="'.$reajuste.'" required>';

                        echo '<label for="name">Monto por Mandado:</label>';
                        echo '<input type="text" id="mandado" name="mandado" value="'.$mandado.'" required>';

                        echo '<label for="name">Comision:</label>';
                        echo '<input type="text" id="comision" name="comision" value="'.$comision.'" required>';

                        
                        echo '<label for="name">Fecha de ingreso: (dd/mm/aaaa)</label>';
                        echo '<input type="date" id="ingreso" name="ingreso" value="'.$ingreso.'" required>';
                        
                        echo '<label for="name">Comentarios:</label>';
                        echo '<input type="text" id="comentario" name="comentario" value="'.$comentario.'" required>';

                        echo '<input type="hidden" id="idempl" name="idempl" value="'.$id_empleado.'"';

                        echo '<label for="name">Estado:  (A=Activo I=Inactivo)</label>';
                        
                        echo'<select id="estado" name="estado">';
                        
                        echo'<option value="a">Activo</option>';
                        echo'<option value="i">Inactivo</option>';
                        
                        echo'</select>';



                        echo '<label for="name">Foto:</label>';     
                        echo '<input name="fileToUpload" type="file" id="fileToUpload" / ><br />';                    
                        
                        echo '</fieldset>';
                        echo '<button type="submit">Actualizar</button>';
                        echo '</form>';

                    }
                  
                  
                                 

                }else{
                  echo '<form action="planilla_insertar_profile.php" method="post" enctype="multipart/form-data">';
                  echo '<h1>Registro de Empleados</h1>';
                  echo '<fieldset>';
                  echo '<legend><span class="number">;)</span>Informacion del Empleado</legend>';

                  echo '<label for="name">Nombre:</label>';
                    echo '<input type="text" id="nombre" name="nombre" required>';
                    
                    echo '<label for="name">Apellido:</label>';
                    echo '<input type="text" id="apellido" name="apellido" required>';
                    
                    echo '<label for="name">Cedula:</label>';
                    echo '<input type="text" id="cedula" name="cedula" required>';

                    echo '<label for="name">Dirección:</label>';
                    echo '<input type="text" id="direccion" name="direccion" required>';

                    echo '<label for="name">Teléfono:</label>';
                    echo '<input type="text" id="telefono" name="telefono" required>';

                    echo '<label for="name">Cuenta Bancaria:</label>';
                    echo '<input type="text" id="cuenta" name="cuenta" required>';

                    echo '<label for="name">Salario Base:</label>';
                    echo '<input type="text" id="base" name="base" required>';

                    echo '<label for="name">Reajuste:</label>';
                    echo '<input type="text" id="reajuste" name="reajuste" required>';

                    echo '<label for="name">Monto por Mandado:</label>';
                    echo '<input type="text" id="mandado" name="mandado" required>';

                    echo '<label for="name">Comision:</label>';
                    echo '<input type="text" id="comision" name="comision" required>';

                    
                    echo '<label for="name">Fecha de ingreso: (dd/mm/aaaa)</label>';
                    echo '<input type="date" id="ingreso" name="ingreso" required>';
                    
                    echo '<label for="name">Comentarios:</label>';
                    echo '<input type="text" id="comentario" name="comentario" required>';

                    echo '<label for="name">Estado:  (A=Activo I=Inactivo)</label>';
                    echo'<select id="estado" name="estado">';
                    
                    echo'<option value="a">Activo</option>';
                    echo'<option value="i">Inactivo</option>';
                    
                    echo'</select>';

                    echo '<label for="name">Foto:</label>';     
                    echo '<input name="fileToUpload" type="file" id="fileToUpload" / ><br />';                    
                    
                    echo '</fieldset>';
                    echo '<button type="submit">Actualizar</button>';
                    echo '</form>';

                }
                echo '<form action="planilla.php" method="post" enctype="multipart/form-data">';
                echo '<button type="submit">Volver</button>';
                echo '</form>';



              ?>
    <script src="js/bootstrap.min.js"></script> 
    </body>
</html>