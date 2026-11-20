<?php
session_start();
    require("db.php");

     if((isset($_POST['marca']))and(!empty($_POST['marca']))){
        $marca = trim(strtoupper ($_POST['marca']));
    }
     if((isset($_POST['tipo']))and(!empty($_POST['tipo']))){
        $tipo = strtoupper ($_POST['tipo']);
    }
    if((isset($_POST['year']))and(!empty($_POST['year']))){
        $year = trim(strtoupper ($_POST['year']));
    }
    if((isset($_POST['placa']))and(!empty($_POST['placa']))){
        $placa = trim(strtoupper ($_POST['placa']));
    }
    if((isset($_POST['vin']))and(!empty($_POST['vin']))){
        $vin = trim(strtoupper ($_POST['vin']));
    }
    if((isset($_POST['motor']))and(!empty($_POST['motor']))){
        $motor = trim(strtoupper ($_POST['motor']));
    }
    if((isset($_POST['tipo2']))and(!empty($_POST['tipo2']))){
        $tipo2 = strtoupper ($_POST['tipo2']);
    }
    
    $sql = "UPDATE VAG SET Marca='$marca',Tipo='$tipo',Year='$year',Placa='$placa',Vin='$vin',Motor='$motor',
    Tipo2='$tipo2' WHERE Placa='$placa'";


   //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: carros_elegir.php");
    
?>