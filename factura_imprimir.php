<?php 
session_start();
include "menu.php";
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente}

if((isset($_REQUEST['documento']))and(!empty($_REQUEST['documento']))){
        $documento = $_REQUEST['documento'];
    }

$tipo=1;
 ?>


<html>
<head>
<style type="text/css">
@media print {
    div,a {display:none}
    .ver {display:block}
    .nover {display:none}
    p { font: 11px/12px sans-serif }
    .mini{
        font: 8px/10px sans-serif 
    }
    #mini{
        font: 8px/10px sans-serif 
    }

}
@page{
  
  margin: 1%;
}
h5{
    font-size:100%    
}


</style>
<script>
function impre(num) {
    document.getElementById(num).className="ver";
    print();
    document.getElementById(num).className="nover";
}
</script>
</head>

<body>
    <a href="consulta_factura.php"><img width="48"  height="48" src= "img/back.png">Volver</a>

    <a href="#" onclick="impre('uno');return false"><img width="48"  height="48" src= "img/imprime.png">Imprime</a>
    
<?php 
$sqlencabezado="SELECT * FROM tbl_Utiles WHERE IDUtilidad= 10";  

      if(($res_encabezado = odbc_exec($id,$sqlencabezado)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr_enca = odbc_fetch_array($res_encabezado)){
        $sociedad=$arr_enca['Descripcion'];
        $cedula = $arr_enca['Valor'];
      } /// Sociedad

$sql_hfacturas="SELECT * FROM hfacturas WHERE nfac= '$documento'";  

      if(($res_hfacturas = odbc_exec($id,$sql_hfacturas)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr_hfacturas = odbc_fetch_array($res_hfacturas)){
        $time=$arr_hfacturas['fechafact'];
        $fechas=strtotime($time);
        $adicional=$arr_hfacturas['Adicional'];
        $nombre=$arr_hfacturas['Nombre'];
        $subtotal=$arr_hfacturas['subtotal'];
        $impuesto=$arr_hfacturas['impuesto'];
        $descuento=$arr_hfacturas['descuento'];
        $total=$arr_hfacturas['total'];
        $tipofact=$arr_hfacturas['Tipo'];
        $cliente=$arr_hfacturas['proveedor'];
        //revisar una vez exista el cliente
        


        $vendedor=$arr_hfacturas['vendedor'];
        $tipo='CONTADO';
        if ($cliente!=0) {
            $tipo='CREDITO';
            $sql_cliente="SELECT * FROM  clientes WHERE codigo= '$cliente'";  

          if(($res_cliente = odbc_exec($id,$sql_cliente)) === false)
              die("Error en Select: ". odbc_errormsg($id));

          while($arr_cliente = odbc_fetch_array($res_cliente)){
            $nombre=$arr_cliente['nombre'];
            
          } /// Sociedad
        }
      } /// Hfacturas

$sql_vendedor="SELECT * FROM Vendedores WHERE codigo like '%$vendedor'";
    if(($res_vendedor = odbc_exec($id,$sql_vendedor)) === false)
          die("Error en Select: ". odbc_errormsg($id));

      while($arr_vendedor = odbc_fetch_array($res_vendedor)){
        $nombre_vendedor=$arr_vendedor['nombre'];
      }

 ?>


<div id="uno">

<h4><b>REPUESTOS LOS PATICAS</b></h4> 
    <p><?php echo $sociedad . ' Cedula '.$cedula ?></p>

    <br>
    <h4><b><?php echo $documento ?></b></h4>
    <h4><b>COPIA FACTURA</b></h4>
    <p>Fecha :<?php echo date("d-m-Y", $fechas);?> Hora: <?php echo date("h:i:s", $fechas); ?></p>
    <hr>
    <p>Nombre: <?php echo ' '.$nombre; ?></p>
    <?php if ($adicional==null) { 

        }else{
            ?>
            <p>Adicional: <?php echo ' '.$adicional; ?></p>
        <?php

        
         
    } ?>
    <p><?php echo $tipo; ?></p>
    <p>Cliente: <?php echo $cliente ?></p>
    <hr>
    <p>Vendedor: <?php echo $vendedor; echo ' - '.$nombre_vendedor;?></p>
    <hr>
    <p>CANT // DESCRIPCION // PRECIO // TOTAL</p>
    <hr>
    <?php 

        $sqldatos="SELECT h.codigo, h.cantidad, h.precio, h.tipo, i.descripcion FROM hgeneral h, inventario i 
        WHERE Factura ='$documento' and h.codigo = i.codigo";  

        if(($res_datos = odbc_exec($id,$sqldatos)) === false)
            die("Error en Select: ". odbc_errormsg($id));

        while($arr_d = odbc_fetch_array($res_datos)){
            $codigo=$arr_d['codigo'];
            $cantidad= $arr_d['cantidad'];
            $descripcion=$arr_d['descripcion'];
            $precio= $arr_d['precio'];
            $totallinea=$cantidad*$precio;
            ?>
            <p><?php echo $cantidad ?> // <?php echo $descripcion; ?></p>
            <p>//<?php echo $codigo; ?> </p><p> <?php echo number_format($precio,2); ?> // <?php echo number_format($totallinea,2); ?></p>
            <hr>
            <?php 

        }
        ///calculo
        $c_total=$subtotal-$descuento+$impuesto;
        $c_mensajeria=$total-$c_total;
        
        if ($c_mensajeria > 0) {
            ?>
            <br>
            <p>SERVICIO DE MENSAJERIA (<?php echo $c_mensajeria; ?>)</p>        

            <?php 


        }

     ?>
    
    
    <br>
    <p>        SUBTOTAL:    <?php echo number_format($subtotal,2); ?></p>
    <p>        DESCUENTO:   <?php echo number_format($descuento,2); ?></p>
    <p>        IMPUESTO:    <?php echo number_format($impuesto,2); ?></p>
    <h4><b>TOTAL:       <?php echo number_format($total,2) ?></b></h4>
    <hr>
    <p>Autorizado para impresion por sistema de computo mediante resolucion #11-97</p>
    <p><b> No se aceptan reclamos o devoluciones sin factura</b> Las piezas electricas no tienen devolucion</p>
    <p>B° Carit Tels: 2227-8252 // 2227-98-92 // 2227-5804  Fax: 2227-2110</p>
    <h4>ESTO ES UNA COPIA DE LA FACTURA/ NO TIENE VALOR PARA RECLAMOS</h4>

    <p>Gracias por su compra</p>
    <p>Visite: www.lospaticas.com</p>
    <br><br>
    <p>COPIA</p>
</div>



<script src="js/bootstrap.min.js"></script>
</body>
</html>

