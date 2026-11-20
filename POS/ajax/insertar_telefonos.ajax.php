<?php

    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['Numero']) && !empty($_REQUEST['Numero'])){
      $Numero = $_REQUEST['Numero'];
    }

    if(isset($_REQUEST['Descripcion']) && !empty($_REQUEST['Descripcion'])){
      $Descripcion = ucwords($_REQUEST['Descripcion']);

    }

    if(isset($_REQUEST['Correo']) && !empty($_REQUEST['Correo'])){
      $Correo = $_REQUEST['Correo'];
    }
     
  echo '<script language="javascript">alert('.$Correo.');</script>';
      //// *************** Incluye la consulta ********

    $sql="INSERT INTO tbl_TEL_Numeros (Numero, Descripcion, Correo) VALUES ('$Numero','$Descripcion','$Correo')";    

      //// *************** Ejecuto la consulta ******************
          
      $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
        $data["data"][]= $res_busqueda;
        var_dump($res_busqueda);
        echo json_encode($data);

