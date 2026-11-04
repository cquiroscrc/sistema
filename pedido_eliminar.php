<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
          $factura = $_REQUEST['fact'];      
    } 
    
     
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $id_codigos = $_REQUEST['id'];        
    }
    
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];        
    }

  $sql = "DELETE FROM Pedidos WHERE ID ='$id_codigos'";
    //echo $sql; exit;
   
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
   
    header("location: pedido_mostrar.php?fact=".$factura."&proveedor=".$proveedor."");
?>
