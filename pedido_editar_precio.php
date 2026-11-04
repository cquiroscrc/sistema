<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
          $factura = $_REQUEST['fact'];      
    } 
    if(isset($_REQUEST['factor']) && !empty($_REQUEST['factor'])){
          $factor = $_REQUEST['factor'];      
    } 
     
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
      $id_codigos = $_REQUEST['id'];        
    }
    
    if(isset($_REQUEST['sugerido']) && !empty($_REQUEST['sugerido'])){
      $sugerido = $_REQUEST['sugerido'];        
    }
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
      $proveedor = $_REQUEST['proveedor'];        
    }
    if(isset($_REQUEST['marcador']) && !empty($_REQUEST['marcador'])){
      $marcador = $_REQUEST['marcador'];        
    }

  $sql = "UPDATE Pedidos set Sugerido = '$sugerido' WHERE ID ='$id_codigos'";
    //echo $sql; exit;
   
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
   
    header("location: pedido_precio.php?fact=".$factura."&proveedor=".$proveedor."&factor=".$factor.'#'.$marcador);
?>
