<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    $sql="SELECT * FROM tmpCxP order by proveedor,nfac,fechaFact";
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    
    foreach ($respuesta as $key => $arr) {
    

      $factura=$arr['nfac'];
      $proveedor=$arr['proveedor'];
      $total=$arr['total'];
      $fecha=$arr['fechaCKS'];
      $banco=$arr['Cuenta'];
      $conta=$arr['CuentaC'];
      $cheque=$arr['chkAsociado'];
      //$total_chk=$total_chk+$total;

      $sql_cxp="UPDATE CXP SET chkCancelacion='12345', Estado='Cancelada' where nfac= '$factura' and proveedor='$proveedor'";
      $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cxp);

    }    
    
    $sql= "DELETE FROM tmpCXP";
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  
  ?>

   <SCRIPT LANGUAGE="javascript"> // Redirecciona al home      
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),5000);
    </SCRIPT> 
