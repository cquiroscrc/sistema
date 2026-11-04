<?php
  session_start(); 
  include("db.php");
  header("Content-type: text/html; charset=utf-8");
   
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2']; 
    }
   
 //(Tipo,Documento,Fecha,Monto,Descripcion,Compa�ia)
    
  $sql = "UPDATE fechas SET Fecha = '".$Date2."' WHERE movimiento ='Facturacion'";
    //echo $sql; exit;
    //$sql = "SELECT * FROM tbl_egresos WHERE Fecha ='$Date2'";
    
               if(($res = odbc_exec($id,mb_convert_encoding($sql,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));

    
       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      header("location: index.php");
    
?>
