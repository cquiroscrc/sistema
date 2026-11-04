<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['ajuste']) && !empty($_REQUEST['ajuste'])){
          $ajuste = $_REQUEST['ajuste']; 
          
    }

  $sql = "DELETE FROM detajutras WHERE codigo ='$codigo' AND IDMovimiento='$ajuste'";
    //echo $sql; exit;
    
    if(($resultado = odbc_exec($id,$sql)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
        
    
       // echo $sql;
      header("location: ajuste.php");
    
?>
