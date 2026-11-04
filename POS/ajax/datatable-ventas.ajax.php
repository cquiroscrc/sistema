<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";


class TablaProductosVentas{

 	/*=============================================
 	 MOSTRAR LA TABLA DE PRODUCTOS
  	=============================================*/ 

	public function mostrarTablaProductosVentas(){

		$item = null;
    	$valor = null;
    	$orden = "id";

  		$productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);
 		
  		if(count($productos) == 0){

  			echo '{"data": []}';

		  	return;
  		}	
		
  		$datosJson = '{
		  "data": [';

		  for($i = 0; $i < count($productos); $i++){

		  	/*=============================================
 	 		TRAEMOS LA IMAGEN
  			=============================================*/ 
  			include "../controladores/fotos.php";
		 
		          //$archivo="<img src='".$filename."' width='40px'>";
		         if (file_exists($filename)) {
		              $existe = 'SI';
		              $archivo="<img src='".$filename."' width='40px'>";
		          } else {
		              $archivo="<img src='vistas/img/productos/default/anonymous.png' width='40px'>";
		          	$existe = 'NO';
		          }	
		          	//echo $existe;

		          $imagen=$archivo; 

		  	//$imagen = "<img src='".$productos[$i]["imagen"]."' width='40px'>";

		  	/*=============================================
 	 		STOCK
  			=============================================*/ 

  			if($productos[$i]["EXISTENCIA"] <= 10){

  				$stock = "<button class='btn btn-danger'>".$productos[$i]["EXISTENCIA"]."</button>";

  			}else if($productos[$i]["EXISTENCIA"] > 11 && $productos[$i]["EXISTENCIA"] <= 15){

  				$stock = "<button class='btn btn-warning'>".$productos[$i]["EXISTENCIA"]."</button>";

  			}else{

  				$stock = "<button class='btn btn-success'>".$productos[$i]["EXISTENCIA"]."</button>";

  			}

		  	/*=============================================
 	 		TRAEMOS LAS ACCIONES
  			=============================================*/ 

		  	$botones =  "<div class='btn-group'><button class='btn btn-primary agregarProducto recuperarBoton' idProducto='".$productos[$i]["CODIGO"]."'>Agregar</button></div>"; 

		  	$datosJson .='[
			      "'.($i+1).'",
			      "'.$imagen.'",
			      "'.$productos[$i]["CODIGO"].'",
			      "'.$productos[$i]["DESCRIPCION"].'",
			      "'.$stock.'",
			      "'.$botones.'"
			    ],';

		  }

		  $datosJson = substr($datosJson, 0, -1);

		 $datosJson .=   '] 

		 }';
		
		echo $datosJson;


	}


}

/*=============================================
ACTIVAR TABLA DE PRODUCTOS
=============================================*/ 
$activarProductosVentas = new TablaProductosVentas();
$activarProductosVentas -> mostrarTablaProductosVentas();

