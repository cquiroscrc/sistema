<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    $sql="SELECT * FROM tmpCxP order by proveedor,nfac,fechaFact";
    echo $sql;
    echo '<br>';
    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    $total_chk=0;
    $proveedor='';
    foreach ($respuesta as $key => $arr) {
      if ($proveedor=='') {
        $proveedor=$arr['proveedor'];
      }

      if ($proveedor!=$arr['proveedor']) {
        // salva el CHEQUE EN EL BANCO
        $sql_pro="SELECT nombre FROM Proveedores WHERE codigo='$proveedor'";
        echo $sql_pro;
        echo '<br>';
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pro);
        foreach ($ejecutar as $key => $value) {
          $nombre=$value['nombre'];
        }
        $descripcion=$proveedor.'-'.$nombre;
        $sql_banco= "INSERT INTO tbl_Bancos (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC) 
        VALUES('$fecha','$banco','$cheque','$descripcion',0,$total_chk,'1900-01-01 00:00:00.000','1',0,'0','$conta')";
        echo $sql_banco;
        echo '<br>';
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_banco);
        $total_chk=0;
        $proveedor=$arr['proveedor'];
      }

      $factura=$arr['nfac'];
      $proveedor=$arr['proveedor'];
      $total=$arr['total'];
      $fecha=$arr['fechaCKS'];
      $banco=$arr['Cuenta'];
      $conta=$arr['CuentaC'];
      $cheque=$arr['chkAsociado'];
      $total_chk=$total_chk+$total;

      $sql_cxp="UPDATE CXP SET chkCancelacion='$cheque', Estado='Cancelada' where nfac= '$factura' and proveedor='$proveedor'";
      echo $sql_cxp;
      echo '<br>';
      $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cxp);

    }    
    $sql_pro="SELECT nombre FROM Proveedores WHERE codigo='$proveedor'";
    echo $sql_pro;
    echo '<br>';
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pro);
    foreach ($ejecutar as $key => $value) {
      $nombre=$value['nombre'];
    }
    $descripcion=$proveedor.'-'.$nombre;
    $sql_banco= "INSERT INTO tbl_Bancos (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC) 
    VALUES('$fecha','$banco','$cheque','$descripcion',0,$total_chk,'1900-01-01 00:00:00.000','1',0,'0','$conta')";
    echo $sql_banco;
    echo '<br>';
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_banco);

    $sql= "DELETE FROM tmpCXP";
    echo $sql;
    echo '<br>';
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
  
  ?>

   <SCRIPT LANGUAGE="javascript"> // Redirecciona al home      
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),5000);
    </SCRIPT> 
