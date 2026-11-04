<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    include "menu.php";

if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $Date1 = $_REQUEST['Date1'];        
} 

if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
  $Date2 = $_REQUEST['Date2'];
}


?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de consulta</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <br>
    <a href="fechas_ventas_mes.php"><img width="48"  height="48" src= "img/back.png">Volver</a> 
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Totales</a>

  <?php

  

    require_once("db.php");
    //Total Inventario suma
    $sql_ti="select sum(costo*existencia) as total from inventario where ubicacion NOT LIKE '9%'" ; 
    if(($resultado = odbc_exec($id,$sql_ti)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $ti=$arr["total"];     

    }
    //Total conteo items de piezas
    $sql_tti="select count(codigo) as total from inventario" ; 
    if(($resultadotti = odbc_exec($id,$sql_tti)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultadotti)){
      $tti=$arr["total"];     

    }
    // Total Conteo existencia de piezas
    $sql_tic="select sum(existencia) as total from inventario" ; 
    if(($resultadoc = odbc_exec($id,$sql_tic)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultadoc)){
      $tic=$arr["total"];     

    }
    // Total inventario Importado
    $sql_tii="select sum(costo*existencia) as total from inventario where 
    (ubicacion NOT LIKE '9%' )AND
    (codigo like '%GR' 
    or codigo like '%EU' 
    or codigo like '%JP' 
    or codigo like '%TW'
    or codigo like '%ZL')" ; 
    
    if(($resultadotii = odbc_exec($id,$sql_tii)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_tii = odbc_fetch_array($resultadotii)){
      $tii=$arr_tii["total"];     
      $til=$ti-$tii;
    }

    // Total inventario pegas
    $sql_tp="select sum(costo*existencia) as total from inventario where 
    ubicacion LIKE '9%' OR ubicacion ='CASITA'" ; 
    
    if(($resultadotp= odbc_exec($id,$sql_tp)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_tp = odbc_fetch_array($resultadotp)){
      $tp=$arr_tp["total"];     

    }
?>
 </div>
  <div id="muestra">
    <center><h1>Totales de inventario</h1>
      <?php
        echo '<center><table border =1>
          <tr class="encabezadoRegistro">
          <th WIDTH=100>Marca</th>
          <th WIDTH=150>Total</th>
          <th WIDTH=150>Importado</th>
          <th WIDTH=150>Local</th>
          <th WIDTH=150>Pegas</th>
          <th WIDTH=150>existencia</th>
          <th WIDTH=150>Items</th>';
        echo '</tr>'; 
        echo "<td><b>Total</td><td><b>".number_format($ti,2). "</td><td><b>".number_format($tii,2)."</td><td><b>".number_format($til,2). "</td><td><b>".number_format($tp,2)."</td><td><b>".number_format($tic,2). "</td><td><b>".number_format($tti,2). "</td>";
        echo '</tr>'; 
        

///*************** aqui hay que hacerlo dinamico ****
$sql_marcas = "SELECT * FROM Marcas";

if(($res_marcas = odbc_exec($id,$sql_marcas)) === false)
    die("Error en Select: ". odbc_errormsg($id));

      while($arr_marcas = odbc_fetch_array($res_marcas)){
        $marca=$arr_marcas['codigo'];
        $descripcion=$arr_marcas['descripcion'];
        $numero=$arr_marcas['numero'];  
        
        $sql_m="select sum(costo*existencia) as total from inventario where 
                MARCA ='".$marca."' and ubicacion NOT LIKE '9%'" ; 
    
                if(($res_m = odbc_exec($id,$sql_m)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
                  $total=0;
                  while($arr_m = odbc_fetch_array($res_m)){
                    $total=$arr_m["total"];     
                  }
        
        $sql_ti="select sum(costo*existencia) as total from inventario where 
                (ubicacion NOT LIKE '9%' ) AND MARCA = '".$marca."' AND (codigo like '%GR' 
                or codigo like '%EU' or codigo like '%JP' or codigo like '%TW'
                or codigo like '%ZL')" ; 
    
                if(($res_ti = odbc_exec($id,$sql_ti)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
                $total_i=0;
                $total_l=0;
                while($arr_ti = odbc_fetch_array($res_ti)){
                  $total_i=$arr_ti["total"];     
                  $total_l=$total-$total_i;
                }
                            
        $sql_e="select sum(existencia) as total from inventario where MARCA = '".$marca."'" ; 
                if(($res_e = odbc_exec($id,$sql_e)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
                $existencia=0;
                while($arr_e = odbc_fetch_array($res_e)){
                  $existencia=$arr_e["total"];     
                }
    
        $sql_c="select count(codigo) as total from inventario where MARCA = '".$marca."'" ; 
                if(($res_c = odbc_exec($id,$sql_c)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
                $cantidad=0;
                while($arr_c = odbc_fetch_array($res_c)){
                  $cantidad=$arr_c["total"];     
                }

        

        $sql_pegas="select sum(costo*existencia) as total from inventario where 
              MARCA = '".$marca."' and ubicacion LIKE '9%' or MARCA='".$marca."' and ubicacion ='CASITA'" ; 
    
                if(($res_pegas= odbc_exec($id,$sql_pegas)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
                $pegas=0;
                while($arr_pegas = odbc_fetch_array($res_pegas)){
                  $pegas=$arr_pegas["total"];     
                }

  
        echo "<td><b>".$descripcion."</td><td>".number_format($total,2). "</td><td>".number_format($total_i,2)."</td><td>".number_format($total_l,2). "</td><td>".number_format($pegas,2)."</td><td>".number_format($existencia,2). "</td><td>".number_format($cantidad,2). "</td>";
        echo '</tr>'; 
        }
       ?>
    </center>    
  </div>  
    
<script type="text/javascript">
function mostrar(){
document.getElementById('muestra').style.display = 'block';}
</script>
<script type="text/javascript">
function ocultar(){
document.getElementById('muestra').style.display = 'none';}
</script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Total Inventario';
</script>
<script src="js/bootstrap.min.js"></script>


</body>
</html>