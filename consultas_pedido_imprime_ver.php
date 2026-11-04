<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
        include "menu.php";
?>
<!DOCTYPE html>
<html>
<style>
#myProgress {
  position: relative;
  width: 100%;
  height: 30px;
  background-color: #ddd;
}

#myBar {
  position: absolute;
  width: 1%;
  height: 100%;
  background-color: #FF0000;
}
#label {
  text-align: center;
  line-height: 30px;
  color: white;
}
</style>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de Pedido</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      
      <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/signin.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <?php
      
    

error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set("America/Costa_Rica");

/** Include PHPExcel */
require_once ("Classes/PHPExcel.php");
include("db.php");


if(isset($_REQUEST['pedidos']) && !empty($_REQUEST['pedidos'])){
          $pedido = $_REQUEST['pedidos'];          
}


$csv_end = "\n";  
$csv_sep = ";";  
$csv_file = "reportes/estudio_pedido.csv";  
$csv="";  
$csv.="Id".$csv_sep."Codigo Tai".$csv_sep."Codigo".$csv_sep."Tecnica".$csv_sep."Descripcion".$csv_sep.'Cantidad'.$csv_end;
// Create new PHPExcel object
 $sql_pedido="SELECT codigo,cantidad,pedido,
        substring(codigo,1,1),substring(codigo,6,3 ),substring(codigo,10,15),Case substring(codigo, 1, 1)
        WHEN '0' then substring(codigo,1,1)+substring(codigo,5,4 )+substring(codigo,9,4 )+' '+substring(codigo,1,4 )+' '+ltrim(rtrim(substring(codigo,13,12 )))
        WHEN '1' then substring(codigo,1,1)+substring(codigo,5,4 )+substring(codigo,9,4 )+' '+substring(codigo,1,4 )+' '+ltrim(rtrim(substring(codigo,13,12 )))
        Else codigo END As Filtro FROM hacer_pedido WHERE pedido='".$pedido."' ORDER BY Filtro";


//echo $sql_pedido;

if(($r_pedido = odbc_exec($id,$sql_pedido)) === false)
        die("Error en Select: ". odbc_errormsg($id));
$i=4; 
$h=1;
    
while($row = odbc_fetch_array($r_pedido)){
      $p_pedido=$row['pedido'];
      $p_codigo=$row['codigo'];
      $p_cantidad=$row['cantidad'];
      $largo=strlen($row['codigo']);
      $inicio=substr($row['codigo'],0,1);
      $final =substr($row['codigo'], -2,2);
      if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
        $p_min_pedido=substr($row['codigo'],1,$largo-3);  
      }else{
        $p_min_pedido=substr($row['codigo'],1);  
      }
      

      $sql="SELECT * FROM inventario WHERE codigo like '$p_codigo%'";    
      $descripcion='CODIGO NUEVO';
      $tecnico='';
      if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($resultado)){
        $descripcion=$arr["DESCRIPCION"];
        $tecnico='';
        $tecnica=$arr['DESC_ALT'];
        $tecnico_inicio=strpos($tecnica,'(');
        $tecnico_fin=  strpos($tecnica,')');
        $tecnico=$arr['DESC_ALT'];
        //$tecnico=substr($tecnica,$tecnico_inicio,$tecnico_fin);
        if ($tecnico==null) {
          $tecnico='';
        }

      }
      //echo $p_codigo;
      $csv.=$h.$csv_sep.$p_codigo.$csv_sep.$p_min_pedido.$csv_sep.$tecnico.$csv_sep.$descripcion.$csv_sep.$p_cantidad.$csv_end;
			$i++;$h++;
	  

}

$i=$i-1;


$callStartTime = microtime(true);
if (!$handle = fopen($csv_file, "w")) {  
    echo "Cannot open file";  
    exit;  
}  
if (fwrite($handle, utf8_decode($csv)) === FALSE) {  
    echo "Cannot write to file";  
    exit;  
}  
fclose($handle);

$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
echo '<div id="myProgress">
  <div id="myBar">
  <div id="label">10%</div></div>
</div>';

echo '<br>';
echo '<div id="botones" style="display:none;">';
echo '<a href="consultas_pedidos.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
echo '<a href="reportes/estudio_pedido.csv"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
echo '</div>';

?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // S_Stock';
    var elem = document.getElementById("myBar");
    var width = 1;
    var id = setInterval(frame, 150);
    function frame() {
      if (width >= 100) {
        clearInterval(id);
        document.getElementById('myProgress').style.display = 'none';
        document.getElementById('botones').style.display = 'block';
      } else {
        width++;
        elem.style.width = width + '%';
        document.getElementById("label").innerHTML = width * 1  + '%';
      }
    }
</script> 
  
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pedidos';
</script> 
  <script src="js/bootstrap.min.js"></script> 
</body>
</html>