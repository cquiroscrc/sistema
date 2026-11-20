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
<link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script>
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
echo '<a href="pedido_cargar.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a href="#Totales"><img width="48"  height="48" src= "img/downdb.png">Ver Totales</a>';
//echo $datos .'/'.$factura.'-'.$proveedor;
?>

<form class="form-signin" action="pedido_resultado.php" method="POST">
           
            <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Pedido</h1>
            
            
            <?php 
            //// *************** Incluye la consulta ******************
            include "consulta_form.php";
            ?>
          <?php 
            echo '<input type="hidden" id="fact" class="form-control" name="fact" required autofocus value="'.$factura.'">';
            echo '<input type="hidden" id="proveedor" class="form-control" name="proveedor" required autofocus value="'.$proveedor.'">';
           ?>
          <br>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>
        
        



  <?php



@$varsubtotal =0;
@$vartotal =0;
@$cant=0;
$codigobusq = '';
@$noexite=0;

$sql="SELECT * FROM Pedidos WHERE Factura='$factura' order by ID DESC";  
    if(($res = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
      
    $marcador1='';
    $marcador2='inicio';
    $marcador3='inicio';
    $contador=1;   

//echo $sql;

//ingresa nombres de cuadro
echo "<center>";
echo "<table border='1'>";
echo "<th>ID</th>";
echo "<th>Factura</th>";
echo "<th>Proveedor</th>";
echo "<th>Codigo</th>";
echo "<th>Descripcion</th>";
echo "<th>Cantidad</th>";
echo "<th>Precio</th>";
echo "<th>Factor</th>";
echo "<th>Sugerido</th>";
echo "<th>Anterior</th>";
echo "<th>Total</th>";
echo "<th>Imagen</th>";
echo "<th>Eliminar</th>";
echo "</tr>";



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
    echo "<td><a href='pedido_codigo_act.php?id=".$id_codigos."&fact=".$factura."&proveedor=".$proveedor."&marcador=".$marcador1."'>".$id_codigos."</a></td>";
    echo "<td>".$arr["Factura"]."</td>";
    echo "<td>".$arr["Proveedor"]."</td>";
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
    echo "<td>".$arr["Cantidad"]."</td>";
    echo "<td>".$arr["Precio"]."</td>";
    echo "<td>".$arr["factor"]."</td>";
    echo "<td>".number_format($arr["Sugerido"],2)."</td>";
    echo "<td>".number_format($arr["PrecioAnterior"],2)."</td>";
    echo "<td>".($arr["Cantidad"]*$arr["Precio"])."</td>";
    $path='subida_cat/';

          if (substr($codigos,0,1)==1) {
            $directorio='1/';
          }
          if (substr($codigos,0,1)==2) {
            $directorio='2/';
          }
          if (substr($codigos,0,1)==3) {
            $directorio='3/';
          }
          if (substr($codigos,0,1)==4) {
            $directorio='4/';
          }
          if (substr($codigos,0,1)==9) {
            $directorio='9/';
          }
          if (substr($codigos,0,1)==0) {
            $directorio='0/';
          }
          $codigos1=rtrim($codigos);
          if (substr($codigos,-2)=='EU') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
             
          } 
          if (substr($codigos,-2)=='GR') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }
          if (substr($codigos,-2)=='TW') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
             
          }
          if (substr($codigos,-2)=='JP') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }
          if (substr($codigos,-2)=='ZL') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }

          $foto=substr ( $codigos1 , 1 );
          $foto=rtrim($foto);

          if (substr($foto,0,1)==' ') {
                //echo 'FOTOS';
                $foto=substr($foto, 2, strlen($foto));
              }
              
          $filename = $path.$directorio.$foto.".jpg";
          $archivo=$filename;

          if (file_exists($filename)) {
              $archivo=$filename;
          } else {
              $archivo=$path."inf.png";
          }

          echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';

          


    echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este codigo del pedido?\")) 
                        location.href=\"pedido_eliminar.php?id=".$id_codigos."&fact=".$factura."&proveedor=".$proveedor."\";
                        '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";

    $vartotal=$vartotal+($arr["Cantidad"]*$arr["Precio"]);
    $varsubtotal=$varsubtotal+($arr["Cantidad"]*$arr["Sugerido"]);
    $cant++;
    echo "</tr> ";
    
    $contador++;    			

}

echo "<td></td><td></td><td></td><td><strong> Cantidad  :".$cant."</td>
<td><strong>Codigos nuevos : ".$noexite."</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>" ;
echo '<td></td><td></td><td></td><td><strong>Total colones: '.number_format($varsubtotal,2).'</td><td><strong>Total $: '.number_format($vartotal,2).'</strong></td><td></td><td></td>
<td></td><td></td><td></td><td></td><td></td></tr>';

echo '</table>';
echo '</center>';
echo '<br>';
echo '<center>';
echo '<div class="botones">';
echo '<a href="#Inicio"><img width="48"  height="48" src= "img/up.png">Inicio</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="pedido_imprime.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/reporte.png">Reporte</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="pedido_etiquetas.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/etiqueta.png">Etiquetas</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="pedido_calculo.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/calculadora.png">Precios</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
//echo '<a href="pedido_aplicar.php?fact='.$factura.'&proveedor='.$proveedor.'&total='.$vartotal.'"><img width="48"  height="48" src= "img/aplicar.png">Aplicar</a>';
echo "<a class = 'echo_link' href='#' onclick='if(confirm(\"Desea aplicar el pedido?\")) 
                        location.href=\"pedido_aplicar.php?fact=".$factura."&proveedor=".$proveedor."&total=".$vartotal."\";
                        '><img width='48'  height='48' src= 'img/aplicar.png'>Aplicar</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<a class = 'echo_link' href='#' onclick='if(confirm(\"Desea Limpiar la etiquetas?\")) 
                        location.href=\"pedido_limpiar.php?fact=".$factura."&proveedor=".$proveedor."&total=".$vartotal."\";
                        '><img width='48'  height='48' src= 'img/limpiar.png'>Limpiar</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";


$sql4="SELECT codigo, nombre FROM Proveedores WHERE codigo='$proveedor'";

if(($prov = odbc_exec($id,$sql4)) === false)
    die("Error en Select: ". odbc_errormsg($id));
  $nombre_prove="Local";
  while($arr4 = odbc_fetch_array($prov)){
    $nombre_prove=$arr4["nombre"];
  }                        


echo '<a href="pedido_mail.php?fact='.$factura.'&proveedor='.$proveedor.'&nombre='.$nombre_prove.'"><img width="48"  height="48" src= "img/send.png">Mail</a>';
echo '</div>';
echo '</center>';


?>
<a name="Totales" id="a"></a>

<?php 
include "consulta.php";
//// *************** Incluye la consulta ******************
 ?>
  <script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pedido';
</script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>