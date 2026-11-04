<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

   
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];
      
    }
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
      $cantidad = $_REQUEST['cantidad'];
         
    }
   
    
    //var_dump($codigo);
    $codigo=strtoupper($codigo);  
    //// *************** Incluye la consulta ******************
    $recarga="no";

    $sqlesta="SELECT * FROM tbl_exi_suc WHERE codigo = '$codigo'";
    $esta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlesta);
    
    foreach ($esta as $key => $value) {
              $sqlact_cant="UPDATE tbl_exi_suc SET cantidad=$cantidad WHERE codigo='$codigo'";
              $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlact_cant);
              $recarga="si";
    }

    
    if ($recarga=="no") {
        
        $sql="INSERT INTO tbl_exi_suc (codigo, cantidad, precio, Ubicacion) VALUES ('$codigo', $cantidad, 0, '00X00')";
        $insertcodigo= ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        
      }  
      
 

    //// *************** Recibe codigo ******************