<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    include "menu.php";

if(isset($_REQUEST['estudio']) && !empty($_REQUEST['estudio'])){
  $estudio = $_REQUEST['estudio'];        
} 



?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.inputmask.bundle.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/anular.css">
    <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>
    <br>
    <a href="antiguedad_seleccion.php"><img width="48"  height="48" src= "img/back.png">Volver</a> 
    <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
    {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
    ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
    ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Totales</a>
    <?php 
    echo '<a href="antiguedad_productos.php?estudio='.$estudio.'"><img width="48"  height="48" src= "img/imprimir.png">Reporte de Codigos</a>'; 
     ?>
    <div id="muestra">
    <center><h1>Antiguedad de Saldos</h1>
      <?php
        echo '<center><table border =1>
          <tr class="encabezadoRegistro">
          <th WIDTH=250>Marca</th>
          <th WIDTH=100>Total</th>
          <th WIDTH=100>Importado</th>
          <th WIDTH=100>Local</th>
          <th WIDTH=70>Items</th>
          <th WIDTH=70>Cantidad</th>';
  
  
    require_once("db.php");

    $tvw_iA=0;$tvw_lA=0;$tvw_iB=0;$tvw_lB=0;$tvw_iC=0;$tvw_lC=0;
    $tdh_iA=0;$tdh_lA=0;$tdh_iB=0;$tdh_lB=0;$tdh_iC=0;$tdh_lC=0;
    $tvv_iA=0;$tvv_lA=0;$tvv_iB=0;$tvv_lB=0;$tvv_iC=0;$tvv_lC=0;
    $tac_iA=0;$tac_lA=0;$tac_iB=0;$tac_lB=0;$tac_iC=0;$tac_lC=0;
    $t_iA=0;$t_lA=0;$t_iB=0;$t_lB=0;$t_iC=0;$t_lC=0;
    $items=0;$itemsVW=0;$itemsDH=0;$itemsVV=0;$itemsAC=0; 
    $piezas=0;$piezasVW=0;$piezasDH=0;$piezasVV=0;$piezasAC=0;   

    $fecha_dia = date('Y-m-d');
    $fecha_b = strtotime ( '-1 year' , strtotime ( $fecha_dia ) ) ;
    $fecha_b = date ( 'Y-m-d' , $fecha_b );

    $fecha_dia = date('Y-m-d');
    $fecha_a = strtotime ( '-6 month' , strtotime ( $fecha_dia ) ) ;
    $fecha_a = date ( 'Y-m-d' , $fecha_a );

    
    


    if ($estudio=='1') {
      ///Total VW A Importado
      $sql="SELECT max(h.fecha) as fechamax ,h.codigo,i.DESCRIPCION,i.EXISTENCIA,i.COSTO,(i.COSTO*i.EXISTENCIA) as totalcosto 
      FROM hgeneral h,inventario i WHERE h.codigo=i.CODIGO AND EXISTENCIA >0 GROUP BY h.codigo,i.DESCRIPCION,i.EXISTENCIA,i.COSTO" ; 
      
      if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
      
      while($arr = odbc_fetch_array($resultado)){
        $codigo=$arr['codigo'];
        $fecha=$arr['fechamax'];
        $inicio=substr($arr['codigo'],0,1);
        $final =substr($arr['codigo'], -2,2);
        if ($fecha>$fecha_a) {
          
        
        
        if ($inicio=='1' ) {
          if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
            $tvw_iA=$tvw_iA+$arr["totalcosto"]; 
            $t_iA=$t_iA+$arr["totalcosto"];    
            $itemsVW++;
            $piezasVW=$piezasVW+$arr['EXISTENCIA'];
          }else{
            $tvw_lA=$tvw_lA+$arr["totalcosto"];
            $t_lA=$t_lA+$arr["totalcosto"];     
            $itemsVW++;
            $piezasVW=$piezasVW+$arr['EXISTENCIA'];
          }
          
        }

        if ($inicio=='2' ) {
          if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
            $tdh_iA=$tdh_iA+$arr["totalcosto"];    
            $t_iA=$t_iA+$arr["totalcosto"];  
            $itemsDH++;
            $piezasDH=$piezasDH+$arr['EXISTENCIA'];
           
          }else{
            $tdh_lA=$tdh_lA+$arr["totalcosto"];
            $t_lA=$t_lA+$arr["totalcosto"];     
            $itemsDH++;
            $piezasDH=$piezasDH+$arr['EXISTENCIA'];
          }
          
        }
        if ($inicio=='0' ) {
          if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
            $tvv_iA=$tvv_iA+$arr["totalcosto"];    
            $t_iA=$t_iA+$arr["totalcosto"]; 
            $itemsVV++;
            $piezasVV=$piezasVV+$arr['EXISTENCIA'];
          }else{
            $tvv_lA=$tvv_lA+$arr["totalcosto"];
            $t_lA=$t_lA+$arr["totalcosto"];  
            $itemsVV++;
            $piezasVV=$piezasVV+$arr['EXISTENCIA'];
          }
          
        }
        if ($inicio=='9' ) {
          if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
            $tac_iA=$tac_iA+$arr["totalcosto"]; 
            $t_iA=$t_iA+$arr["totalcosto"]; 
            $itemsAC++;
            $piezasAC=$piezasAC+$arr['EXISTENCIA']; 
          }else{
            $tac_lA=$tac_lA+$arr["totalcosto"];
            $t_lA=$t_lA+$arr["totalcosto"];  
            $itemsAC++;
            $piezasAC=$piezasAC+$arr['EXISTENCIA'];
          }
          
        }
        
      }
    }
        $items=$itemsAC+$itemsVV+$itemsDH+$itemsVW;
        $piezas=$piezasAC+$piezasVV+$piezasDH+$piezasVW;
        
        echo '</tr>'; 
        echo "<td><b>Total A</td><td><b>".number_format($t_lA+$t_iA,2). "</td><td><b>".number_format($t_iA,2)."</td><td><b>".number_format($t_lA,2). "</td><td><b>".$items."</td><td><b>".$piezas."</td>";
        echo '</tr>'; 
        echo "<td><b>Volkswagen A</td><td>".number_format($tvw_lA+$tvw_iA,2). "</td><td>".number_format($tvw_iA,2)."</td><td>".number_format($tvw_lA,2). "</td><td>".$itemsVW."</td><td>".$piezasVW."</td>";
        echo '</tr>'; 
        echo "<td><b>Daihatsu A</td><td>".number_format($tdh_lA+$tdh_iA,2). "</td><td>".number_format($tdh_iA,2)."</td><td>".number_format($tdh_lA,2). "</td><td>".$itemsDH."</td><td>".$piezasDH."</td>";
        echo '</tr>'; 
        echo "<td><b>Volvo A</td><td>".number_format($tvv_lA+$tvv_iA,2). "</td><td>".number_format($tvv_iA,2)."</td><td>".number_format($tvv_lA,2). "</td><td>".$itemsVV."</td><td>".$piezasVV."</td>";
        echo '</tr>'; 
        echo "<td><b>Accesorios A</td><td>".number_format($tac_lA+$tac_iA,2). "</td><td>".number_format($tac_iA,2)."</td><td>".number_format($tac_lA,2). "</td><td>".$itemsAC."</td><td>".$piezasAC."</td>";
    
    }

    elseif ($estudio=='2') {
      $sql="SELECT max(h.fecha) as fechamax ,h.codigo,i.DESCRIPCION,i.EXISTENCIA,i.COSTO,(i.COSTO*i.EXISTENCIA) as totalcosto 
      FROM hgeneral h,inventario i WHERE h.codigo=i.CODIGO AND EXISTENCIA >0 GROUP BY h.codigo,i.DESCRIPCION,i.EXISTENCIA,i.COSTO" ; 
    
    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $codigo=$arr['codigo'];
      $inicio=substr($arr['codigo'],0,1);
      $final =substr($arr['codigo'], -2,2);
      $fecha=$arr['fechamax'];
      if ($fecha>$fecha_b && $fecha<$fecha_a ) {
        
      //Si es VW
      if ($inicio=='1' ) {
        if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
          $tvw_iB=$tvw_iB+$arr["totalcosto"]; 
          $t_iB=$t_iB+$arr["totalcosto"];   
          $itemsVW++;
          $piezasVW=$piezasVW+$arr['EXISTENCIA'];     
        }else{
          $tvw_lB=$tvw_lB+$arr["totalcosto"];
          $t_lB=$t_lB+$arr["totalcosto"];     
          $itemsVW++;
          $piezasVW=$piezasVW+$arr['EXISTENCIA'];
        }
        
      }

      if ($inicio=='2' ) {
        if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
          $tdh_iB=$tdh_iB+$arr["totalcosto"];    
          $t_iB=$t_iB+$arr["totalcosto"];  
          $itemsDH++;
          $piezasDH=$piezasDH+$arr['EXISTENCIA'];
        }else{
          $tdh_lB=$tdh_lB+$arr["totalcosto"];
          $t_lB=$t_lB+$arr["totalcosto"];     
          $itemsDH++;
          $piezasDH=$piezasDH+$arr['EXISTENCIA'];
        }
        
      }
      if ($inicio=='0' ) {
        if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
          $tvv_iB=$tvv_iB+$arr["totalcosto"];    
          $t_iB=$t_iB+$arr["totalcosto"];    
          $itemsVV++;
          $piezasVV=$piezasVV+$arr['EXISTENCIA'];
        }else{
          $tvv_lB=$tvv_lB+$arr["totalcosto"];
          $t_lB=$t_lB+$arr["totalcosto"];     
          $itemsVV++;
          $piezasVV=$piezasVV+$arr['EXISTENCIA'];
        }
        
      }
      if ($inicio=='9' ) {
        if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
          $tac_iB=$tac_iB+$arr["totalcosto"]; 
          $t_iB=$t_iB+$arr["totalcosto"];        
          $itemsAC++;
          $piezasAC=$piezasAC+$arr['EXISTENCIA'];
        }else{ 
          $tac_lB=$tac_lB+$arr["totalcosto"];
          $t_lB=$t_lB+$arr["totalcosto"];     
          $itemsAC++;
          $piezasAC=$piezasAC+$arr['EXISTENCIA'];
        }
        
      }
      
    }
  }
      $items=$itemsAC+$itemsVV+$itemsDH+$itemsVW;
      $piezas=$piezasAC+$piezasVV+$piezasDH+$piezasVW;
        
      echo '</tr>'; 
      echo "<td><b>Total B</td><td><b>".number_format($t_lB+$t_iB,2). "</td><td><b>".number_format($t_iB,2)."</td><td><b>".number_format($t_lB,2). "</td><td><b>".$items."</td><td><b>".$piezas."</td>";
      echo '</tr>'; 
      echo "<td><b>Volkswagen B</td><td>".number_format($tvw_lB+$tvw_iB,2). "</td><td>".number_format($tvw_iB,2)."</td><td>".number_format($tvw_lB,2). "</td><td><b>".$itemsVW."</td><td><b>".$piezasVW."</td>";
      echo '</tr>'; 
      echo "<td><b>Daihatsu B</td><td>".number_format($tdh_lB+$tdh_iB,2). "</td><td>".number_format($tdh_iB,2)."</td><td>".number_format($tdh_lB,2)."</td><td><b>".$itemsDH."</td><td><b>".$piezasDH."</td>";
      echo '</tr>'; 
      echo "<td><b>Volvo B</td><td>".number_format($tvv_lB+$tvv_iB,2). "</td><td>".number_format($tvv_iB,2)."</td><td>".number_format($tvv_lB,2). "</td><td><b>".$itemsVV."</td><td><b>".$piezasVV."</td>";
      echo '</tr>'; 
      echo "<td><b>Accesorios B</td><td>".number_format($tac_lB+$tac_iB,2). "</td><td>".number_format($tac_iB,2)."</td><td>".number_format($tac_lB,2)."</td><td><b>".$itemsAC."</td><td><b>".$piezasAC."</td>";
   
      
    }elseif ($estudio=='3') {
      $sql="SELECT max(h.fecha) as fechamax ,h.codigo,i.DESCRIPCION,i.EXISTENCIA,i.COSTO,(i.COSTO*i.EXISTENCIA) as totalcosto 
      FROM hgeneral h,inventario i WHERE h.codigo=i.CODIGO AND EXISTENCIA >0 GROUP BY h.codigo,i.DESCRIPCION,i.EXISTENCIA,i.COSTO" ; 
    
    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $codigo=$arr['codigo'];
      $inicio=substr($arr['codigo'],0,1);
      $final =substr($arr['codigo'], -2,2);
      $fecha=$arr['fechamax'];
      if ($fecha<$fecha_b) {
        

      //Si es VW
      if ($inicio=='1' ) {
        if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
          $tvw_iC=$tvw_iC+$arr["totalcosto"]; 
          $t_iC=$t_iC+$arr["totalcosto"];        
          $itemsVW++;
          $piezasVW=$piezasVW+$arr['EXISTENCIA'];   
        }else{
          $tvw_lC=$tvw_lC+$arr["totalcosto"];
          $t_lC=$t_lC+$arr["totalcosto"];     
          $itemsVW++;
          $piezasVW=$piezasVW+$arr['EXISTENCIA'];   
        }
        
      }

      if ($inicio=='2' ) {
        if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
          $tdh_iC=$tdh_iC+$arr["totalcosto"];    
          $t_iC=$t_iC+$arr["totalcosto"];  
          $itemsDH++;
          $piezasDH=$piezasDH+$arr['EXISTENCIA'];   
        }else{
          $tdh_lC=$tdh_lC+$arr["totalcosto"];
          $t_lC=$t_lC+$arr["totalcosto"];     
          $itemsDH++;
          $piezasDH=$piezasDH+$arr['EXISTENCIA'];   
        }
        
      }
      if ($inicio=='0' ) {
        if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
          $tvv_iC=$tvv_iC+$arr["totalcosto"];    
          $t_iC=$t_iC+$arr["totalcosto"];    
          $itemsVV++;
          $piezasVV=$piezasVV+$arr['EXISTENCIA'];   
        }else{
          $tvv_lC=$tvv_lC+$arr["totalcosto"];
          $t_lC=$t_lC+$arr["totalcosto"];     
          $itemsVV++;
          $piezasVV=$piezasVV+$arr['EXISTENCIA'];   
        }
        
      }
      if ($inicio=='9' ) {
        if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
          $tac_iC=$tac_iC+$arr["totalcosto"]; 
          $t_iC=$t_iC+$arr["totalcosto"];        
          $itemsAC++;
          $piezasAC=$piezasAC+$arr['EXISTENCIA'];   
        }else{
          $tac_lC=$tac_lC+$arr["totalcosto"];
          $t_lC=$t_lC+$arr["totalcosto"];     
          $itemsAC++;
          $piezasAC=$piezasAC+$arr['EXISTENCIA'];   
        }
        
      }
    }
    }
        $items=$itemsAC+$itemsVV+$itemsDH+$itemsVW;
        $piezas=$piezasAC+$piezasVV+$piezasDH+$piezasVW;
        echo '</tr>'; 
        echo "<td><b>Total C</td><td><b>".number_format($t_lC+$t_iC,2). "</td><td><b>".number_format($t_iC,2)."</td><td><b>".number_format($t_lC,2). "</td><td><b>".$items."</td><td><b>".$piezas."</td>";
        echo '</tr>'; 
        echo "<td><b>Volkswagen C</td><td>".number_format($tvw_lC+$tvw_iC,2). "</td><td>".number_format($tvw_iC,2)."</td><td>".number_format($tvw_lC,2).  "</td><td><b>".$itemsVW."</td><td><b>".$piezasVW."</td>";
        echo '</tr>'; 
        echo "<td><b>Daihatsu C</td><td>".number_format($tdh_lC+$tdh_iC,2). "</td><td>".number_format($tdh_iC,2)."</td><td>".number_format($tdh_lC,2).  "</td><td><b>".$itemsDH."</td><td><b>".$piezasDH."</td>";
        echo '</tr>'; 
        echo "<td><b>Volvo C</td><td>".number_format($tvv_lC+$tvv_iC,2). "</td><td>".number_format($tvv_iC,2)."</td><td>".number_format($tvv_lC,2).  "</td><td><b>".$itemsVV."</td><td><b>".$piezasVV."</td>";
        echo '</tr>'; 
        echo "<td><b>Accesorios C</td><td>".number_format($tac_lC+$tac_iC,2). "</td><td>".number_format($tac_iC,2)."</td><td>".number_format($tac_lC,2).  "</td><td><b>".$itemsAC."</td><td><b>".$piezasAC."</td>";
        echo '</tr>'; 

      
    }

    if ($estudio=='4'){


      $tnoVW_iC=0;$tnoVW_lC=0;$itemsVWNO=0;$piezasVWNO=0;
      $tnoVV_iC=0;$tnoVV_lC=0;$itemsVVNO=0;$piezasVVNO=0;
      $tnoDH_iC=0;$tnoDH_lC=0;$itemsDHNO=0;$piezasDHNO=0;
      $tnoAC_iC=0;$tnoAC_lC=0;$itemsACNO=0;$piezasACNO=0;

      $sql_no ="SELECT * from inventario where CODIGO not in (select distinct codigo from hgeneral) and EXISTENCIA >0";
      
      if(($resultado_no = odbc_exec($id,$sql_no)) === false)
        die("Error en Select: ". odbc_errormsg($id));
      
      while($arr = odbc_fetch_array($resultado_no)){
          $codigo=$arr['CODIGO'];
          $inicio=substr($arr['CODIGO'],0,1);
          $final =substr($arr['CODIGO'], -2,2);
          if ($inicio=='1' ) {
            if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
              $tnoVW_iC=$tnoVW_iC+($arr["COSTO"]*$arr['EXISTENCIA']);        
              $itemsVWNO++;
              $piezasVWNO=$piezasVWNO+$arr['EXISTENCIA'];   
            }else{
              $tnoVW_lC=$tnoVW_lC+($arr["COSTO"]*$arr['EXISTENCIA']);        
              $itemsVWNO++;
              $piezasVWNO=$piezasVWNO+$arr['EXISTENCIA'];   
            }
          }elseif ($inicio=='2') {
            if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
              $tnoDH_iC=$tnoDH_iC+($arr["COSTO"]*$arr['EXISTENCIA']);        
              $itemsDHNO++;
              $piezasDHNO=$piezasDHNO+$arr['EXISTENCIA'];   
            }else{
              $tnoDH_lC=$tnoDH_lC+($arr["COSTO"]*$arr['EXISTENCIA']);        
              $itemsDHNO++;
              $piezasDHNO=$piezasDHNO+$arr['EXISTENCIA'];   
            }
          }elseif ($inicio=='9') {
            if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
              $tnoAC_iC=$tnoAC_iC+($arr["COSTO"]*$arr['EXISTENCIA']);        
              $itemsACNO++;
              $piezasACNO=$piezasACNO+$arr['EXISTENCIA'];   
            }else{
              $tnoAC_lC=$tnoAC_lC+($arr["COSTO"]*$arr['EXISTENCIA']);        
              $itemsACNO++;
              $piezasACNO=$piezasACNO+$arr['EXISTENCIA'];   
            }
          }elseif ($inicio=='0') {
            if ($final=='GR' || $final=='EU' || $final=='JP' ||$final=='TW'){
              $tnoVV_iC=$tnoVV_iC+($arr["COSTO"]*$arr['EXISTENCIA']);        
              $itemsVVNO++;
              $piezasVVNO=$piezasVVNO+$arr['EXISTENCIA'];   
            }else{
              $tnoVV_lC=$tnoVV_lC+($arr["COSTO"]*$arr['EXISTENCIA']);        
              $itemsVVNO++;
              $piezasVVNO=$piezasVVNO+$arr['EXISTENCIA'];   
            }
          }

        
      }
      echo '</tr>'; 
      echo "<td><b>Total sin Mov</td><td><b>".number_format($tnoVW_iC+$tnoVW_lC+$tnoDH_iC+$tnoDH_lC+$tnoVV_iC+$tnoVV_lC+$tnoAC_iC+$tnoAC_lC,2). "</td><td><b>".number_format($tnoVW_iC+$tnoDH_iC+$tnoVV_iC+$tnoAC_iC,2)."</td><td><b>".number_format($tnoVW_lC+$tnoDH_lC+$tnoVV_lC+$tnoAC_lC,2).
      "</td><td><b>".number_format($itemsVWNO+$itemsDHNO+$itemsVVNO+$itemsACNO,2)."</td><td><b>".number_format($piezasVWNO+$piezasDHNO+$piezasVVNO+$piezasACNO,2)."</td></tr>";
      echo "<td><b>Volkswagen sin Mov</td><td><b>".number_format($tnoVW_iC+$tnoVW_lC,2). "</td><td><b>".number_format($tnoVW_iC,2)."</td><td><b>".number_format($tnoVW_lC,2).  "</td><td><b>".$itemsVWNO."</td><td><b>".$piezasVWNO."</td></tr>";
      echo "<td><b>Daihatsu sin Mov</td><td><b>".number_format($tnoDH_iC+$tnoDH_lC,2). "</td><td><b>".number_format($tnoDH_iC,2)."</td><td><b>".number_format($tnoDH_lC,2).  "</td><td><b>".$itemsDHNO."</td><td><b>".$piezasDHNO."</td></tr>";
      echo "<td><b>Volvo sin Mov</td><td><b>".number_format($tnoVV_iC+$tnoVV_lC,2). "</td><td><b>".number_format($tnoVV_iC,2)."</td><td><b>".number_format($tnoVV_lC,2).  "</td><td><b>".$itemsVVNO."</td><td><b>".$piezasVVNO."</td></tr>";
      echo "<td><b>Accesorios sin Mov</td><td><b>".number_format($tnoAC_iC+$tnoAC_lC,2). "</td><td><b>".number_format($tnoAC_iC,2)."</td><td><b>".number_format($tnoAC_lC,2).  "</td><td><b>".$itemsACNO."</td><td><b>".$piezasACNO."</td></tr>";
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

<script src="js/bootstrap.min.js"></script>


</body>
</html>