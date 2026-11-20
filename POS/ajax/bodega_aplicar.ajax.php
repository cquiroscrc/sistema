<?php

    session_start();
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['razon']) && !empty($_REQUEST['razon'])){
      $razon = $_REQUEST['razon']; 
    }

    $id_acceso=$_SESSION["id"];

    if(isset($_REQUEST['ajuste']) && !empty($_REQUEST['ajuste'])){
      $ajuste = $_REQUEST['ajuste'];
    }
            
    $razon="CONTEO";
  
    
    $sql = "SELECT * FROM detabodega WHERE IDMovimiento='$ajuste'";
    echo $sql .'<br>';  
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $value) {
    
      $codigo=$value['Codigo'];
      $cantidad=$value['Cantidad'];
      $tipo=$value['IDTipoMovimiento'];

      $sql_inventario="SELECT * FROM tbl_exi_suc WHERE codigo = '$codigo'";
      
      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_inventario);
      foreach ($respuesta as $key => $a_inventario) {
        $existencia=$a_inventario['cantidad'];
        $precio=$a_inventario['precio'];
      }//existencia

      //Update
      if ($tipo==1) { //Agregar
      
        $actual=(int)$existencia+(int)$cantidad;
        $sql_update="UPDATE tbl_exi_suc SET cantidad='$actual' WHERE codigo='$codigo'";
      
      }elseif ($tipo==2) { // Eliminar
        
        $actual=(int)$existencia-(int)$cantidad;
        $sql_update="UPDATE tbl_exi_suc SET cantidad ='$actual' WHERE codigo='$codigo'";
      
      }
      
      $respuesta_update = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);

      //Insert


      //Insertar
     
      //cambio consecutivo
      $consecutivo = $ajuste + 1;
      
      $sql_consecutivo="UPDATE Consecutivos SET numero='$consecutivo' WHERE tipo = '02'";
      
      $respuesta_consecutivo = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
      
      //consecutivo
     
      
      //aplica ajuste
      $sql_aplique="UPDATE ajubodega SET estado='1', IDUsuario='$id_acceso', Comentario='$razon' WHERE IDMovimiento = '$ajuste'";
     
      $respuesta_aplique = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_aplique);
    
      //Aplicar

    } //Fin de while   
