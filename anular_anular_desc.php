<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $documento = $_REQUEST['documento'];      
    } 
    if(isset($_REQUEST['id_acceso']) && !empty($_REQUEST['id_acceso'])){
          $id_acceso = $_REQUEST['id_acceso'];      
    } 
    
    $sql="SELECT * FROM tbl_Egresos WHERE Documento ='$documento'";  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($res)){
        $descripcion=$arr['Descripcion'];
      }
    $descripcion=$descripcion ."/ NULA";

    $sql_hfacturas = "UPDATE tbl_Egresos  SET Monto=0 , Descripcion='NULA' 
                      WHERE Documento='$documento'";

   // echo $sql_hfacturas;
   //exit ;               
    if(($res_f = odbc_exec($id,$sql_hfacturas)) === false)
      die("Error en Select: ". odbc_errormsg($id));




header("location: anular_descuento.php");  
  
?>