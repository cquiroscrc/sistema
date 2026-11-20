<?php

require_once "../controladores/proveedores.controlador.php";
require_once "../modelos/proveedores.modelo.php";

class AjaxProveedores{

	/*=============================================
	EDITAR PROVEEDORES
	=============================================*/	

	public $idProveedores;

	public function ajaxEditarProveedores(){
		
		//$tabla = "Proveedores"
		$item = "codigo";
		$valor = $this->idProveedores;

		$respuesta = ControladorProveedores::ctrMostrarProveedores($item, $valor);

		echo json_encode($respuesta);

	}
}

/*=============================================
EDITAR PROVEEDORES
=============================================*/	
if(isset($_POST["idProveedores"])){

	$proveedores = new AjaxProveedores();
	$proveedores -> idProveedores = $_POST["idProveedores"];
	$proveedores -> ajaxEditarProveedores();
}
