<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
 require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
  $fecha_cierre = $_REQUEST['fecha'];
}  
$fecha1=$fecha_cierre;
$fecha2=$fecha_cierre;

$varsubtotal=0;
$varimpuesto=0;
$vardescuento=0;
$vartotal=0;
$varsubtotalC=0;
$varimpuestoC=0;
$vardescuentoC=0;
$vartotalC=0;
$varmensajeria=0;
$tempmensa=0;
$tempmensaC=0;
$totalrecibos=0;
$totalegresos=0;
$totalingresos=0;
$totaldepositos=0;
$totaldif=0;

 $sql="SELECT * FROM hfacturas WHERE fechafact BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  order by nfac";  
 $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
 
 foreach ($respuesta as $key => $arr) {

  //hacer los totales


    if ($arr["Tipo"]==0){
      $varsubtotal = $varsubtotal + $arr["subtotal"];
      $varimpuesto = $varimpuesto + $arr["impuesto"];
      $vardescuento = $vardescuento + $arr["descuento"];
      $vartotal= $vartotal + $arr["total"];
      $temptotal=$varsubtotal+$varimpuesto-$vardescuento;
      
      

    }else{
      $varsubtotalC = $varsubtotalC + $arr["subtotal"];
      $varimpuestoC = $varimpuestoC + $arr["impuesto"];
      $vardescuentoC = $vardescuentoC + $arr["descuento"];
      $vartotalC= $vartotalC + $arr["total"];  
      $temptotalC=$varsubtotalC+$varimpuestoC-$vardescuentoC;
      
    }
  }

  $sql_egresos="SELECT e.Fecha, e.Documento, e.Tipo, e.Descripcion, e.Monto, t.Descripcion as egreso
  FROM tbl_Egresos e, tbl_TiposEgresos t
  WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'
  AND e.Tipo = t.Tipo order by e.Tipo,e.Documento";
  
  $e_descuentos=0;
  $e_repuestos=0;
  $e_papeleria=0;
  $e_combustible=0;
  $e_atencion=0;
  $e_encomiendas=0;
  $e_luz=0;
  $e_agua=0;
  $e_telefono=0;
  $e_otros=0;
  $e_alfaro=0;

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_egresos);
  $totalegresos=0;
  foreach ($respuesta as $key => $arr_egresos) {
    $tipoE=$arr_egresos["Tipo"];
    if ($tipoE==0) {
      $e_descuentos=$e_descuentos+ $arr_egresos['Monto'];
    }
    if ($tipoE==1) {
      $e_repuestos=$e_repuestos+ $arr_egresos['Monto'];
    }
    if ($tipoE==2) {
      $e_papeleria=$e_papeleria+ $arr_egresos['Monto'];
    }
    if ($tipoE==3) {
      $e_combustible=$e_combustible+ $arr_egresos['Monto'];
    }
    if ($tipoE==4) {
      $e_atencion=$e_atencion+ $arr_egresos['Monto'];
    }
    if ($tipoE==5) {
      $e_encomiendas=$e_encomiendas+ $arr_egresos['Monto'];
    }
    if ($tipoE==6) {
      $e_luz=$e_luz+ $arr_egresos['Monto'];
    }
    if ($tipoE==7) {
      $e_agua=$e_agua+ $arr_egresos['Monto'];
    }
    if ($tipoE==8) {
      $e_telefono=$e_telefono+ $arr_egresos['Monto'];
    }
    if ($tipoE==9) {
      $e_otros=$e_otros+ $arr_egresos['Monto'];
    }
    if ($tipoE==10) {
      $e_alfaro=$e_alfaro+ $arr_egresos['Monto'];
    }

    $totalegresos=$totalegresos+$arr_egresos["Monto"];
  }

$sql_depositos="SELECT * FROM tbl_Bancos WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000' AND Descripcion like 'Caja del %'  order by Cuenta, Documento";  

  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_depositos);
  foreach ($respuesta as $key => $arr_depositos) {
    $totaldepositos=$totaldepositos+$arr_depositos["Credito"];
  }

$sql_recibos="SELECT * FROM cxc WHERE Fecha BETWEEN '$fecha1 00:00:00.000' AND '$fecha2 23:59:59.000'  and NFAC like'R-%' order by NFac";  

