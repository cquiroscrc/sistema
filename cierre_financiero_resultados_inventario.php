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
    echo '<h3>Inventario</h3>';
    echo '<center><table border =1>
            <tr class="encabezadoRegistro">
                <th>Fecha</th>
                <th>Volkswagen</th>
                <th>C. VW</th>
                <th>Volvo</th>
                <th>C. Vol</th>
                <th>Daihatsu</th>
                <th>C. Dai</th>
                <th>Accesorios</th>
                <th>C. Acces</th>';
        echo '</tr>';
    $sql = "SELECT TOP 12 * from tbl_Financiero ORDER BY Fecha DESC";

    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
        $fecha = strtotime($arr['Fecha']);
        $tcostoVW = $arr['T_CostoVW'];
        $texisVW = $arr['T_ExisVW'];
        $tcostoVV = $arr['T_CostoVV'];
        $texisVV = $arr['T_ExisVV'];
        $tcostoDH = $arr['T_CostoDH'];
        $texisDH = $arr['T_ExisDH'];
        $tcostoAC = $arr['T_CostoAC'];
        $texisAC = $arr['T_ExisAC'];
        

        echo '<tr>';
        echo '<td>'.date("d-m-Y", $fecha).'</td><td>'.number_format($tcostoVW,2).'</td><td>'.number_format($texisVW,2).'</td><td>'.number_format($tcostoVV,2).'</td><td>'.number_format($texisVV,2).'</td><td>'.number_format($tcostoDH,2).'</td><td>'.number_format($texisDH,2).'</td><td>'.number_format($tcostoAC,2).'</td><td>'.number_format($texisAC,2).'</td>';       
       
    }
    echo '</table></center>';
   

?>
</div>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>