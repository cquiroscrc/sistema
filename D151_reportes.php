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
?>
<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resultado de consulta metas</title>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/anular.css">
<link rel="StyleSheet" href="css/calendar.css">
<link rel="icon" href="img/favicon.ico">

</head>

<body>
  
<br>

  
<div  margin:20px;" id ="pulsador" onclick="window.location.href='D151.php';"><a href="#"><img width="48"  height="48" src= "img/back.png">Volver</a> </div>


  <?php
require_once("db.php");
//error_reporting(1);
if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
$fecha2 = $_REQUEST['Date2'];

}
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
$tipo = $_REQUEST['tipo'];

}
if(isset($_REQUEST['vend']) && !empty($_REQUEST['vend'])){
$vend = $_REQUEST['vend'];

}
//var de veces
$cantidad=0;

if ($fecha1 != ""){
  
  
      //Comienza la consulta
      $sql="SELECT TOP 20 proveedor,sum(subtotal) as Subtotal,sum(descuento) as Descuento from hfacturas where (fechafact BETWEEN '".$fecha1." 00:00:00.000'AND '".$fecha2." 23:59:59.000') AND proveedor <> 0 GROUP BY proveedor ORDER BY Subtotal desc" ;  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

     


     
      //ingresa nombres de cuadro
      echo '<center>';
      echo "<H2>Reporte de ventas por Cliente de ".$fecha1." a ".$fecha2." </H2>";
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Cliente</b></th> ";
      echo "<th><b>Nombre</b></th> ";
      echo "<th><b>Cedula</b></th> ";
      echo "<th><b>Subtotal</b></th>";
      echo "<th><b>Descuentos</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TTotal=0;
      $total_porcentaje=0;
      while($arr = odbc_fetch_array($res)){
        
        /// Totales para sacar cuentas
        $subtotal=$arr["Subtotal"];
        $descuento=$arr["Descuento"];
        $proveedor=$arr["proveedor"];
        echo "<td>".$proveedor."</td> ";
        // Cargar nombre
         $sql_cl="SELECT * FROM clientes WHERE codigo='".$proveedor."'" ;  
     
          if(($res_cl = odbc_exec($id,$sql_cl)) === false)
            die("Error en Select: ". odbc_errormsg($id));
         while($arr_cl = odbc_fetch_array($res_cl)){
          $nombre=$arr_cl["nombre"];
          $cedula=$arr_cl["cedula"];
         } 
        //echo "<td>".$arr["nombre"]."</td>";
        echo "<td>".$nombre."</td>";
        echo "<td>".$cedula."</td>";
        // volver
        echo "<td>".number_format($subtotal,2)."</td>";
            $TSubtotal = $TSubtotal + $subtotal;
        echo "<td>".number_format($descuento,2)."</td>";
            $TDescuentos = $TDescuentos + $descuento;
            $total = $subtotal - $descuento;
        echo "<td>".number_format($total,2)."</td>";
        echo "</tr> ";
      }

    }
    
    //echo 'SQL '.$sql;echo '<br>';echo 'Facturas '.$sqlFacturas;

echo '<a href="javascript:window.print(); void 0;"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>';
//echo'<input type="image" src="img/imprime.png"  width="48"  height="48" name="imprimir" value="Imprimir"  onClick="window.print();"/>';



?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // D151 Totales';
</script> 
<script src="js/bootstrap.min.js"></script> 
</body>
</html>