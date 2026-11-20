<?php
  session_start(); 
  include("db.php");
  header("Content-type: text/html; charset=utf-8");
    if(isset($_REQUEST['recibos']) && !empty($_REQUEST['recibos'])){
          $recibos = $_REQUEST['recibos'];      
    }
    if ($_REQUEST['recibos']==0) {
      $recibos = $_REQUEST['recibos'];
    } 
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto'];      
          $monto=$monto*-1;
    } 
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2']; 
    }
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
          $tipo = $_REQUEST['tipo']; 
    }
    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $documento = $_REQUEST['documento']; 
    }
 //(Tipo,Documento,Fecha,Monto,Descripcion,Compa�ia)
    
  $sql = "INSERT INTO cxc
            (IDCliente,NFac,IDCompañia,Fecha,DiasCredito,FechaLimite,Subtotal,Impuesto,Descuento,Total,Saldo,Estado)
            values
            ('".$recibos."','R-".$documento."','0','".$Date2."','0','".$Date2."','".$monto."','0','0','".$monto."','0','Cancelada')";
    //echo $sql; exit;
    
    
               if(($res = odbc_exec($id,mb_convert_encoding($sql,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));

      header("location: cierre_dia_resultado.php?Date2=".$Date2."&tipo=dia");
    
?>

