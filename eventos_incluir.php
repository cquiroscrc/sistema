<?php
  session_start();
 

    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
          $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['cod']) && !empty($_REQUEST['cod'])){
          $cod = $_REQUEST['cod'];      
    } 
    if(isset($_REQUEST['marcador']) && !empty($_REQUEST['marcador'])){
          $marcador = $_REQUEST['marcador'];      
    } 
    
    if(isset($_REQUEST['sugerido']) && !empty($_REQUEST['sugerido'])){
          $cantidad = $_REQUEST['sugerido'];      
    }
    if(isset($_REQUEST['existe']) && !empty($_REQUEST['existe'])){
          $existe = $_REQUEST['existe'];      
    }

    include("db.php");
    
    $sql1="SELECT * from inventario where codigo = '$codigo'";
    if(($pedi = odbc_exec($id,$sql1)) === false)
        die("Error en Select: ". odbc_errormsg($id));  

      while($arr3 = odbc_fetch_array($pedi)){
        $costo=$arr3["COSTO"];
        $venta=$arr3["VENTA"];
        $descripcion=$arr3["DESCRIPCION"];
        $desc_alt=$arr3["DESC_ALT"];
        $ubicacion=$arr3["ubicacion"];
      }

  if ($existe=='si') {
    $sql = "UPDATE tbl_TrasOUT SET cantidad='$cantidad' WHERE codigo='$codigo'";
  }else{
    $sql = "INSERT INTO tbl_TrasOUT
            (IDTraslado,Codigo,Cantidad,Costo,Venta,IDBodega,Ubicacion,Estado,Descripcion,DESC_ALT,descuento,Proveedor,IV)
            values
            ('1','".$codigo."',".$cantidad.",".$costo.",".$venta.",1,'".$ubicacion."','A','".$descripcion."','".$desc_alt."',10,999,13)";
    }
    //echo $sql; exit;
    if(($eje = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));  
       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      header("location: eventos_resultado.php?codigo=".$cod.'#'.$marcador);
    
?>
