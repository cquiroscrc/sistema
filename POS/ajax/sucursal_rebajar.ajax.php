<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
    }

        
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************
      
    $sql="DELETE FROM tbl_exi_suc WHERE codigo = '$codigo'";    

      //// *************** Ejecuto la consulta ******************
    //var_dump($sql);         
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        
