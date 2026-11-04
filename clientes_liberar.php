<?php
session_start();
    require("db.php");

$existe="NO";
$sql="SELECT * FROM clientes WHERE  codigo = 1998 ";  
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        $cliente=$arr['codigo'];
        $pasa=$arr['comentario'];
        $existe = 'SI';
      }
if ($existe=="SI") {
      // Actualiza historial para cambiar numero 9999  
      $sql_u_h = "UPDATE cxc  SET IDCLIENTE='9999' 
                          WHERE IDCliente='$cliente'";
                   
        if(($res = odbc_exec($id,$sql_u_h)) === false)
          die("Error en Select: ". odbc_errormsg($id));
      // Actualiza numero de cliente
       $sql_u_id = "UPDATE clientes  SET codigo='$pasa' 
                          WHERE codigo='$cliente'";
                   
        if(($res = odbc_exec($id,$sql_u_id)) === false)
          die("Error en Select: ". odbc_errormsg($id));
     

     // Actualiza historial para cambiar numero  
     $sql_u_h2 = "UPDATE cxc  SET IDCLIENTE='$pasa' 
                          WHERE IDCliente='9999'";
                   
        if(($res = odbc_exec($id,$sql_u_h2)) === false)
          die("Error en Select: ". odbc_errormsg($id));

   


    //Actualiza comentario de cliente
    $sql_u_c = "UPDATE clientes  SET comentario='NUEVO' 
                          WHERE codigo='$pasa'";
                   
        if(($res = odbc_exec($id,$sql_u_c)) === false)
          die("Error en Select: ". odbc_errormsg($id));
    
}


       // echo 'Registro Guardado';
       header("location: clientes_descuento.php");
    


?>