$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_recibos);
foreach ($respuesta as $key => $arr_recibos) {
  $totalrecibos=$totalrecibos+$arr_recibos["Total"];
}
$totalingresos=$vartotal+$totalrecibos;
$totalcaja=$totalingresos-$totalegresos;
$totaldif=$totaldepositos-$totalingresos+$totalegresos;
if($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" ) { /// **** Cambio
    echo '<div class="col-sm-5">';
    echo '<h3 style="color:blue;"><b>Contado:</b></h3>';
    echo '<h4>Subtotal : ' . number_format($varsubtotal, 2) . '</h4>';
    echo '<h4>Descuento : ' . number_format($vardescuento, 2) . '</h4>';
    echo '<h4>Impuesto : ' . number_format($varimpuesto, 2) . '</h4>';
    echo '<h3><b>Total : ' . number_format($vartotal, 2) . '</b></h3>';
    echo '</div>';

    echo '<div class="col-sm-5">';
    //echo '<center>';
    echo '<h3 style="color:green;"><b>Credito:</b></h3>';

    echo '<h4>Subtotal : ' . number_format($varsubtotalC, 2) . '</h4>';
    echo '<h4>Descuento : ' . number_format($vardescuentoC, 2) . '</h4>';
    echo '<h4>Impuesto : ' . number_format($varimpuestoC, 2) . '</h4>';
    echo '<h3><b>Total : ' . number_format($vartotalC, 2) . '</b></h3>';
    //echo '</center>';
    echo '</div>';


    echo '<div class="col-sm-2">';
    echo '<h3 style="color:red;"><b>Resultado:</b></h3>';

    echo '<h4>Ingresos : ' . number_format($totalingresos, 2) . '</h4>';
    echo '<h4>Egresos : ' . number_format($totalegresos, 2) . '</h4>';
    echo '<h4>Total Caja : ' . number_format($totalcaja, 2) . '</h4>';
    echo '<h4>Depositos : ' . number_format($totaldepositos, 2) . '</h4>';
    echo '<h3 style="display:inline;"> Diferencia : </h3>';

    if ($totaldif < 0) {

        echo '<h3 style="display:inline;color:red;"><b>' . number_format($totaldif, 2) . '</b></h3>';
    } else {
        echo '<h3 style="display:inline;color:green;"><b>' . number_format($totaldif, 2) . '</b></h3>';
    }

    echo '</div>';

}
if ($totaldif >2000) {
   
    echo '<h4 style="display: inline">Diferencias</h4><h4 class="parpadea" style="display: inline; color:green"><b> Sobra</b></h4>';
}
if ($totaldif <-2000) {

    echo '<h4 style="display: inline">Diferencias</h4><h4 class="parpadea" style="display: inline; color:red"><b> Falta</b></h4>';
}
  


  // Actualización de la BD
  $existe=0;

  $sql_existe = "SELECT * FROM cierrediario WHERE Fecha='$fecha2'";
  $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_existe);
  foreach ($respuesta as $key => $arr) {
    $existe=1;
  }
  $totalrecibos=$totalrecibos*-1;
  if ($existe==1) {

    $sql_actualiza="UPDATE cierrediario SET I_VentasEfectivo ='".$vartotal."',
                  I_VentasCredito ='".$vartotalC."', I_RecibosDinero='".$totalrecibos."',
                  E_Descuentos='".$e_descuentos."', E_Repuestos='".$e_repuestos."',
                  E_Papeleria='".$e_papeleria."', E_Combustible='".$e_combustible."',
                  E_AtencionPublico='".$e_atencion."', E_Encomiendas='".$e_encomiendas."',
                  E_Luz='".$e_luz."',E_Agua='".$e_agua."',E_Telefono='".$e_telefono."',
                  E_Otros='".$e_otros."',E_Alfaro='".$e_alfaro."',T_Caja='".$totalcaja."',
                  T_Depositos='".$totaldepositos."',T_FaltaSobra='".$totaldif."'
                WHERE Fecha ='".$fecha2."'";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_actualiza);

            
 
  }
  if ($existe==0) {

    $sql_cierre="INSERT INTO cierrediario 
        (Fecha,I_VentasEfectivo,I_VentasCredito,I_RecibosDinero,E_Descuentos,E_Repuestos,E_Papeleria,e_Combustible,
          E_AtencionPublico,E_Encomiendas,E_Luz,E_Agua,E_Telefono,E_Otros,E_Alfaro,T_Caja,T_Depositos,T_FaltaSobra,Estado,Compañia,Notas)
        values ('".$fecha2."','".$vartotal."','".$vartotalC."','".$totalrecibos."','".$e_descuentos."',
          '".$e_repuestos."','".$e_papeleria."','".$e_combustible."','".$e_atencion."','".$e_encomiendas."',
          '".$e_luz."','".$e_agua."','".$e_telefono."','".$e_otros."','".$e_alfaro."','".$totalcaja."','".$totaldepositos."','".$totaldif."','0','0','')";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cierre);
    
  }
         
     ?>





