<?php
session_start();
include "menu.php";
?>
<html>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Usuarios</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> <!--  Imagenes fancy -->
        <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
        <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script> <!--  Imagenes fancy -->

        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/anular.css">
        
        <link rel="icon" href="favicon.ico">
    </head>
    <body>
    <a href="#" onClick="location.href = 'cierre_financiero_resultados.php'"><img width="48"  height="48" src= "img/back.png">Volver</a> 
    <a href="#" onClick="location.href = 'cierre_financiero_resultados_inventario.php'"><img width="48"  height="48" src= "img/facts.png">Inventario</a> 
    <a href="#" onClick="location.href = 'cierre_financiero_resultados_gastos.php'"><img width="48"  height="48" src= "img/etiqueta.png">Gastos</a> 
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>

<div id="muestra">
<?php
    require("db.php");



$sql = "SELECT TOP 12 * from tbl_Financiero ORDER BY Fecha DESC";

 if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

    echo '<h1>Cierre Financiero</h1>';
    echo '<center>';
    echo '<h3>Inventario de Ventas 1</h3>';
    echo '<center><table border =1>
            <tr class="encabezadoRegistro">
                <th>Fecha</th>
                <th>C. VW Local</th>
                <th>M. VW Local</th>
                <th>C. VW Ext</th>
                <th>M. VW Ext</th>
                <th>C. Vol Local</th>
                <th>M. Vol Local</th>
                <th>C. Vol Ext</th>
                <th>M. Vol Ext</th>';
        echo '</tr>';
    $sql = "SELECT TOP 12 * from tbl_Financiero ORDER BY Fecha DESC";

    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
        $fecha = strtotime($arr['Fecha']);
        $CanVenLVW = $arr['T_VenCanLVW'];
        $CanVenEVW = $arr['T_VenCanEVW'];
        $MVenLVW = $arr['T_VenMonLVW'];
        $MVenEVW = $arr['T_VenMonEVW'];
        $CanVenLVV = $arr['T_VenCanLVV'];
        $CanVenEVV = $arr['T_VenCanEVV'];
        $MVenLVV = $arr['T_VenMonLVV'];
        $MVenEVV = $arr['T_VenMonEVV'];
        
        echo '<tr>';
        echo '<td>'.date("d-m-Y", $fecha).'</td><td>'.number_format($CanVenLVW,2).'</td><td>'.number_format($MVenLVW,2).'</td><td>'.number_format($CanVenEVW,2).'</td><td>'.number_format($MVenEVW,2).'</td>
        <td>'.number_format($CanVenLVV,2).'</td><td>'.number_format($MVenLVV,2).'</td><td>'.number_format($CanVenEVV,2).'</td><td>'.number_format($MVenEVV,2).'</td>';       
       
    }
    echo '</table></center>';
    echo '<center>';
    echo '<h3>Inventario de Ventas 2</h3>';

    echo '<center><table border =1>
            <tr class="encabezadoRegistro">
                <th>Fecha</th>
                <th>C. DH Local</th>
                <th>M. DH Local</th>
                <th>C. DH Ext</th>
                <th>M. DH Ext</th>
                <th>C. ACC Local</th>
                <th>M. ACC Local</th>
                <th>C. ACC Ext</th>
                <th>M. ACC Ext</th>';
        echo '</tr>';
    $sql = "SELECT TOP 12 * from tbl_Financiero ORDER BY Fecha DESC";

    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
        $fecha = strtotime($arr['Fecha']);
        $CanVenLDH = $arr['T_VenCanLDH'];
        $CanVenEDH = $arr['T_VenCanEDH'];
        $MVenLDH = $arr['T_VenMonLDH'];
        $MVenEDH = $arr['T_VenMonEDH'];
        $CanVenLAC = $arr['T_VenCanLAC'];
        $CanVenEAC = $arr['T_VenCanEAC'];
        $MVenLAC = $arr['T_VenMonLAC'];
        $MVenEAC = $arr['T_VenMonEAC'];
        

        echo '<tr>';
        echo '<td>'.date("d-m-Y", $fecha).'<td>'.number_format($CanVenLDH,2).'</td><td>'.number_format($MVenLDH,2).'</td><td>'.number_format($CanVenEDH,2).'</td><td>'.number_format($MVenEDH,2).'</td>
        <td>'.number_format($CanVenLAC,2).'</td><td>'.number_format($MVenLAC,2).'</td><td>'.number_format($CanVenEAC,2).'</td><td>'.number_format($MVenEAC,2).'</td>';       
       
    }
    echo '</table></center>';
   


?>
</div>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>