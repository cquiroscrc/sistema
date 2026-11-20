<?php
session_start();

    include("db.php");

    $reajuste=0;
    $base=0;
    $ccss=0;
     if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }
    
    if((isset($_POST['base']))and(!empty($_POST['base']))){
        $base = $_POST['base'];
    }

    if((isset($_POST['ccss']))and(!empty($_POST['ccss']))){
        $ccss = $_POST['ccss'];
    }

    if((isset($_POST['reajuste']))and(!empty($_POST['reajuste']))){
        $reajuste = $_POST['reajuste'];
    }

    if((isset($_POST['salxhora']))and(!empty($_POST['salxhora']))){
        $salxhora = $_POST['salxhora'];
    }
    if((isset($_POST['extras']))){
        $extras = $_POST['extras'];
        $extras=$extras*$salxhora*1.5;
    }
    if((isset($_POST['vales']))){
        $vales = $_POST['vales'];
    }
    if((isset($_POST['compras']))){
        $compras = $_POST['compras'];
    }
    if((isset($_POST['otros_egresos']))){
        $otros_egresos = $_POST['otros_egresos'];
    }
    if((isset($_POST['valor_comision']))){
        $valor_comision = $_POST['valor_comision'];
    }
    if((isset($_POST['comision']))){
        $comision = $_POST['comision'];
        $comision = $comision*$valor_comision;
    }
    if((isset($_POST['valor_mandado']))){
        $valor_mandado = $_POST['valor_mandado'];
    }
    if((isset($_POST['mandado']))){
        $mandado = $_POST['mandado'];
        $mandado=$mandado*$valor_mandado;
    }
    if((isset($_POST['otros_ingresos']))){
        $otros_ingresos = $_POST['otros_ingresos'];
    }
    if((isset($_POST['idempl']))){
        $id_empleado = $_POST['idempl'];
    }
    if((isset($_REQUEST['fecha']))and(!empty($_REQUEST['fecha']))){
        $fecha = $_REQUEST['fecha'];
    }
    
    if((isset($_REQUEST['planilla']))and(!empty($_REQUEST['planilla']))){
        $planilla = $_REQUEST['planilla'];
    }
   // $fecha=$_SESSION['planilla']['fecha'];
    $mandadoccss=$mandado*0.1034;
    $ccss=$ccss+$mandadoccss;
    $ingresos=$base+$reajuste+$extras+$mandado+$comision+$otros_ingresos;
    $egresos=$vales+$compras+$ccss+$otros_egresos;
    $total=$ingresos-$egresos;
    
    $newdate=strtotime("$fecha");
    $newdate = date ( 'Y-m-d' , $newdate );
    $sql = "INSERT INTO rh_planilla
            (id_planilla,fecha,id_empleado,base,reajuste,salxhora,extras,vales,compras,otros_egresos,comision,mandados,ccss,otros_ingresos,total)
            values
            ('".$planilla."','".$newdate."','".$id_empleado."','".$base."','".$reajuste."','".$salxhora."','".$extras."','".$vales."','".$compras."','".$otros_egresos."','".$comision."','".$mandado."','".$ccss."','".$otros_ingresos."','".$total."')
            ";

    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));        

       // echo 'Registro Guardado';
      header("location: planilla_planilla.php?fecha=".$fecha."&planilla=".$planilla);
    
?>

