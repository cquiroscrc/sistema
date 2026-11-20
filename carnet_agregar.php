<?php
session_start();
    require("db.php");

    if((isset($_POST['carnet']))and(!empty($_POST['carnet']))){
        $carnet = $_POST['carnet'];
    }

    if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }

    if((isset($_POST['cedula']))and(!empty($_POST['cedula']))){
        $cedula = $_POST['cedula'];
    }

    if((isset($_POST['telefono']))and(!empty($_POST['telefono']))){
        $telefono = $_POST['telefono'];
    }

    if((isset($_POST['celular']))and(!empty($_POST['celular']))){
        $celular = $_POST['celular'];
    }

    if((isset($_POST['correo']))and(!empty($_POST['correo']))){
        $correo = $_POST['correo'];
    }

    if((isset($_POST['tipo']))and(!empty($_POST['tipo']))){
        $tipo = $_POST['tipo'];
    }

    

    $sql="SELECT * FROM fechas WHERE movimiento ='Facturacion'" ;  
    if(($res = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
    while($arr = odbc_fetch_array($res)){
        $fecha_sis=strtotime($arr['Fecha']);
    }



    $sql = "INSERT INTO clientes
            (codigo,nombre,descuento,credito,saldo,comentario,telefono,direccion,cedula,celular,correo,nacimiento,afiliacion)
            values
            (".$carnet.",'".strtoupper($nombre)."',10,0,0,'".$comentario."','".$telefono."','".$direccion."',
            '".$cedula."','".$celular."','".$correo."','".date("Y-m-d h:i:s.m",$nacimiento)."','".date("Y-m-d h:i:s.m", $fecha_sis)."')";

    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        
       // echo 'Registro Guardado';
       
    







?>