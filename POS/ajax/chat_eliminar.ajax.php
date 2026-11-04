<?php
    session_start();
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    if(isset($_REQUEST['id_Mensaje']) && !empty($_REQUEST['id_Mensaje'])){
      $id_Mensaje = $_REQUEST['id_Mensaje'];
    }
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
        $tipo = $_REQUEST['tipo'];
    }

    $sql="SELECT * FROM Mensajes WHERE Id=$id_Mensaje";
    $ejecutar=ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

    foreach ($ejecutar as $key => $valor) {
        $envia=$valor["Envia"];
        $recibe=$valor["Recibe"];
        $estado=$valor["Estado"];
    }
    $usuario=$_SESSION["usuario"];

    if ($estado=='L'){
        if ($usuario==$recibe){
            $sql_update="UPDATE Mensajes SET Estado='ER' WHERE Id=$id_Mensaje";
        }
    }

    if($usuario==$envia){
        if($estado<>'SL'){
            $sql_update="UPDATE Mensajes SET Estado='EE' WHERE Id=$id_Mensaje";
        }

    }

    if($estado=='EE'|| $estado=='ER'){
        $sql_update="UPDATE Mensajes SET Estado='E' WHERE Id=$id_Mensaje";
    }

        $ejecutar=ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);
        echo $sql_update;

