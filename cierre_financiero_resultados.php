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
    <a href="#" onClick="location.href = 'cierre_financiero.php'"><img width="48"  height="48" src= "img/back.png">Volver</a> 
    <a href="#" onClick="location.href = 'cierre_financiero_resultados_inventario.php'"><img width="48"  height="48" src= "img/facts.png">Inventario</a> 
    <a href="#" onClick="location.href = 'cierre_financiero_resultados_ventas.php'"><img width="48"  height="48" src= "img/cierre.png">Ventas</a> 
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
    
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo '<center>';
    echo '<h3>Ingresos  / Egresos</h3>';
    echo '<center><table border =1>
            <tr class="encabezadoRegistro">
                <th>Fecha</th>
                <th>Ventas</th>
                <th>Descuento</th>
                <th>Total Ventas</th>
                <th>C. Locales</th>
                <th>C. Exteriores</th>
                <th>T.Compras</th>
                <th>Gastos</th>
                <th>G. Fijos</th>
                <th>Total</th>
                <th>Diferencia</th>';
        echo '</tr>';
    
         while($arr = odbc_fetch_array($resultado)){
                  
            $fecha = strtotime($arr['Fecha']);
            $ventas = $arr['I_Ventas'];
            $descuento = $arr['I_Descuentos'];
            $totalventas = $arr['I_TotalVentas'];
            $diferencia = $arr['Diferencia'];
            $local = $arr['E_ComprasL'];
            $exterior = $arr['E_ComprasE'];
            $tcompras=$arr["E_TotCompras"];
            $gastos = $arr['Gastos'];
            $gastosfijos = $arr['GastosFijos'];
            $tegresos = $arr['TotalEgresos'];

            echo '<tr>';
            echo '<td>'.date("d-m-Y", $fecha).'</td><td>'.number_format($ventas,2).'</td>
            <td>'.number_format($descuento,2).'</td><td>'.number_format($totalventas,2).'</td>
            <td>'.number_format($local,2).'</td><td>'.number_format($exterior,2).'</td>
            <td>'.number_format($tcompras,2).'</td><td>'.number_format($gastos,2).'</td>
            <td>'.number_format($gastosfijos,2).'</td><td>'.number_format($tegresos,2).'</td>
            <td>'.number_format($diferencia,2).'</td>';       
            
        }
    
    
    echo '</table></center>';
 


?>
</div>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>