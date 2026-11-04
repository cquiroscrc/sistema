<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
     $actividad='000000';

    if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = strtoupper($_POST['nombre']);
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
    if((isset($_POST['actividad']))and(!empty($_POST['actividad']))){
        $actividad = $_POST['actividad'];
    }
 
    $ejecute="NO";
    $mensaje="";
    $largotipo= strlen($cedula);
    
    // Cedula Fisica
    if ($tipo=='01') {
       if ($largotipo<>9) {
            $mensaje="Número de cedula mal ingresado 9 Digitos";
            $ejecute="NO";
       }else{
            $ejecute="SI";
       }
    }

    // Cedula Juridica
    if ($tipo=='02') {
       if ($largotipo<>10) {
            $mensaje="Número de cedula Juridica mal ingresado 10 Digitos";
            $ejecute="NO";
       }else{
            $ejecute="SI";
       }
    }
    
    //DIMEX
    if ($tipo=='03') {
      if ($largotipo==11 or $largotipo==12) {
          $ejecute="SI";    
     }else{
          $mensaje="Número de DIMEX mal ingresado 11 o 12 Digitos";
          $ejecute="NO";
     }
    }
    
    //NITE
    if ($tipo=='04') {
     if ($largotipo<>10) {
          $mensaje="Número de NITE mal ingresado 10 Digitos";
          $ejecute="NO";
     }else{
          $ejecute="SI";
     }
    }


    if ($ejecute=="SI") {
      
    
        $consulta = "SELECT * FROM Consecutivos WHERE TIPO= '07'";

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

              foreach ($ejecutar as $key => $value) {
                $codcliente = $value["numero"];
              }


        $consulta="SELECT * FROM fechas WHERE movimiento ='Facturacion'" ; 

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

              foreach ($ejecutar as $key => $value) {
                $fecha_sis = $value['Fecha'];
              } 
        
        
        $inserta = "INSERT INTO clientes (codigo,nombre,telefono,direccion,cedula,credito,descuento,saldo,comentario,celular,correo,nacimiento,afiliacion,provincia,canton,distrito,barrio,otras_senas,TIPO_NIT,TEL_COD_PAIS_ISO,GLN,ACEPTA_DOC_ELECTRONICO,EMITE_DOC_ELECTRONICO,actividad_comercial) Values ('$codcliente' ,'$nombre','$telefono','CASA','$cedula',0,10,0,'NUEVOS','$celular','$correo','2016-01-01 00:00:00.000','$fecha_sis',1,1,1,1,'CASA','$tipo',506,'0000000000000','S','N','$actividad')";

        

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);


        $codcliente=$codcliente+1; 
        $actualiza = "UPDATE Consecutivos SET numero = $codcliente  WHERE TIPO= '07'";

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($actualiza);


    }

    if ($ejecute=="SI") {
    ?>
    1
     <?php 

    }else{
      ?>
      <?php echo $mensaje; ?>
      <?php 

    }
     ?>