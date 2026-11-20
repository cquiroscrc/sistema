<?php
require("../../modelos/conexion.php");
require("../../controladores/utilitaria.controlador.php");
require("../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
    $idmensaje = $_REQUEST['id'];
}
if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
    $tipo = $_REQUEST['tipo'];
}
?>

<!--=====================================
VER MENSAJE
======================================-->
<link rel="stylesheet" href="vistas/bower_components/summernote/summernote-bs4.css">
      	<div style="padding: 35px">
            <?php
            $sql="SELECT * FROM Mensajes WHERE Id=$idmensaje";

            $ejecutar=ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

            foreach ($ejecutar as $key => $valor) {
                $mensaje=$valor['Detalle'];
                $envia=$valor['Envia'];
                $estado=$valor['Estado'];
                $asunto=$valor['Asunto'];
                $fecha=$valor['Fecha'];
            }
            if ($estado=='SL') {
                if ($tipo=='Enviado') {

                }else {
                    $sql_update = "UPDATE Mensajes SET Estado='V' WHERE Id=$idmensaje";
                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_update);
                }
            }
            echo "<h4 style='display:inline;color:black'>Envia :</h4><h4 id='enviamensaje' style='display:inline;color:blue'>".$envia."</h4><h4 style='display:inline;color:black'>   Asunto: </h4><h4 id='asuntomensaje' style='display:inline;color:red'>".$asunto."</h4><h6 style='display:inline;'>   Fecha: ".$fecha."</h6><br>";
//            Aqui va el mensaje
            echo "<pre width='10' id='mensajemensaje'>".$mensaje."</pre>";
            //echo "<textarea width='10' id='mensajemensaje'>".$mensaje."</textarea>";
            ?>
        </div>
    <div class="box-footer no-padding">
		<button class="btn btn-warning" onclick="Responder(<?php echo $idmensaje;?>)"><i class='fa fa-share-square'></i> Responder</button>
        <button class="btn btn-success" onclick='MarcaMensaje(<?php echo $idmensaje.',"'.$tipo.'"';?>)'><i class='fa fa-thumbs-up'></i> Marcar como Hecho</button>
    </div>
<script src="vistas/bower_components/summernote/summernote-bs4.min.js"></script>
<script>
    $(function () {
        //Add text editor
        $('#mensajemensaje').summernote()
    })
</script>

