<?php
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
    }

    if(isset($_REQUEST['ubicacion']) && !empty($_REQUEST['ubicacion'])){
      $ubicacion = $_REQUEST['ubicacion'];
    }

        
     $codigo=strtoupper($codigo);  
     $ubicacion=strtoupper($ubicacion);  
      //// *************** Incluye la consulta ******************
    $sql="UPDATE tbl_exi_suc SET Ubicacion='$ubicacion' WHERE codigo='$codigo'";  
      

      //// *************** Ejecuto la consulta ******************
    

      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        
