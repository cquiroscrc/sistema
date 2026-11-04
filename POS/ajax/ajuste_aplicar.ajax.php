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
            
    $razon=strtoupper($razon);
  
    
    $sql = "SELECT * FROM detajutras WHERE IDMovimiento='$ajuste'";
    echo $sql .'<br>';  
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $value) {
    
      $codigo=$value['Codigo'];
      $cantidad=$value['Cantidad'];
      $tipo=$value['IDTipoMovimiento'];

      $sql_inventario="SELECT * FROM inventario WHERE CODIGO = '$codigo'";
      
      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_inventario);
      foreach ($respuesta as $key => $a_inventario) {
        $existencia=$a_inventario['EXISTENCIA'];
        $precio=$a_inventario['COSTO'];
      }//existencia

      //Update
      if ($tipo==1) { //Agregar
      
        $actual=(int)$existencia+(int)$cantidad;
        $sql_update="UPDATE inventario SET EXISTENCIA='$actual' WHERE CODIGO='$codigo'";
      
      }elseif ($tipo==2) { // Eliminar
        
        $actual=(int)$existencia-(int)$cantidad;
        $sql_update="UPDATE inventario SET EXISTENCIA ='$actual' WHERE CODIGO='$codigo'"; 
      
      }
      
      $respuesta_update = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);

      //Insert

      $sql_insert="INSERT INTO hgeneral 
      (Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,
        precio,estado) values 
      ('".$ajuste."','".date("Y-m-d h:i:s.m")."','".$codigo."','".$cantidad."','Ajuste','".$tipo."',
        '0','1','".$id_acceso."','1','".$existencia."','".$actual."','".$precio."','Aplicado')";
      
      echo $sql_insert;
     
      $respuesta_insert = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_insert);


      //Insertar
     
      //cambio consecutivo
      $consecutivo = $ajuste + 1;
      
      $sql_consecutivo="UPDATE Consecutivos SET numero='$consecutivo' WHERE tipo = '05'";
      
      $respuesta_consecutivo = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
      
      //consecutivo
     
      
      //aplica ajuste
      $sql_aplique="UPDATE ajutras SET estado='1', IDUsuario='$id_acceso', Comentario='$razon' WHERE IDMovimiento = '$ajuste'";
     
      $respuesta_aplique = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_aplique);
    
      //Aplicar

    } //Fin de while   
