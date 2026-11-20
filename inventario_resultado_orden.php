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
      <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> 
      <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script>
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="img/favicon.ico">
  

  <script>  /// devolverse
    $(document).keydown(function(tecla){ 
              if (tecla.keyCode == 37) { 
                window.location="inventario_consulta.php";
              }
          });
  </script>  
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
   
    //
     echo '<a href="inventario_consulta.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';
    ?>  
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>
    

    
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
      $cod = '6'.$_REQUEST['codigodes'];
      $familia=0;
    }  
    
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

    ?>
    <center><h1>Consulta de codigos para Inventario <?php echo $codigo ?></h1>
    <div class ="container">
    <div id="muestra"> 

    <?php 

    
    
    if ($familia==1) {
      $codigo='1___ '.$codigo;

    }
    //echo $codigo; exit;
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
      elseif (substr($codigo,0,1)==4) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==9) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }elseif (substr($codigo,0,1)==6) {
            $fam=substr($codigo,1,1);
            $des=substr($codigo,3,strlen($codigo));
           $sql="SELECT * FROM inventario WHERE codigo like '$fam%' 
           AND descripcion like '%$des%' OR DESC_ALT like '%$des%' AND codigo like '$fam%' order by descripcion";    
      }

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
        //echo $codigo;
        //echo substr($codigo,0,1);
        //echo $sql; exit;
        //echo'<a name="inicio" id="a">';
        
        
        echo '<center><table border =1>
                <tr class="encabezadoRegistro">
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Prom Mens</th>
                    <th>Transito</th>
                    <th>Venta</th>
                    <th>Costo</th>
                    <th>$</th>
                    <th>Proveedor</th>
                    <th>Ubicacion</th>
                    <th>Foto</th>';
        
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
          $Ubicacion=$arr["ubicacion"];

          $sql_pedido="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
          if(($pedi = odbc_exec($id,$sql_pedido)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=$arr3["pedido"];
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
          
            }
         
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigobusq'";
          if(($pedi = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=$arr3["pedido"];
            }
          
          
          
          $existe='no';
          $esta_pedido=0;
          $sql_pedido = "SELECT codigo, cantidad FROM hacer_pedido where codigo='$codigos'";

                if(($r_pedido = odbc_exec($id,$sql_pedido)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
                
                
                    while($row = odbc_fetch_array($r_pedido)){

                      $existe='si';
                      $esta_pedido= $row['cantidad'];

                    }
            
            
           

            
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

              
              echo '<td><a href="inventario_resultado_movimientos.php?codigo='.$codigos.'&sele='.$codigo.'">'.$codigos.'</a></td>';
              echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
              echo '<td><b>'.number_format($existencia,2).'</td>';
              echo '<td style="color:blue">'.number_format($promedio,2).'</td>'; 
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
          echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$ped_totales.'">'.number_format($pedidos,2).'</a></td>';


              //echo '<td>'.number_format($pedidos,2).'</td>';
              echo '<td>'.number_format($venta,2).'</td>';
              echo '<td style="color:green">'.number_format($costo,2).'</td>';
              echo '<td style="color:red">'.number_format($costodol,2).'</td>';
              echo '<td style="color:blue"><b>'.$nombre_prove.'</b></td>';
              
              echo '<td><b>'.$Ubicacion.'</td>';
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

              echo '<tr>';

              $reglon++;
              $contador++;
                
                
            
            
        }


    }
    echo '</table>';
    echo '</center>';

    
    ?>

    </div>
    <div ALIGN=left >
    </div>
   
    </div>
    


    echo '</table>';
    echo '</center>';

    
    ?>

    </div>
    <div ALIGN=left >
    </div>
   
    </div>
    
<script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>    
    
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Estudio';
</script>
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>
