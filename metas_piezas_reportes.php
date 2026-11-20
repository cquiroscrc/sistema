<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    include "menu.php";
?>
<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resultado de consulta metas</title>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/anular.css">
<link rel="StyleSheet" href="css/calendar.css">
<link rel="icon" href="favicon.ico">

</head>

<body>
  
<br>

  
<div  margin:20px;" id ="pulsador" onclick="window.location.href='metas_piezas.php';"><a href="#"><img width="48"  height="48" src= "img/back.png">Volver</a> </div>


<?php
require_once("db.php");
//error_reporting(1);
if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
$fecha2 = $_REQUEST['Date2'];

}

if(isset($_REQUEST['vend']) && !empty($_REQUEST['vend'])){
$vend = $_REQUEST['vend'];

}
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
  $tipo = $_REQUEST['tipo'];
  if ($tipo=='VW') {
    $marca='1';
    $nombre_marca="Volkswagen";
  }elseif ($tipo=='DH') {
    $marca='2';
    $nombre_marca="Daihatsu";
  }elseif ($tipo=='EM') {
    $marca='3';
    $nombre_marca="Empi";
  }elseif ($tipo=='TY') {
    $marca='4';
    $nombre_marca="Toyota";
  }elseif ($tipo=='NS') {
    $marca='4';
    $nombre_marca="Nissan";
  }elseif ($tipo=='HY') {
    $marca='4';
    $nombre_marca="Hyundai";
  }elseif ($tipo=='AC') {
    $marca='9';
    $nombre_marca="Accesorios";
  }elseif ($tipo=='TT') {
    $marca='';
    $nombre_marca="Todos";
  }
}




