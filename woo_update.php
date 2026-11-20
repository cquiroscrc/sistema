<?php
session_start();
    require("db.php");

    $sql_select="SELECT i.CODIGO, i.VENTA, i.potencia, w.Codigo FROM inventario i, woocommerce w WHERE i.CODIGO= w.Codigo";

    if(($resultado = odbc_exec($id,$sql_select)) === false)
    die("Error en Select: ". odbc_errormsg($id));
    while($arr = odbc_fetch_array($resultado)){
        $precio=$arr['VENTA'];
        $potencia=$arr['potencia'];
        $precio=$precio+($potencia*100);
        $codigo=$arr['CODIGO'];

        
       	$sql = "UPDATE woocommerce SET Precio='$precio' WHERE Codigo='$codigo'";
        
        if(($resultado2 = odbc_exec($id,$sql)) === false)
    	die("Error en Select: ". odbc_errormsg($id));


    }
    
    


   //echo $sql; exit;
    
?>