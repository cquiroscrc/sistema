<?php
require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
    $id = $_REQUEST['id'];
}
?>

<h3 style="color:green">Enviados <i class='fa fa-paper-plane'></i></h3>
<table class="table table-striped">
    <thead>
    <tr><th></th><th>Para:</th><th>Nombre</th><th>Asunto</th><th>Estado</th><th>Fecha</th></tr>
    </thead>
    <?php

    $sql="SELECT * FROM Mensajes WHERE Envia='$id' AND Estado <> 'E' AND ESTADO <>'EE' ORDER BY Fecha DESC";
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($ejecutar as $key => $value) {
        $usuarioRecibe=$value["Recibe"];
        $asuntoEnvia=$value["Asunto"];
        $estadoEnvia=$value["Estado"];
        $idEnvia=$value["Id"];
        $fechaEnvia=$value["Fecha"];
        $sql_perfiles="SELECT * FROM acc_usuarios WHERE usuario='$usuarioRecibe'";

        $ejecutar2 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_perfiles);
        foreach ($ejecutar2 as $key => $value2) {
            $nombreMen=$value2["nombre"];
            $fotoMen=$value2["foto"];
        }
        echo "<tr>";
        echo"<td width='30'><span>";
        if($fotoMen != ""){
            echo '<img src="'.$fotoMen.'" style="width:32px;height:32px;" class="img-size-50 mr-3 img-circle">';
        }else{
            echo '<img src="vistas/img/usuarios/default/anonymous.png"  class="img-size-50 mr-3 img-circle" style="width:32px;height:32px;">';
        }
        echo"</span></td>";
        echo "<td width='100'>".$usuarioRecibe."</td><td width='200'>".$nombreMen."</td>";
        $tipo=',"Enviado"';
        echo "<td width='75'><button class='btn btn-primary' onclick='AbrirMensaje(".$idEnvia.$tipo.")'>".$asuntoEnvia."</button></td>";
        if ($estadoEnvia=='SL') {
             echo "<td width='40'><a onclick='Echat(".$idEnvia.$tipo.")'><i style='font-size:24px' class='fa fa-star text-yellow' ></i></a></td>";
        }
        if ($estadoEnvia=='V') {
            echo "<td width='40'><a onclick='Echat(".$idEnvia.$tipo.")'><i style='font-size:24px' class='fa fa-envelope-open text-blue' ></i></a></td>";
        }
        if ($estadoEnvia=='L') {
            echo "<td width='40'><a onclick='Echat(".$idEnvia.$tipo.")'><i style='font-size:24px' class='fa fa-thumbs-up text-green' ></i></a></td>";
        }
        if ($estadoEnvia=='ER') {
            echo "<td width='40'><a onclick='Echat(".$idEnvia.$tipo.")'><i style='font-size:24px' class='fa fa-thumbs-up text-green' ></i></a></td>";
        }

        echo "<td width='40'>".$fechaEnvia."</td></tr>";
    }
    ?>
</table>



