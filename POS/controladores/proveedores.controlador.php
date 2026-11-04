<?php

class ControladorProveedores{

	/*=============================================
	CREAR ProveedorS
	=============================================*/

	static public function ctrCrearProveedores(){

		if(isset($_POST["nuevoProveedor"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoProveedor"])){

				$tabla = "Proveedores";


				$datos = array("nombre" => $_POST["nuevoProveedor"],
					           "direccion" => $_POST["nuevoDireccion"],
					           "codigo" => $_POST["nuevoCodigo"],
					           "telefono" => $_POST["nuevoTelefono"],
					           "pais" =>$_POST["nuevoPais"],
					           "credito" =>$_POST["nuevoCredito"],
					           "comentarios" =>$_POST["nuevoComentarios"],
					           "shortname" =>$_POST["nuevoShortname"],
					           "tipo" =>$_POST["nuevoTipo"]);


				$respuesta = ModeloProveedores::mdlIngresarProveedores($tabla, $datos);
								
				//print_r($respuesta);
				if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El Proveedor ha sido guardado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "proveedores";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					swal({
						  type: "error",
						  title: "¡El Proveedor no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "proveedores";

							}
						})

			  	</script>';

			}

		}

	}

	/*=============================================
	MOSTRAR Proveedor
	=============================================*/

	static public function ctrMostrarProveedores($item, $valor){

		$tabla = "Proveedores";

		$respuesta = ModeloProveedores::mdlMostrarProveedores($tabla, $item, $valor);

		return $respuesta;
	
	}

	/*=============================================
	EDITAR Proveedor
	=============================================*/

	static public function ctrEditarProveedores(){

		if(isset($_POST["editarProveedor"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarProveedor"])){

				$tabla = "Proveedores";

				$datos = array("nombre" => $_POST["editarProveedor"],
					           "direccion" => $_POST["editarDireccion"],
					           "codigo" => $_POST["editarCodigo"],
					           "telefono" => $_POST["editarTelefono"],
					           "pais" =>$_POST["editarPais"],
					           "credito" =>$_POST["editarCredito"],
					           "comentarios" =>$_POST["editarComentarios"],
					           "shortname" =>$_POST["editarShortname"],
					           "tipo" =>$_POST["editarTipo"]);
				
				$respuesta = ModeloProveedores::mdlEditarProveedores($tabla, $datos);
			

				if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El Proveedor ha sido cambiado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "proveedores";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					swal({
						  type: "error",
						  title: "¡El Proveedor no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "proveedores";

							}
						})

			  	</script>';

			}

		}

	}

	/*=============================================
	BORRAR Proveedor
	=============================================*/

	static public function ctrBorrarProveedores(){

		if(isset($_GET["idProveedores"])){

			$tabla ="Proveedores";
			$datos = $_GET["idProveedores"];

			$respuesta = ModeloProveedores::mdlBorrarProveedores($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

					swal({
						  type: "success",
						  title: "El Proveedor ha sido borrado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "proveedores";

									}
								})

					</script>';
			}
		}
		
	}
}
