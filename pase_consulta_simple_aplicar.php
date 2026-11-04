<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");

    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
        $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['codigoOrigen']) && !empty($_REQUEST['codigoOrigen'])){
        $codigoOrigen = $_REQUEST['codigoOrigen'];      
    } 
    

  $sql = "SELECT * FROM Inventario WHERE CODIGO='$codigo'";
  
 
  if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  while($arr = odbc_fetch_array($resultado)){ 
    $codigo=$arr['CODIGO'];
    $existencia=$arr['EXISTENCIA'];
  }  
    
  $sql_inventario="SELECT * FROM inventario WHERE CODIGO = '$codigoOrigen'";
    if(($r_inventario = odbc_exec($id,$sql_inventario)) === false)
      die("Error en Select: ". odbc_errormsg($id));
  
    while($a_inventario = odbc_fetch_array($r_inventario)){ 
      $existenciaOrigen=$a_inventario['EXISTENCIA'];
    }

  //Update existencias 
  $actual=(int)$existencia+(int)$existenciaOrigen;
  echo $codigo;
  echo "<br>";
  echo $codigoOrigen;
  echo "<br>";
  echo $existencia;
  echo "<br>";
  echo $existenciaOrigen;
  echo "<br>";
  echo $actual;
  //exit;
  if ($codigoOrigen=='98Z99') {
    
  }else{
    $sql_update="UPDATE inventario SET EXISTENCIA='$actual' WHERE CODIGO='$codigo'";
      if(($r_update = odbc_exec($id,$sql_update)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    echo $sql_update;  

    //Update Hgeneral
    $sql_update_h="UPDATE hgeneral SET CODIGO='$codigo' WHERE CODIGO='$codigoOrigen'";
      if(($h_update = odbc_exec($id,$sql_update_h)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    echo $sql_update_h;

    // Elimina Codigo
    $sql_eliminar="DELETE FROM Inventario where CODIGO='$codigoOrigen'";
      if(($r_eliminar = odbc_exec($id,$sql_eliminar)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    echo $sql_eliminar;
  }
  header("location: pase_consulta_simple.php");
    
?>
