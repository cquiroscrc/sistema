<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
          $factura = $_REQUEST['fact'];      
    } 
    
     
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $id_codigos = $_REQUEST['id'];        
    }
    
    if(isset($_REQUEST['etiqueta']) && !empty($_REQUEST['etiqueta'])){
      $etiqueta = $_REQUEST['etiqueta'];        
    }
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];        
    }
    if(isset($_REQUEST['marcador']) && !empty($_REQUEST['marcador'])){
      $marcador = $_REQUEST['marcador'];        
    }

  $sql = "UPDATE Pedidos set Etiquetas = '$etiqueta' WHERE ID ='$id_codigos'";
    //echo $sql; exit;
   
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
   
    header("location: pedido_etiquetas.php?fact=".$factura."&proveedor=".$proveedor.'#'.$marcador);
?>
