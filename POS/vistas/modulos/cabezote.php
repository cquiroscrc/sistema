 <header class="main-header">
	<!--=====================================
	LOGOTIPO
	======================================-->
	<a href="inicio" class="logo">
		<!-- logo mini -->
		<span class="logo-mini">
			<img src="vistas/img/plantilla/icono-blanco.png" class="img-responsive" style="padding:10px">
		</span>
		<!-- logo normal -->
		<span class="logo-lg">
			<img src="vistas/img/plantilla/logo-blanco-lineal.png" class="img-responsive" style="padding:10px 0px">
		</span>
	</a>
	<!--=====================================
	BARRA DE NAVEGACIÓN
	======================================-->
	<nav class="navbar navbar-static-top" role="navigation">
		<!-- Botón de navegación -->
	 	<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        	<span class="sr-only">Toggle navigation</span>
      	</a>
		<div class="navbar-custom-menu">
            <!--        aqui-->
            <!-- Messages Dropdown Menu -->
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i style="color:white;font-size:28px" class="fa fa-comments"></i>
<!--                cantidad de mensajes-->
                <span id="mensajesSL" class="badge badge-danger navbar-badge"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="chat" id="listado_mensajes" class="dropdown-item">
                    <!-- Message Start -->
                </a>
                <div class="dropdown-divider"></div>
                <div class="dropdown-divider"></div>
                <a href="chat" class="dropdown-item dropdown-footer">Ver todos los mensajes</a>
            </div>
            <!--        aqui-->
			<ul class="nav navbar-nav">
				<input type="hidden" id="FechaReportes" name="FechaReportes" >
				<li>
					<span class="logo-lg" style="color:white;" class="hidden-s"><h4><b id="FacturaActualizada"></b>&nbsp;&nbsp;</h4>
					 </span>
				</li>
				<li>
					<span class="logo-lg" style="color:red" class="hidden-s"><h4><b id="FechaActualizada"></b></h4>
					 </span>
				</li>
				<!-- Perfil -->
				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<?php
					if($_SESSION["foto"] != ""){
						echo '<img src="'.$_SESSION["foto"].'" class="user-image">';
					}else{
						echo '<img src="vistas/img/usuarios/default/anonymous.png" class="user-image">';
					}
					?>
						<span class="hidden-xs"><b><?php  echo $_SESSION["nombre"]; ?></b></span>
					</a>
					<!-- Dropdown-toggle -->
					<ul class="dropdown-menu">
						<li class="user-body">
							<div class="pull-right">
								<ul class="menu">
			                	<a href="#">
						            <h4 style="color:red">
						            	<i style="color:blue" class="fa fa-wifi"></i>
						            	<?php  echo $_SESSION["ip"]; ?>
						                <?php echo '<p id=usuario_id>'.$_SESSION["usuario"].'</p>'; ?>
						            </h4>
			                	</ul>
			                   	<br>
			                   	<button onclick="CambioPassword(<?php echo $_SESSION["id"]; ?>)" class="btn btn-warning btn-xs">Cambiar clave</button>
								<a href="salir" style="color:white;" class="btn btn-danger btn-xs">Salir</a>
							</div>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>

 </header>

 <script language="JavaScript">

 	function CambioPassword(id){
 		swal({
                title:"Cambio de contraseña",
                text: "Digite la nueva contraseña",
                input: 'password',
                type: "question",
                
              }).then(resultado => {
                  if (resultado.value) {
                      let pass = resultado.value;
                      console.log(pass);
                      $.ajax('ajax/actualizar_pass.ajax.php?id='+id+'&pass='+pass)
                      .done(function(response){
                        //console.log(response);
                        swal({
		                  type: "success",
		                  title: "El cliente ha sido agregado correctamente",
		                  showConfirmButton: true,
		                  confirmButtonText: "Cerrar"
		                  }).then(function(result){
		                      if (result.value) {
		                        window.location.replace('inicio');
		                      }
		                  });
                        
                      });
              }
            });


 	}



 	$(document).ready(function(){
 	    ActualizaDatos();
 		Acc();
 	});

    function Acc() {
        $.ajax('vistas/modulos/Actualiza.php')
            .done(function(response){

                var recibido= JSON.parse(response);
                $('#listado_mensajes').empty()
                $('#mensajesSL').text(recibido.mensaje);
                $('#FechaReportes').val(recibido.fecha);
                $('#FacturaActualizada').text(recibido.factura);
                $('#FechaActualizada').text(recibido.fecha_actual);
                $('#listado_mensajes').append(recibido.html);
                if (recibido.mensaje!=0){
                    swal({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tienes un mensaje nuevo',
                        showConfirmButton: false,
                        timer: 1000
                    })

                }

            });
    }
    var auto = setInterval(
        function()
        {
            $('#FechaReportes').fadeOut('slow').load(Acc()).fadeIn("slow");
        }, 120000);

	function ActualizaDatos(){
        $('#listado_mensajes').empty()
		<?php 
			$sql_consecutivo="SELECT * FROM Consecutivos WHERE tipo='01'";       // Consecutivo factura    
		    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivo);
		  
		    for ( $i = sizeof($respuesta) - 1; $i >= 0; $i--) {      
		      $FacturaActual=$respuesta[$i]["numero"];
		      $FacturaActual=str_pad($FacturaActual, 14, "0", STR_PAD_LEFT); 
  			  $FacturaActual='F-'.$FacturaActual; 
  			  $FacturaActual=trim($FacturaActual);
		     
		    }

		    $fechas="SELECT * FROM fechas";
		    $fecha = ControladorUtilitaria::ctrMostrarUtilitariaVar($fechas);
            foreach ($fecha as $key => $value) {
             $fechaActual=$value["Fecha"];
            }
            $fechaActual=strtotime($fechaActual);
            $fechaReportes=date("m/d/Y", $fechaActual);
            $fechaActual = date("d/m/Y", $fechaActual);
            //Mensajes
            $UserActual=$_SESSION["usuario"];
            $sql_mensajes="SELECT * FROM Mensajes WHERE Recibe='$UserActual' AND ESTADO ='SL'";
            $men=0;
            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_mensajes);
            foreach ($ejecutar as $key => $value) {
              $enviaMen=$value["Envia"];
              $asuntoMen=$value["Asunto"];
              $fechaMen=$value["Fecha"];

              $sql_perfiles="SELECT * FROM acc_usuarios WHERE usuario='$enviaMen'";
                $ejecutar2 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_perfiles);
                foreach ($ejecutar2 as $key => $value2) {
                    $nombreMen=$value2["nombre"];
                    $fotoMen=$value2["foto"];
                }
                if($fotoMen != ""){

                }else{
                    $fotoMen="vistas/img/usuarios/default/anonymous.png";
                }
              ?>
                        var html= '<div class="media">\
                        <img src="<?php echo $fotoMen; ?>" style="width:32px;height:32px;" alt="User Avatar" class="img-size-50 mr-3 img-circle">\
                        <div class="small-body">\
                          <h3 id="mensajesEnvia" class="dropdown-item-title">\
                          <?php echo $nombreMen; ?>\
                        </h3>\
                        <p id="mensajesAsunto" class="text-sm"><?php echo $asuntoMen; ?> <span class="float-right"><i class="fa fa-star text-yellow"></i></span></p>\
                        <p class="text-sm text-muted"><i class="fa fa-bell"></i> <?php echo $fechaMen; ?></p>\
                        </div></div>';
                        $('#listado_mensajes').append(html);

              <?php
            $men=$men+1;
            }
		 ?>

	    $('#mensajesSL').text("<?php echo $men; ?>");
	    $('#FechaReportes').val("<?php echo $fechaReportes; ?>");
	    $('#FacturaActualizada').text("<?php echo $FacturaActual; ?>");
	    $('#FechaActualizada').text("<?php echo $fechaActual; ?>");
	    setTimeout("Acc()",60000)
	}
</script>