<?php
session_start();

    include("db.php");


     if((isset($_POST['empleado']))and(!empty($_POST['empleado']))){
        $empleado = $_POST['empleado'];
    }
    
    if((isset($_POST['dias']))and(!empty($_POST['dias']))){
        $dias = $_POST['dias'];
    }

    if((isset($_POST['Date2']))and(!empty($_POST['Date2']))){
        $Date2 = $_POST['Date2'];
    }

    
    $newdate=strtotime("$Date2");
    $newdate = date ( 'Y-m-d' , $newdate );
    $sql = "INSERT INTO vacaciones
            (id_empleado,fecha,cantidad)
            values
            ('".$empleado."','".$newdate."',".$dias.")";

    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
       // echo 'Registro Guardado';
      header("location: planilla_vacaciones_reporte.php");
    
?>

