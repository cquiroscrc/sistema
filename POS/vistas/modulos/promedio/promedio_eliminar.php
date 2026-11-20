<?php

 require("../../../modelos/conexion.php");
 require("../../../controladores/utilitaria.controlador.php");
 require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['pedido']) && !empty($_REQUEST['pedido'])){
          $pedido = $_REQUEST['pedido']; 
          
    }
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
          $p_id = $_REQUEST['id']; 
          
    }

  $sql = "DELETE FROM hacer_pedido WHERE codigo ='$codigo' AND pedido='$pedido' AND id='$p_id'";
    //echo $sql; exit;
   $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

   echo "exito";
   
?>
