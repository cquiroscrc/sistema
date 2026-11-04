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
        echo '<br>';
        echo '<a  href="consulta_documento.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
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
        echo '<center><table border =0>
          <tr class="encabezadoRegistro">
          <th WIDTH=250>Fecha</th>
          <th WIDTH=200>Factura</th>
          <th>Movimiento</th>
          <th>Cliente</th>
          <th>Vendedor</th>
          <th>Estado</th>
          <th>Tipo</th>';
        echo '</tr>';

      if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
        //$documento='F-0000000';  
        $documento = $_REQUEST['documento'];
      } 

      $sql="SELECT DISTINCT Factura, fecha, movimiento, codcliente,vendedor,estado,tipo FROM hgeneral WHERE Factura like '%$documento%' ORDER BY fecha DESC";  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));


      while($arr = odbc_fetch_array($res)){
          
          echo '<td>'.$arr["fecha"].'</td>
          
          <td><a href="consulta_documentos_resultados.php?factura='.$arr["Factura"].'&documento='.$documento.'">'.$arr['Factura'].'</a></td>
          <td>'.$arr["movimiento"].'</td>
          <td>'.$arr["codcliente"].'</td>
          <td>'.$arr["vendedor"].'</td>
          <td>'.$arr["estado"].'</td>
          <td>'.$arr["tipo"].'</td>';
          echo '</tr>';       
      }

      //Datos de factura
     
      
    ?>
      
    
  <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ver Documento';
    </script>
  </body>
</html>