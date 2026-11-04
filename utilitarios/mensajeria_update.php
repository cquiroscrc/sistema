<?php

    include("db.php");

    $sql = "UPDATE inventario SET EXISTENCIA=1000 WHERE CODIGO='9SM'";
    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
       echo '<br>'; 
       echo '<br>';
       echo '<H1>Actualizado la mensajería</H1>';


    $sql = "UPDATE inventario SET EXISTENCIA=1000 WHERE CODIGO='9ENCOMIENDA'";
    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
       echo '<br>'; 
       echo '<br>';
       echo '<H1>Actualizado Encomiendas</H1>'; 

    $sql = "UPDATE inventario SET EXISTENCIA=50 WHERE CODIGO='9IMPRESIONZL'";
    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
       echo '<br>'; 
       echo '<br>';
       echo '<H1>Actualizado Impresion</H1>';   

    $sql = "UPDATE inventario SET EXISTENCIA=50 WHERE CODIGO='9SERVDIGITALZL'";
    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
       echo '<br>'; 
       echo '<br>';
       echo '<H1>Actualizado Servicio Digital</H1>';  


    $sql = "UPDATE inventario SET EXISTENCIA=0 , COSTO=0, VENTA=0 WHERE CODIGO='9TM'";
    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
       echo '<br>'; 
       echo '<br>';
       echo '<H1>Actualizado el transporte</H1>';   


?>

<SCRIPT LANGUAGE="javascript"> // Redirecciona al home      
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),1500);
 </SCRIPT>