<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
          $factura = $_REQUEST['factura'];      
    } 
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $fecha = $_REQUEST['Date2'];      
    } 
    if(isset($_REQUEST['motorizado']) && !empty($_REQUEST['motorizado'])){
          $motorizado = $_REQUEST['motorizado'];      
    } 

    $sql="SELECT * FROM mandados WHERE factura ='$factura' and fecha='$fecha' and motorizado='$motorizado'";  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($res)){
        $lugar=$arr['lugar'];
      }
    $lugar=$lugar ."/ NULA";

    $sql_hfacturas = "UPDATE mandados  SET cantidad=0 , lugar='$lugar' 
                      WHERE factura ='$factura' and fecha='$fecha' and motorizado='$motorizado'";  

   //echo $sql_hfacturas;
   //exit ;               
    if(($res_f = odbc_exec($id,$sql_hfacturas)) === false)
      die("Error en Select: ". odbc_errormsg($id));




header("location: ingreso_mandado.php");  
  
?>