<?php

class ControladorClientes{

	/*=============================================
	CREAR CLIENTES
	=============================================*/

	static public function ctrCrearCliente(){

		if(isset($_POST["nuevoCliente"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoCliente"]) &&
			   preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["nuevoCorreo"]) && 
			   preg_match('/^[()\-0-9 ]+$/', $_POST["nuevoTelefono"])){

			   	$tabla = "clientes";

			   	$datos = array("nombre"=>strtoupper($_POST["nuevoCliente"]),
					           "cedula"=>$_POST["nuevoCedula"],
					           "correo"=>$_POST["nuevoCorreo"],
					           "telefono"=>$_POST["nuevoTelefono"],
					           "celular"=>$_POST["nuevaCelular"],
					           "tipo"=>$_POST["tipoCli"],
					       	   "actividad_comercial"=>$_POST["nuevaActividad"]);

			   	$respuesta = ModeloClientes::mdlIngresarCliente($tabla, $datos);

			   	if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El cliente ha sido guardado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "clientes";

									}
								})

					</script>';

					$consulta = "SELECT * FROM Consecutivos WHERE TIPO= '07'";

			        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

			              foreach ($ejecutar as $key => $value) {
			                $codcliente = $value["numero"];
			              }

					$codcliente=$codcliente+1; 
			        $actualiza = "UPDATE Consecutivos SET numero = $codcliente  WHERE TIPO= '07'";

			        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($actualiza);

				}

			
				if($respuesta == "error"){

					echo'<script>

						swal({
							  type: "error",
							  title: "¡El cliente no puede ir vacío o llevar caracteres especiales!",
							  showConfirmButton: true,
							  confirmButtonText: "Cerrar"
							  }).then(function(result){
								if (result.value) {

								window.location = "clientes";

								}
							})

				  	</script>';



				}
				if($respuesta != "ok" && $respuesta != "error") {
					echo'<script>

						swal({
							  type: "error",
							  title: "'.$respuesta.'",
							  showConfirmButton: true,
							  confirmButtonText: "Cerrar"
							  }).then(function(result){
								if (result.value) {

								window.location = "clientes";

								}
							})

				  	</script>';

				}

			}
		}
	}

	/*=============================================
	MOSTRAR CLIENTES
	=============================================*/

	static public function ctrMostrarClientes($item, $valor){

		$tabla = "clientes";

		$respuesta = ModeloClientes::mdlMostrarClientes($tabla, $item, $valor);

		return $respuesta;

	}

	/*=============================================
	EDITAR CLIENTE
	=============================================*/

	static public function ctrEditarCliente(){

		if(isset($_POST["editarCliente"])){

			if( preg_match('/^[()\-0-9 ]+$/', $_POST["editarTelefono"])){

			   	$tabla = "clientes";

			   	$datos = array("codigo"=>$_POST["idCliente"],
			   				   "nombre"=>$_POST["editarCliente"],
					           "cedula"=>$_POST["editarDocumentoId"],
					           "correo"=>$_POST["editarEmail"],
					           "telefono"=>$_POST["editarTelefono"],
					           "celular"=>$_POST["editarCelular"],
					           "comentario"=>$_POST["editarComentario"],
                                "actividad_comercial"=>$_POST["editarActividad"],
                                "Activo"=>$_POST["Activo"]);

			   	$respuesta = ModeloClientes::mdlEditarCliente($tabla, $datos);

			   	if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El cliente ha sido cambiado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "clientes";

									}
								})

					</script>';

				}

			}else{

				echo'<script>

					swal({
						  type: "error",
						  title: "¡El cliente no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "clientes";

							}
						})

			  	</script>';



			}

		}

	}

	/*=============================================
	ELIMINAR CLIENTE
	=============================================*/

	static public function ctrEliminarCliente(){

		if(isset($_GET["idCliente"])){

			$tabla ="clientes";
			$datos = $_GET["idCliente"];

			$respuesta = ModeloClientes::mdlEliminarCliente($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El cliente ha sido borrado correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result){
								if (result.value) {

								window.location = "clientes";

								}
							})

				</script>';

			}		

		}

	}

}

