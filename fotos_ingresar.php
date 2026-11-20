<?php
session_start();
include "menu.php";
?>
  <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta charset="utf-8">  
        <title>Cierre diario</title>
        <script src="js/jquery.min.js"></script>
        <script src="js/jquery.inputmask.bundle.min.js"></script>
        <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/signin.css">
        <link rel="StyleSheet" href="css/calendar.css">

        <link rel="icon" href="favicon.ico">
        

    </head>


    <body>
        

        <?php
          require_once("db.php");
        ?>
        <div class ="container">
        <div align="center">   
        <form class="form-signin" name="form1" id="form1" method="post" action="fotos_guardar.php" enctype="multipart/form-data">
            <table align="center" border="0" cellspacing="0">
            <center><h1><span class="glyphicon glyphicon-camera" aria-hidden="true"></span>&nbsp;Cargar Multiple Archivos</h1>
      </center>  
           
              
              <div class="col-sm-16">
                <input type="file"  id="archivo[]" name="archivo[]" multiple="">
                <br>
                <select class="form-control" id="tipo" name="tipo" required>
                <optgroup >
                  <?php             
                    include("db.php");
                    $sql = "SELECT * FROM Marcas";

                    if(($res = odbc_exec($id,$sql)) === false)
                      die("Error en Select: ". odbc_errormsg($id));

                    while($arr = odbc_fetch_array($res)){
                        $descripcion=$arr['descripcion'];
                        $marca=$arr['numero'];
                        echo '<option value="'.$marca.'">'.$descripcion.'</option>'; 
                    }  
                  echo '</optgroup>'
                ?>
                </select>
              </div>
              <br>
              
            
            <button type="submit" class="btn btn-primary">Cargar</button>
            </table> 
        </form>

          
      </div>
    <script type="text/javascript" language="JavaScript">
      document.title = 'Sistema / Agregar fotos';
    </script> 
    <script src="js/bootstrap.min.js"></script> 
    </body>
</html>