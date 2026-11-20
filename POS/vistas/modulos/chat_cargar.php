<?php
require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
    $id = $_REQUEST['id'];
}
?>

<!--=====================================
VER MENSAJE
======================================-->
<h3 style="color: darkred">Recibidos <i class='fa fa-inbox'></i></h3>

<table class="table table-striped">
    <thead>
    <tr><th></th><th>De:</th><th>Nombre</th><th>Asunto</th><th>Estado</th><th>Fecha</th></tr>
    </thead>
    <?php

    $sql="SELECT * FROM Mensajes WHERE Recibe='$id' AND Estado <> 'E' AND ESTADO <>'ER' ORDER BY Fecha DESC";
    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
    foreach ($ejecutar as $key => $value) {
        $usuarioEnvia=$value["Envia"];
        $asuntoEnvia=$value["Asunto"];
        $estadoEnvia=$value["Estado"];
        $idEnvia=$value["Id"];
        $fechaEnvia=$value["Fecha"];
        $sql_perfiles="SELECT * FROM acc_usuarios WHERE usuario='$usuarioEnvia'";
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
            echo '<img src="vistas/img/usuarios/default/anonymous.png" class="img-size-50 mr-3 img-circle" style="width:32px;height:32px;">';
        }
        echo"</span></td>";
        echo "<td width='100'>".$usuarioEnvia."</td><td width='200'>".$nombreMen."</td>";
        $tipo=',"Recibido"';
        echo "<td width='75'><button class='btn btn-primary' onclick='AbrirMensaje(".$idEnvia.$tipo.")'>".$asuntoEnvia."</button></td>";
        if ($estadoEnvia=='SL') {
            echo "<td width='40'><i style='font-size:24px' class='fa fa-star text-yellow' ></i></td>";
        }
        if ($estadoEnvia=='V') {
            echo "<td width='40'><i style='font-size:24px' class='fa fa-envelope-open text-blue' ></i></td>";
        }
        if ($estadoEnvia=='L') {
            echo "<td width='40'><a onclick='Echat(".$idEnvia.$tipo.")'><i style='font-size:24px' class='fa fa-thumbs-up text-green' ></i></a></td>";
        }
        if ($estadoEnvia=='EE') {
            echo "<td width='40'><a onclick='Echat(".$idEnvia.$tipo.")'><i style='font-size:24px' class='fa fa-thumbs-up text-green' ></i></a></td>";
        }
        echo "<td width='40'>".$fechaEnvia."</td></tr>";

    }
    ?>
</table>


