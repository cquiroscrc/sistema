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
      echo '<a href="pedir_stock_consulta.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';
    ?>  

    <center><h1>Consulta de codigos</h1>
    <div class ="container">
     
    <?php

  

    require_once("db.php");
    $cod="XX";

    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $cod = $_REQUEST['codigo'];
      $familia=0;
    }

    if(isset($_REQUEST['codigovw']) && !empty($_REQUEST['codigovw'])){
      $cod = $_REQUEST['codigovw'];
      $familia=0;
    }
    if(isset($_REQUEST['codigovwn']) && !empty($_REQUEST['codigovwn'])){
      $cod = $_REQUEST['codigovwn'];
      $familia=0;
    }
    if(isset($_REQUEST['codigovwf']) && !empty($_REQUEST['codigovwf'])){
      $cod = $_REQUEST['codigovwf'];
      $familia=1;
    }
    if(isset($_REQUEST['codigod']) && !empty($_REQUEST['codigod'])){
      $cod = $_REQUEST['codigod'];
      $familia=0;
    }
    if(isset($_REQUEST['codigoa']) && !empty($_REQUEST['codigoa'])){
      $cod = $_REQUEST['codigoa'];
      $familia=0;
    }
    if(isset($_REQUEST['codigov']) && !empty($_REQUEST['codigov'])){
      $cod = $_REQUEST['codigov'];
      $familia=0;
    }
    if(isset($_REQUEST['codigou']) && !empty($_REQUEST['codigou'])){
      $cod = $_REQUEST['codigou'];
      $familia=0;
    }
    if(isset($_REQUEST['codigodes']) && !empty($_REQUEST['codigodes'])){
      $cod = '8'.$_REQUEST['codigodes'];
      $familia=0;
    }  
    
    if (substr($cod,0,5)=='1___ ') {
      $codigo=$cod;
    }else{
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
    echo '<a href="pedir_stock_imprime.php?codigo='.$codigo.'"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>';

    $sql5="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad=5";

    if(($prom2 = odbc_exec($id,$sql5)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    while($arr5 = odbc_fetch_array($prom2)){
        $tiempo=$arr5["Valor"];
    }

    $suge=0;
    if ($codigo != ""){
    	$reglon=1;
      
      if (substr($codigo,0,1)==1 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==0 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==2) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==3) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==9) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }elseif (substr($codigo,0,1)==8) {
            $fam=substr($codigo,1,1);
            $des=substr($codigo,3,strlen($codigo));
           $sql="SELECT * FROM inventario WHERE codigo like '$fam%' 
           AND descripcion like '%$des%' order by descripcion";    
      }

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
        //echo $codigo;
        //echo substr($codigo,0,1);
        //echo $sql; exit;
        //echo'<a name="inicio" id="a">';
        
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
                    <th>Sugerido</th>';
        
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
          $existencia=$arr["EXISTENCIA"];
          $costo=$arr["COSTO"];
          $venta=$arr["VENTA"];
          $costodol=$arr["costodol"];
          $proveedor=$arr["proveedor"];
          
         
          
            

           
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
              substring(factura,1,1)='F' AND 
              FECHA >= Dateadd(mm,-6,Getdate())AND  
              FECHA <= GETDATE()";
  
          if(($prom = odbc_exec($id,$sql2)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
            while($arr2 = odbc_fetch_array($prom)){
              $promedio=$arr2["promedio"];
          
            }
         
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigobusq'";
          if(($pedi = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=$arr3["pedido"];
            }
          
          
            
            
           $promedio2=$promedio*2;
           $total=$existencia+$pedidos;
            
            //echo 'NO PEDIR '.$nopedir. ' Suge ='.$suge;
            

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
              $suge = ($promedio*$tiempo)- $existencia-$pedidos;  

              if ($promedio2>$total) {
               
              
                echo '<td><a style="color:black" href="pedir_stock_movimientos.php?codigo='.$codigos.'&sele='.$codigo.'&marcador='.$marcador1.'">'.$codigos.'</a></td>';
                echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
                echo '<td><b>'.number_format($existencia,2).'</td><td>'.number_format($costo,2).
                     '</td><td>'.number_format(($venta+ 100),2).'</td><td>'.number_format($costodol,2).'</td>';
                echo '<td><b>'.$nombre_prove.'</td>';
                echo '<td style="color:blue">'.number_format($promedio,2).'</td><td style="color:red">
                   '.number_format($promedio*$tiempo,2).'</td><td><b>  
                   '.number_format($pedidos,2).'</td>';          
                echo '<td style="color:red"><b>'.number_format($suge,2).'</td>';
                echo '</tr>';  
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
    
    </div>
    
    
    
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // B_Stock';
</script> 
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>
