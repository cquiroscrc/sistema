<?php
	require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");;

    if(isset($_REQUEST['pedidos']) && !empty($_REQUEST['pedidos'])){
      $pedidos = $_REQUEST['pedidos'];        
    }

      $sql = "DELETE FROM hacer_pedido WHERE pedido ='$pedidos'";
  	//echo $sql; exit;
  	$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  	echo "exito";
