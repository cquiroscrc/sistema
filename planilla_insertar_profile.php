<?php
session_start();
    include("db.php");

    $base=0;
    $reajuste=0;    
     if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }
    if((isset($_POST['apellido']))and(!empty($_POST['apellido']))){
        $apellido = $_POST['apellido'];
    }
    if((isset($_POST['cedula']))and(!empty($_POST['cedula']))){
        $cedula = $_POST['cedula'];
    }
    if((isset($_POST['direccion']))and(!empty($_POST['direccion']))){
        $direccion = $_POST['direccion'];
    }
    if((isset($_POST['telefono']))and(!empty($_POST['telefono']))){
        $telefono = $_POST['telefono'];
    }
    if((isset($_POST['cuenta']))and(!empty($_POST['cuenta']))){
        $cuenta = $_POST['cuenta'];
    }
    if((isset($_POST['base']))and(!empty($_POST['base']))){
        $base = $_POST['base'];
    }
    if((isset($_POST['reajuste']))and(!empty($_POST['reajuste']))){
        $reajuste = $_POST['reajuste'];
    }
    $mandado=0;
    if((isset($_POST['mandado']))and(!empty($_POST['mandado']))){
        $mandado = $_POST['mandado'];
    }
    $comision=0;
    if((isset($_POST['comision']))and(!empty($_POST['comision']))){
        $comision = $_POST['comision'];
    }
    if((isset($_POST['ingreso']))and(!empty($_POST['ingreso']))){
        $ingreso = $_POST['ingreso'];
    }
    if((isset($_POST['comentario']))and(!empty($_POST['comentario']))){
        $comentario = $_POST['comentario'];
    }
    if((isset($_POST['estado']))and(!empty($_POST['estado']))){
        $estado = $_POST['estado'];
    }

    $salxhora=($base+$reajuste)/196;
 
 

    /*** Archivo   ***/
    $target_dir = "subidas/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk=1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    //revisa si el
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "El Archivo es una imagen " .$check["mime"].".";
            $uploadOk=1;
        }else{
            echo " El archivo no es una imagen " .$check["mime"].".";
            $uploadOk=0;
        }
    }

    if (file_exists($target_file)) {
        echo "Lo siento, archivo ya existe.";
        $uploadOk=0;
        $foto=$target_file;

    }

    if ($_FILES["fileToUpload"]["size"]>5000000) {
        echo "Lo siento archivo muy grande";
        $uploadOk=0;
    }

    if($imageFileType !="jpg" && $imageFileType != "png" && $imageFileType !="jpeg" && $imageFileType !="gif" ){
        echo "Lo siento solo JPG, JPEG, PNG & GIF son permitido ";
        $uploadOk=0;
    }

    if ($uploadOk==0) {
        echo "Los siento, su archivo no se pudo subir.";

    }else{
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "El archivo ".basename($_FILES["fileToUpload"]["name"])." ha sido subido";
            //echo "<script type='text/javascript'>alert('$target_file');</script>";
            //header("location: mostrar.php?archivo=".basename($_FILES["fileToUpload"]["name"]));
            $foto=$target_file;
        }else{
            echo "Lo siento, ocurrio un error mientras se subia el archivo.";
    }
    }





    $sql = "INSERT INTO rh_empleados
            (nombre,apellido,cedula,direccion,telefono,cuenta,base,reajuste,salxhora,mandado,comision,ingreso,comentario,estado,foto)
            values
            ('".$nombre."','".$apellido."','".$cedula."','".$direccion."','".$telefono."','".$cuenta."','".$base."','".$reajuste."','".$salxhora."','".$mandado."','".$comision."','".$ingreso."','".$comentario."','".$estado."','".$foto."')
            ";

    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

       // echo 'Registro Guardado';
       header("location: planilla.php?msg=guardado");
   







?>