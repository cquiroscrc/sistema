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
      <title>Resultado de consulta</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      
      <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <br>
    <a href="pase_consulta_simple.php"><img width="48"  height="48" src= "img/back.png">Volver</a>    
    <center><h1>Consulta de codigos Origen</h1>
    <div class ="container">
     
    <?php

  

    require_once("db.php");

    $cod="XX";
    $familia=2;
    
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
    $suge=0;
    if ($codigo != ""){
    	$reglon=1;
      
       //// *************** Incluye la consulta ******************
      include "consulta_resultado.php";
      //// *************** Incluye la consulta ******************
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
        //echo $cod; echo '<br>';
        //echo substr($codigo,0,1);
        //echo $sql; exit;
        echo "<h1>".$codigo."</h1>";     
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Venta</th>
                    <th>Costo</th>
                    <th>Transito</th>
                    <th>Ubicacion</th>';
        
            echo '</tr>';
        while($arr = odbc_fetch_array($resultado)){
       

      //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $existencia=$arr["EXISTENCIA"];
          $costo=$arr["COSTO"];
          $venta=$arr["VENTA"];
          $costodol=$arr["costodol"];
          $proveedor=$arr["proveedor"];
          $ubicacion=$arr["ubicacion"];
          $desc_tecnica=$arr["DESC_ALT"];

          
            echo '<td><a href="pase_consulta_simple_movimientos.php?codigo='.$codigos.'&sele='.$codigo.'">'.$codigos.'</a></td>';
            echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
            echo '<td>'.number_format($existencia,2).'</td></td><td style="color:green"><b>'.number_format(($venta+ 100),2).'</b></td><td style="color:red">'.number_format($costo,2).'</td>';
          
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
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
          echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$ped_totales.'">'.number_format($pedidos,2).'</a></td>';
          
          echo '<td>'.$ubicacion.'</td></tr>';

         $reglon++;
          			
        }

    }

    ?>

    </div>
    
    
    </center>
    
   
    
    <script>// poner Hora
      var myVar = setInterval(function(){myTimer()},1000);
      function myTimer() {
      var d = new Date();
      document.getElementById("demo").innerHTML = d.toLocaleTimeString();
      }
    </script>
  <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Pase Consulta';
</script> 
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>