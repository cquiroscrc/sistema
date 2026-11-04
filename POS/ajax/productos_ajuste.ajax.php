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
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
         
    }
    if(isset($_REQUEST['ajuste']) && !empty($_REQUEST['ajuste'])){
      $ajuste = $_REQUEST['ajuste'];
         
    }
    
    //var_dump($codigo);
    $codigo=strtoupper($codigo);  
    //// *************** Incluye la consulta ******************
    $sql ="SELECT * FROM inventario WHERE codigo='".$codigo."'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $value) {
      $costo=$value['COSTO'];
    }

    $Idcampo=0;
  
    $sql ="SELECT * FROM detajutras WHERE IDMovimiento='".$ajuste."'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $value) {
      $Idcampo=$value['ID'];
    }
      $Idcampo=$Idcampo+1;
    
    if ($Idcampo==1) {
        $entro=0;
        $sql="SELECT * FROM ajutras WHERE IDMovimiento='".$ajuste."'";
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        foreach ($respuesta as $key => $value) {
          $entro=1;  
        }

        if ($entro==0) {
          
          $sql= "INSERT INTO ajutras (IDMovimiento,IDTipoMovimiento,FechaMov,IDUsuario,Estado) values ('".$ajuste."','".$tipo."','".date("Y-m-d h:i:s.m")."','99','0')";
          $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
          
        }
    }
    $xntro=false;
    $sql="SELECT * FROM detajutras WHERE codigo='$codigo' AND IDMovimiento='$ajuste'";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $value) {
        $sqlact_cant="UPDATE detajutras SET cantidad=$cantidad , IDTipoMovimiento=$tipo WHERE codigo='$codigo'";
        $actualizar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlact_cant);
        $xntro=true;
    }
      
    if ($xntro==false) {
        $sql = "INSERT INTO detajutras (IDMovimiento,IDTipoMovimiento,Codigo,Cantidad,Costo,IDBodegaO,IDBodegaD,ID)
        values ('".$ajuste."','".$tipo."','".$codigo."','".$cantidad."','".$costo."','1','1','".$Idcampo."')";
        $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    }
    
