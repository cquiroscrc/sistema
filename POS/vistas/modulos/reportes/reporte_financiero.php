<?php
require("../../../modelos/conexion.php");
require("../../../controladores/utilitaria.controlador.php");
require("../../../modelos/utilitaria.modelo.php");
    
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
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_salarios);
    foreach ($respuesta as $key => $row) {
            $total_sal=$total_sal + $row['total'];
    }

    $subtotal=$alquiler+$agua+$luz+$telefono+$ccss+$tributacion+$total_sal;
    $financiero=($subtotal*$costof)/100;
    $reserva=($subtotal+$financiero)/12;
    $total=$subtotal+$financiero+$reserva;
    
    /// Hasta aqui los Gastos


    $sql_facturas="SELECT sum(total) AS total FROM hfacturas where fechafact BETWEEN '$newdate1' AND '$newdate2'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_facturas);
    foreach ($respuesta as $key => $arr) {
      $facturas=$arr["total"];
    }
    
    $sql_descuentos="SELECT sum(E_Descuentos) AS total FROM cierrediario where Fecha BETWEEN '$newdate1' AND '$newdate2'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_descuentos);
    foreach ($respuesta as $key => $arr) {
      $descuentos=$arr["total"];
    }

    $tventas=$facturas-$descuentos;
    
    $sql_comprasEX="SELECT sum(c.total) as CExterior from compras c where Fecha BETWEEN '$newdate1' and '$newdate2' AND 
        proveedor in (select codigo from proveedores where tipo='E' like '29%')";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_comprasEX);
    $tcomprasEX=0;
    foreach ($respuesta as $key => $arr) {
      $comprasEX=$arr["CExterior"];
    }

    $tcomprasEX=$comprasEX*$tc;

    $sql_comprasEX="SELECT sum(c.total) as CExterior from compras c where Fecha BETWEEN '$newdate1' and '$newdate2' AND 
            proveedor in (select codigo from proveedores where tipo='E' NOT like '29%')";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_comprasEX);
    $tcomprasEX2=0;
    foreach ($respuesta as $key => $arr) {
        $comprasEX2=$arr["CExterior"];
    }

    $tcomprasEX=$comprasEX2+$tcomprasEX;

    

    $sql_comprasL="SELECT sum(c.total) as CExterior from compras c where Fecha BETWEEN '$newdate1' and '$newdate2' AND 
        proveedor in (select codigo from proveedores where tipo='L')";
    $comprasL=0;
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_comprasL);
    foreach ($respuesta as $key => $arr) {
      $comprasL=$arr["CExterior"];
    }
    $totcompras=$tcomprasEX+$comprasL;

    $sql_egresos="SELECT sum( E_Papeleria+E_Combustible+E_AtencionPublico+E_Encomiendas+E_Luz+E_Agua+E_Telefono+E_Otros) as Total From cierrediario
        where Fecha BETWEEN '$newdate1' and '$newdate2'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_egresos);
    foreach ($respuesta as $key => $arr) {
      $egresos=$arr["Total"];
    }

    $total_egresos=$egresos+$total+$totcompras;
    $diferencia=$tventas-$total_egresos;

    // Hasta aqui ventas, compras y egresos

    
    // volkswagen
    $sql_tvw="select sum(costo*existencia) as total from inventario where 
    codigo like '1%' " ;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tvw);
    foreach ($respuesta as $key => $arr_tvw) {
        $tvw=$arr_tvw["total"];
    }

    $sql_tvwc="select sum(existencia) as total from inventario where codigo like '1%'" ;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tvwc);
    foreach ($respuesta as $key => $arr_tvwc) {
      $tvwc=$arr_tvwc["total"];
    }
    

    //daihatsu
    $sql_td="select sum(costo*existencia) as total from inventario where 
    codigo like '2%' " ;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_td);
    foreach ($respuesta as $key => $arr_td) {
        $td=$arr_td["total"];
    }

    $sql_tdc="select sum(existencia) as total from inventario where codigo like '2%'" ;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tdc);
    foreach ($respuesta as $key => $arr_tdc) {
      $tdc=$arr_tdc["total"];
    }
    //Volvo
    
    $sql_tv="select sum(costo*existencia) as total from inventario where 
    codigo like '0%' " ;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tv);
    foreach ($respuesta as $key => $arr_tv) {
      $tv=$arr_tv["total"];
      if($tv === NULL) {$tv=0;}
    }
   
    $sql_tvc="select sum(existencia) as total from inventario where codigo like '0%'" ;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tvc);
    foreach ($respuesta as $key => $arr_tvc) {
      $tvc=$arr_tvc["total"];     
      if($tvc === NULL) {$tvc=0;}     
    }

    //Accesorios
    $sql_ta="select sum(costo*existencia) as total from inventario where 
    codigo like '9%' " ;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_ta);
    foreach ($respuesta as $key => $arr_ta) {
      $ta=$arr_ta["total"];
    }

    $sql_tac="select sum(existencia) as total from inventario where codigo like '9%'" ;

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_tac);
    foreach ($respuesta as $key => $arr_tac) {
      $tac=$arr_tac["total"];
    }

    /// ventas inventario 
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '1%' and substring(codigo,len(codigo)-1,2) in ('GR','TW','JP','EU','ZL')
          AND movimiento='Venta'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $montoVWEXT=$arr["monto"];
      $cantidadesVWEXT=$arr["cantidades"];
    }    
    
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '1%' and substring(codigo,len(codigo)-1,2) NOT IN ('GR','TW','JP','EU','ZL')
          AND movimiento='Venta'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $montoVWL=$arr["monto"];
      $cantidadesVWL=$arr["cantidades"];
    }    

    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '2%' and substring(codigo,len(codigo)-1,2) in ('GR','TW','JP','EU','ZL')
          AND movimiento='Venta'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $montoDEXT=$arr["monto"];
      $cantidadesDEXT=$arr["cantidades"];
    }    
    
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '2%' and substring(codigo,len(codigo)-1,2) NOT IN ('GR','TW','JP','EU','ZL')
          AND movimiento='Venta'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $montoDL=$arr["monto"];
      $cantidadesDL=$arr["cantidades"];
    }    

    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '0%' and substring(codigo,len(codigo)-1,2) in ('GR','TW','JP','EU','ZL')
          AND movimiento='Venta'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $montoVVEXT=$arr["monto"];
      $cantidadesVVEXT=$arr["cantidades"]; 
       if ($montoVVEXT=='') {
            $montoVVEXT=0;
          }
    }    
    
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '0%' and substring(codigo,len(codigo)-1,2) NOT IN ('GR','TW','JP','EU','ZL')
          AND movimiento='Venta'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $montoVVL=$arr["monto"];
      $cantidadesVVL=$arr["cantidades"]; 
      if ($montoVVL=='') {
            $montoVVL=0;
          }
    }    

    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '9%' and substring(codigo,len(codigo)-1,2) in ('GR','TW','JP','EU','ZL')
          AND movimiento='Venta'";

    $montoACCEXT=0;
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $montoACCEXT=$arr["monto"];
      $cantidadesACCEXT=$arr["cantidades"]; 
      if ($montoACCEXT=='') {
            $montoACCEXT=0;
          }
    }    
    
    $sql="SELECT sum(precio * cantidad) as monto, COUNT(*) as cantidades FROM hgeneral 
          where fecha BETWEEN '$fecha1' and '$fecha2' AND
          codigo like '9%' and substring(codigo,len(codigo)-1,2) NOT IN ('GR','TW','JP','EU','ZL')
          AND movimiento='Venta'";

    $montoACCL=0;
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($respuesta as $key => $arr) {
      $montoACCL=$arr["monto"];
      $cantidadesACCL=$arr["cantidades"];     
      if ($montoACCL=='') {
            $montoACCL=0;
          }
    }    

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
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

    ?>




