<?php

 require("../../../modelos/conexion.php");
 require("../../../controladores/utilitaria.controlador.php");
 require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    

  $sql = "DELETE FROM woocommerce WHERE codigo ='$codigo'";
    //echo $sql; exit;
   $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

   echo "exito";
   
?>
