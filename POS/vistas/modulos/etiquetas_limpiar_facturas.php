<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['Factura']) && !empty($_REQUEST['Factura'])){
      $Factura = $_REQUEST['Factura'];
    }   
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];
    }   
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
    }   
        

 $sqlpedido="UPDATE Pedidos SET Etiquetas='0' WHERE Factura='$Factura'";

    if ($tipo=='compra') {
      $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlcompra);  
    }else{
      $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlpedido);  
    }
  



