<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");

    if(isset($_REQUEST['numajuste']) && !empty($_REQUEST['numajuste'])){
          $ajuste = $_REQUEST['numajuste']; 
          
    }
    if(isset($_REQUEST['id_acceso']) && !empty($_REQUEST['id_acceso'])){
          $id_acceso = $_REQUEST['id_acceso']; 
    }

    if(isset($_REQUEST['razon']) && !empty($_REQUEST['razon'])){
          $razon = $_REQUEST['razon']; 
    }


  $sql = "SELECT * FROM detajutras WHERE IDMovimiento='$ajuste'";
  echo $sql .'<br>';  
  if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  while($arr = odbc_fetch_array($resultado)){ 
    $codigo=$arr['Codigo'];
    $cantidad=$arr['Cantidad'];
    $tipo=$arr['IDTipoMovimiento'];
  

    

    $sql_inventario="SELECT * FROM inventario WHERE CODIGO = '$codigo'";
    
    if(($r_inventario = odbc_exec($id,$sql_inventario)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  
    while($a_inventario = odbc_fetch_array($r_inventario)){ 
     
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
    
   
    if(($r_update = odbc_exec($id,$sql_update)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    // existencia
   

    //Insert

    $sql_insert="INSERT INTO hgeneral 
    (Factura,fecha,codigo,cantidad,movimiento,tipo,codcliente,maquina,vendedor,bodega,anterior,actual,
      precio,estado) values 
    ('".$ajuste."','".date("Y-m-d h:i:s.m")."','".$codigo."','".$cantidad."','Ajuste','".$tipo."',
      '0','1','".$id_acceso."','1','".$existencia."','".$actual."','".$precio."','Aplicado')";
    
   
    if(($r_insert = odbc_exec($id,$sql_insert)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    //Insertar
   
    //cambio consecutivo
    $consecutivo = $ajuste + 1;
    
    $sql_consecutivo="UPDATE Consecutivos SET numero='$consecutivo' WHERE tipo = '05'";
    
   
    if(($r_consecutivo = odbc_exec($id,$sql_consecutivo)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  
    //consecutivo
   
    
    //aplica ajuste
    $sql_aplique="UPDATE ajutras SET estado='1', IDUsuario='$id_acceso', Comentario='$razon' WHERE IDMovimiento = '$ajuste'";
   
    if(($r_aplique = odbc_exec($id,$sql_aplique)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  
    //Aplicar
  /* 
    echo 'SQLS <br>';
    echo $sql_inventario;
    echo '<br>';
    echo $sql_update;
    echo '<br>';
    echo $sql_insert;
    echo '<br>';
    echo $sql_consecutivo;
    echo '<br>';
    echo $sql_aplique;
    echo '<hr>';
    */
  } //Fin de while       
        


    // Quitar comentario al final
  header("location: ajuste.php");
    
?>
