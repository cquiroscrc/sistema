<?php
session_start();
include "menu.php";
?>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro Planilla</title>
        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        <link rel="icon" href="img/favicon.ico">
    </head>
    <body>
      

              <?php
              if((isset($_REQUEST['fecha']))and(!empty($_REQUEST['fecha']))){
                    $fecha = $_REQUEST['fecha'];
              }
              if((isset($_REQUEST['planilla']))and(!empty($_REQUEST['planilla']))){
                    $planilla = $_REQUEST['planilla'];
              } 
                include("db.php");
                    echo 'Fecha '.$fecha;
                    echo ' Planilla '.$planilla;
                    echo '<form action="planilla_hacer_planilla.php" method="post" enctype="multipart/form-data">';
                    echo '<h1>Seleccione Empleado</h1>';
                    echo '<fieldset>';
                    echo '<legend><span class="number">;0</span>Seleccion de empleado</legend>';
                    echo '<input type="hidden" id="fecha" name="fecha" value="'.$fecha.'">';
                    echo '<input type="hidden" id="planilla" name="planilla" value="'.$planilla.'">';
                    echo '<label for="name">Nombre de empleado:</label>';                                                          
                    echo'<select id="empleado" name="empleado">';

                    $sql = "SELECT * FROM rh_empleados WHERE estado ='a' ORDER BY id_empleado";

                    if(($result = odbc_exec($id,$sql)) === false)
                        die("Error en Select: ". odbc_errormsg($id));
                    
                    
                    while($row = odbc_fetch_array($result)){
                        $id_empleado = $row['id_empleado'];
                        $nombre= $row['nombre'];
                        $apellido=$row['apellido'];
                        echo'<option value="'.$id_empleado.'">'.$nombre .' '.$apellido.'</option>';
                    }
                        
                    
                    
                    
                    echo'</select>';
                    echo '</fieldset>';
                    
                    
                    echo '<button type="submit">Seleccionar</button>';
                    echo '</form>';
                    echo '<form action="planilla_planilla.php" method="post" enctype="multipart/form-data">';
                    echo '<button type="submit">Volver</button>';
                    echo '</form>';

              ?>
    <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Add Planilla';
</script>
    <script src="js/bootstrap.min.js"></script> 
    </body>
</html>