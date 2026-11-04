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
        width: 75px;
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


if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
  $factura = $_REQUEST['fact'];
  
} 

if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
  $proveedor = $_REQUEST['proveedor'];
  
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

$marcador1='';
$marcador2='inicio';
$marcador3='inicio';
$contador=1;

//$sql="SELECT p.ID, p.Factura, p.Codigo, p.Cantidad, p.Precio, p.Sugerido, p.PrecioAnterior, p.Proveedor, i.DESCRIPCION FROM Pedidos p, inventario i WHERE Factura='$factura' AND i.codigo = p.codigo order by ID";  

//$sql="SELECT * FROM Pedidos WHERE Factura='$factura' order by substring(Codigo,1,1),substring(Codigo,5,4), 
  //        substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12),Codigo";  
$sql="SELECT p.ID,p.Sugerido,p.Precio,p.Proveedor,p.Factura,p.Codigo,p.Cantidad,p.etiquetas,p.Fecha,i.Ubicacion,p.caja,
    substring(p.codigo,1,1),substring(p.codigo,6,3 ),substring(p.codigo,10,15),Case substring(p.codigo, 1, 1)
    WHEN '0' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
    WHEN '1' then substring(p.codigo,1,1)+substring(p.codigo,5,4 )+substring(p.codigo,9,4 )+' '+substring(p.codigo,1,4 )+' '+ltrim(rtrim(substring(p.codigo,13,12 )))
    Else p.codigo END As Filtro FROM Pedidos as  p LEFT join Inventario as i on p.codigo=i.codigo WHERE Factura='".$factura."' ORDER BY Filtro";

    
    if(($res = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

    





  
    

//echo $sql;

//ingresa nombres de cuadro
echo "<center>";
echo "<table border = '1'>";
echo "<tr>";
//echo "<td><b>ID</b></td> ";
//echo "<td><b>Factura</b></td>";
echo "<td><b>Codigo</b></td> ";
echo "<td><b>Descripcion</b></td> ";
echo "<td><b>Ubicacion</b></td> ";
echo "<td><b>Cantidad</b></td> ";
echo "<td><b>Etiquetas</b></td> ";
echo "<td><b>Aplicar</b></td> ";

echo "</tr> ";
echo "<tr> ";


while($arr = odbc_fetch_array($res)){
//ingresa datos en cuadro
$id_codigos= $arr["ID"];
$codigos=$arr["Codigo"];
$descripcion='NUEVO';

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

echo "<td>".$arr["Codigo"]."</td>";

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
echo '<td><a name="'.$codigos.'" id="a"></a>'.$descripcion.'</td>';
echo "<td>".$arr["Ubicacion"]."</td>";
echo "<td>".$arr["Cantidad"]."</td>";
echo '<form action="pedido_editar_etiquetas.php" method="POST">';
echo '<td><input type="number" id="etiqueta"  name="etiqueta" required autofocus value="'.$arr["etiquetas"].'"></td>';
echo '<input type="hidden" id="fact"  name="fact" required autofocus value="'.$factura.'">';
echo '<input type="hidden" id="id"  name="id" required autofocus value="'.$id_codigos.'">' ;           
echo '<input type="hidden" id="marcador"  name="marcador" required autofocus value="'.$marcador1.'">' ;           
echo '<input type="hidden" id="proveedor"  name="proveedor" required autofocus value="'.$proveedor.'">' ;           
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
<td><strong>Codigos nuevos : ".$noexite."</td><td></td><td></td><td></td><td></td></tr>" ;
echo '<td><strong>Total colones: '.number_format($varsubtotal,2).'</td>
<td><strong>Total $: '.number_format($vartotal,2).'</strong>
</td><td></td><td></td></td><td></tr>';

echo '</table>';
echo '</center>';
echo '<br>';
echo '<center>';
echo '<div class="botones">';
echo '<a href="pedido_mostrar.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/back.png">Volver</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="#Inicio"><img width="48"  height="48" src= "img/up.png">Inicio</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="pedido_etiquetas_imprime.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/aplicar.png">Genera Etiquetas</a>';
echo '</div>';
echo '</center>';


?>
<a name="Totales" id="a"></a>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pedido Etiquetas';
</script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>