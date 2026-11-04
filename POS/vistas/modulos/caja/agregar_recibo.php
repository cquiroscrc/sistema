<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    if(isset($_REQUEST['recibos']) && !empty($_REQUEST['recibos'])){
          $recibos = $_REQUEST['recibos'];      
    }
    if ($_REQUEST['recibos']==0) {
      $recibos = $_REQUEST['recibos'];
    } 
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto'];      
          $monto=$monto;
    } 
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2']; 
    }
   
    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $documento = $_REQUEST['documento']; 
    }
 //(Tipo,Documento,Fecha,Monto,Descripcion,Compa�ia)
    
  $sql = "INSERT INTO cxc
            (IDCliente,NFac,IDCompañia,Fecha,DiasCredito,FechaLimite,Subtotal,Impuesto,Descuento,Total,Saldo,Estado)
            values
            ('".$recibos."','R-".$documento."','0','".$Date2."','0','".$Date2."','".$monto."','0','0','".$monto."','0','Cancelada')";
   
   $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

   echo "exito";
   

