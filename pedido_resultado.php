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
      <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
  



  <body>
    <br>
    
     
    <?php
    
    if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
          $factura = $_REQUEST['fact'];      
    } 
    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
          $proveedor = $_REQUEST['proveedor'];      
    }



    echo '<a href="pedido_mostrar.php?fact='.$factura.'&proveedor='.$proveedor.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
    
    echo '<center><h1>Consulta de codigos</h1>';

  

    require_once("db.php");
    $cod="XX";
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

    echo '<h1>'.$codigo.'</h1>';
    echo '<div class ="container">';

    $sql5="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad=5";
          if(($prom2 = odbc_exec($id,$sql5)) === false)
              die("Error en Select: ". odbc_errormsg($id));
          while($arr5 = odbc_fetch_array($prom2)){
              $tiempo=$arr5["Valor"];
            }


    //echo $codigo; exit;
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
                    <th>Imagen</th>
                    <th>Add</th>';
        
            echo '</tr>';
        while($arr = odbc_fetch_array($resultado)){
       

      //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $desc_tecnica=$arr["DESC_ALT"];
          $existencia=$arr["EXISTENCIA"];
          $costo=$arr["COSTO"];
          $venta=$arr["VENTA"];
          $costodol=$arr["costodol"];
          $proveedores=$arr["proveedor"];


          
            echo '<td><a href="pedido_movimientos.php?codigo='.$codigos.'&sele='.$codigo.'&fact='.$factura.'&proveedor='.$proveedores.'">'.$codigos.'</a></td>';
            echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
            echo '<td style="color:red">'.number_format($existencia,2).'</td><td>'.number_format($costo,2).
                 '</td><td>'.number_format(($venta+ 100),2).'</td><td style="color:red">'.number_format($costodol,2).'</td>';
            


           
          $sql4="SELECT codigo, shortname FROM Proveedores WHERE codigo='$proveedores'";

          if(($prov = odbc_exec($id,$sql4)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            $nombre_prove="Local";
            while($arr4 = odbc_fetch_array($prov)){
              $nombre_prove=$arr4["shortname"];
            }

          
          echo '<td>'.$nombre_prove.'</td>';

          $codigobusq =$codigos;
          $promedio=0;
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
            }

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
          $suge=$promedio*(int)$tiempo;  
          
          echo '<td>'.number_format($promedio,2).'</td>';  
          echo'<td>'.number_format($suge,2).'</td>';
          $suge = $suge- $existencia-$pedidos;
          echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$ped_totales.'">'.number_format($pedidos,2).'</a></td>'; 

          
                    
          

          
          echo '<td style="color:blue">'.number_format($suge,2).'</td>';
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
          
          echo '<td><a href="pedido_agregar.php?codigo='.$codigos.'&sele='.$codigo.'&fact='.$factura.'&proveedor='.$proveedor.'"><img width="32"  height="32" src= "img/add.png"></a></td></tr>';

         $reglon++;
          			
        }

    }

    ?>

    </div>
    
    
    </center>

 
    
<script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>   
    
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pedido Consulta';
</script>   
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>