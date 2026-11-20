  <!-- summernote -->
  <link rel="stylesheet" href="vistas/bower_components/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Redactar nuevo mensaje</h3>
              </div>
              <div class="card-body">
                <div class="form-group">
                    <select class="form-control"  id="para" name="para" required>
                        <option value="">Para:</option>
                        <option value="todos">Todos</option>
                        <?php
                        $sql="SELECT * FROM acc_usuarios order By nombre";
                        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                        foreach ($ejecutar as $key => $value) {
                            echo '<option value="'.$value["usuario"].'">'.$value["nombre"].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                  <input id="MenAsunto" class="form-control" placeholder="Asunto:">
                </div>
                <div class="form-group">
                    <textarea id="compose_textarea" class="form-control" style="height: 300px">

                    </textarea>
                </div>
                  <button class="btn btn-success"  onclick='EnviarMensaje()'>Enviar</button>
              </div>
            </div>
        </div>
      </div>
    </section>
<!-- Summernote -->
<script src="vistas/bower_components/summernote/summernote-bs4.min.js"></script>
<!-- Page Script -->
<script>
  $(function () {
    //Add text editor
    $('#compose_textarea').summernote()
  })
  /*=============================================
  ENVIAR MENSAJE
  =============================================*/
  function EnviarMensaje() {
      var para = $("#para").val();
      var mensaje = $("#compose_textarea").val();
      var asunto = $("#MenAsunto").val();
      var envia = '<?php echo $_SESSION["usuario"];?>';

      var param = {
          Recibe: para,
          mensaje : mensaje,
          asunto : asunto,
          Envia : envia
      };

      $.ajax({
          data: param,
          url: "vistas/modulos/chat_enviar.php",
          method: "post",
          success: function (data) {
            console.log(data);
              $("#MenAsunto").val("");
              $("#compose_textarea").val("");
              var dropDown = document.getElementById("para");
              dropDown.selectedIndex = 0;
             window.location.replace('chat');
          }
      });

  }
</script>

