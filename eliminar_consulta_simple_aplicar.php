<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include("db.php");

    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
        $codigo = $_REQUEST['codigo'];      
    } 
    

  $sql = "SELECT * FROM Inventario WHERE CODIGO='$codigo'";
  
 
  if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  
  while($arr = odbc_fetch_array($resultado)){ 
    $codigo=$arr['CODIGO'];
    $existencia=$arr['EXISTENCIA'];
    $ubicacion=$arr['ubicacion'];
  }  
  echo "no ha entrado ".$codigo;
  echo substr ($ubicacion,0,1);

  if (substr ($ubicacion,0,1)=='9') {
    echo 'Noventa '.$codigo;
    //exit;  
    //Eliminar Hgeneral
    $sql_update_h="DELETE FROM hgeneral WHERE CODIGO='$codigo'";
      if(($h_update = odbc_exec($id,$sql_update_h)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    echo $sql_update_h;

    // Elimina Codigo
    $sql_eliminar="DELETE FROM Inventario where CODIGO='$codigo'";
      if(($r_eliminar = odbc_exec($id,$sql_eliminar)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    echo $sql_eliminar;
  }
  


  if ($existencia==0) {
    echo 'Existencia '.$codigo;
    //exit;  
    //Eliminar Hgeneral
    $sql_update_h="DELETE FROM hgeneral WHERE CODIGO='$codigo'";
      if(($h_update = odbc_exec($id,$sql_update_h)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    echo $sql_update_h;

    // Elimina Codigo
    $sql_eliminar="DELETE FROM Inventario where CODIGO='$codigo'";
      if(($r_eliminar = odbc_exec($id,$sql_eliminar)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    echo $sql_eliminar;
  }
 header("location: eliminar_consulta_simple.php");
    
?>