if ($fecha1 != ""){
  
  /// Sacar la meta
  $fecha_metas = strtotime ($fecha1 ) ;
  $ano = date('Y',$fecha_metas);
  $mes = date('m',$fecha_metas);

  $sql_meta="SELECT * FROM tbl_metas WHERE mes =".$mes." AND ano=".$ano."";
    if(($res_meta = odbc_exec($id,$sql_meta)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    while($arr_meta = odbc_fetch_array($res_meta)){
      $meta=$arr_meta["meta"];
    }
  ///////

  
    if ($vend=="T") {
      //Comienza la consulta
      echo '<center>';
      echo "<h3>Metas de piezas ".$nombre_marca."</h3>";
    
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Vendedor</b></th> ";
      echo "<th><b>Nombre</b></th> ";
      echo "<th><b>Importado</b></th> ";
      echo "<th><b>Local</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "<th><b>Porcentaje</b></th> ";
      echo "</tr> ";
      echo "<tr> ";
      
      $TTotal=0;
      $total_porcentaje=0;
      $total_total=0;
      $total_importado=0;
      $total_local=0;
      // total de piezas
      $sql_total_total="SELECT SUM(h.cantidad) AS  cantidad  FROM hgeneral h, inventario i  WHERE h.fecha >= '".$fecha1." 00:00:00.000' AND h.fecha <= '".$fecha2." 23:59:00.000' AND h.CODIGO like '".$marca."%' AND i.MARCA ='".$tipo."' and movimiento='Venta' and h.codigo=i.CODIGO"; 
                    // Esta por defecto en Daihatsu   ------------------****
                    
        if(($resTotal = odbc_exec($id,$sql_total_total)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arrTotal = odbc_fetch_array($resTotal)){
          $total_total=$arrTotal['cantidad'];
        }

      
      $sql="SELECT * FROM Vendedores" ;  
      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $vendedor=$arr['codigo'];
        $nombre=$arr['nombre'];
      
        $sql_total="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '".$fecha1." 00:00:00.000' AND h.fecha <= '".$fecha2." 23:59:00.000' AND 
                    h.vendedor like'%".(int)$vendedor."' and h.CODIGO like '".$marca."%' and i.MARCA = '".$tipo."' and movimiento='Venta' and h.codigo=i.CODIGO"; 
                    // Esta por defecto en Daihatsu   ------------------****

        if(($resTotales = odbc_exec($id,$sql_total)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arrTotales = odbc_fetch_array($resTotales)){
          $total=$arrTotales['cantidad'];
        }

        // TW *************
        $sql_tw="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '".$fecha1." 00:00:00.000' AND h.fecha <= '".$fecha2." 23:59:00.000' AND 
                    h.vendedor like'%".(int)$vendedor."' and h.CODIGO like '".$marca."%TW%' and i.MARCA = '".$tipo."' and movimiento='Venta' and h.codigo=i.CODIGO"; 


         
                    // Esta por defecto en Daihatsu   ------------------****

        if(($restw = odbc_exec($id,$sql_tw)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arrtw = odbc_fetch_array($restw)){
          $tw=$arrtw['cantidad'];
        }

        $sql_jp="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '".$fecha1." 00:00:00.000' AND h.fecha <= '".$fecha2." 23:59:00.000' AND 
                    h.vendedor like'%".(int)$vendedor."' and h.CODIGO like '".$marca."%JP%' and i.MARCA = '".$tipo."' and movimiento='Venta' and h.codigo=i.CODIGO";  
                    // Esta por defecto en Daihatsu   ------------------****

        if(($resjp = odbc_exec($id,$sql_jp)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arrjp = odbc_fetch_array($resjp)){
          $jp=$arrjp['cantidad'];
        }
        
        $sql_eu="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '".$fecha1." 00:00:00.000' AND h.fecha <= '".$fecha2." 23:59:00.000' AND 
                    h.vendedor like'%".(int)$vendedor."' and h.CODIGO like '".$marca."%EU%' and i.MARCA = '".$tipo."' and movimiento='Venta' and h.codigo=i.CODIGO";     // Esta por defecto en Daihatsu   ------------------****

        if(($reseu = odbc_exec($id,$sql_eu)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arreu = odbc_fetch_array($reseu)){
          $eu=$arreu['cantidad'];
        }

        $sql_gr="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '".$fecha1." 00:00:00.000' AND h.fecha <= '".$fecha2." 23:59:00.000' AND 
                    h.vendedor like'%".(int)$vendedor."' and h.CODIGO like '".$marca."%GR%' and i.MARCA = '".$tipo."' and movimiento='Venta' and h.codigo=i.CODIGO"; 
                    // Esta por defecto en Daihatsu   ------------------****

        if(($resgr = odbc_exec($id,$sql_gr)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arrgr = odbc_fetch_array($resgr)){
          $gr=$arrgr['cantidad'];
        }
        $sql_zl="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '".$fecha1." 00:00:00.000' AND h.fecha <= '".$fecha2." 23:59:00.000' AND 
                    h.vendedor like'%".(int)$vendedor."' and h.CODIGO like '".$marca."%ZL%' and i.MARCA = '".$tipo."' and movimiento='Venta' and h.codigo=i.CODIGO";     // Esta por defecto en Daihatsu   ------------------****

        if(($reszl = odbc_exec($id,$sql_zl)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arrzl = odbc_fetch_array($reszl)){
          $zl=$arrzl['cantidad'];
        }
        $importado=$tw+$jp+$eu+$gr+$zl;

        $local=$total-$importado;
          echo "<td>".$arr["codigo"]."</td> ";
          echo "<td>".$arr["nombre"]."</td>";
          echo "<td>".number_format($importado,2)."</td>";
          echo "<td>".number_format($local,2)."</td>";
          echo "<td>".number_format($total,2)."</td>";
          if ($total== null) {
            $porcentaje=0;
          }else{
            $porcentaje=($total*100)/$total_total;
          }
          $total_porcentaje=$total_porcentaje+$porcentaje;
          echo "<td>".number_format($porcentaje,2)." %</td>";
              $total_importado=$total_importado+$importado;
              $total_local=$total_local+$local;
              $TTotal= $TTotal + $total;
          echo "</tr> ";
       
      }
    }else{
        //totales individuales
        $sql="SELECT * FROM Vendedores WHERE codigo='".$vend."'" ;  
       
        if(($res = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

        //ingresa nombres de cuadro
        echo '<center>';
        echo "<table border = '1'> ";
        echo "<tr> ";
        echo "<th><b>Vendedor</b></th> ";
        echo "<th><b>Nombre</b></th> ";
        echo "<th><b>Total</b></th> ";
        echo "</tr> ";
        echo "<tr> ";

        $TTotal=0;

        $sql_total="SELECT SUM(cantidad) AS  cantidad FROM hgeneral  WHERE fecha >= '"
                   .$fecha1." 00:00:00.000' AND fecha <= '".$fecha2." 23:59:00.000' AND 
                    vendedor like'%".(int)$vendedor."' and CODIGO like '".$marca."%' AND MARCA ='".$nombre_marca."' and movimiento='Venta'";


        echo "<td>".$vendedor."</td> ";
        echo "<td>".$nombre."</td>";
        echo "<td>".number_format($total,2)."</td>";
        $TTotal= $TTotal + $total;
        echo "</tr> ";
      }
      //Totales
        echo "<td><strong> Fecha  :".$fecha1." --- ".$fecha2."</td>
        <td></td><td></td><td></td><td></td><td></td></tr>" ;
        
        echo '<th><strong>Total: '.number_format($TTotal,2).'</th>
        <th>Importado: '.number_format($total_importado,2).'</th><th>Local: '.number_format($total_local,2).'</th><th></th><th></th><th></th></tr>';
        echo '</table>';
        echo '</center>';

}// primer if

if ($tipo=='Todos') {
    // Todas las marcas
      echo '<center>';
      echo "<h3>Metas de piezas ".$nombre_marca."</h3>";
    
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Vendedor</b></th> ";
      echo "<th><b>Nombre</b></th> ";
      // aqui van las marcas
      $sql_marcas="SELECT codigo FROM Metas ";
      if(($res_marcas = odbc_exec($id,$sql_marcas)) === false)
                  die("Error en Select: ". odbc_errormsg($id));

        while($arr_marcas = odbc_fetch_array($res_marcas)){
          echo "<th><b>".$arr_marcas['codigo']."</b></th> ";
        }
      echo "</tr> ";
      echo "<tr> ";
      
      
      $sql="SELECT * FROM Vendedores" ;  
      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $vendedor=$arr['codigo'];
        $nombre=$arr['nombre'];

        $sql_total="SELECT SUM(h.cantidad) AS  cantidad FROM hgeneral h, inventario i WHERE h.fecha >= '".$fecha1." 00:00:00.000' AND h.fecha <= '".$fecha2." 23:59:00.000' AND 
                    h.vendedor like'%".(int)$vendedor."' and h.CODIGO like '".$marca."%' and i.MARCA = '".$tipo."' and movimiento='Venta' and h.codigo=i.CODIGO"; 
                    // Esta por defecto en Daihatsu   ------------------****

        if(($resTotales = odbc_exec($id,$sql_total)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
        unset($total_m);
        //$total_m = array();
        $marca_con=0;
        
        while($arrTotales = odbc_fetch_array($resTotales)){
          $total_m[$marca_con]=$arrTotales['cantidad'];
        }

      }//vendedores
}//Todos  

//echo $sql_total;
echo '<a href="javascript:window.print(); void 0;"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>';
//echo'<input type="image" src="img/imprime.png"  width="48"  height="48" name="imprimir" value="Imprimir"  onClick="window.print();"/>';
?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Metas Piezas';
</script> 
<script src="js/bootstrap.min.js"></script> 
</body>
</html>
