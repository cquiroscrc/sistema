<?php
session_start();
include "menu.php";
?>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        <link rel="StyleSheet" href="css/calendar.css">
        <link rel="icon" href="favicon.ico">
        <style>
          .CalFld{

            padding:1px;
            margin:auto;
          }
        </style>
    </head>
    <body>
      <?php
     
        include("db.php");
            echo '<a href="planilla_vacaciones_reporte.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';
            echo '<form action="planilla_vacaciones_incluir.php" method="post" enctype="multipart/form-data">';
            echo '<h1>Seleccione Empleado</h1>';
            echo '<fieldset>';
            echo '<legend><span class="number">;0</span>Seleccion de empleado para Vacaciones</legend>';
            echo '<label for="name">Nombre de empleado:</label>';                                                          
            echo'<select id="empleado" name="empleado">';

            $sql = "SELECT * FROM rh_empleados ORDER BY id_empleado";


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
            echo '<label for="name">Cantidad de dias:</label>';
            echo '<input type="text" id="dias" name="dias" required>';
            ?>
            <tbody>
              <tr>
              
              
              <td >
                <script>
                  DrawCalFld('Date2', 'today');
                </script>
              </td>

              </tr>
            </tbody>
            <?php 
            echo '<br>';
            echo '<center>';
            echo '<button type="submit">Seleccionar</button>';
            echo '</form>';
            echo '</center>';
            

      ?>
    <script>
        W.F=D.F;

        function Date1OnChange(){
        if (F.Date2) Cal_SetDate(F.Date2, this.value);
        }

        F.Date1.onchange=Date1OnChange;
    </script>
    <script src="js/bootstrap.min.js"></script> 
    </body>
</html>