<?php
  session_start();
  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  include "menu.php";
  include("db.php");

  ?>
<!DOCTYPE html>  
<html>  
<head>  
  <meta charset="utf-8">  
  <title>Ajustes de codigos</title>

  <script src="js/jquery.min.js"></script>
  <script src="js/jquery.inputmask.bundle.min.js"></script>
  
  <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/anulars.css">
  
  <link rel="icon" href="favicon.ico">
  <style>
    input{
      display: none;
    }
  </style>
</head>
<body>  
<?php  


  $sql = "SELECT i.codigo as codigos ,i.descripcion,i.proveedor, h.movimiento, h.codcliente, max(h.fecha) as fecha FROM inventario i, hgeneral h 
where movimiento = 'PEDIDO' and i.codigo = h.codigo and proveedor <> codcliente
GROUP BY i.codigo, descripcion,movimiento,codcliente,proveedor ORDER BY i.CODIGO";
  //echo $sql .'<br>';  
  if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  $cant=0;
  while($arr = odbc_fetch_array($resultado)){ 
    $codigo=$arr['codigos'];
    $descripcion=$arr['descripcion'];
    $proveedor=$arr['proveedor'];
    $codcliente=$arr['codcliente'];
    $codcliente= trim($codcliente);
  //  echo $codigo .'-'.$descripcion.'-'.'-'.$proveedor.'-'.$codcliente.'X<br>';
    $cant=$cant +1;

    $sql_update="UPDATE inventario SET proveedor='$codcliente' WHERE CODIGO='$codigo'";
    //echo $sql_update . '<br>';
    //echo '<br>';
  
      if(($r_update = odbc_exec($id,$sql_update)) === false)
        die("Error en Select: ". odbc_errormsg($id));
   
  


  } //Fin de while       
  echo '<br>';
  echo '<br>';
  echo '<br>';      
  echo '<h1>Se actualizaron '.$cant.' registros</h1>';
  echo '<br>';      
  echo '<a class="agregarUsuario" href="index.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';
    // Quitar comentario al final
  //header("location: ajuste.php");
    
?>
<script src="js/bootstrap.min.js"></script> 
</html>