<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");
   
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
          $fecha = $_REQUEST['fecha']; 
    }
   
 //(Tipo,Documento,Fecha,Monto,Descripcion,Compa�ia)
    
  $sql = "UPDATE fechas SET Fecha = '".$fecha."' WHERE movimiento ='Facturacion'";
    //echo $sql; exit;
    //$sql = "SELECT * FROM tbl_egresos WHERE Fecha ='$Date2'";
    
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
echo "exito";    
?>
