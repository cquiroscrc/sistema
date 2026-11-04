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
<link rel="icon" href="img/favicon.ico">

</head>

<body>
  
<br>

  
<div  margin:20px;" id ="pulsador" onclick="window.location.href='metas.php';"><a href="#"><img width="48"  height="48" src= "img/back.png">Volver</a> </div>


  <?php
require_once("db.php");
//error_reporting(1);
if(isset($_REQUEST['Date1']) && !empty($_REQUEST['Date1'])){
  $fecha1 = $_REQUEST['Date1'];
  
} 
if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
$fecha2 = $_REQUEST['Date2'];

}
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
$tipo = $_REQUEST['tipo'];

}
if(isset($_REQUEST['vend']) && !empty($_REQUEST['vend'])){
$vend = $_REQUEST['vend'];

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

  if ($tipo=="N") {
    //Sin reporte
    // No se hace este reporte
     header("location: metas.php?no=1");
  }elseif ($tipo=="6"){
    if ($vend=="T") {
      //Comienza la consulta
      $sql="SELECT * FROM Vendedores" ;  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

     


     
      //ingresa nombres de cuadro
      echo '<center>';
      echo "<h3>Meta = ".number_format($meta,2)." x vendedor</h3>";
      echo "<br>";
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Vendedor</b></th> ";
      echo "<th><b>Nombre</b></th> ";
      echo "<th><b>Subtotal</b></th>";
      echo "<th><b>Descuentos Aparte</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "<th><b>Porcentaje</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TTotal=0;
      $total_porcentaje=0;
      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $sqlTotales= "SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                     .$fecha1." 00:00:00.000' AND fechafact <= '".$fecha2." 23:59:00.000' AND 
                     vendedor like'%".(int)$vendedor."'"; 
        
        if(($resTotales = odbc_exec($id,$sqlTotales)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

        while($arrTotales = odbc_fetch_array($resTotales)){
          $subtotal=$arrTotales['venta'];
        }

        $sqlDescuentos="SELECT SUM(Monto) AS descuento FROM tbl_egresos WHERE Fecha >= '".$fecha1. " 00:00:00.000' and Fecha <= '"
                        .$fecha2." 23:59:00.000' AND Descripcion like '%".(int)$vendedor." %' and tipo =0";
        
        if(($resDescuentos = odbc_exec($id,$sqlDescuentos)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

        while($arrDescuentos = odbc_fetch_array($resDescuentos)){
          $descuento=$arrDescuentos['descuento'];
        }
        $descuento=0;

        /// Totales para sacar cuentas
        $sqltotal= "SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                     .$fecha1." 00:00:00.000' AND fechafact <= '".$fecha2." 23:59:00.000'"; 
        if(($totales_res = odbc_exec($id,$sqltotal)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

        while($totales_arr = odbc_fetch_array($totales_res)){
          $subtotal_total=$totales_arr['venta'];
        }

        $sqlDescuent="SELECT SUM(Monto) AS descuento FROM tbl_egresos WHERE Fecha >= '".$fecha1. " 00:00:00.000' and Fecha <= '"
                        .$fecha2." 23:59:00.000' and tipo =0";
        
        if(($resDescuent = odbc_exec($id,$sqlDescuent)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

        while($arrDescuent = odbc_fetch_array($resDescuent)){
          $descuento_total=$arrDescuent['descuento'];
        }

        $total_total=$subtotal_total-$descuento_total;

        echo "<td>".$arr["codigo"]."</td> ";
        echo "<td>".$arr["nombre"]."</td>";
        echo "<td>".number_format($subtotal,2)."</td>";
            $TSubtotal = $TSubtotal + $subtotal;
        echo "<td>".number_format($descuento,2)."</td>";
            $TDescuentos = $TDescuentos + $descuento;
            $total = $subtotal - $descuento;
        echo "<td>".number_format($total,2)."</td>";
        $porcentaje=($total*100)/$total_total;
        $total_porcentaje=$total_porcentaje+$porcentaje;
        echo "<td>".number_format($porcentaje,2)." %</td>";
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
      echo "<th><b>Subtotal</b></th>";
      echo "<th><b>Descuentos Aparte</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TTotal=0;


      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $nombre=$arr['nombre'];
      }
      
      $sqlTotales= "SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                 .$fecha1." 00:00:00.000' AND fechafact <= '".$fecha2." 23:59:00.000' AND 
                 vendedor like'%".(int)$vendedor."'"; 
    
      if(($resTotales = odbc_exec($id,$sqlTotales)) === false)
                die("Error en Select: ". odbc_errormsg($id));

      while($arrTotales = odbc_fetch_array($resTotales)){
        $subtotal=$arrTotales['venta'];
      }

      $sqlDescuentos="SELECT SUM(Monto) AS descuento FROM tbl_egresos WHERE Fecha >= '".$fecha1. " 00:00:00.000' and Fecha <= '"
                    .$fecha2." 23:59:00.000' AND Descripcion like '%".(int)$vendedor." %'";
    
      if(($resDescuentos = odbc_exec($id,$sqlDescuentos)) === false)
                die("Error en Select: ". odbc_errormsg($id));

      while($arrDescuentos = odbc_fetch_array($resDescuentos)){
        $descuento=$arrDescuentos['descuento'];
      }

      echo "<td>".$vendedor."</td> ";
      echo "<td>".$nombre."</td>";
      echo "<td>".number_format($subtotal,2)."</td>";
      $TSubtotal = $TSubtotal + $subtotal;
      echo "<td>".number_format($descuento,2)."</td>";
      $TDescuentos = $TDescuentos + $descuento;
      $total = $subtotal - $descuento;
      echo "<td>".number_format($total,2)."</td>";
      $TTotal= $TTotal + $total;
      echo "</tr> ";
    }
    //Totales
      echo "<td><strong> Fecha  :".$fecha1." --- ".$fecha2."</td>
      <td></td><td></td><td></td>
      <td></td><td></td></tr>" ;
      echo "<td><strong>Subtotal: ".number_format($TSubtotal,2)."</td>
      <td><strong>Descuento: ".number_format($TDescuentos,2)."</td>
      <td></td><td></td><td></td><td></td><tr>";
      echo '<th><strong>Total: '.number_format($TTotal,2).'</th>
      <th></th><th></th><th></th><th></th><th>'.number_format($total_porcentaje,2).'%</th></tr>';
      echo '</table>';
      echo '</center>';


  }elseif ($tipo=="1") {
    //Totales
    if ($vend=="T") {
      //Comienza la consulta
      $sql="SELECT * FROM Vendedores" ;  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
      echo '<center>';
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Vendedor</b></th> ";
      echo "<th><b>Nombre</b></th> ";
      echo "<th><b>Subtotal</b></th>";
      echo "<th><b>Descuentos Aparte</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TTotal=0;

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $sqlTotales= "SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                     .$fecha1." 00:00:00.000' AND fechafact <= '".$fecha2." 23:59:00.000' AND 
                     vendedor like'%".(int)$vendedor."' AND impuesto <> 0"; 
        
        if(($resTotales = odbc_exec($id,$sqlTotales)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

        while($arrTotales = odbc_fetch_array($resTotales)){
          $subtotal=$arrTotales['venta'];
        }

        $sqlDescuentos="SELECT SUM(Monto) AS descuento FROM tbl_egresos WHERE Fecha >= '".$fecha1. " 00:00:00.000' and Fecha <= '"
                        .$fecha2." 23:59:00.000' AND Descripcion like '%".(int)$vendedor." %'";
        
        if(($resDescuentos = odbc_exec($id,$sqlDescuentos)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

        while($arrDescuentos = odbc_fetch_array($resDescuentos)){
          $descuento=$arrDescuentos['descuento'];
        }


        echo "<td>".$arr["codigo"]."</td> ";
        echo "<td>".$arr["nombre"]."</td>";
        echo "<td>".number_format($subtotal,2)."</td>";
            $TSubtotal = $TSubtotal + $subtotal;
        echo "<td>".number_format($descuento,2)."</td>";
            $TDescuentos = $TDescuentos + $descuento;
            $total = $subtotal - $descuento;
        echo "<td>".number_format($total,2)."</td>";
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
      echo "<th><b>Subtotal</b></th>";
      echo "<th><b>Descuentos Aparte</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TTotal=0;

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $nombre=$arr['nombre'];
      }
      
      $sqlTotales= "SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                 .$fecha1." 00:00:00.000' AND fechafact <= '".$fecha2." 23:59:00.000' AND 
                 vendedor like'%".(int)$vendedor."' AND impuesto <> 0"; 
    
      if(($resTotales = odbc_exec($id,$sqlTotales)) === false)
                die("Error en Select: ". odbc_errormsg($id));

      while($arrTotales = odbc_fetch_array($resTotales)){
        $subtotal=$arrTotales['venta'];
      }

      $sqlDescuentos="SELECT SUM(Monto) AS descuento FROM tbl_egresos WHERE Fecha >= '".$fecha1. " 00:00:00.000' and Fecha <= '"
                    .$fecha2." 23:59:00.000' AND Descripcion like '%".(int)$vendedor." %'";
    
      if(($resDescuentos = odbc_exec($id,$sqlDescuentos)) === false)
                die("Error en Select: ". odbc_errormsg($id));

      while($arrDescuentos = odbc_fetch_array($resDescuentos)){
        $descuento=$arrDescuentos['descuento'];
      }

      echo "<td>".$vendedor."</td> ";
      echo "<td>".$nombre."</td>";
      echo "<td>".number_format($subtotal,2)."</td>";
      $TSubtotal = $TSubtotal + $subtotal;
      echo "<td>".number_format($descuento,2)."</td>";
      $TDescuentos = $TDescuentos + $descuento;
      $total = $subtotal - $descuento;
      echo "<td>".number_format($total,2)."</td>";
      $TTotal= $TTotal + $total;
      echo "</tr> ";
    }
    //Totales
      echo "<td><strong> Fecha  :".$fecha1." --- ".$fecha2."</td>
      <td></td><td></td>
      <td></td><td></td></tr>" ;
      echo "<td><strong>Subtotal: ".number_format($TSubtotal,2)."</td>
      <td><strong>Descuento: ".number_format($TDescuentos,2)."</td>
      <td></td><td></td><td></td><tr>";
      echo '<th><strong>Total: '.number_format($TTotal,2).'</th>
      <th></th><th></th><th></th><th></th></tr>';
      echo '</table>';
      echo '</center>';

  }elseif ($tipo=="2") {
    //Totales usados
    if ($vend=="T") {
      //Comienza la consulta
      $sql="SELECT * FROM Vendedores" ;  

      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
      echo '<center>';
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Vendedor</b></th> ";
      echo "<th><b>Nombre</b></th> ";
      echo "<th><b>Subtotal</b></th>";
      echo "<th><b>Descuentos Aparte</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TTotal=0;

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $sqlTotales= "SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                     .$fecha1." 00:00:00.000' AND fechafact <= '".$fecha2." 23:59:00.000' AND 
                     vendedor like'%".(int)$vendedor."' AND impuesto = 0"; 
        
        if(($resTotales = odbc_exec($id,$sqlTotales)) === false)
                    die("Error en Select: ". odbc_errormsg($id));

        while($arrTotales = odbc_fetch_array($resTotales)){
          $subtotal=$arrTotales['venta'];
        }
          $descuento=0;
        
        echo "<td>".$arr["codigo"]."</td> ";
        echo "<td>".$arr["nombre"]."</td>";
        echo "<td>".number_format($subtotal,2)."</td>";
            $TSubtotal = $TSubtotal + $subtotal;
        echo "<td>".number_format($descuento,2)."</td>";
            $TDescuentos = $TDescuentos + $descuento;
            $total = $subtotal - $descuento;
        echo "<td>".number_format($total,2)."</td>";
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
      echo "<td><b>Vendedor</b></td> ";
      echo "<td><b>Nombre</b></td> ";
      echo "<td><b>Subtotal</b></td>";
      echo "<td><b>Descuentos Aparte</b></td> ";
      echo "<td><b>Total</b></td> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TTotal=0;

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $nombre=$arr['nombre'];
      }
      
      $sqlTotales= "SELECT SUM(subtotal)- SUM(descuento) AS  venta FROM hfacturas  WHERE fechafact >= '"
                 .$fecha1." 00:00:00.000' AND fechafact <= '".$fecha2." 23:59:00.000' AND 
                 vendedor like'%".(int)$vendedor."' AND impuesto = 0"; 
    
      if(($resTotales = odbc_exec($id,$sqlTotales)) === false)
                die("Error en Select: ". odbc_errormsg($id));

      while($arrTotales = odbc_fetch_array($resTotales)){
        $subtotal=$arrTotales['venta'];
      }

      $descuento=0;


      echo "<td>".$vendedor."</td> ";
      echo "<td>".$nombre."</td>";
      echo "<td>".number_format($subtotal,2)."</td>";
      $TSubtotal = $TSubtotal + $subtotal;
      echo "<td>".number_format($descuento,2)."</td>";
      $TDescuentos = $TDescuentos + $descuento;
      $total = $subtotal - $descuento;
      echo "<td>".number_format($total,2)."</td>";
      $TTotal= $TTotal + $total;
      echo "</tr> ";
    }
    //Totales
    echo "<td><strong> Fecha  :".$fecha1." --- ".$fecha2."</td>
    <td></td><td></td>
    <td></td><td></td></tr>" ;
    echo "<td><strong>Subtotal: ".number_format($TSubtotal,2)."</td>
    <td><strong>Descuento: ".number_format($TDescuentos,2)."</td>
    <td></td><td></td><td></td><tr>";
    echo '<th><strong>Total: '.number_format($TTotal,2).'</th>
    <th></th><th></th><th></th><th></th></tr>';
    echo '</table>';
    echo '</center>';

  }elseif($tipo=="3"){
    //Reporte de facturas
    if ($vend=="T") {
      // No se hace este reporte para todos
      header("location: metas.php?no=1");
    }else{
      // facturas
       $sql="SELECT * FROM Vendedores WHERE codigo='".$vend."'" ;  
     
      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
      echo '<center>';
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Vendedor</b></th> ";
      echo "<th><b>Fecha</b></th> ";
      echo "<th><b>Nombre</b></th> ";
      echo "<th><b>Factura</b></th> ";
      echo "<th><b>Subtotal</b></th>";
      echo "<th><b>Descuento</b></th> ";
      echo "<th><b>Impuesto</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TImpuesto=0;
      $TTotal=0;
      $TMensajeria=0;

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $nombre=$arr['nombre'];
      }

      $sqlFacturas= "SELECT * FROM hfacturas  WHERE fechafact >= '".$fecha1. " 00:00:00.000' AND fechafact <= '"
            .$fecha2." 23:59:00.000' and vendedor like '%".(int)$vendedor."' and impuesto <> 0 order by nfac";

      if(($resFacturas = odbc_exec($id,$sqlFacturas)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arrFacturas = odbc_fetch_array($resFacturas)){
        //rellenar tabla
        echo "<td>".$vendedor."</td> ";
        echo "<td>".$arrFacturas['fechafact']."</td> ";
        echo "<td>".$nombre."</td> ";
        echo "<td>".$arrFacturas['nfac']."</td> ";
        echo "<td>".number_format($arrFacturas['subtotal'],2)."</td>";
        $TSubtotal=$TSubtotal+$arrFacturas['subtotal'];
        echo "<td>".number_format($arrFacturas['descuento'],2)."</td> ";
        $TDescuentos=$TDescuentos+$arrFacturas['descuento'];
        echo "<td>".number_format($arrFacturas['impuesto'],2)."</td> ";
        $TImpuesto=$TImpuesto+$arrFacturas['impuesto'];
        echo "<td>".number_format($arrFacturas['total'],2)."</td> ";
        $TTotal=$TTotal+$arrFacturas['total'];
        $temp=$arrFacturas['subtotal']+$arrFacturas['impuesto']-$arrFacturas['descuento'];
        $mensa=$arrFacturas['total']-$temp;
        $TMensajeria=$TMensajeria+$mensa;
        echo "</tr> ";
        echo "<tr> ";
      }
      //Totales
      echo "<td></td><td><strong> Fecha  :".$fecha1." --- ".$fecha2."</td>
      <td></td><td></td><td></td><td></td><td></td><td></td></tr>" ;
      echo "<td></td><td><strong>Subtotal: ".number_format($TSubtotal,2)."</td>
      <td><strong>Descuento: ".number_format($TDescuentos,2)."</td>
      <td></td><td></td><td></td><td></td><td></td><tr>";
      echo "<td></td><td><strong>Impuesto: ".number_format($TImpuesto,2)."</td>
      <td><strong>Total: ".number_format($TTotal,2)."</td>
      <td><strong>Mensajeria: ".number_format($TMensajeria,2)."</td>
      <td></td><td></td><td></td><td></td></tr>";
      echo '</table>';
      echo '</center>';

    }
    
    //echo 'SQL '.$sql;echo '<br>';echo 'Facturas '.$sqlFacturas;
  }elseif ($tipo=="4") {
    //Facturas Usadas
    if ($vend=="T") {
      // No se hace este reporte para todos
      header("location: metas.php?no=1");
    }else{
      // facturas
       $sql="SELECT * FROM Vendedores WHERE codigo='".$vend."'" ;  
     
      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
      echo '<center>';
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Vendedor</b></th> ";
      echo "<th><b>Fecha</b></th> ";
      echo "<th><b>Nombre</b></th> ";
      echo "<th><b>Factura</b></th> ";
      echo "<th><b>Subtotal</b></th>";
      echo "<th><b>Descuento</b></th> ";
      echo "<th><b>Impuesto</b></th> ";
      echo "<th><b>Total</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      $TSubtotal=0;
      $TDescuentos=0;
      $TImpuesto=0;
      $TTotal=0;
      $TMensajeria=0;

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $nombre=$arr['nombre'];
      }

      $sqlFacturas= "SELECT * FROM hfacturas  WHERE fechafact >= '".$fecha1. " 00:00:00.000' AND fechafact <= '"
            .$fecha2." 23:59:00.000' and vendedor like '%".(int)$vendedor."' and impuesto = 0 order by nfac";

      if(($resFacturas = odbc_exec($id,$sqlFacturas)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arrFacturas = odbc_fetch_array($resFacturas)){
        //rellenar tabla
        echo "<td>".$vendedor."</td> ";
        echo "<td>".$arrFacturas['fechafact']."</td> ";
        echo "<td>".$nombre."</td> ";
        echo "<td>".$arrFacturas['nfac']."</td> ";
        echo "<td>".number_format($arrFacturas['subtotal'],2)."</td>";
        $TSubtotal=$TSubtotal+$arrFacturas['subtotal'];
        echo "<td>".number_format($arrFacturas['descuento'],2)."</td> ";
        $TDescuentos=$TDescuentos+$arrFacturas['descuento'];
        echo "<td>".number_format($arrFacturas['impuesto'],2)."</td> ";
        $TImpuesto=$TImpuesto+$arrFacturas['impuesto'];
        echo "<td>".number_format($arrFacturas['total'],2)."</td> ";
        $TTotal=$TTotal+$arrFacturas['total'];
        $temp=$arrFacturas['subtotal']+$arrFacturas['impuesto']-$arrFacturas['descuento'];
        $mensa=$arrFacturas['total']-$temp;
        $TMensajeria=$TMensajeria+$mensa;
        echo "</tr> ";
        echo "<tr> ";
      }
      //Totales
      echo "<td><strong> Fecha  :".$fecha1." --- ".$fecha2."</td>
      <td></td><td></td><td></td><td></td><td></td><td></td></tr>" ;
      echo "<td><strong>Subtotal: ".number_format($TSubtotal,2)."</td>
      <td><strong>Descuento: ".number_format($TDescuentos,2)."</td>
      <td></td><td></td><td></td><td></td><td></td><tr>";
      echo "<td><strong>Impuesto: ".number_format($TImpuesto,2)."</td>
      <td><strong>Total: ".number_format($TTotal,2)."</td>
      <td><strong>Mensajeria: ".number_format($TMensajeria,2)."</td>
      <td></td><td></td><td></td><td></td></tr>";
      echo '</table>';
      echo '</center>';

    }

  }elseif ($tipo=="5") {
    //Descuentos aparte
    
    
    if ($vend=="T") {
      // No se hace este reporte para todos
      header("location: metas.php?no=1");
    }else{
      // facturas
       $sql="SELECT * FROM Vendedores WHERE codigo='".$vend."'" ;  
     
      if(($res = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      //ingresa nombres de cuadro
      echo '<center>';
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<td><b>Vendedor</b></td> ";
      echo "<td><b>Nombre</b></td> ";
      echo "<td><b>Documento</b></td> ";
      echo "<td><b>Fecha</b></td>";
      echo "<td><b>Descripcion</b></td> ";
      echo "<td><b>Total</b></td> ";
      echo "</tr> ";
      echo "<tr> ";

      $TTotal=0;
      $TMensajeria=0;

      while($arr = odbc_fetch_array($res)){
        $vendedor=$arr['codigo'];
        $nombre=$arr['nombre'];
      }
      $sqlDescuentos= "SELECT * FROM tbl_egresos  WHERE fecha >= '".$fecha1. " 00:00:00.000' and fecha <= '"
     .$fecha2." 23:59:00.000' AND descripcion like '%".(int)$vendedor." %' order by documento";

      

      if(($resDescuentos = odbc_exec($id,$sqlDescuentos)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arrDescuentos = odbc_fetch_array($resDescuentos)){
        //rellenar tabla
        echo "<td>".$vendedor."</td> ";
        echo "<td>".$nombre."</td> ";
        echo "<td>".$arrDescuentos['Documento']."</td> ";
        echo "<td>".$arrDescuentos['Fecha']."</td>";
        echo "<td>".$arrDescuentos['Descripcion']."</td> ";
        echo "<td>".number_format($arrDescuentos['Monto'],2)."</td> ";
        $TTotal=$TTotal+$arrDescuentos['Monto'];
        echo "</tr> ";
        echo "<tr> ";
      }
      //Totales
      echo "<td><strong> Fecha  :".$fecha1." --- ".$fecha2."</td>
      <td></td><td></td><td></td><td></td><td></td></tr>" ;
      echo "<td><strong>Total: ".number_format($TTotal,2)."</td>
      <td></td><td></td><td></td><td></td><td></td><tr>";
      echo "<td></td><td></td><td></td><td></td><td></td><td></td></tr>";
      echo '</table>';
      echo '</center>';

    }

  } 

}
echo '<a href="javascript:window.print(); void 0;"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>';
//echo'<input type="image" src="img/imprime.png"  width="48"  height="48" name="imprimir" value="Imprimir"  onClick="window.print();"/>';

$self = "metas_reportes.php"; //Obtenemos la página en la que nos encontramos
//header("refresh:10; url=$self"); //Refrescamos cada 300 segundos
$self=$self."?Date1=".$fecha1."&Date2=".$fecha2."&tipo=".$tipo."&vend=".$vend;
echo $self;
echo "<META HTTP-EQUIV='refresh' CONTENT='240; URL=".$self."'>"; 
//header("Refresh: 5; url=".$self);


?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Metas';
</script> 
<script src="js/bootstrap.min.js"></script> 
</body>
</html>