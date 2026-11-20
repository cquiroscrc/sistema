<?php 
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");
if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
      $factura = $_REQUEST['factura'];      
}
$consulta="SELECT * FROM DOC_ELECTRONICO_PROCESADO WHERE NUMERO_DOC = '$factura'";
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);
foreach ($respuesta as $key => $value) {
 
 $pdfContent=$value["PDF"];
}

header('Content-type: application/pdf');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Disposition: inline;filename=$factura".".pdf");
header("Content-length: ".strlen($pdfContent));
die($pdfContent);

 ?>