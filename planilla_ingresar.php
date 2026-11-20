<?php
session_start();
include "menu.php";
?>

  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
        <link rel="stylesheet" href="css/main.css">
        <link rel="StyleSheet" href="css/calendar.css">
        <link rel="icon" href="img/favicon.ico">
    </head>
    <body>
     <style>
      .CalFld{
        background:#FFFFD8;
        padding:1px;
        margin:auto;
      }
    </style> 

              <?php
                    echo '<a href="planilla.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';
                    unset( $_SESSION["planilla"] );
                    echo '<div class="container">';
                    echo '<form class="form-signin" action="planilla_planilla.php" method="post" enctype="multipart/form-data">';
                    echo '<h1>Registro de Planilla</h1>';
                    echo '<fieldset>';
                    echo '<legend color="white"><span class="number">:0</span>Creacion de Planilla</legend>';

                    echo '<label for="name">Planilla:</label>';
                    echo '<input type="text" id="planilla" name="planilla" required>';
                                                            
                    //echo '<label for="name">Fecha de Planilla: (dd/mm/aaaa)</label>';
                    //echo '<input type="date" id="fecha" name="fecha" required>';
                    
                    echo '</fieldset>';
                    ?>
                        <tbody>
                          <tr>
                          
                          
                          <td >
                            <script>
                              DrawCalFld('fecha', 'today');
                            </script>
                          </td>

                          </tr>
                        </tbody>
                        <br>
                    <?php 
                    echo '<center>';
                    echo '<button type="submit">Ingresar</button>';
                    echo '</center>';
                    echo '</div>';

              ?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Registro Planilla';
</script>              
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>