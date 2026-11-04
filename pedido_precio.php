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
<title>Pedidos</title>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/anular.css">
<link rel="StyleSheet" href="css/calendar.css">
<link rel="icon" href="favicon.ico">
</head>

<body>
    <style type="text/css">

    .botones{
        width:850px;
        height: 75px;
        padding:15px;
        background-color:#eee;
        border:1px solid #444;
    }
    input{
        background-color:white;
        color:black;
        width: 150px;
    }
    </style>

 <a name="Inicio" id="a"></a> 

<?php
require_once("db.php");
//error_reporting(1);

if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
  $datos = $_REQUEST['datos'];
  $pos=strpos($datos,'*');
  $factura=substr($datos,0,$pos);
  $proveedor=substr($datos,$pos+1,5);
} 

$seguro=0;
if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
  $factura = $_REQUEST['fact'];
} 

if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
  $proveedor = $_REQUEST['proveedor'];
} 

if(isset($_REQUEST['factor']) && !empty($_REQUEST['factor'])){
  $factor = $_REQUEST['factor'];
} 


echo '<br>';
echo '<a href="pedido_mostrar.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
//echo '<a href="#Totales"><img width="48"  height="48" src= "img/downdb.png">Ver Totales</a>';
//echo $datos .'/'.$factura.'-'.$proveedor;
echo '<br>';

@$varsubtotal =0;
@$vartotal =0;
@$cant=0;
$codigobusq = '';
@$noexite=0;


$sql="SELECT p.ID, p.Factura, p.Codigo, p.Cantidad, p.Precio, p.Sugerido, p.PrecioAnterior, p.Proveedor, i.DESCRIPCION FROM Pedidos p, inventario i WHERE Factura='$factura' AND i.codigo = p.codigo order by p.Codigo";  

//$sql="SELECT * FROM Pedidos WHERE Factura='$factura' order by substring(Codigo,1,1),substring(Codigo,5,4), 
  //        substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12),Codigo";  
/*
$sql="SELECT p.ID,p.Sugerido,p.Precio,p.Proveedor,p.Factura,p.Codigo,p.Cantidad,p.etiquetas,p.Fecha,i.Ubicacion,p.caja,
    substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1)
    WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
    WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
    Else p.codigo END As Filtro FROM Pedidos as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE Factura=".$factura." ORDER BY Filtro";
*/
    
    if(($res = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
 

//echo $sql;
echo "<h2>Factor del pedido ".number_format($factor,0)."</h2>";
//ingresa nombres de cuadro
echo "<center>";
echo "<table border = '1'>";
echo "<tr>";
echo "<td><b>ID</b></td> ";
echo "<td><b>Factura</b></td>";
echo "<td><b>Codigo</b></td> ";
echo "<td><b>Descripcion</b></td> ";
echo "<td><b>Cantidad</b></td> ";
echo "<td><b>Precio</b></td> ";
echo "<td><b>Sugerido</b></td> ";
echo "<td><b>Anterior</b></td> ";
echo "<td><b>Colones</b></td> ";
echo "<td><b>Aplicar</b></td> ";

echo "</tr> ";
echo "<tr> ";

$marcador1='';
$marcador2='inicio';
$marcador3='inicio';
$contador=1;

while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro
$id_codigos= $arr["ID"];

echo "<td>".$id_codigos."</td> ";
echo "<td>".$arr["Factura"]."</td>";
echo "<td>".$arr["Codigo"]."</td>";
$codigos=$arr["Codigo"];
$descripcion='NUEVO';
$sql_inventario="SELECT DESCRIPCION FROM inventario WHERE CODIGO='$codigos'";  
    if(($res_inve = odbc_exec($id,$sql_inventario)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    while($arr_inve = odbc_fetch_array($res_inve)){
        $descripcion=mb_convert_encoding($arr_inve["DESCRIPCION"],  "ISO-8859-1", "UTF-8");
    }
if ($descripcion=='NUEVO') {
    $descripcion=' *********** CODIGO NO EXISTE **********';
    $noexite++;
}
 if ($marcador1=='') {
    $marcador=$codigos;
    $marcador1='inicio';
  }
  
  if ($contador==2) {
    $marcador1=$marcador2;
    $marcador2=$marcador3;
    $marcador3=$codigos;
    
  }
  if ($contador==3) {
    $marcador1=$marcador2;
    $marcador2=$marcador3;
    $marcador3=$codigos;
    $contador=1;
  }


echo '<td><a name="'.$codigos.'" id="a"></a>'.$descripcion.'</td>';
echo "<td>".$arr["Cantidad"]."</td>";
echo "<td>".$arr["Precio"]."</td>";
echo "<td style='color:grey'>".$arr["Sugerido"]."</td>";
echo "<td style='color:blue'>".$arr["PrecioAnterior"]."</td>";
echo '<form action="pedido_editar_precio.php" method="POST">';
if ($arr["Sugerido"]<=$arr["PrecioAnterior"]) {
    echo '<td><input style="color:green" type="text" id="sugerido"  name="sugerido" required autofocus value="'.number_format($arr["Sugerido"],2).'"></td>';    
}else{
    echo '<td><input style="color:red" type="text" id="sugerido"  name="sugerido" required autofocus value="'.number_format($arr["Sugerido"],2).'"></td>';
}

echo '<input type="hidden" id="fact"  name="fact" required autofocus value="'.$factura.'">';
echo '<input type="hidden" id="id"  name="id" required autofocus value="'.$id_codigos.'">' ;           
echo '<input type="hidden" id="factor"  name="factor" required autofocus value="'.$factor.'">' ;           
echo '<input type="hidden" id="proveedor"  name="proveedor" required autofocus value="'.$proveedor.'">' ; 
echo '<input type="hidden" id="marcador"  name="marcador" required autofocus value="'.$marcador1.'">' ;          
echo '<td><button  type="submit">Actualizar</button></td>';
echo '</form>';
//echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este codigo del pedido?".$etiqueta."\")) 
//                    location.href=\"pedido_editar_etiquetas.php?id=".$id_codigos."&fact=".$factura."&etiqueta=#etiqueta\";
//                    '><img width='48'  height='48' src= 'img/editar.png'></a></td>";

$vartotal=$vartotal+($arr["Cantidad"]*$arr["Precio"]);
$varsubtotal=$varsubtotal+($arr["Cantidad"]*$arr["Sugerido"]);
$cant++;
echo "</tr> ";
$contador++;			

}

echo "<td><strong> Cantidad  :".$cant."</td>
<td><strong>Codigos nuevos : ".$noexite."</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>" ;
echo '<td><strong>Total colones: '.number_format($varsubtotal,2).'</td>
<td><strong>Total $: '.number_format($vartotal,2).'</strong>
</td><td></td><td></td></td><td></td><td></td><td></td></tr>';
echo '<br>';
echo '</table>';
echo '</center>';
echo '<br>';
echo '<center>';
echo '<div class="botones">';
echo '<a href="pedido_mostrar.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/back.png">Volver</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="#Inicio"><img width="48"  height="48" src= "img/up.png">Inicio</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '</div>';
echo '</center>';


?>
<a name="Totales" id="a"></a>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pedido Precio';
</script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>