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
    <a href="#" onClick="location.href = 'cierre_financiero_resultados_ventas.php'"><img width="48"  height="48" src= "img/cierre.png">Ventas</a> 
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
    echo '<center>';
    echo '<h3>Gastos</h3>';

    echo '<center><table border =1>
            <tr class="encabezadoRegistro">
                <th>Fecha</th>
                <th>Salarios</th>
                <th>Alquiler</th>
                <th>C.C.S.S</th>
                <th>Tributacion</th>
                <th>Agua</th>
                <th>Luz</th>
                <th>Telefono</th>
                <th>Reserva</th>
                <th>C. Financiero</th>
                <th>Total</th>';
        echo '</tr>';
    $sql = "SELECT TOP 12 * from tbl_Financiero ORDER BY Fecha DESC";

    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
        $fecha = strtotime($arr['Fecha']);
        $salario = $arr['GF_Salarios'];
        $alquiler = $arr['GF_Alquiler'];
        $ccss = $arr['GF_CCSS'];
        $tributacion = $arr['GF_Tributacion'];
        $agua = $arr['GF_Agua'];
        $luz = $arr['GF_Luz'];
        $telefono = $arr['GF_Telefono'];
        
        $financiero= $arr['GF_CostoFinancieroP'];
        $subtotal=$salario+$alquiler+$ccss+$tributacion+$agua+$luz+$telefono;
    
        $c_financiero=($subtotal*$financiero)/100;

        $total = ($subtotal+$c_financiero)/12;
        $g_total=$subtotal+$c_financiero+$total;

        echo '<tr>';
        echo '<td>'.date("d-m-Y", $fecha).'<td>'.number_format($salario,2).'</td><td>'.number_format($alquiler,2).'</td><td>'.number_format($ccss,2).'</td><td>'.number_format($tributacion,2).'</td>
        <td>'.number_format($agua,2).'</td><td>'.number_format($luz,2).'</td><td>'.number_format($telefono,2).'</td><td>'.number_format($total,2).'</td><td>'.number_format($c_financiero,2).'</td><td>'.number_format($g_total,2).'</td>';       
       
    }
    echo '</table></center>';


?>
</div>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>