<?php
session_start();
    require("db.php");

    if((isset($_POST['retencion1']))and(!empty($_POST['retencion1']))){
        $retencion1 = $_POST['retencion1'];
    }
    if((isset($_POST['retencion2']))and(!empty($_POST['retencion2']))){
        $retencion2 = $_POST['retencion2'];
    }
    if((isset($_POST['retencion3']))and(!empty($_POST['retencion3']))){
        $retencion3 = $_POST['retencion3'];
    }
    if((isset($_POST['conyugue']))and(!empty($_POST['conyugue']))){
        $conyugue = $_POST['conyugue'];
    }
    if((isset($_POST['hijo']))and(!empty($_POST['hijo']))){
        $hijo = $_POST['hijo'];
    }
    if((isset($_POST['aviso']))and(!empty($_POST['aviso']))){
        $aviso = $_POST['aviso'];
    }
    if((isset($_POST['aviso2']))and(!empty($_POST['aviso2']))){
        $aviso2 = $_POST['aviso2'];
    }
    if((isset($_POST['descuento']))and(!empty($_POST['descuento']))){
        $descuento = $_POST['descuento'];
    }
    
    //Retencion 1
    $sql= "UPDATE tbl_Utiles SET Valor='$retencion1' WHERE IDUtilidad='14'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));
    //Retencion 2
    $sql= "UPDATE tbl_utiles SET Valor='$retencion2' WHERE IDUtilidad='15'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Retencion 3
    $sql= "UPDATE tbl_Utiles SET Valor='$retencion3' WHERE IDUtilidad='16'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Conyugue
    $sql= "UPDATE tbl_Utiles SET Valor='$conyugue' WHERE IDUtilidad='17'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //hijo
    $sql= "UPDATE tbl_Utiles SET Valor='$hijo' WHERE IDUtilidad='18'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Aviso
    $sql= "UPDATE tbl_Utiles SET Descripcion='$aviso' WHERE IDUtilidad='11'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Aviso 2
    $sql= "UPDATE tbl_Utiles SET Valor='$aviso2' WHERE IDUtilidad='11'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    //Descuento
    $sql= "UPDATE tbl_Utiles SET Valor='$descuento' WHERE IDUtilidad='2'";
    //echo $sql; exit;
    if(($resultado = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));


// echo 'Registro Guardado';
       header("location: ../../utilidades");

?>