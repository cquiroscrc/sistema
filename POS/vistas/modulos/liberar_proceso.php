<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../modelos/conexion.php");
  require("../../controladores/utilitaria.controlador.php");
  require("../../modelos/utilitaria.modelo.php");

      //RESERVAR EL ESTADO
      $sql_estado = "UPDATE consecutivos set estado = 'L' WHERE TIPO ='01'";
      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_estado);

    ?>  
    <SCRIPT LANGUAGE="javascript"> // Redirecciona al home
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),1500);
    </SCRIPT> 


<script src="js/bootstrap.min.js"></script>
</html>
