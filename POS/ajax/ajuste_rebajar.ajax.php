<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
    }

    if(isset($_REQUEST['ajuste']) && !empty($_REQUEST['ajuste'])){
      $ajuste = $_REQUEST['ajuste'];
    }
            
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************
      
    $sql="DELETE FROM detajutras WHERE codigo = '$codigo' AND IDMovimiento='$ajuste'";

      //// *************** Ejecuto la consulta ******************
    //var_dump($sql);         
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        
