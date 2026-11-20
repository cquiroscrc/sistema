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
      
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      
      <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="img/favicon.ico">
    
  </head>
    
  <body>
    <br>
    <a href="planilla_aguinaldos.php"><img width="48"  height="48" src= "img/back.png">Volver</a> 
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime </a>   
    <div id='muestra'>
    <center><h1>Reporte de aguinaldos</h1>
    
     
    <?php

   
  

   include("db.php");
    
    if(isset($_REQUEST['datos']) && !empty($_REQUEST['datos'])){
      $datos = $_REQUEST['datos'];
      
    }

    if(isset($_REQUEST['empleados']) && !empty($_REQUEST['empleados'])){
      $empleados = $_REQUEST['empleados'];
      
    }  
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
      $tipo = $_REQUEST['tipo'];
      
    }  

     

    if ($tipo=='parcial') {
        echo '<center><table border =1>
        <tr class="encabezadoRegistro">
        <th>Planilla</th>
        <th>Fecha</th>
        <th WIDTH=250>Empleado</th>
        <th>ID</th>
        <th>Base</th>
        <th></th>
        <th>Total</th>';
        echo '</tr>';
    }else{
        echo '<center><table border =1>
        <tr class="encabezadoRegistro">
        <th>Planilla</th>
        <th>Fecha</th>
        <th WIDTH=250>Empleado</th>
        <th>ID</th>
        <th>Base</th>
        <th>Comision</th>
        <th>T. Base</th>
        <th>Reajuste</th>
        <th>Extras</th>
        <th>T. Reajuste</th>
        <th>Total</th>';
        echo '</tr>';
    }

    
    $ano=$datos-1;
    $fecha1=$ano.'-12-01';
    $fecha2=$datos.'-11-30';
    
    $newdate=strtotime("$fecha2");
    $newdate = date ( 'Y-m-d' , $newdate );
    $fecha2=$newdate;
   
    
    $treajuste_parcial=0;
    $treajuste=0;
    $ttotal=0;
    $ttotal_parcial=0;
    $total=0;
      
    if ($empleados=='todos') {
      $sql="SELECT r.id_planilla, r.fecha, r.id_empleado, r.base, r.ccss, r.extras,r.reajuste, r.total, r.mandados, e.nombre, e.apellido, r.comision
      FROM rh_planilla r, rh_empleados e WHERE Fecha BETWEEN '$fecha1' AND
      '$fecha2' AND e.id_empleado = r.id_empleado order by r.id_empleado, r.fecha"; 
    }else{
      $sql="SELECT r.id_planilla, r.fecha, r.id_empleado, r.base, r.ccss, r.extras,r.reajuste, r.total, r.mandados, e.nombre, e.apellido, r.comision
      FROM rh_planilla r, rh_empleados e WHERE Fecha BETWEEN '$fecha1' AND
      '$fecha2' AND e.id_empleado = r.id_empleado AND r.id_empleado= '$empleados' order by r.id_empleado, r.fecha";
    }

    
    //echo $sql; 
    //exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    
    



    $empleado=0; 
    $nombre='';                       
    
      while($row = odbc_fetch_array($resultado)){
    
        if ($empleado==0) {
          $empleado=$row['id_empleado'];
          $nombre=$row['nombre'].' '.$row['apellido'];
        }


        if ($empleado!=$row['id_empleado']) {
          
          if ($tipo=='parcial') {
            echo '<th>Total</th><th></th><th>'.$nombre.'</th><th>'.$empleado.'</th><th>'.number_format($ttotal_parcial,2).'</th><th>'.number_format($ttotal_parcial,2).'</th></tr>';       
          }else{
            echo '<th>Total</th><th></th><th>'.$nombre.'</th><th>'.$empleado.'</th><th><th></th></th><th>'.number_format($ttotal_parcial,2).'</th><th></th><th></th><th>'.number_format($treajuste_parcial,2).'</th><th>'.number_format($treajuste_parcial+$ttotal_parcial,2).'</th></tr>';
          }      

          $empleado=$row['id_empleado'];
          $nombre=$row['nombre'].' '.$row['apellido'];
          $treajuste_parcial=0;
          $ttotal_parcial=0;

        } 
            
          $tbase=($row["base"]+$row["comision"])/12;
          $ttotal=($row["reajuste"]+$row["extras"]+$row["mandados"])/12;
          $ttotal_parcial=$ttotal_parcial+$tbase;
          $treajuste_parcial=$treajuste_parcial+$ttotal;
          $total=$total+$tbase;
          $treajuste=$treajuste+$ttotal;

          echo '<td>'.$row["id_planilla"].'</td>';
          echo '<td>'.date("d-m-Y", strtotime($row["fecha"])).'</td>';
          echo '<td style="color:blue">'.$row["nombre"].' '.$row["apellido"].'</td><td>'.$row["id_empleado"].'</td>';
          echo '<td>'.number_format($row["base"],2).'</td>';
          echo '<td>'.number_format($row["comision"],2).'</td>';
          echo '<td style="color:green">'.number_format($tbase,2).'</td>';
          if ($tipo=='total') {
            echo '<td>'.number_format($row["reajuste"],2).'</td>';
            echo '<td>'.number_format($row["extras"],2).'</td>';
            echo '<td style="color:red">'.number_format($ttotal,2).'</td>';
            echo '<td style="color:green"><b>'.number_format($tbase+$ttotal,2).'</b></td>';          
          }
          echo '</tr>'; 

      }
      if ($tipo=='parcial') {
            echo '<th>Total</th><th></th><th>'.$nombre.'</th><th>'.$empleado.'</th><th></th><th></th><th>'.number_format($ttotal_parcial,2).'</th></tr>';       
          }else{
            echo '<th>Total</th><th></th><th>'.$nombre.'</th><th>'.$empleado.'</th><th></th><th></th><th>'.number_format($ttotal_parcial,2).'</th><th></th><th></th><th>'.number_format($treajuste_parcial,2).'</th><th>'.number_format($treajuste_parcial+$ttotal_parcial,2).'</th></tr>';
          }      
      
    
    if ($tipo=='parcial') {
      echo '<th>Total</th><th>Firma:</th><th>______________________</th><th></th><th></th><th></th><th>'.number_format($total,2).'</th></tr>';
    }else{
      echo '<th>Total</th><th>Firma:</th><th>______________________</th><th></th><th></th><th></th><th>'.number_format($total,2).'</th><th></th><th></th><th>'.number_format($treajuste,2).'</th><th>'.number_format($treajuste+$total,2).'</th></tr>';
    }  
    
    ?>

    </div>
    
    
    </center>
    <div>
      <h3><img width="200"  height="100" src="img/navidad.png">Te envio un mundo de buenos deseos! </h3><h3>Esperamos que sea una Navidad especial y magica  </h3>

    </div>
    
   
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Aguinaldos';
</script>    
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>