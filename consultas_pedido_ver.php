<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    include "menu.php";
    if(isset($_REQUEST['pedidos']) && !empty($_REQUEST['pedidos'])){
          $pedido = $_REQUEST['pedidos'];
          
       }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de Pedido</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> 
      <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script>
      <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
   
    <br>
    <a  href="consultas_pedidos.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
 <?php
     echo '<a href="consultas_pedido_imprime.php?pedidos='.$pedido.'"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>';
  ?>
    
    <center><h1>Ver Pedido <?php echo $pedido ?></h1>
    <div class ="container">
     
    <?php

  

    require_once("db.php");
    
    
      
        

      //ingresa nombres de cuadro
        //echo $codigo;
        //echo substr($codigo,0,1);
        //echo $sql;
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th>Item</th>
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Cantidad</th>
                    <th>Foto</th>
                    <th>Eliminar</th>';
        
        echo '</tr>';

        


        //descripcion de producto
        
        
          $sql_pedido="SELECT * FROM hacer_pedido WHERE pedido='$pedido'";
          
          if(($r_pedido = odbc_exec($id,$sql_pedido)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    
          $fila=1;
          while($row = odbc_fetch_array($r_pedido)){
                  $p_pedido=$row['pedido'];
                  $p_codigo=$row['codigo'];
                  $p_cantidad=$row['cantidad'];
                  $p_id=$row['id'];
                  $descripcion='CODIGO NUEVO';
                  $sql="SELECT * FROM inventario WHERE codigo like '$p_codigo%' order by codigo";    
    
                  if(($resultado = odbc_exec($id,$sql)) === false)
                        die("Error en Select: ". odbc_errormsg($id));

                  while($arr = odbc_fetch_array($resultado)){
                    
                    $descripcion=$arr["DESCRIPCION"];
                  }
                  echo '<td>'.$fila.'</td>';
                  echo '<td>'.$p_codigo.'</td>';
                  echo '<td>'.$descripcion.'</td>';
                  echo '<td>'.number_format($p_cantidad,2).'</td>';
                  $path='subida_cat/';

                    if (substr($p_codigo,0,1)==1) {
                      $directorio='1/';
                    }
                    if (substr($p_codigo,0,1)==2) {
                      $directorio='2/';
                    }
                    if (substr($p_codigo,0,1)==3) {
                      $directorio='3/';
                    }
                    if (substr($p_codigo,0,1)==9) {
                      $directorio='9/';
                    }
                    if (substr($p_codigo,0,1)==0) {
                      $directorio='0/';
                    }
                    $codigos1=rtrim($p_codigo);
                    if (substr($p_codigo,-2)=='EU') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                       
                    } 
                    if (substr($p_codigo,-2)=='GR') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                    }
                    if (substr($p_codigo,-2)=='TW') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                       
                    }
                    if (substr($p_codigo,-2)=='JP') {
                       $codigos1=substr($p_codigo,0,-2);
                       $codigos1=rtrim($codigos1);
                    }
                    if (substr($p_codigo,-2)=='ZL') {
                       $codigos1=substr($p_codigo,0,-2);
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

                    echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$p_codigo.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';

          

                  if ($acceso=='caja') {
                      
                  }else{

                  if ($acceso=='ventas') {
                //nada
                  }else{
                      echo '<td><a href="consultas_pedido_eliminar.php?codigo='.$p_codigo.'&pedido='.$p_pedido.'&id='.$p_id.'"><img width="32"  height="32" src= "img/eliminar.png"></a></td>';
                  }
                  } 

                  //<a class="agregarUsuario" href="" onClick="window.print();"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>  
                  echo '</tr>';  
                  $fila++;
          }
       echo '</table>';   


         

    ?>

    </div>
    
    
    </center>
    
   <a href="consultas_pedidos.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
  <?php
     echo '<a href="consultas_pedido_imprime.php?pedidos='.$pedido.'"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>';
  ?>   
   <script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>
    <script>// poner Hora
      var myVar = setInterval(function(){myTimer()},1000);
      function myTimer() {
      var d = new Date();
      document.getElementById("demo").innerHTML = d.toLocaleTimeString();
      }
    </script>
    <script type="text/javascript" language="JavaScript">
          document.title = 'Sistema / Consulta Transito';
        </script>
    <script src="js/bootstrap.min.js"></script> 
  </body>
</html>