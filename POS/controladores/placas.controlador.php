<?php

class ControladorPlacas{

	/*=============================================
	CREAR CLIENTES
	=============================================*/

	static public function ctrCrearPlacas(){

		if(isset($_POST["nuevoPlaca"])){

				
			   	$tabla = "VAG";

			   	$datos = array("Placa"=>strtoupper($_POST["nuevoPlaca"]),
					           "Marca"=>strtoupper($_POST["nuevoMarca"]),
					           "Tipo"=>strtoupper($_POST["nuevoTipo"]),
					           "Year"=>strtoupper($_POST["nuevoYear"]),
					           "Vin"=>strtoupper($_POST["nuevoVin"]),
					           "Motor"=>strtoupper($_POST["nuevaMotor"]),
					           "Tipo2"=>strtoupper($_POST["nuevoAdicional"]));

			   	$respuesta = ModeloPlacas::mdlIngresarPlacas($tabla, $datos);
			   	var_dump($respuesta);
			   	if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "La Placa ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "placas";

									}
								})

					</script>';

				}

		}

	}

	/*=============================================
	MOSTRAR CLIENTES
	=============================================*/

	static public function ctrMostrarPlacas($item, $valor){

		$tabla = "VAG";

		$respuesta = ModeloPlacas::mdlMostrarPlacas($tabla, $item, $valor);

		return $respuesta;

	}

}

