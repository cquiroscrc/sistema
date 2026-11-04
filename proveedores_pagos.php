<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

include "menu.php";
include("db.php");

?>

<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.inputmask.bundle.min.js"></script>
    <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/anular.css">
    <link rel="StyleSheet" href="css/calendar.css">
    <link rel="icon" href="favicon.ico">
    
  </head>
<body>

<div align="center">
  
  
  <form class="form-signin" action="proveedores_pagos_resultados.php" method="POST" name="F;">
    <center><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Pago Cheques</h1>
      </center>  
  <br>

  <select class="form-control" id="datos" name="datos" onChange="habilitar(this.form)">
      <optgroup >
  <?php 
   $sql="SELECT DISTINCT dbo.LPAD('0',5,rtrim(cxp.Proveedor)) + '-'+ Proveedores.nombre,Proveedores.codigo,Proveedores.nombre
        FROM cxp INNER JOIN Proveedores ON cxp.Proveedor = Proveedores.codigo
        WHERE (cxp.Estado = 'Pendiente') AND (cxp.Proveedor <> '1')";  
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        echo "<option value='".$arr['codigo']."'>".$arr['codigo']." - ".$arr['nombre']."</option>";
      }

   ?>
    </optgroup>
  </select>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>

  </form>
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Reporte</a>
  <?php 
    
    echo '<a href="proveedores_ingreso.php"><img width="48"  height="48" src= "img/imprimir.png">Hacer Cheques</a>';      
    echo '<div id="muestra">';
    echo '<center>';
    echo "<table border = '1'> ";
    echo "<tr> ";
    echo "<th><b>Fecha</b></th> ";
    echo "<th><b>Factura</b></th>";
    echo "<th><b>Proveedor</b></th>";
    echo "<th><b>Subtotal</b></th> ";
    echo "<th><b>Total</b></th> ";
    echo "<th><b>Saldo</b></th> ";
    echo "<th><b>Agregar</b></th> ";
    echo "</tr> ";
    echo "<tr> ";
    
    $saldo_parcial=0;
    $saldo=0;
    $proveedor='';
    $sql="SELECT * FROM tmpCxP order by proveedor,nfac,fechaFact";  

    if(($res = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($res)){
      
      if ($proveedor=='') {
        $proveedor=$arr['proveedor'];
      }

      if ($proveedor!=$arr['proveedor']) {
        echo "<th>Total Proveedor</th> ";
        echo "<th></th>";
        echo "<th>".number_format($saldo_parcial,2)."</th>";
        echo "<th></th>";
        echo "<th>Proveedor</th>";  
        echo "<th>".$proveedor."</th>";
        echo "<th></th>";  
        echo "</tr> ";  
        $saldo_parcial=0;
        $proveedor=$arr['proveedor'];
      }

      echo "<td>".$arr["fechaFact"]."</td> ";
      echo "<td>".$arr["nfac"]."</td>";
      echo "<td>".$arr["proveedor"]."</td>";
      $datos=$arr['proveedor'];
      $factura=$arr['nfac'];
      echo "<td>".number_format($arr["subtotal"],2)."</td>";
      echo "<td>".number_format($arr["total"],2)."</td>";
      //    if ($arr["chkCancelacion"]==0) {
      //      $varsaldo= $varsaldo + $arr["Total"];  
      //    }
      $saldo_parcial=$saldo_parcial+$arr['total'];
      $saldo=$saldo+$arr['total'];

      echo "<td style='color:blue'>".number_format($saldo_parcial,2)."</td>";
      echo '<td><a href="proveedores_pagos_excluir_prin.php?datos='.$datos.'&factura='.$factura.'"><img width="32"  height="32" src= "img/del.png"></a></td></tr>';
      echo "</tr> ";
    }
      echo "<th>Total Proveedor</th> ";
      echo "<th></th>";
      echo "<th>".number_format($saldo_parcial,2)."</th>";
      echo "<th></th>";
      echo "<th></th>";
      echo "<th>".$proveedor."</th>";  
      echo "<th></th>";
      echo "</tr> ";  
      echo "<td>Total</td> ";
      echo "<td></td>";
      echo "<td>".number_format($saldo,2)."</td>";
      echo "<td></td>";
      echo "<td></td>";
      echo "<td></td>";  
      echo "<td></td>";
      echo "</tr> ";  

   ?>
<script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
