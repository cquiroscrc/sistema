<?php
//session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Chat Los Paticas
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Chat Los Paticas</li>
    </ol>
  </section>
  <section class="content">
        <div class="row">
            <div class="col-lg-6">
                <div class="box box-primary">
                <div id="resultado_chats" style="height: 340px;overflow-y: scroll;">
    <!--                aqui van los nombres de los chats-->
                </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="box box-warning">
                <div id="resultado_mensajes"style="height: 340px;overflow-y: scroll;">
                    <!--                aqui van los mensajes-->
                </div>
                </div>
            </div>
        </div>
      <div class="row">
          <div class="col-lg-6">
              <div class="box box-danger">
                  <div id="resultado_recibidos" style="height: 400px;overflow-y: scroll;">
                  </div>
              </div>
          </div>
          <div class="col-lg-6">
              <div class="box box-success" style="height: 400px;overflow-y: scroll;">
                  <?php
                  include "chat_redactar.php";
                  ?>
              </div>
          </div>
      </div>

  </section>

</div>

<script>
/*=============================================
  DOCUMENT READY
  =============================================*/
$(document).ready(function() {
    CargarMensajes();

});

/*=============================================
          MY AJAX HH (ejecuta AJAX)
 =============================================*/
function myAjaxHH(url,data){

    var ajaxhh=$.ajax({
        "method":"POST",
        "url": url,
        "data": data
    });
    return ajaxhh;
    console.log(ajaxhh);
}

/*=============================================
  ABRIR MENSAJE
  =============================================*/
function AbrirMensaje(id_Mensaje,tipo) {

  $('#resultado_mensajes').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
  $.ajax('vistas/modulos/chat_mensajes.php?id='+id_Mensaje+'&tipo='+tipo)
  .done(function(response){
      console.log(response);
    $('#resultado_mensajes').html(response);
    //CargarMensajes();
  });

}
/*=============================================
  ABRIR MENSAJE
  =============================================*/
function Echat(id_Mensaje,tipo) {

    swal({
        title: '¿Está seguro de eliminar el mensaje?',
        text: "¡Si no lo está puede cancelar la acción!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, eliminar mensaje!'
    }).then(function(result){
        if (result.value) {
            var param = {
                id_Mensaje: id_Mensaje,
                tipo:tipo
            };
            url="ajax/chat_eliminar.ajax.php";
            data="";
            data=param;
            myAjaxHH(url,data)
                .done( function(data) {
                    var parent = $(this).parent().remove();
                    swal({
                        type: "success",
                        title: "El mensaje ha sido eliminado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        console.log(result);
                        window.location.replace('chat');
                    });

                });
        }

    })

}

function CargarMensajes(){

    $('#resultado_chats').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    var User= $("#usuario_id").text();
    $.ajax('vistas/modulos/chat_cargar.php?id='+User)
        .done(function(response){
            $('#resultado_chats').html(response);
        });
    $('#resultado_recibidos').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    var User= $("#usuario_id").text();
    $.ajax('vistas/modulos/chat_enviados.php?id='+User)
        .done(function(response){
            $('#resultado_recibidos').html(response);
        });

}
function MarcaMensaje(id,tipo){

    $('#resultado_chats').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');

    $.ajax('vistas/modulos/chat_marcar.php?id='+id+'&tipo='+tipo)
        .done(function(response){
            console.log(response);
            CargarMensajes();
        });

}
function Responder(id){
    $("#para").val($("#enviamensaje").text())
    $("#MenAsunto").val("RE: "+$("#asuntomensaje").text());

    var x = $("#mensajemensaje")[0].innerHTML;
    $('#compose_textarea').summernote('pasteHTML', x);
    //$('#compose_textarea').summernote('insertText', x);

}



</script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Chat';
</script>