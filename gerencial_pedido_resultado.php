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
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de consulta Pedido</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
    <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script>
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
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
        width: 50px;
    }
    </style>

    <br>
    <a name="inicio" id="a">
    <?php
    if(isset($_REQUEST['s_pedido']) && !empty($_REQUEST['s_pedido'])){
      $s_pedido = $_REQUEST['s_pedido'];
    }
    //
     echo '<a href="gerencial_pedido.php?s_pedido='.$s_pedido.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
    ?>  

    <center><h1>Consulta de codigos</h1>
    <div class ="container">
     
    <?php

  

    require_once("db.php");
    //// *************** Incluye recibir ******************
      include "consulta_recibe.php";
      //// *************** Incluye recibir ******************
    
    if (substr($cod,0,5)=='1___ ') {
      $codigo=$cod;
    }
    else{
      $cant=stripos($cod,"_",0);
      if ($cant<>0) {
        $codigo= substr($cod,0,$cant);  
      }else{
        $codigo=$cod;
      }
    }
    
    $codigo= trim($codigo);
    $codigobusq = '';
    
    if ($familia==1) {
      $codigo='1___ '.$codigo;

    }
    //echo $codigo; exit;
    $sql5="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad=5";
          if(($prom2 = odbc_exec($id,$sql5)) === false)
              die("Error en Select: ". odbc_errormsg($id));
          while($arr5 = odbc_fetch_array($prom2)){
              $tiempo=(int)$arr5["Valor"];
            }

    $suge=0;
    if ($codigo != ""){
    	$reglon=1;
      
      //// *************** Incluye la consulta ******************
      include "consulta_resultado.php";
      //// *************** Incluye la consulta ******************

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
        //echo $codigo;
        //echo substr($codigo,0,1);
        //echo $sql; exit;
        //echo'<a name="inicio" id="a">';
        echo '<h2>'.$s_pedido.'</h2>';
        echo "<h3>".$codigo."</h3>";
        echo '<a href="gerencial_pedido_crear.php?s_pedido='.$s_pedido.'&codigo='.$codigo.'"><img width="48"  height="48" src= "img/agregar.png">Agregar Codigo Nuevo</a>';
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Costo</th>
                    <th>Venta</th>
                    <th>Dolar</th>
                    <th>Proveedor</th>
                    <th>Prom Mens</th>
                    <th>Prom x '.$tiempo.'</th>
                    <th>Transito</th>
                    <th>Sugerido</th>
                    <th>Pedido</th>
                    <th>Agregar</th>
                    <th>Imagen</th>';
        
            echo '</tr>';
            
            $marcador1='';
            $marcador2='inicio';
            $marcador3='inicio';
            $contador=1;

        while($arr = odbc_fetch_array($resultado)){
       

      //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $desc_tecnica=$arr["DESC_ALT"];
          $existencia=number_format($arr["EXISTENCIA"]);
          $costo=$arr["COSTO"];
          $venta=$arr["VENTA"];
          $costodol=$arr["costodol"];
          $proveedor=$arr["proveedor"];
          
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
          
            

           
          $sql4="SELECT codigo, shortname FROM Proveedores WHERE codigo='$proveedor'";

          if(($prov = odbc_exec($id,$sql4)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            $nombre_prove="Local";
            while($arr4 = odbc_fetch_array($prov)){
              $nombre_prove=$arr4["shortname"];
            }

          
          

          $codigobusq =$codigos;

         /// aqui val el valor de estudio, cantidad de meses tambien en moviemientos.php
          $sql2="SELECT (SUM(cantidad)/6) as promedio from hgeneral where codigo = '$codigobusq' 
              AND movimiento not in('Ajuste') AND 
              substring(factura,1,2)='F-' AND 
              FECHA >= Dateadd(mm,-6,Getdate())AND  
              FECHA <= GETDATE()";
  
          if(($prom = odbc_exec($id,$sql2)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
            while($arr2 = odbc_fetch_array($prom)){
              $promedio=$arr2["promedio"];
              if($promedio === NULL) {$promedio=0;}
          
            }
         
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigobusq'";
          if(($pedi = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));  
          
            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=$arr3["pedido"];
              if($pedidos === NULL) {$pedidos=0;}
            }
          
          
          
          $existe='no';
          $esta_pedido=0;
          $sql_pedido = "SELECT codigo, cantidad FROM hacer_pedido where codigo='$codigos' and pedido='$s_pedido'";


                if(($pedido_resultado = odbc_exec($id,$sql_pedido)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
    
    
                while($row = odbc_fetch_array($pedido_resultado)){
                      $existe='si';
                      $esta_pedido= $row['cantidad'];

                }
              
            $posicion=strripos($desc_tecnica, "NO PEDIR");
            
            $suge = ($promedio * $tiempo) - ($existencia-$pedidos);
            

            if ($posicion===false) {
                $nopedir=1; 
            }else{
              $nopedir=1; //// PARA NO VER LOS NO PEDIR PONER EN 0 y PARA SI PONER EN 1
              if ($suge>0.1) {
                $nopedir=1;
              }
            }
            
           

            
            //echo 'NO PEDIR '.$nopedir. ' Suge ='.$suge;
            if ($nopedir==1) {   // NO PEDIR

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

              echo '<td><a style="color:black" href="gerencial_pedido_movimientos.php?s_pedido='.$s_pedido.'&codigo='.$codigos.'&sele='.$codigo.'&marcador='.$marcador1.'">'.$codigos.'</a></td>';
              echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
              echo '<td><b>'.number_format($existencia,2).'</td><td>'.number_format($costo,2).
                   '</td><td>'.number_format(($venta+ 100),2).'</td><td>'.number_format($costodol,2).'</td>';
              echo '<td><b>'.$nombre_prove.'</td>';
              echo '<td style="color:blue">'.number_format($promedio,2).'</td><td style="color:red">
                 '.number_format($promedio*$tiempo,2).'</td>';

              /// Pedidos por separado
            $ped_totales="";
            unset($ped_factura);
            $sqlped= "SELECT p.Factura, p.Cantidad, t.shortname from pedidos p, Proveedores t where p.Proveedor=t.codigo and p.codigo = '$codigos'";
            if(($pedi2 = odbc_exec($id,$sqlped)) === false)
                die("Error en Select: ". odbc_errormsg($id)); 
            $cont=0;   
            while($arr_ped = odbc_fetch_array($pedi2)){

              $ped_factura[]=trim($arr_ped['shortname'])." / ".$arr_ped['Factura']." / ".$arr_ped['Cantidad']." *-* "; 
                $cont++;
            }
            if(isset($ped_factura)){
                if ($cont==0) {
                  $ped_totales=$ped_factura[0];
                }else{
                  for($x = 0; $x < $cont; $x++) {
                    $ped_totales=$ped_totales.$ped_factura[$x];
                  }
                }  
                
            }else{
                $ped_totales="";
            }

            //Pedidos por separado *****
          echo '<td><b><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$ped_totales.'">'.number_format($pedidos,2).'</a><b></td>';             
              $suge = ($promedio*$tiempo)- $existencia-$pedidos;
              if ($suge>0) {
                echo '<td style="color:red"><b>'.number_format($suge,2).'</td>';  
              }else{
                echo '<td style="color:green"><b>'.number_format($suge,2).'</td>';
              }    


              echo '<form action="gerencial_pedido_incluir.php" method="POST">';
              if ($esta_pedido==0) {
                echo '<td  style="color:black"><input type="text" id="sugerido"  name="sugerido" required autofocus value="'.$esta_pedido.'"></td>';    
              }else{
                echo '<td  style="color:blue"><b><input type="text" id="sugerido"  name="sugerido" required autofocus value="'.$esta_pedido.'"></td>';                
              }
              echo '<input type="hidden" id="existe"  name="existe" required autofocus value="'.$existe.'">';
              echo '<input type="hidden" id="codigo"  name="codigo" required autofocus value="'.$codigos.'">';
              echo '<input type="hidden" id="cod"  name="cod" required autofocus value="'.$codigo.'">' ;           
              echo '<input type="hidden" id="s_pedido"  name="s_pedido" required autofocus value="'.$s_pedido.'">' ;   
              echo '<input type="hidden" id="marcador"  name="marcador" required autofocus value="'.$marcador1.'">' ;           
              if ($esta_pedido==0) {
                echo '<td><button  type="submit">OK</button></td>';
              }else{
                echo '<td style="color:red"><button  type="submit">OK</button></td>';
              }
              echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';
              echo '</form>';
              echo '<tr>';

              $reglon++;
              $contador++;
                
                
            }  
            
        }


    }
    echo '</table>';
    echo '</center>';

    
    ?>

    </div>
    <div ALIGN=left >
    <?php 
    echo '<a href="gerencial_pedido.php?s_pedido='.$s_pedido.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
     ?>
    </div>
    
<script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>    
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Pedido C';
</script>    

<script src="js/bootstrap.min.js"></script> 
  </body>
</html>
 height="48" src= "img/back.png">Volver</a>';
     ?>
    </div>
    
<script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>    
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Pedido C';
</script>    

<script src="js/bootstrap.min.js"></script> 
  </body>
</html>
body>
</html>
