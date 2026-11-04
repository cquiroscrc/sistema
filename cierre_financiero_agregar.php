<?php
session_start();
    require("db.php");
    
    $tributacion=0;
    if((isset($_POST['Date1']))and(!empty($_POST['Date1']))){
        $fecha1 = $_POST['Date1'];
    }
    if((isset($_POST['Date2']))and(!empty($_POST['Date2']))){
        $fecha2 = $_POST['Date2'];
    }
    if((isset($_POST['costof']))and(!empty($_POST['costof']))){
        $costof = $_POST['costof'];
    }
    
    if((isset($_POST['agua']))and(!empty($_POST['agua']))){
        $agua = $_POST['agua'];
    }
    if((isset($_POST['luz']))and(!empty($_POST['luz']))){
        $luz = $_POST['luz'];
    }
    if((isset($_POST['telefono']))and(!empty($_POST['telefono']))){
        $telefono = $_POST['telefono'];
    }
    if((isset($_POST['ccss']))and(!empty($_POST['ccss']))){
        $ccss = $_POST['ccss'];
    }
    if((isset($_POST['tributacion']))and(!empty($_POST['tributacion']))){
        $tributacion = $_POST['tributacion'];
    }
    if((isset($_POST['tc']))and(!empty($_POST['tc']))){
        $tc = $_POST['tc'];
    }
    $alquiler=0;
    $total_sal=0;
    
    $newdate1 = strtotime ($fecha1) ;
    $newdate1 = date ( 'Y-m-d 00:00:00.000' , $newdate1 );
    
    $newdate2 = strtotime ($fecha2) ;
    $newdate2 = date ( 'Y-m-d 23:59:59.000' , $newdate2 );

    $sql_salarios = "SELECT * FROM rh_planilla where fecha BETWEEN '$newdate1' AND '$newdate2'";
    //echo $sql; exit;
    if(($result = odbc_exec($id,$sql_salarios)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    
    while($row = odbc_fetch_array($result)){
            $total_sal=$total_sal + $row['total'];
    }

    $subtotal=$alquiler+$agua+$luz+$telefono+$ccss+$tributacion+$total_sal;
    $financiero=($subtotal*$costof)/100;
    $reserva=($subtotal+$financiero)/12;
    $total=$subtotal+$financiero+$reserva;
    
    /// Hasta aqui los Gastos


    $sql_facturas="SELECT sum(total) AS total FROM hfacturas where fechafact BETWEEN '$newdate1' AND '$newdate2'";
    if(($resultado = odbc_exec($id,$sql_facturas)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $facturas=$arr["total"];     

    }
    
    $sql_descuentos="SELECT sum(E_Descuentos) AS total FROM cierrediario where Fecha BETWEEN '$newdate1' AND '$newdate2'";
    if(($resultado = odbc_exec($id,$sql_descuentos)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $descuentos=$arr["total"];     

    }
    $tventas=$facturas-$descuentos;
    
    $sql_comprasEX="SELECT sum(c.total) as CExterior from compras c where Fecha BETWEEN '$newdate1' and '$newdate2' AND 
        proveedor in (select codigo from proveedores where tipo='E')";
    if(($resultado = odbc_exec($id,$sql_comprasEX)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $comprasEX=$arr["CExterior"];  


    }
    $tcomprasEX=$comprasEX*$tc;
    

    $sql_comprasL="SELECT sum(c.total) as CExterior from compras c where Fecha BETWEEN '$newdate1' and '$newdate2' AND 
        proveedor in (select codigo from proveedores where tipo='L')";
    if(($resultado = odbc_exec($id,$sql_comprasL)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    $comprasL=0;
    while($arr = odbc_fetch_array($resultado)){
      $comprasL=$arr["CExterior"];     

    }
    $totcompras=$tcomprasEX+$comprasL;

    $sql_egresos="SELECT sum( E_Papeleria+E_Combustible+E_AtencionPublico+E_Encomiendas+E_Luz+E_Agua+E_Telefono+E_Otros) as Total From cierrediario
        where Fecha BETWEEN '$newdate1' and '$newdate2'";
    if(($resultado = odbc_exec($id,$sql_egresos)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $egresos=$arr["Total"];     

    }

    $total_egresos=$egresos+$total+$totcompras;
    $diferencia=$tventas-$total_egresos;

    // Hasta aqui ventas, compras y egresos

    
    // volkswagen
    $sql_tvw="select sum(costo*existencia) as total from inventario where 
    codigo like '1%' " ; 
    
    if(($resultadotvw = odbc_exec($id,$sql_tvw)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_tvw = odbc_fetch_array($resultadotvw)){
      $tvw=$arr_tvw["total"];     

    }

    $sql_tvwc="select sum(existencia) as total from inventario where codigo like '1%'" ; 
    if(($resultadovwc = odbc_exec($id,$sql_tvwc)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_tvwc = odbc_fetch_array($resultadovwc)){
      $tvwc=$arr_tvwc["total"];     

    }
    

    //daihatsu
    $sql_td="select sum(costo*existencia) as total from inventario where 
    codigo like '2%' " ; 
    
    if(($resultadotd = odbc_exec($id,$sql_td)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_td = odbc_fetch_array($resultadotd)){
      $td=$arr_td["total"];     

    }

    $sql_tdc="select sum(existencia) as total from inventario where codigo like '2%'" ; 
    if(($resultadodc = odbc_exec($id,$sql_tdc)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_tdc = odbc_fetch_array($resultadodc)){
      $tdc=$arr_tdc["total"];     

    }
    //Volvo
    
    $sql_tv="select sum(costo*existencia) as total from inventario where 
    codigo like '0%' " ; 
    
    if(($resultadotv = odbc_exec($id,$sql_tv)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_tv = odbc_fetch_array($resultadotv)){
      $tv=$arr_tv["total"];
      if($tv === NULL) {$tv=0;}     
      
    }
   
    $sql_tvc="select sum(existencia) as total from inventario where codigo like '0%'" ; 
    if(($resultadovc = odbc_exec($id,$sql_tvc)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_tvc = odbc_fetch_array($resultadovc)){
      $tvc=$arr_tvc["total"];     
      if($tvc === NULL) {$tvc=0;}     
    }
    //Accesorios
    $sql_ta="select sum(costo*existencia) as total from inventario where 
    codigo like '9%' " ; 
    
    if(($resultadota = odbc_exec($id,$sql_ta)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_ta = odbc_fetch_array($resultadota)){
      $ta=$arr_ta["total"];     

    }

    $sql_tac="select sum(existencia) as total from inventario where codigo like '9%'" ; 
    if(($resultadoac = odbc_exec($id,$sql_tac)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr_tac = odbc_fetch_array($resultadoac)){
      $tac=$arr_tac["total"];     

    }

    /// ventas inventario 
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '1%' and substring(codigo,len(codigo)-1,2) in ('GR','TW','JP','EU')
          AND movimiento='Venta'";

    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $montoVWEXT=$arr["monto"];
      $cantidadesVWEXT=$arr["cantidades"];     

    }    
    
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '1%' and substring(codigo,len(codigo)-1,2) NOT IN ('GR','TW','JP','EU')
          AND movimiento='Venta'";

    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $montoVWL=$arr["monto"];
      $cantidadesVWL=$arr["cantidades"];     

    }    

    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '2%' and substring(codigo,len(codigo)-1,2) in ('GR','TW','JP','EU')
          AND movimiento='Venta'";

    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $montoDEXT=$arr["monto"];
      $cantidadesDEXT=$arr["cantidades"];     

    }    
    
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '2%' and substring(codigo,len(codigo)-1,2) NOT IN ('GR','TW','JP','EU')
          AND movimiento='Venta'";

    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $montoDL=$arr["monto"];
      $cantidadesDL=$arr["cantidades"];     

    }    

    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '0%' and substring(codigo,len(codigo)-1,2) in ('GR','TW','JP','EU')
          AND movimiento='Venta'";

    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $montoVVEXT=$arr["monto"];
      $cantidadesVVEXT=$arr["cantidades"]; 
       if ($montoVVEXT=='') {
            $montoVVEXT=0;
          }      

    }    
    
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '0%' and substring(codigo,len(codigo)-1,2) NOT IN ('GR','TW','JP','EU')
          AND movimiento='Venta'";

    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($resultado)){
      $montoVVL=$arr["monto"];
      $cantidadesVVL=$arr["cantidades"]; 
      if ($montoVVL=='') {
            $montoVVL=0;
          }      

    }    

    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '9%' and substring(codigo,len(codigo)-1,2) in ('GR','TW','JP','EU')
          AND movimiento='Venta'";

    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    $montoACCEXT=0;
    while($arr = odbc_fetch_array($resultado)){
      $montoACCEXT=$arr["monto"];
      $cantidadesACCEXT=$arr["cantidades"]; 
      if ($montoACCEXT=='') {
            $montoACCEXT=0;
          }    

    }    
    
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '9%' and substring(codigo,len(codigo)-1,2) NOT IN ('GR','TW','JP','EU')
          AND movimiento='Venta'";

    if(($resultado = odbc_exec($id,$sql)) === false)
      die("Error en Select: ". odbc_errormsg($id));
    $montoACCL=0;
    while($arr = odbc_fetch_array($resultado)){
      $montoACCL=$arr["monto"];
      $cantidadesACCL=$arr["cantidades"];     
      if ($montoACCL=='') {
            $montoACCL=0;
          }
    }    
/*
    echo 'Gastos Fijos: '.number_format($total,2);
    echo "<br>";
    echo 'Total de Ventas: '.number_format($facturas,2);
    echo "<br>";
    echo 'Total de descuentos: '.number_format($descuentos,2);
    echo "<br>";   
    echo 'Compras al exterior :'.number_format($comprasEX,2). ' por el tipo de cambio : '.$tc.' / '.number_format($comprasEX*$tc,2) ;
    echo "<br>";
    echo 'Compras Locales: '.number_format($comprasL);
    echo "<br>";
    echo 'Gastos: '.number_format($egresos,2);
    echo "<br>";
    echo 'Total Volkswagen: '.number_format($tvw);
    echo "<br>";
    echo 'Total Daihatsu: '.number_format($td,2);
    echo "<br>";
    echo 'Total Volvo: '.number_format($tv,2);
    echo "<br>";
    echo 'Total Accesorios:'.number_format($ta,2);
    echo "<br>";
    /// hasta aqui inventarios
*/

    
    $sql = "INSERT INTO tbl_Financiero
            (Compañia,fecha,I_Ventas,I_Descuentos,I_TotalVentas,
            E_ComprasL,E_ComprasE,E_TotCompras,Gastos,GastosFijos,TotalEgresos,Diferencia,
            T_CostoVW,T_ExisVW,T_CostoVV,T_ExisVV,T_CostoDH,T_ExisDH,T_CostoAC,T_ExisAC,
            T_VenCanLVW,T_VenMonLVW,T_VenCanEVW,T_VenMonEVW,T_VenCanLVV,T_VenMonLVV,T_VenCanEVV,T_VenMonEVV,
            T_VenCanLDH,T_VenMonLDH,T_VenCanEDH,T_VenMonEDH,T_VenCanLAC,T_VenMonLAC,T_VenCanEAC,T_VenMonEAC,
            GF_Salarios,GF_Alquiler,GF_CCSS,GF_Tributacion,GF_Agua,GF_Luz,GF_Telefono,GF_CostoFinancieroP)
            values
            (0,'".$newdate2."',".$facturas.",".$descuentos.",".$tventas.",".$comprasL.",".$tcomprasEX."
              ,".$totcompras.",".$egresos.",".$total.",".$total_egresos.",".$diferencia."
              ,".$tvw.",".$tvwc.",".$tv.",".$tvc.",".$td.",".$tdc.",".$ta.",".$tac."
              ,".$cantidadesVWL.",".$montoVWL.",".$cantidadesVWEXT.",".$montoVWEXT."
              ,".$cantidadesVVL.",".$montoVVL.",".$cantidadesVVEXT.",".$montoVVEXT."
              ,".$cantidadesDL.",".$montoDL.",".$cantidadesDEXT.",".$montoDEXT."
              ,".$cantidadesACCL.",".$montoACCL.",".$cantidadesACCEXT.",".$montoACCEXT."
              ,".$total_sal.",".$alquiler.",".$ccss.",".$tributacion.",".$agua."
              ,".$luz.",".$telefono.",".$costof.")";

    //echo $sql; exit;
    if(($res = odbc_exec($id,mb_convert_encoding($sql,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));
        
       // echo 'Registro Guardado';
    header("location: cierre_financiero_resultados.php");
    
    

    ?>




