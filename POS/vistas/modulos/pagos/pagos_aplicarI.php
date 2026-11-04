<?php

  date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

    if(isset($_REQUEST['proveedor']) && !empty($_REQUEST['proveedor'])){
        $proveedor = $_REQUEST['proveedor'];
    }
    if(isset($_REQUEST['cuentaBancaria']) && !empty($_REQUEST['cuentaBancaria'])){
        $cuentaBancaria = $_REQUEST['cuentaBancaria'];
    }
    if(isset($_REQUEST['cuentaContable']) && !empty($_REQUEST['cuentaContable'])){
        $cuentaContable = $_REQUEST['cuentaContable'];
    }
    if(isset($_REQUEST['fechaChk']) && !empty($_REQUEST['fechaChk'])){
        $fechaChk = $_REQUEST['fechaChk'];
    }
    if(isset($_REQUEST['numeroChk']) && !empty($_REQUEST['numeroChk'])){
        $numeroChk = $_REQUEST['numeroChk'];
    }
    if(isset($_REQUEST['detalle']) && !empty($_REQUEST['detalle'])){
        $detalle = $_REQUEST['detalle'];
    }
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
        $monto = $_REQUEST['monto'];
    }

        $sql_banco= "INSERT INTO tbl_Bancos (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC) 
        VALUES('$fechaChk','$cuentaBancaria','$numeroChk','$detalle',0,$monto,'1900-01-01 00:00:00.000','1',0,'0','$cuentaContable')";
        
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_banco);
