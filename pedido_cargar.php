<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    include "menu.php";
    require_once("db.php");
    
?>
<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    <title>Cierre diario</title>
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.inputmask.bundle.min.js"></script>
    <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signin.css">
    
    <link rel="icon" href="favicon.ico">
    
   

  </head>
<body>








<div align="center"><big style="font-weight: bold;"><big><big><big>   
  
  
  <form class="form-signin" action="pedido_mostrar.php" method="POST" name="F;">
  <table align="center" border="0" cellspacing="0">
    <h2><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>&nbsp;Pedidos</h2>
    <h3>Seleccione pedido pendientes</h3>
    
  </table>
  <select class="form-control" id="datos" name="datos" onChange="habilitar(this.form)">
      <optgroup >
  <?php 
   $sql="SELECT DISTINCT p.Factura, p.Proveedor, n.codigo, n.nombre FROM Pedidos p, Proveedores n where p.Proveedor = n.codigo";  
      $conta=1;
 
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        echo "<option value='".$arr['Factura']."*".$arr['Proveedor']."'>".$arr['Factura']." - ".$arr['Proveedor']." - ".$arr['nombre']."</option>";
        $conta++;
      }

   ?>
    </optgroup>
  </select>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>

  </form>

  <form class="form-signin" action="pedido_mostrar.php" method="POST" name="F;">
  <table align="center" border="0" cellspacing="0">
    <h2><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>&nbsp;Pedidos Nuevo</h2>
    <h3>Seleccione Proveedor</h3>
    
  </table>
  <select class="form-control" id="proveedor" name="proveedor" onChange="habilitar(this.form)">
      <optgroup >
  <?php 
   $sql_proveedor="SELECT * FROM Proveedores order by codigo DESC";  
      $conta=1;
 
      if(($resultado_pro = odbc_exec($id,$sql_proveedor)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr_proveedor = odbc_fetch_array($resultado_pro)){
        echo "<option value='".$arr_proveedor['codigo']."'>".$arr_proveedor['codigo']." - ".$arr_proveedor['nombre']."</option>";
        $conta++;
      }

   ?>
    </optgroup>
  </select>
  
  <h3>Factura</h3>
  <input type="text" id="fact" class="form-control" name="fact" required autofocus>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Crear</button>

  </form>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pedidos';
</script> 
<script src="js/bootstrap.min.js"></script>
</BODY>
</HTML>