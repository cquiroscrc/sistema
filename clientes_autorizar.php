<?php
session_start();
    require("db.php");

$pasa='1998';

if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
      $datos = $_REQUEST['datos'];
    }


     // Actualiza historial para cambiar numero al 9999      
     $sql_u_h = "UPDATE cxc  SET IDCLIENTE='9999' 
                          WHERE IDCliente='$datos'";
                   
        if(($res = odbc_exec($id,$sql_u_h)) === false)
          die("Error en Select: ". odbc_errormsg($id));

    // Actualiza numero de cliente
    $sql_u_id = "UPDATE clientes  SET codigo='$pasa' 
                          WHERE codigo='$datos'";
                   
        if(($res = odbc_exec($id,$sql_u_id)) === false)
          die("Error en Select: ". odbc_errormsg($id));
    
    // Actualiza historial para cambiar numero al pasa      
     $sql_u_h2 = "UPDATE cxc  SET IDCLIENTE='$pasa' 
                          WHERE IDCliente='9999'";
                   
        if(($res = odbc_exec($id,$sql_u_h2)) === false)
          die("Error en Select: ". odbc_errormsg($id));


    //Actualiza comentario de cliente
    $sql_u_c = "UPDATE clientes  SET comentario='$datos' 
                          WHERE codigo='$pasa'";
                   
        if(($res = odbc_exec($id,$sql_u_c)) === false)
          die("Error en Select: ". odbc_errormsg($id));
    



       // echo 'Registro Guardado';
       header("location: clientes_descuento.php");
    


?>