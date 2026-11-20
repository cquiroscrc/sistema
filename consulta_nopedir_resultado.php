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
  <?php

  

    require_once("db.php");

      
?>

  <body>
    <br>
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>

    <div id="muestra" class ="container">
    <center><h1>Consulta de codigos Parecidos</h1>
    
    

     <?php 
      // llenado de tabla
      echo '<center><table border =1>
              <tr class="encabezadoRegistro">
              <th>ID</th>
              <th WIDTH=180>Codigo</th>
              <th WIDTH=200>Descripcion</th>
              <th>Existencia</th>
              <th>Venta</th>
              <th>Costo</th>
              <th>Promedio</th>
              <th>Transito</th>
              <th>UM</th>
              <th>Ubicacion</th>
              ';
              
      echo '</tr>';   

      //ingresa datos en cuadro
      
        $sql="SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,COSTO, DESC_ALT,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            Else CODIGO END As Filtro FROM inventario  WHERE DESC_ALT LIKE '%NO PEDIR%' ORDER BY Filtro";



          if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
              $conteo=1;
              while($arr = odbc_fetch_array($resultado)){
                $codigo_tmp=$arr["CODIGO"];
                $descripcion=$arr["DESCRIPCION"];
                $venta=$arr["VENTA"];
                $existencia=$arr["EXISTENCIA"];
                $costo=$arr["COSTO"];
                $ubicacion= $arr["ubicacion"];
                $sql_transito="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigo_tmp'";
                        if(($pedi = odbc_exec($id,$sql_transito)) === false)
                          die("Error en Select: ". odbc_errormsg($id));  

                          while($arr_transito = odbc_fetch_array($pedi)){
                            $transito=$arr_transito["pedido"];
                          }

                        $sql_prom="SELECT (SUM(cantidad)/6) as promedio from hgeneral where codigo = '$codigo_tmp' AND movimiento not in('Ajuste') AND substring(factura,1,1)='F' AND FECHA >= Dateadd(mm,-6,Getdate())AND FECHA <= GETDATE()";
  
                          if(($prom = odbc_exec($id,$sql_prom)) === false)
                              die("Error en Select: ". odbc_errormsg($id));
                            
                            while($arr_prom = odbc_fetch_array($prom)){
                              $promedio=$arr_prom["promedio"];
                            }
                        
                        $sql_um ="SELECT MAX([FECHA]) AS fecha FROM hgeneral where codigo = '$codigo_tmp' AND movimiento not in('Ajuste') AND substring(factura,1,1)='F'";

                          if(($um_r = odbc_exec($id,$sql_um)) === false)
                              die("Error en Select: ". odbc_errormsg($id));
                            
                            while($arr_um = odbc_fetch_array($um_r)){
                              $um=strtotime($arr_um['fecha']);
                              //$um=$arr_um["fecha"];
                              $um= date("d-m-Y", $um);
                            }

                        echo '<td>'.$conteo.'</td>';    
                        echo '<td>'.$codigo_tmp.'</td>';
                        echo '<td>'.$descripcion.'</td>';
                        echo '<td>'.number_format($existencia,2).'</td>';
                        echo '<td style="color:green"><b>'.number_format(($venta),2).'</b></td>';
                        echo '<td style="color:red">'.number_format($costo,2).'</td>';
                        echo '<td>'.number_format($transito,2).'</td>';
                        echo '<td>'.number_format($promedio,2).'</td>';
                        echo '<td>'.$um.'</td>';
                        echo '<td>'.$ubicacion.'</td>';
                        echo '</tr>';
                        $conteo++;
                    }
                 
         
    ?>

    </div>
    
    
    </center>
    
<script type="text/javascript" language="JavaScript">
      document.title = 'Sistema / Consulta Parecidos';
</script>   
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>