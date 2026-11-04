<?php
  session_start();
  include("db.php");

    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $documento = $_REQUEST['documento'];      
    } 
    
    $codigos= array();
    $cantidades= array();
    $existencias=array();       

    $select ="SELECT h.codigo, h.cantidad, h.Factura, i.existencia FROM hgeneral h, inventario i WHERE h.Factura='".$documento."' and h.codigo = i.codigo";

    if(($res_select = odbc_exec($id,$select)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    while($arr_1 = odbc_fetch_array($res_select)){
      $codigos[]=$arr_1['codigo'];
      $cantidades[]= $arr_1['cantidad'];
      $existencias[]= $arr_1['existencia'];
    } //while
    
    foreach ($codigos as $key => $value) {
      $exis=$existencias[$key]- $cantidades[$key];

      $sql_inventario = "UPDATE inventario SET EXISTENCIA='".$exis.
                        "' WHERE CODIGO='".$value."'";

      if(($res_inventario = odbc_exec($id,$sql_inventario)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    }// foreach  
    
    $sql_hgeneral = "UPDATE hgeneral  SET estado='ANULADA', cantidad='0'
        WHERE Factura='".$documento."'";
  
    if(($res_g = odbc_exec($id,$sql_hgeneral)) === false)
      die("Error en Select: ". odbc_errormsg($id));

    $sql_hfacturas = "UPDATE hfacturas  SET estado='ANULADA', 
                          subtotal='0', impuesto='0', descuento='0',total='0'
                      WHERE nfac='".$documento."'";

    if(($res_f = odbc_exec($id,$sql_hfacturas)) === false)
      die("Error en Select: ". odbc_errormsg($id));


header("location: anular_factura.php");  
  
?>