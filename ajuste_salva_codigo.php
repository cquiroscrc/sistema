<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion'];      
    } 
    if(isset($_REQUEST['existencia']) && !empty($_REQUEST['existencia'])){
          $existencia = $_REQUEST['existencia'];      
    } 
    if(isset($_REQUEST['ajuste']) && !empty($_REQUEST['ajuste'])){
          $ajuste = $_REQUEST['ajuste'];      
    } 
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
          $tipo = $_REQUEST['tipo'];      
    } 
    if(isset($_REQUEST['costo']) && !empty($_REQUEST['costo'])){
          $costo = $_REQUEST['costo'];      
    } 
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
          $cantidad = $_REQUEST['cantidad'];      
    } 

    $Idcampo=0;
  
    $sql ="SELECT * FROM detajutras WHERE IDMovimiento='".$ajuste."'";

   // echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
            
    while($arr = odbc_fetch_array($resultado)){ 
      $Idcampo=$arr['ID'];
    }
      $Idcampo=$Idcampo+1;
    
    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    

    if ($Idcampo==1) {
      $entro=0;
      $sqlbusqueda="SELECT * FROM ajutras WHERE IDMovimiento='".$ajuste."'";
    
      if(($resultadobusqueda = odbc_exec($id,$sqlbusqueda)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
      while($arrbusqueda = odbc_fetch_array($resultadobusqueda)){ 
        $entro=1;  
      }

      if ($entro==0) {
        
        $sqlnuevo = "INSERT INTO ajutras
        (IDMovimiento,IDTipoMovimiento,FechaMov,IDUsuario,Estado)
        values
        ('".$ajuste."','".$tipo."','".date("Y-m-d h:i:s.m")."','99','0')";
        
        if(($resultadonuevo = odbc_exec($id,$sqlnuevo)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
        $arrnuevo = odbc_fetch_array($resultadonuevo);
        
      }
      
      
    }

     $sqlInsert = "INSERT INTO detajutras
        (IDMovimiento,IDTipoMovimiento,Codigo,Cantidad,Costo,IDBodegaO,IDBodegaD,ID)
        values
        ('".$ajuste."','".$tipo."','".$codigo."','".$cantidad."','".$costo."','1','1','".$Idcampo."')";
      
      echo $sqlnuevo;echo '<br>'; echo $sqlInsert;
      
      if(($resultadoInsert = odbc_exec($id,$sqlInsert)) === false)
         die("Error en Select: ". odbc_errormsg($id));
            
        $arrInsert = odbc_fetch_array($resultadoInsert);   

    header("location: ajuste.php");
   
?>
