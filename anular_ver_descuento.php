<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
include "menu.php";
?>
<!DOCTYPE html> 
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    <title>Ver Factura</title>
  
    
    <link rel="stylesheet" href="css/anular.css">
    <link rel="icon" href="favicon.ico">
  </head>
  <body>
    <style type="text/css">
 
  .botones{
    width:850px;
    height: 75px;
    padding:10px;
    background-color:#eee;
    border:1px solid #444;
  }
  .drecibos{
    width:300px;
    clear:left;
    float:left;
    border:1px solid #444;
    
  }
  .degresos{
    width:550px;
   
    float:left;
    border:1px solid #444;
    
  }
  .ddepositos{
    width:450px;
    float:left;
    border:1px solid #444;
  }
  #footer {
position: relative;
margin-top: -50px; /* altura en px del footer con valor negativo */
height: 50px; /* Altura del Footer en px*/
clear: both;
text-align: center;
color: #FFFFFF;
}
  </style>
    
    <?php
      require_once("db.php");
        $fecha=0;
        $subtotal=0;
        $impuesto=0;
        $descuento=0;
        $total=0;
        $tipo=0;
        $estado=0;
        $proveedor=0;
        $vendedor=0;
        $mensajeria=0;

      if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
        
        $documento = $_REQUEST['documento'];
      } 

      $sql="SELECT * FROM tbl_Egresos WHERE Documento ='$documento'";  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($res)){
        $fecha=$arr['Fecha'];
        $monto=$arr['Monto'];
        $descripcion=$arr['Descripcion'];
      }

      //Datos de factura
      echo '<br>';
      echo '<center>';
      echo '<table>';
      echo '<td>';
      echo '<h1>Datos del descuento : NC-'.$documento.'</h1>';
      echo '</td></tr>';
      echo '<td>';
      echo '<h1>A nombre de: '.$descripcion.'</h1>';
      echo '</td></tr>';
      echo '<td>';
      echo '<h1>Monto: '.number_format($monto,2).'</h1>';
      echo '</td></tr>';
      echo '<td>';
      echo '<h1>Fecha: '.$fecha.'</h1>';
      echo '</td></tr></table>';
      // se cargan los datos de la factura
     
      
      
      
      $estado=trim($estado);
      if ($estado=='ANULADA') {
        
      }else{
        echo '<div>';
        echo '<br>';
        echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea anular el descuento?\")) 
        location.href=\"anular_anular_desc.php?documento=".$documento."&id_acceso=".$id_acceso."\";
        '><img width='48'  height='48' src= 'img/eliminar.png'>ANULAR DESCUENTO</a></td>";          
        echo '</div>';  
      }
      
    ?>
  <script src="js/jquery.js"></script>
  <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Anular Desc';
</script> 
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>