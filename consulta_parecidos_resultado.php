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

      // Borra la tabla TMP
      $sql_delete = "DELETE FROM tmp_codigos";
      
      if(($res = odbc_exec($id,$sql_delete)) === false)
        die("Error en Select: ". odbc_errormsg($id));


      // Hace selecciona todos los codigos    
      $sql="SELECT * FROM inventario ";

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

        while($arr = odbc_fetch_array($resultado)){
          $codigos=$arr["CODIGO"];
          $codigos1=$codigos;

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

        // Compara Inventario, con importados  
        $sql2="SELECT * FROM inventario WHERE CODIGO like '$codigos1%' AND CODIGO != '$codigos'";
        
          if(($resultado2 = odbc_exec($id,$sql2)) === false)
              die("Error en Select: ". odbc_errormsg($id));

              while($arr_tmp = odbc_fetch_array($resultado2)){
                  $codigo_r=$arr_tmp["CODIGO"];
                  
                  $sqlinserttmp="INSERT INTO tmp_codigos (Codigo) values ('".$codigo_r."')";
                  
                  if(($r_tmp = odbc_exec($id,$sqlinserttmp)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
              }
        }          
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
              <th>UM</th>';
              
      echo '</tr>';   

      //ingresa datos en cuadro
      
        $sql3="SELECT DISTINCT Codigo FROM tmp_codigos ORDER BY Codigo";

          if(($resultado3 = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));
              $conteo=1;
              while($arr_tmp = odbc_fetch_array($resultado3)){
                $codigo_tmp=$arr_tmp["Codigo"];
                // LLenar datos
                $sql4="SELECT * FROM inventario WHERE CODIGO = '$codigo_tmp'";
                  if(($resultado4 = odbc_exec($id,$sql4)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
                    while($arr_fin = odbc_fetch_array($resultado4)){
                        $descripcion=$arr_fin["DESCRIPCION"];
                        $venta=$arr_fin["VENTA"];
                        $existencia=$arr_fin["EXISTENCIA"];
                        $costo=$arr_fin["COSTO"];
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
                        echo '</tr>';
                        $conteo++;
                    }
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