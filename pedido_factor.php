<?php
  session_start();
  include("db.php");

    $seguro=0;

	if(isset($_REQUEST['fact']) && !empty($_REQUEST['fact'])){
	  $factura = $_REQUEST['fact'];
	} 

	if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
	  $proveedor = $_REQUEST['proveedor'];
	} 

	if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
	  $monto = $_REQUEST['monto'];
	} 
	if(isset($_REQUEST['tc']) && !empty($_REQUEST['tc'])){
	  $tc = $_REQUEST['tc'];
	} 
	if(isset($_REQUEST['aduana']) && !empty($_REQUEST['aduana'])){
	  $aduana = $_REQUEST['aduana'];
	} 
	if(isset($_REQUEST['seguro']) && !empty($_REQUEST['seguro'])){
	  $seguro = $_REQUEST['seguro'];
	} 
	if(isset($_REQUEST['impuesto']) && !empty($_REQUEST['impuesto'])){
	  $impuesto = $_REQUEST['impuesto'];
	} 

	if(isset($_REQUEST['monto'])){
		$factores=($monto*$tc)+$aduana+$seguro-$impuesto;
		$factores=$factores/$monto;
		$factor=(int)$factores;
	}

	if(isset($_REQUEST['factor']) && !empty($_REQUEST['factor'])){
	  $factor = $_REQUEST['factor'];
	} 

	$sql_corre="SELECT Codigo, Precio, Sugerido FROM Pedidos WHERE Factura='$factura'";
	if(($res_corre = odbc_exec($id,$sql_corre)) === false)
	    die("Error en Select: ". odbc_errormsg($id));

	while($arr_corre = odbc_fetch_array($res_corre)){
	    $precio_corre=$arr_corre['Precio'];
	    $sugerido_corre=$precio_corre*$factor*2;
	    $codigo_corre=$arr_corre['Codigo'];

	    $sql_actualiza="UPDATE Pedidos SET Sugerido='$sugerido_corre', factor='$factor' WHERE Codigo='$codigo_corre'";
	    if(($res_actualiza = odbc_exec($id,$sql_actualiza)) === false)
	        die("Error en Select: ". odbc_errormsg($id));


	}            
   
    header("location: pedido_precio.php?fact=".$factura."&proveedor=".$proveedor."&factor=".$factor);
?>
