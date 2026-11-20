<?php
session_start();
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");
$cuenta='';
if(isset($_REQUEST['fini']) && !empty($_REQUEST['fini'])){
          $fini = $_REQUEST['fini'];
}
if(isset($_REQUEST['ffin']) && !empty($_REQUEST['ffin'])){
    $ffin = $_REQUEST['ffin'];
}
if(isset($_REQUEST['tipor']) && !empty($_REQUEST['tipor'])){
    $tipor = $_REQUEST['tipor'];
}


$sql="SELECT * FROM tbl_Cuentas WHERE Descripcion ='TAICOSTA'";
$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
foreach ($respuesta as $key => $arr) {
    $cuenta=$arr['Cuenta'];
    $cuenta=trim($cuenta);
    $url="reporte_excel.php?fini=".$fini."&ffin=".$ffin."&tipor=".$tipor."&cuenta=".$cuenta;
  //  header("Location: $url");
  ?>
    <script type="text/javascript">
        window.open('<?php echo $url;?>', '_blank');
    </script>
  <?php
}
?>
 <SCRIPT LANGUAGE="javascript"> // Redirecciona al home
      function closed() {
        window.open('','_parent','');
        window.close();
      }
     setTimeout(closed(),500);
 </SCRIPT>
