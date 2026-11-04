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
    <link rel="icon" href="img/favicon.ico">
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
        //Resive el codigo de documento y le da formato
        $documento ='F-'.sprintf("%014s",   $documento);
      } 
      
      $sql="SELECT * FROM hfacturas WHERE nfac ='$documento'";  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($res)){
        $documento= $arr['nfac'];
        $fecha=$arr['fechafact'];
        $subtotal=$arr['subtotal'];
        $impuesto=$arr['impuesto'];
        $descuento=$arr['descuento'];
        $total=$arr['total'];
        $tipo=$arr['Tipo'];
        $estado=$arr['estado'];
        $proveedor=$arr['proveedor'];
        $vendedor=$arr['vendedor'];
        $nombre=$arr['Nombre'];
        $adicional=$arr['Adicional'];
        $parcial=$subtotal-$descuento+$impuesto;
        $mensajeria=$total-$parcial;

      }

      //Craga los datos de factura
      echo '<br>';
      echo '<center>';
      echo '<h1>Datos de la factura :'.$documento.'</h1>';
      echo '<h1>'.$nombre.'</h1>';
      echo '<h1>'.$adicional.'</h1>';
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<td><b>Codigo</b></td> ";
      echo "<td><b>Cantidad</b></td>";
      echo "<td><b>Descripcion</b></td> ";
      echo "<td><b>Precio</b></td> ";
      echo "<td><b>Tipo</b></td> ";
      echo "</tr> ";
      // se cargan los datos de la factura
     
      $sqldatos="SELECT h.codigo, h.cantidad, h.precio, h.tipo, i.descripcion FROM hgeneral h, inventario i 
      WHERE Factura ='$documento' and h.codigo = i.codigo";  

      if(($res_datos = odbc_exec($id,$sqldatos)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr_d = odbc_fetch_array($res_datos)){
        echo "<tr> ";
        echo '<td>'.$arr_d['codigo'].'</td>';
        echo '<td>'.$arr_d['cantidad'].'</td>';
        echo '<td>'.$arr_d['descripcion'].'</td>';
        echo '<td>'.number_format($arr_d['precio'],2).'</td>';
        echo '<td>'.$arr_d['tipo'].'</td>';
        echo '</tr>';
      }

      echo '</table>'; 

      echo '<h1>Totales de factura :'.$documento.'</h1>';
     
      echo '<center>';
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<td><b>Fecha</b></td> ";
      echo "<td><b>Subtotal</b></td>";
      echo "<td><b>Descuento</b></td>";
      echo "<td><b>Impuesto</b></td> ";
      echo "<td><b>Mensajeria</b></td> ";
      echo "<td><b>Total</b></td> ";
      echo "<td><b>Vendedor</b></td> ";
      echo "<td><b>Cliente</b></td> ";
      echo "<td><b>Estado</b></td> ";
      echo "</tr> ";
      echo "<tr> ";
      echo '<td>'.$fecha.'</td>';
      echo '<td>'.number_format($subtotal,2).'</td>';
      echo '<td>'.number_format($descuento,2).'</td> ';
      echo '<td>'.number_format($impuesto,2).'</td>';
      echo '<td>'.number_format($mensajeria,2).'</td>';
      echo '<td>'.number_format($total,2).'</td>';
      echo '<td>'.$vendedor.'</td>';
      echo '<td>'.$proveedor.'</td>';
      echo '<td>'.$estado.'</td>';
      echo '</tr>';
      echo '</table>';

      // Si esta anulada no deja volverla anular
      $estado=trim($estado);
      if ($estado=='ANULADA') {
        
      }else{
        echo '<div>';
        echo '<br>';
        // Inicia proseso de anulacion.
        echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Hacer la nocta de credito de la factura?\")) 
        location.href=\"anular_proceso.php?consecutivo=".$documento."\";
        '><img width='48'  height='48' src= 'img/devolucion.png'>NOTA DE CREDITO</a></td>";          
        echo '</div>';  
       
      }
      
    ?>
  <script src="js/jquery.js"></script>
<script type="text/javascript" language="JavaScript">
      document.title = 'Sistema / Anular';
  </script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>